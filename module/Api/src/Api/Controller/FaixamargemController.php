<?php
namespace Api\Controller;

use Zend\View\Model\JsonModel;
use Core\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Client;
use Core\Ad\adLDAPFactory;
use Zend\Json\Json;
use Zend\Db\ResultSet\HydratingResultSet;
use Core\Stdlib\StdClass;
use Core\Hydrator\ObjectProperty;
use Core\Hydrator\Strategy\ValueStrategy;

class FaixamargemController extends AbstractRestfulController
{
    
    /**
     * Construct
     */
    public function __construct()
    {
        
    }
    
    public function listarEmpresasAction()
    {
        $data = array();
        
        try {

            $pNode = $this->params()->fromQuery('node',null);

            $sql = "select distinct emp, cod_empresa id_empresa
                        from VW_SKEMPRESA
                    where emp not in ('CD','EC','M2','PAR','PLA','SP','TL')
                    order by emp";
            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);

            $objReturn = $this->getCallbackModel();
            
        } catch (\Exception $e) {
            $objReturn = $this->setCallbackError($e->getMessage());
        }
        
        return $objReturn;
    }

    public function listaritenslojaAction()
    {
        $data = array();
        
        try {

            $pData    = $this->params()->fromQuery('data',null);

            $em = $this->getEntityManager();
            
            $sql = "
            select 'RA' emp, '123,98' valor1, '20,65' valor2 from dual union
            select 'PA' emp, '100,98' valor1, '-10' valor2 from dual union
            select 'GO' emp, '64,00' valor1, '-1,65' valor2 from dual ";

            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('valor1', new ValueStrategy);
            $hydrator->addStrategy('valor2', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }

    public function faixamargemAction()
    {
        $arrayY     = array();
        $arrayX     = array();
        $margem     = array();
        $filiais    = array();
        
        try {

            $x          = $this->params()->fromPost('x',null);
            $y          = $this->params()->fromPost('y',null);
            $v          = $this->params()->fromPost('valorprincipal',null);
            $codEmpresa = $this->params()->fromPost('codEmpresa',null);
            $pDataInicio= $this->params()->fromPost('dataInicio',null);
            $pDataFim   = $this->params()->fromPost('dataFinal',null);
            $idMarcas   = $this->params()->fromPost('idMarcas',null);
            $codProdutos= $this->params()->fromPost('idProduto',null);
            $categoria  = $this->params()->fromPost('categoria',null);

            $dataXy = [
                "emp", //Filial
                "mb", //Margem
                "fx_mb_v", //Faixa Margem
                "Faixa Faturamento",
                "Pareto Faturamento"
            ];

            $dataZ = [
                "rol", //Filial
                "mb", //Margem
                "qtd", //Faixa Margem
                "lb",
                "cc",
                "nf"
            ];

            $fortmatZ = [
                0,
                2,
                0,
                0,
                0,
                0
            ];

            $dataZcol = [
             "round(sum(a.rol),0)",
             "case when  sum(a.rol) > 0 then round( (sum(a.lb) / sum(a.rol) ) * 100 ,2) else 0 end",
             "round(sum(a.qtd),0)",
             "round(sum(a.lb),0)",
             "round(count(distinct cc),0)",
             "round(count(distinct nf),0)"
            ];

            $x = empty($x) ? 0 : $x;
            $y = !$y && $y != '0' ? 1 : $y;
            $z = empty($v) ? 0 : $v;
            $v = !$v ? 'rol' : $dataZ[$v];

            /////////////////////////////////////////////////////////////////
            if($codEmpresa){
                $codEmpresa = implode(",",json_decode($codEmpresa));
            }
            $andFilial ='';
            if($codEmpresa){
                $andFilial = " and cod_empresa in ($codEmpresa)";
            }

            if($codProdutos){
                $codProdutos =  implode("','",json_decode($codProdutos));
            }
            
            if($categoria){
                $categoria = implode("','",json_decode($categoria));
            }

            $andData = '';
            if($pDataInicio){
                $andData = "and trunc(a.data) >= to_date('".$pDataInicio."')";
            }else{
                $sysdateInicio = 'add_months(trunc(sysdate,\'MM\'),-0)';
                // $andData = "and trunc(a.data) >= to_char($sysdateInicio,'dd/mm/yyyy')";
            }
            if($pDataFim){
                $andData .= " and trunc(a.data) <= to_date('".$pDataFim."')";
            }else{
                $andData .= " and trunc(a.data) <= sysdate";
            }

            if($idMarcas){
                $idMarcas =  implode(",",json_decode($idMarcas));
            }
            $andMarca = '';
            if($idMarcas){
                $andMarca = "and a.marca in (select descricao_marca from VW_SKMARCA where cod_marca in ($idMarcas))";
            }
            $andProduto = '';
            if($codProdutos){
                $andProduto = " and a.cod_produto in ('$codProdutos')";
            }

            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            $conn->beginTransaction();

            try {

                $sqlTmp ='';
                if($categoria){
                    
                    $sqlTmp ="insert into vw_skproduto_categoria_tmp
                    select cod_produto, categoria
                    from vw_skproduto_categoria where categoria in ('$categoria')";

                    $stmt = $conn->prepare($sqlTmp);
                    $stmt->execute();

                    $andProduto = " and a.cod_produto in (select cod_produto
                                                         from vw_skproduto_categoria_tmp
                                                       where categoria in ('$categoria'))";
                }

                $sql1 = "select $dataXy[$y] AS y
                            FROM (select emp
                                        ,mb
                                        ,qtd
                                        ,rol
                                        ,(CASE WHEN mb <= 15 THEN 1 
                                            WHEN mb > 15 AND mb <= 20 THEN 2
                                            WHEN mb > 20 AND mb <= 29 THEN 3
                                            WHEN mb >= 30 THEN 4 END) fx_mb_o
                                        ,(CASE WHEN mb <= 15 THEN '0-15' 
                                            WHEN mb > 15 AND mb <= 20 THEN '16-20'
                                            WHEN mb > 20 AND mb <= 29 THEN '21-29'
                                            WHEN mb >= 30 THEN '30-x' END) AS fx_mb_v
                                    FROM (SELECT TRUNC(a.data,'MM') AS data
                                                    ,a.emp 
                                                    ,a.cod_produto
                                                    ,a.nota
                                                    ,a.cnpj_parceiro as cc
                                                    ,SUM(qtd) AS qtd
                                                    ,SUM(rol) AS rol
                                                    ,SUM(lb) AS lb
                                                    ,SUM(cmv) AS cmv
                                                    ,ROUND(SUM(lb)/SUM(rol)*100) AS mb
                                                FROM VM_SKVENDANOTA a
                                            WHERE TRUNC(a.data,'MM') >= '01/11/2021'
                                            $andFilial
                                            $andData
                                            $andMarca
                                            $andProduto
                                            GROUP BY TRUNC(a.data,'MM'), a.emp, a.cod_produto, a.nota, a.cnpj_parceiro)
                                    )
                            GROUP BY $dataXy[$y]
                            ORDER BY 1 DESC";
                            
                $stmt = $conn->prepare($sql1);
                $stmt->execute();
                $resultY = $stmt->fetchAll();

                $hydrator = new ObjectProperty;
                $hydrator->addStrategy('y', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSetY = new HydratingResultSet($hydrator, $stdClass);
                $resultSetY->initialize($resultY);
                
                $sql = "select $dataXy[$x] as x
                                ,$dataXy[$y] as y
                                ,$dataZcol[$z] as z
                        from  (select emp
                                        ,mb
                                        ,rol
                                        ,qtd
                                        ,lb
                                        ,nf
                                        ,cc
                                        ,(CASE WHEN mb <= 15 THEN 1 
                                            WHEN mb > 15 AND mb <= 20 THEN 2
                                            WHEN mb > 20 AND mb <= 29 THEN 3
                                            WHEN mb >= 30 THEN 4 END) fx_mb_o
                                        ,(CASE WHEN mb <= 15 THEN '0-15' 
                                            WHEN mb > 15 AND mb <= 20 THEN '16-20'
                                            WHEN mb > 20 AND mb <= 29 THEN '21-29'
                                            WHEN mb >= 30 THEN '30-x' END) AS fx_mb_v
                                FROM (SELECT TRUNC(a.data,'MM') AS data
                                             ,a.emp
                                             ,a.cod_produto
                                             ,a.nota as nf
                                             ,a.cnpj_parceiro as cc
                                             ,SUM(qtd) AS qtd
                                             ,SUM(rol) AS rol
                                             ,SUM(lb) AS lb
                                             ,SUM(cmv) AS cmv
                                             ,ROUND(SUM(lb)/SUM(rol)*100) AS mb
                                        FROM VM_SKVENDANOTA a
                                        where TRUNC(a.data,'MM') >= '01/11/2021'
                                        $andFilial
                                        $andData
                                        $andMarca
                                        $andProduto
                                        GROUP BY TRUNC(a.data,'MM'), a.emp, a.cod_produto, a.nota, a.cnpj_parceiro)
                                ) a
                                group by $dataXy[$x]
                                        ,$dataXy[$y]
                            order by 1, 2 desc";
                        
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll();

                $hydrator = new ObjectProperty;
                $hydrator->addStrategy('x', new ValueStrategy);
                $hydrator->addStrategy('y', new ValueStrategy);
                $hydrator->addStrategy('z', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $conn->commit();

            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            $yCategoria = array();
            $posicaoY = 0;
            foreach ($resultSetY as $row) {
                $elementos = $hydrator->extract($row);

                $yCategoria[(string)$elementos['y']] = $posicaoY;
                
                $arrayY[]   = $elementos['y'];

                $posicaoY++;

            }

            $arrayPosition  = array();
            $contLine       = 0;
            $contColuna     = 0;
            $xAnterior      ='';
            $zMinMax        = array();
            foreach ($resultSet as $row) {
                
                $elementos = $hydrator->extract($row);

                $paramY     = $elementos['y'];
                $zMinMax[]  = $elementos['z'];

                if($xAnterior && $elementos['x'] <> $xAnterior){

                    while($contLine < count($yCategoria)){// adiciona posição null (Caso ultima margem anterior nao seja final)

                        $arrayPosition[] = [$contColuna,$contLine, null];
                        $contLine++;
                    }

                    $arrayX[]= $xAnterior;
                    $contLine=0;
                    $contColuna++;

                }

                if( count($yCategoria) > 0){

                    while($contLine < $yCategoria[$paramY] ){// adiciona posição null (Caso margem não seja a inicial)

                        $arrayPosition[] = [$contColuna,$contLine, null];

                        $contLine++;
                    }
                }

                $valorZ = $elementos['z'];
                $arrayPosition[] = array($contColuna,$yCategoria[$paramY],$valorZ);
                $contLine++;
                $xAnterior = $elementos['x'];

            }

            while($contLine < count($yCategoria)){/// adiciona posição null (Caso ultima margem nao seja final na ultima coluna)

                $arrayPosition[] = [$contColuna,$contLine, null];

                $contLine++;
            }

            $arrayX[]= $xAnterior;

            sort($zMinMax);
            $min = 0;
            for ($i=0; $i < count($zMinMax); $i++) {
                
                if($zMinMax[$i]){
                    $min = $zMinMax[$i];
                    break;
                }
            }

            $this->setCallbackData($arrayPosition);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        $objReturn = $this->getCallbackModel();
        $objReturn->yCategories = $arrayY;
        $objReturn->xCategories = $arrayX;
        $objReturn->zMinMax = [$min,$zMinMax[count($zMinMax)-1]];
        $objReturn->nmPrincipal = $v;
        $objReturn->valorFormat = $fortmatZ[$z];
        // $objReturn->referencia  = array('incio'=> $resultCount[0]['DATAINICIO'],'fim'=> $resultCount[0]['DATAFIM']);

        return $objReturn; 
    }

    public function listargrupomarcaAction(){
        // return array idEmpresas

        $marcas = array();

        $marcas[] = ['id'=> 'G1 EVERTONOPE','idMarcas'=> [10376,
        580,598,10426,181,583,10307,602,10103,10020,172,334,77,175,64,10407,10406,10160,106,10016,117,522,270,8,7,
        10102,10223,10123,10410,1017,10158,10129,10011,195,73,10137,582,100,354,10394,10325,99,88,10202,82,10146,
        300,10351,10418,214,542,10023,10321,349,584,293,341,1013,10017,3,555,556,10148,10157,566,10388,122,538,330,
        1020,342,10176,567,23593,81,200,60,616,319,264,289,10396,70,148,10341,47,304,10186,134,10353,105,610,10100,
        10141,10026,10029,10436,10237,288,1001,10201,51,10200,154
        ]];
        $marcas[] = ['id'=> 'G2 MAYKONRS','idMarcas'=> [10159,10104,163,10412,10421,10101,10314,10126,10154,59,10305,
        205,10281,10316,10302,92,199,61,1012,10133,10405,10244,10444,10300,197,10013,10136,10413,10411,10422,10415,10373,
        302,617,10027,10198,9,10,10372,11,12,10403,322,97,10395,10419,23,539,10014,10140,10414,113,104,10423,10139,261,
        280,519,10107,10404,10425,10193,346,10153,10375,10440,140,10345,244,335,356,10191,10184,255,10112,121,83,10409,
        279,10179,10420,150
        ]];
        $marcas[] = ['id'=> 'G3 WELISONOPE','idMarcas'=> [161,10328,10192,13,131,612,10301,10174,290,10293,10131,169,604,
        211,115,143,10342,10343,10432,10143,553,10021,10274,10279,10386,10235,620,267,10295,10135,38,10441,10187,10352,89,
        75,76,10400,10319,206,594,10416,10234,613,22,10196,10206,10433,146,282,10389,314,74,560,1015,9999,72,10114,351,
        10165,328,19,10355,10178,10183,614
        ]];

        $this->setCallbackData($marcas);
        return $this->getCallbackModel();
    }

    public function listarmarcaAction()
    {
        $data = array();

        $emp = $this->params()->fromQuery('emp',null);

        try {

            $session = $this->getSession();
            $usuario = $session['info'];

            $em = $this->getEntityManager();
            
            $sql = "select distinct cod_marca as id_marca, descricao_marca marca
                        from VW_SKMARCA
                    order by descricao_marca
            ";
            
            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }

    public function listarprodutosAction()
    {
        $data = array();
        
        try {

            $pEmp    = $this->params()->fromQuery('emp',null);
            $pCod    = $this->params()->fromQuery('codItem',null);
            $tipoSql = $this->params()->fromQuery('tipoSql',null);

            if(!$pCod){
                throw new \Exception('Parâmetros não informados.');
            }

            $em = $this->getEntityManager();

            if(!$tipoSql){
                $filtroProduto = "like upper('".$pCod."%')";
            }else{
                $produtos =  implode("','",json_decode($pCod));
                $filtroProduto = "in ('".$produtos."')";
            }
            
            $sql = "select distinct nvl(COD_NBS,'') COD_ITEM, DESCRICAO
                from VW_SKPRODUTO 
            where 1 =1 
            and nvl(COD_NBS,'') $filtroProduto";            

            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }

    public function listaridprodutoAction()
    {
        $data = array();
        
        try {

            $pEmp    = $this->params()->fromQuery('emp',null);
            $pCod    = $this->params()->fromQuery('idProduto',null);
            $tipoSql = $this->params()->fromQuery('tipoSql',null);

            if(!$pCod){
                throw new \Exception('Parâmetros não informados.');
            }

            $em = $this->getEntityManager();

            if(!$tipoSql){
                $filtroProduto = " in upper(".$pCod.")";
            }else{
                $produtos =  implode(",",json_decode($pCod));
                $filtroProduto = " in ($produtos)";
            }

            $sql = "select distinct nvl(COD_PRODUTO,'') ID_PRODUTO, DESCRICAO
                from VW_SKPRODUTO 
            where 1 = 1 
            and nvl(COD_PRODUTO,'') $filtroProduto";

            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('id_produto', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }

    public function listarcategoriaAction()
    {
        $data = array();
        
        try {

            $em = $this->getEntityManager();

            $sql = "select distinct categoria
                        from vw_skproduto_categoria
                    order by categoria";
  
            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }

    public function listarclientesAction()
    {
        $data = array();
        
        try {

            $pEmp    = $this->params()->fromQuery('emp',null);
            $idPessoa= $this->params()->fromQuery('idPessoa',null);
            $tipoSql = $this->params()->fromQuery('tipoSql',null);

            if(!$idPessoa){
                throw new \Exception('Parâmetros não informados.');
            }

            $em = $this->getEntityManager();

            if(!$tipoSql){
                $filtroCliente = "and ( id_pessoa like upper('".$idPessoa."%') or nome like upper('%".$idPessoa."%'))";
            }else{

                $Cliente =  implode("','",json_decode($idPessoa));
                $filtroCliente = "and id_pessoa in (".$Cliente.")";
            }
            
            $sql = "select id_pessoa,
                           nome descricao
                        from ms.pessoa
                    where 1 =1
                    $filtroCliente
                    order by id_pessoa";

            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            // $hydrator->addStrategy('custo_contabil', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }

    public function listareixosAction()
    {
        $data = array();
        
        try {

            // $pEmp    = $this->params()->fromQuery('emp',null);
            $data[] = ['id'=> 'ROL','name'=> 'ROL','vExemplo'=> 1000000];
            $data[] = ['id'=> 'MB','name'=> 'MB','vExemplo'=> 30];
            $data[] = ['id'=> 'NF','name'=> 'NF','vExemplo'=> 1000];
            $data[] = ['id'=> 'QTDE','name'=> 'QTDE','vExemplo'=> 200];
            $data[] = ['id'=> 'lcrt','name'=> 'Limite Crédito','vExemplo'=> 1000000];

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }
    
}
