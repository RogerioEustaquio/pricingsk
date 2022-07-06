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

class BolhasvendasController extends AbstractRestfulController 
{
    
    /**
     * Construct
     */
    public function __construct()
    {
        
    }

    private function create_data($data){

        $ObjDate = date_create();
        $dia = (float) substr($data,0,2);
        $mes = (float) substr($data,3,2);
        $ano = (float) substr($data,6,4);

        $ObjDate->setDate( $ano, $mes, $dia); // Ou date_date_set($ObjDate, $ano, $mes, $dia); 
        $timeEmissao = date_timestamp_get($ObjDate);

        return $timeEmissao ;
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

    public function funcregionais($id){
        // return array idEmpresas

        $regionais = array();

        // $regionais[] = ['id'=> 'R1','idEmpresas'=> ['AR','BX','CG','JN','MA','NA','RE','SA']];
        // $regionais[] = ['id'=> 'R2','idEmpresas'=> ['FZ','IM','AP','MB','M1','SL','TE']];
        // $regionais[] = ['id'=> 'R3','idEmpresas'=> ['BH','CB','LE','GO','JF','RJ','SN','VC']];
        
        // 13/06/2022
        $regionais[] = ['id'=> 'R1','idEmpresas'=> ['AR','BX','CG','FZ','JN','MA','NA','RE']];
        $regionais[] = ['id'=> 'R2','idEmpresas'=> ['IM','AP','MB','M1','RJ','SL','TE']];
        $regionais[] = ['id'=> 'R3','idEmpresas'=> ['BH','CB','LE','GO','JF','SA','SN','VC']];

        foreach($regionais as $row){

            if($row['id'] == $id){
                return $row['idEmpresas'];
            }
        }

        return null;
    }

    public function listarregionalAction()
    {
        $data = array();
        
        try {

            $pEmp    = $this->params()->fromQuery('emp',null);

            $data[] = ['id'=> 'R1','regional'=> 'R1 '];
            $data[] = ['id'=> 'R2','regional'=> 'R2 '];
            $data[] = ['id'=> 'R3','regional'=> 'R3 '];

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

    public function listarmarcaAction()
    {
        $data = array();

        $emp = $this->params()->fromQuery('emp',null);

        try {

            // $session = $this->getSession();
            // $usuario = $session['info'];

            $em = $this->getEntityManager();
            
            $sql = "select distinct cod_marca as id_marca
                          , descricao_marca marca
                        from VW_SKMARCA
                    order by descricao_marca";
            
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

    public function listareixosAction()
    {
        $data = array();
        
        try {

            // $pEmp    = $this->params()->fromQuery('emp',null);

            $data[] = ['id'=> 'QTD','name'=> 'QTD','vExemplo'=> 1000];
            $data[] = ['id'=> 'ROL','name'=> 'ROL','vExemplo'=> 1000000];
            $data[] = ['id'=> 'LB','name'=> 'LB','vExemplo'=> 1000000];
            $data[] = ['id'=> 'MB','name'=> 'MB','vExemplo'=> 30];
            $data[] = ['id'=> 'CC','name'=> 'CC','vExemplo'=> 1000];
            $data[] = ['id'=> 'CMV','name'=> 'CMV','vExemplo'=> 1000];
            $data[] = ['id'=> 'GIRO','name'=> 'GIRO','vExemplo'=> 300];
            $data[] = ['id'=> 'TRI','name'=> 'TRI','vExemplo'=> 3000];
            $data[] = ['id'=> 'ESTOQUEVALOR','name'=> 'Estoque Valor','vExemplo'=> 200];

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }

    public function posicionamentoAction()
    {
        $data = array();
        
        try {

            $posicionamento = $this->params()->fromPost('posicionamento',null);
            $idEmpresas     = $this->params()->fromPost('idEmpresas',null);
            $regional       = $this->params()->fromPost('regional',null);
            $pDataInicio    = $this->params()->fromPost('datainicio',null);
            $pDataFim       = $this->params()->fromPost('datafim',null);
            $marcas         = $this->params()->fromPost('marcas',null);
            $codproduto     = $this->params()->fromPost('idproduto',null);
            $categorias     = $this->params()->fromPost('categorias',null);

            $em = $this->getEntityManager();

            $idEmpresas= json_decode($idEmpresas);
            $andEmp = '';
            $andEmpItem = '';
            if($idEmpresas){
                $idEmpresas =  implode(",",$idEmpresas);
                $andEmp = " and cod_empresa in ($idEmpresas) ";
                $andEmpItem = " and cod_emp in ($idEmpresas) ";
                
            }

            if($regional){

                $arrayEmps = json_decode($regional);
                $regional = '';
                foreach($arrayEmps as $idRow){
                    
                    $arrayLinha = $this->funcregionais($idRow);
                    $regional .= implode("','",$arrayLinha);
                }

                if( $regional){

                    $andEmp = " and emp in ('$regional') ";
                    $andEmpItem = " and emp in ('$regional') ";
                }
            }
            
            $andData = '';
            $andDataFim = " and trunc(data,'MM') = trunc(sysdate,'MM')";
            if($pDataInicio){
                $andData = "and trunc(data) >= to_date('".$pDataInicio."')";
                $sysdateInicio = "to_date('".$pDataInicio."')";
            }else{
                $sysdateInicio = 'add_months(trunc(sysdate,\'MM\'),-0)';
                $andData = "and trunc(data) >= to_char($sysdateInicio,'dd/mm/yyyy')";
            }
            if($pDataFim){
                $andData .= " and trunc(data) <= to_date('".$pDataFim."')";
                $sysdateFim = "to_date('".$pDataFim."')";

                $andDataFim = " and trunc(data,'MM') = trunc($sysdateFim,'MM')";
            }else{
                $sysdateFim = 'sysdate';
                $andData .= " and trunc(data) <= sysdate";
            }

            if($marcas){
                $marcas =  implode("','",json_decode($marcas));
            }

            $andMarca = '';
            if($marcas){
                $andMarca = "and marca in ('$marcas')";
            }

            $andProduto = '';
            $andProdutoItem = '';
            if($codproduto){
                $codproduto =  implode(",",json_decode($codproduto));
            }
            if($codproduto){
                $andProduto = " and a.cod_produto in ($codproduto) ";
                $andProdutoItem = " and i.cod_produto in ($codproduto) ";
            }

            if($categorias){
                $categorias =  implode("','",json_decode($categorias));
            }

            $andCategorias = '';
            if($categorias){
                $andCategorias = "and c.categoria in ('$categorias')";
            }

            $colDs = 'cod_empresa';
            $colDescricao = 'emp';
            switch ($posicionamento) {
                case 1:
                    $colDs = 'cod_empresa';
                    $colDescricao = 'emp';
                    break;
                case 2:
                    $colDs = 'marca';
                    $colDescricao = 'marca';
                    break;
                case 3:
                    $colDs = 'categoria';
                    $colDescricao = 'categoria';
                    break;
                case 4:
                    $colDs = 'a.cod_produto';
                    $colDescricao = "a.cod_produto||' '||substr(a.descricao,0,20)||'...'";
                    break;
                default:
                    # code...
                    break;
            }

            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            
            $sql1 = "select to_char($sysdateInicio,'dd/mm/yyyy')  as datainicio,
                            to_char($sysdateFim,'dd/mm/yyyy') as datafim 
                        from dual";

            $stmt = $conn->prepare($sql1);
            $stmt->execute();
            $resultCount = $stmt->fetchAll();

            $tableCategoria = '';
            $andTableCategoria   = '';
            if($colDs ==  'categoria' || $andCategorias){

                $conn->beginTransaction();

                // $sqlTmp ="insert into vw_skproduto_categoria_tmp
                // (select distinct c.cod_produto, c.categoria
                //     from vw_skproduto_categoria c
                //         ,vm_skvendaitem_master i
                //  where c.cod_produto = i.cod_produto
                //  $andEmpItem
                //  $andData
                //  $andMarca
                //  $andProdutoItem
                //  $andCategorias
                //  )";

                $sqlTmp ="insert into vw_skproduto_categoria_tmp
                (select distinct c.cod_produto, c.categoria
                    from vw_skproduto_categoria c
                 where 1 = 1
                 $andCategorias
                 )";

                //  print "$sqlTmp";
                //  exit;

                $stmt = $conn->prepare($sqlTmp);
                $stmt->execute();
                // $conn->commit();

                $tableCategoria = ", vw_skproduto_categoria_tmp c ";
                $andTableCategoria   = 'and a.cod_produto = c.cod_produto';



            #####################################################################################

            // $sql = "select distinct c.cod_produto, c.categoria
            //             from vw_skproduto_categoria_tmp c";
            // $stmt = $conn->prepare($sql);
            // $stmt->execute();
            // $results = $stmt->fetchAll();
            // $hydrator = new ObjectProperty;
            // $hydrator->addStrategy('cod_produto', new ValueStrategy);
            // $stdClass = new StdClass;
            // $resultSet = new HydratingResultSet($hydrator, $stdClass);
            // $resultSet->initialize($results);

            // foreach ($resultSet as $row) {

            //     $elementos = $hydrator->extract($row);

                
            //     print"".$elementos['codProduto'] .", ". $elementos['categoria'].":";

            // }
            
            // exit;
            #########################################################################################

            }

            $sql = "select  descricao ds,
                            descricao,
                            rol,
                            0 decrol,
                            lb,
                            0 declb,
                            mb,
                            2 decmb,
                            ultimamb,
                            2 decultimamb,
                            qtde as qtd,
                            0 decqtd,
                            nf,
                            0 decnf,
                            cc,
                            0 deccc,
                            cmv,
                            0 deccmv,
                            0 giro,
                            2 decgiro,
                            0 tri,
                            2 dectri,
                            estoque_valor as estoquevalor,
                            0 decestoque_valor,
                            
                            med_accumulated
            from (select   ds, descricao,
                            rol,
                            lb,
                            mb,
                            ultimamb,
                            qtde,
                            nf,
                            cc,
                            cmv,
                            estoque_valor,
                            fr_rol,
                            sum(sum(fr_rol)) over (partition by rede order by rol desc rows unbounded preceding) as med_accumulated  
                    from (select rede, ds, descricao,
                                    rol,
                                    lb,
                                    mb,
                                    ultimamb,
                                    qtde,
                                    nf,
                                    cc,
                                    cmv,
                                    estoque_valor,
                                    100*ratio_to_report((case when rol > 0 then rol end)) over (partition by rede) fr_rol
                            from (select ax.*, bx.estoque_valor 
                                    from (select 'JS' as rede,
                                                 $colDs as ds,
                                                 $colDescricao as descricao,
                                                 round(sum(rob) ,2) as rob,
                                                 round(sum(rol),2) as rol,
                                                 round(sum(
                                                    case when trunc(data,'MM') = trunc($sysdateFim,'MM')
                                                        then cmv
                                                        else 0
                                                    end
                                                 ),2) as cmv,
                                                 round(sum(lb),2) as lb,
                                                 CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END as mb,
                                                 CASE WHEN SUM(case when trunc(data,'MM') = trunc($sysdateFim,'MM')
                                                                    then 
                                                                        rol
                                                                    else 0
                                                                end)<>0
                                                 THEN
                                                     ROUND(
                                                        SUM(case when trunc(data,'MM') = trunc($sysdateFim,'MM')
                                                            then 
                                                                lb
                                                            else 0
                                                        end)/
                                                        SUM(case when trunc(data,'MM') = trunc($sysdateFim,'MM')
                                                            then 
                                                                rol
                                                            else 0
                                                        end)*100 ,4)
                                                 ELSE 0 END as ultimamb,
                                                 sum(qtd) as qtde,
                                                 count(distinct nota) as nf,
                                                 count(distinct cnpj_parceiro) as cc
                                            from vm_skvendanota a
                                                 $tableCategoria
                                            where 1 =1
                                            $andTableCategoria
                                            $andEmp
                                            $andData
                                            $andMarca
                                            $andProduto
                                            $andCategorias
                                            group by $colDs, $colDescricao) ax,
                                            (select $colDs as ds,
                                                    round(sum(a.valor),2) as estoque_valor 
                                                from vw_skestoque_master a
                                                     $tableCategoria
                                             where 1 = 1 
                                             $andDataFim   -- Ultimo mês = mês
                                             $andTableCategoria
                                             $andEmp
                                             $andData
                                             $andMarca
                                             $andProduto
                                             $andCategorias
                                            group by $colDs) bx
                                    where ax.ds = bx.ds))
                group by rede, ds, descricao, rol, lb, mb, ultimamb, qtde, nf, cc, cmv, estoque_valor, fr_rol)
                where 1 = 1
                -- Remover esse filtro se utilizar o filtro de marca
                -- med_accumulated <= 80
                order by med_accumulated asc";
            // print "$sql";
            // exit;
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('rob', new ValueStrategy);
            $hydrator->addStrategy('rol', new ValueStrategy);
            $hydrator->addStrategy('cmv', new ValueStrategy);
            $hydrator->addStrategy('lb', new ValueStrategy);
            $hydrator->addStrategy('mb', new ValueStrategy);
            $hydrator->addStrategy('estoquevalor', new ValueStrategy);
            $hydrator->addStrategy('ultimamb', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $elementos = $hydrator->extract($row);

                $elementos['giro'] = $elementos['cmv'] > 0 ?  ($elementos['cmv'] *12)/ $elementos['estoquevalor']  : 0;

                $elementos['tri']  = $elementos['giro'] * $elementos['ultimamb'];

                $data[] = $elementos;
                
            }

            if($colDs ==  'categoria' || $andCategorias){
                $conn->commit();
            }

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        $objReturn = $this->getCallbackModel();
        $objReturn->referencia = array('incio'=> $resultCount[0]['DATAINICIO'],'fim'=> $resultCount[0]['DATAFIM']);

        return $objReturn; 
    }

    
}
