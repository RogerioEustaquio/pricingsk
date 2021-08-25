<?php
namespace Api\Controller;

use Zend\View\Model\JsonModel;
use Zend\Http\Client;
use Zend\Json\Json;
use Zend\Db\ResultSet\HydratingResultSet;
use Core\Ad\adLDAPFactory;
use Core\Stdlib\StdClass;
use Core\Hydrator\ObjectProperty;
use Core\Hydrator\Strategy\ValueStrategy;
use Core\Mvc\Controller\AbstractRestfulController;

class AnalisegraficoController extends AbstractRestfulController
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

            // $sql = "select e.apelido emp, e.id_empresa
            //             from ms.empresa e
            //         where e.id_empresa not in (26, 27, 28, 11, 20, 102, 101)
            //         order by e.apelido";

            $sql = 'select distinct emp, cod_empresa id_empresa
                    from VW_SKEMPRESA';

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

    // public function funcregionais($id){
    //     // return array idEmpresas

    //     $regionais = array();

    //     $regionais[] = ['id'=> 'R1','idEmpresas'=> [9,2,29,23,25,24,13,19]];
    //     $regionais[] = ['id'=> 'R2','idEmpresas'=> [12,10,15,16,21,3,22,8]];
    //     $regionais[] = ['id'=> 'R3','idEmpresas'=> [6,4,5,17,18,14,7]];

    //     foreach($regionais as $row){

    //         if($row['id'] == $id){
    //             return $row['idEmpresas'];
    //         }
    //     }

    //     return null;
    // }

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

    // public function listartabelaprecoAction()
    // {
    //     $data = array();
        
    //     try {

    //         $pEmp    = $this->params()->fromQuery('emp',null);
    //         $pCod    = $this->params()->fromQuery('codTabPreco',null);
    //         $tipoSql = $this->params()->fromQuery('tipoSql',null);

    //         if(!$pCod){
    //             throw new \Exception('Parâmetros não informados.');
    //         }

    //         $em = $this->getEntityManager();

    //         if(!$tipoSql){
    //             $filtroProduto = "like upper('".$pCod."%')";
    //         }else{
    //             $produtos =  implode(",",json_decode($pCod));
    //             $filtroProduto = "in ($produtos)";
    //         }

    //         $sql = "select distinct nvl(COD_TAB_PRECO,'') COD_TAB_PRECO, NOME_TAB_PRECO descricao
    //         from SK_PRODUTO_TABELA_TMP
    //         where 1 =1 
    //         and COD_TAB_PRECO $filtroProduto";

    //         $conn = $em->getConnection();
    //         $stmt = $conn->prepare($sql);
    //         // $stmt->bindValue(1, $pEmp);
            
    //         $stmt->execute();
    //         $results = $stmt->fetchAll();

    //         $hydrator = new ObjectProperty;
    //         $hydrator->addStrategy('cod_tab_preco', new ValueStrategy);
    //         $stdClass = new StdClass;
    //         $resultSet = new HydratingResultSet($hydrator, $stdClass);
    //         $resultSet->initialize($results);

    //         $data = array();
    //         foreach ($resultSet as $row) {
    //             $data[] = $hydrator->extract($row);
    //         }

    //         $this->setCallbackData($data);
            
    //     } catch (\Exception $e) {
    //         $this->setCallbackError($e->getMessage());
    //     }
        
    //     return $this->getCallbackModel();
    // }

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
                $filtroProduto = "in ($produtos)";
            }

            $sql = "select distinct nvl(COD_PRODUTO,'') ID_PRODUTO, DESCRICAO
                from VW_SKPRODUTO 
            where 1 =1 
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

    // public function listargrupodescontosAction()
    // {
    //     $data = array();
        
    //     try {

    //         // $idEmpresa      = $this->params()->fromQuery('idEmpresa',null);

    //         $em = $this->getEntityManager();
    //         $conn = $em->getConnection();

    //         $sql = "select distinct nvl(grupo_desconto,'') cod_grupo_desconto,
    //                     nvl(grupo_desconto,'') descricao
    //             from SK_PRODUTO_TABELA_TMP
    //         where grupo_desconto is not null
    //         order by 1";
            
    //         // $sql = " select distinct nvl(cod_grupo,'') cod_grupo_desconto,
    //         //             nvl(cod_grupo,'') descricao
    //         //     from vw_skalcada_desconto
    //         // where cod_grupo is not null
    //         // order by 1 ";

    //         $stmt = $conn->prepare($sql);
    //         // $stmt->bindParam(':idEmpresa', $idEmpresa);
    //         $stmt->execute();
    //         $results = $stmt->fetchAll();

    //         $hydrator = new ObjectProperty;
    //         $hydrator->addStrategy('grupo_desconto', new ValueStrategy);
    //         $stdClass = new StdClass;
    //         $resultSet = new HydratingResultSet($hydrator, $stdClass);
    //         $resultSet->initialize($results);

    //         $data = array();
    //         foreach ($resultSet as $row) {
    //             $data[] = $hydrator->extract($row);
    //         }

    //         $this->setCallbackData($data);
    //         $this->setMessage("Solicitação enviada com sucesso.");
            
    //     } catch (\Exception $e) {
    //         $this->setCallbackError($e->getMessage());
    //     }
        
    //     return $this->getCallbackModel();
    // }

    public function listarmarcaAction()
    {
        $data = array();

        $emp = $this->params()->fromQuery('emp',null);

        try {

            $session = $this->getSession();
            $usuario = $session['info'];

            $em = $this->getEntityManager();
            
            $sql = 'select distinct cod_marca as id_marca, descricao_marca marca
             from VW_SKMARCA
            order by descricao_marca';
            
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
    
    // public function listargrupodescontoAction()
    // {
    //     $data = array();
        
    //     try {

    //         // $idEmpresa      = $this->params()->fromQuery('idEmpresa',null);

    //         $em = $this->getEntityManager();
    //         $conn = $em->getConnection();

    //         $sql = "select distinct nvl(grupo_desconto,'') grupo_desconto
    //             from SK_PRODUTO_TABELA_TMP";

    //         $stmt = $conn->prepare($sql);
    //         // $stmt->bindParam(':idEmpresa', $idEmpresa);
    //         $stmt->execute();
    //         $results = $stmt->fetchAll();

    //         $hydrator = new ObjectProperty;
    //         $hydrator->addStrategy('grupo_desconto', new ValueStrategy);
    //         $stdClass = new StdClass;
    //         $resultSet = new HydratingResultSet($hydrator, $stdClass);
    //         $resultSet->initialize($results);

    //         $data = array();
    //         foreach ($resultSet as $row) {
    //             $data[] = $hydrator->extract($row);
    //         }

    //         $this->setCallbackData($data);
    //         $this->setMessage("Solicitação enviada com sucesso.");
            
    //     } catch (\Exception $e) {
    //         $this->setCallbackError($e->getMessage());
    //     }
        
    //     return $this->getCallbackModel();
    // }


    // public function gerarexcelAction()
    // {
    //     $data = array();
        
    //     try {

    //         $session = $this->getSession();

    //         if($session['exportestoque']){

    //             ini_set('memory_limit', '5120M' );

    //             $em = $this->getEntityManager();
    //             $conn = $em->getConnection();

    //             $sql = $session['exportestoque'] ;
                
    //             $conn = $em->getConnection();
    //             $stmt = $conn->prepare($sql);
                
    //             $stmt->execute();
    //             $results = $stmt->fetchAll();

    //             $hydrator = new ObjectProperty;
    //             // $hydrator->addStrategy('estoque', new ValueStrategy);
    //             // $hydrator->addStrategy('custo_medio', new ValueStrategy);
    //             // $hydrator->addStrategy('valor', new ValueStrategy);
    //             // $hydrator->addStrategy('custo_operacao', new ValueStrategy);
    //             // $hydrator->addStrategy('pis', new ValueStrategy);
    //             // $hydrator->addStrategy('cofins', new ValueStrategy);
    //             // $hydrator->addStrategy('icms', new ValueStrategy);
    //             $stdClass = new StdClass;
    //             $resultSet = new HydratingResultSet($hydrator, $stdClass);
    //             $resultSet->initialize($results);

    //             $data = array();
                
    //             $output = 'COD_EMPRESA;NOME_EMPRESA;COD_PRODUTO;DESCRICAO_PRODUTO;ESTOQUE;CUSTO_MEDIO;VALOR;CUSTO_OPERACAO;PIS;COFINS;ICMS;CURVA;CLIENTES'."\n";

    //             $i=0;
    //             foreach ($resultSet as $row) {
    //                 $data[] = $hydrator->extract($row);

    //                 $codEmpresa     = $data[$i]['codEmpresa'];
    //                 $nomeEmpresa    = $data[$i]['nomeEmpresa'];

    //                 $estoque        = $data[$i]['estoque'] >0 ? $data[$i]['estoque'] : null ;
    //                 $custoMedio     = $data[$i]['custoMedio'] >0 ? $data[$i]['custoMedio'] : null ;
    //                 $valor          = $data[$i]['valor'] >0 ? $data[$i]['valor'] : null ;
    //                 $custoOperacao  = $data[$i]['custoOperacao'] >0 ? $data[$i]['custoOperacao'] : null ;
    //                 $pis            = $data[$i]['pis'] >0 ? $data[$i]['pis'] : null ;
    //                 $cofins         = $data[$i]['cofins'] >0 ? $data[$i]['cofins'] : null ;
    //                 $icms           = $data[$i]['icms'] >0 ? $data[$i]['icms'] : null ;

    //                 $output  .= $codEmpresa.';'.
    //                             $nomeEmpresa.';'.
    //                             $data[$i]['codProduto'].';'.
    //                             $data[$i]['descricaoProduto'].';'.
    //                             $estoque.';'.
    //                             $custoMedio.';'.
    //                             $valor.';'.
    //                             $custoOperacao.';'.
    //                             $pis.';'.
    //                             $cofins.';'.
    //                             $icms.';'.
    //                             $data[$i]['curva'].';'.
    //                             $data[$i]['clientes'].';'.
    //                             "\n";
    //                 $i++;
    //             }

    //             $response = new \Zend\Http\Response();
    //             $response->setContent($output);
    //             $response->setStatusCode(200);

    //             $headers =[
    //                     'Pragma' => 'public',
    //                     'Cache-control' => 'must-revalidate, post-check=0, pre-check=0',
    //                     'Cache-control' => 'private',
    //                     'Expires' => '0000-00-00',
    //                     'Content-Type' => 'application/CSV; charset=utf-8',
    //                     'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Base Preço.csv',
    //                 ];
    //             $responseHeaders = new \Zend\Http\Headers();
    //             $responseHeaders->addHeaders($headers);
    //             $response->setHeaders($responseHeaders);

    //             return $response;

    //         }

    //         $this->setCallbackData($data);

    //         $objReturn = $this->getCallbackModel();
            
    //     } catch (\Exception $e) {
    //         $objReturn = $this->setCallbackError($e->getMessage());
    //     }
        
    //     return $objReturn;
    // }

    public function listarfichaitemgraficoAction()
    {
        $data = array();
        
        try {
            $emp     = $this->params()->fromPost('idEmpresas',null);
            $idMarcas       = $this->params()->fromPost('marca',null);
            $idMarcasG      = $this->params()->fromPost('idMarcasG',null);
            $codProdutos    = $this->params()->fromPost('idProduto',null);
            $produtos       = $this->params()->fromPost('produto',null);
            $tpPessoas      = $this->params()->fromPost('tpPessoas',null);
            $data           = $this->params()->fromPost('data',null);
            $idCurvas       = $this->params()->fromPost('idCurvas',null);
            $idOmvUsers     = $this->params()->fromPost('idOmvUsers',null);
            $indicadoresAdd = $this->params()->fromPost('indicadoresAdd',null);
            $idRegionais    = $this->params()->fromPost('idRegionais',null);

            $indicadoresAdd = json_decode($indicadoresAdd);
            if($emp){
                $emp =  implode("','",json_decode($emp));
            }
            if($idMarcas){
                $idMarcas = implode(",",json_decode($idMarcas));
            }
            if($idMarcasG){
                $idMarcasG = implode(",",json_decode($idMarcasG));
            }
            if($produtos){
                $produtos =  implode("','",json_decode($produtos));
            }
            if($codProdutos){
                $codProdutos =  implode(",",json_decode($codProdutos));
            }
            // if($tpPessoas){
            //     $tpPessoas = implode("','",json_decode($tpPessoas));
            // }
            // if($idCurvas){
            //     $idCurvas = implode("','",json_decode($idCurvas));
            // }
            // if($idOmvUsers){
            //     $idOmvUsers = implode("','",json_decode($idOmvUsers));
            // }

            // if($idRegionais){

            //     $arrayIdEmps = json_decode($idRegionais);
            //     $idRegionais = '';
            //     foreach($arrayIdEmps as $idRow){
                    
            //         $arrayLinha = $this->funcregionais($idRow);
            //         $idRegionais .= implode(",",$arrayLinha);
            //     }
            // }

            $andSql = '';
            if($emp){
                $andSql = " and emp in ('$emp')";
            }

            // if($idRegionais){
            //     $andSql .= " and e.COD_EMPRESA in ($idRegionais)";
            // }

            if($idMarcas){
                $andSql .= " and cod_marca in ($idMarcas)";
            }
            if($idMarcasG){
                $andSql .= " and cod_marca in ($idMarcasG)";
            }
            
            if($produtos){
                $andSql .= " and cod_nbs in ('$produtos')";
            }

            if($codProdutos){
                $andSql .= " and cod_produto in ($codProdutos)";
            }

            // if($tpPessoas){
            //     $andSql .= " and p.tipo_pessoa in ('$tpPessoas')";
            // }

            // if($idCurvas){
            //     $andSql .= " and t.id_curva_abc in ('$idCurvas')";
            // }
            
            if($data){
                $sysdate = "to_date('01/".$data."')";
            }else{
                $sysdate = 'sysdate';
            }

            if($data){
                $andSql .= " and trunc(data, 'MM') >= add_months(trunc($sysdate,'MM'),-11)";
                $andSql .= " and trunc(data, 'MM') <= add_months(trunc($sysdate,'MM'),0)";
            }else{
                $andSql .= " and trunc(data, 'MM') >= add_months(trunc(sysdate,'MM'),-11)";
            }
            
            // if($idOmvUsers){
            //     $andSql .= " and (vi.id_empresa, vi.id_item, vi.id_categoria) in (
            //         select id_empresa, id_item, id_categoria
            //           from js.omv_analise_log a 
            //          where a.data_aprovacao >= add_months(sysdate, -12) -- Filtro de data também
            //            and usuario_aprovacao in ('$idOmvUsers') -- Usuários selecioados
            //       )
            //       ";
            // }
            
            $em = $this->getEntityManager();
            
            $meses = [null,
                     'Janeiro',
                     'Fevereiro',
                     'Março',
                     'Abril',
                     'Maio',
                     'Junho',
                     'Julho',
                     'Agosto',
                     'Setembro',
                     'Outubro',
                     'Novembro',
                     'Dezembro'];

            $conn = $em->getConnection();

            $sql = "select add_months(trunc($sysdate,'MM'),-11) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-10) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-9) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-8) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-7) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-6) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-5) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-4) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-3) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-2) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-1) as id from dual union all
                    select add_months(trunc($sysdate,'MM'),-0) as id from dual
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data1 = array();
            $categories = array();

            $arrayDesc      = array();
            $arrayPreco     = array();
            $arrayImposto   = array();
            $arrayRolUni    = array();
            $arrayCusto     = array();
            $arrayImpostoPc = array();
            $arrayDescPc    = array();
            $arrayRob       = array();
            $arrayRol       = array();
            $arrayCmv       = array();
            $arrayLb        = array();
            $arrayMb        = array();
            $arrayQtde      = array();
            $arrayNf        = array();
            $arrayCc        = array();
            $arrayTkmcc     = array();
            $arrayTkmnf     = array();
            $arrayLbcc      = array();
            $arrayLbnf      = array();
            $arrayRobdia    = array();
            $arrayRoldia    = array();
            $arrayCmvdia    = array();
            $arrayLbdia     = array();
            $arrayQtdedia   = array();
            $arrayNfdia     = array();
            $arrayCcdia     = array();
            $arrayEtqMesIni = array();

            // $categories[] = 0;

            foreach ($resultSet as $row) {
                $data1 = $hydrator->extract($row);
                $categories[] = $meses[(float) substr($data1['id'], 3, 2)];

                $arrayDesc[]        = 0;
                $arrayPreco[]       = 0;
                $arrayImposto[]     = 0;
                $arrayRolUni[]      = 0;
                $arrayCusto[]       = 0;
                $arrayLucro[]       = 0;
                $arrayImpostoPc[]   = 0;
                $arrayDescPc[]      = 0;
                $arrayRob[]         = 0;
                $arrayRol[]         = 0;
                $arrayCmv[]         = 0;
                $arrayLb[]          = 0;
                $arrayMb[]          = 0;
                $arrayQtde[]        = 0;
                $arrayNf[]          = 0;
                $arrayCc[]          = 0;
                $arrayTkmcc[]       = 0;
                $arrayTkmnf[]       = 0;
                $arrayLbcc[]        = 0;
                $arrayLbnf[]        = 0;
                $arrayRobdia[]      = 0;
                $arrayRoldia[]      = 0;
                $arrayCmvdia[]      = 0;
                $arrayLbdia[]       = 0;
                $arrayQtdedia[]     = 0;
                $arrayNfdia[]       = 0;
                $arrayCcdia[]       = 0;
                $arrayEtqMesIni[]   = 0;

            }

            $consultaEstoque = false;
            $EstoqueMesInicial  = array();
            $EstoqueMesFinal    = array();
            $EstoqueDias        = array();
            $EstoqueInRol       = array();
            $EstoqueInLb        = array();
            $EstoqueInGiro      = array();

            if($indicadoresAdd){

                for ($i=0; $i < count($indicadoresAdd); $i++) {
            
                    if($indicadoresAdd[$i]->value){
                        $consultaEstoque = true;
                    }
                }
            }

            if($consultaEstoque){

                $EstoqueMes = $this->estoquemes($idEmpresas,$idMarcas,$idMarcasG,$codProdutos,$data,$idCurvas,$idOmvUsers,$tpPessoas,$idRegionais);
                $EstoqueMesInicial  = $EstoqueMes[0];
                $EstoqueMesFinal    = $EstoqueMes[1];
                $EstoqueDias        = $EstoqueMes[2];
                $EstoqueInRol       = $EstoqueMes[3];
                $EstoqueInLb        = $EstoqueMes[4];
                $EstoqueInGiro      = $EstoqueMes[5];
            }

            // $sql = "select trunc(a.dtentsai,'MM') as data,
            //                 --e.emp, p.cod_marca, m.descricao_marca, p.cod_nbs,
            //                 --a.codprod as cod_produto, p.descricao as descricao_produto,
            //                 round(sum(a.rol),2) as rol,
            //                 round(sum(a.lbreal),2) as lb,
            //                 round((case when sum(a.lbreal) > 0 then sum(a.lbreal)/sum(a.rol) end)*100,2) as mb
            //         from sankhya.VW_JS_PRICE_VENDAS_PRINCIPAL@Sk a,
            //                 vw_skproduto p,
            //                 vw_skmarca m,
            //                 VW_SKEMPRESA e
            //         where a.codprod = p.cod_produto
            //         and p.cod_marca = m.COD_MARCA
            //         and a.codemp = e.COD_EMPRESA
            //         $andSql
            //         group by trunc(a.dtentsai,'MM')
            //         order by 1
            //         ";

            $sql = "select a.data,
                           a.rol,
                           a.lb,
                           a.qtde,
                           a.mb,
                           du.dias,
                           round(a.rol/du.dias,2) as rol_dia,
                           round(a.lb/du.dias,2) as lb_dia,
                           round(a.qtde/du.dias,0) as qtde_dia
                    from (select trunc(data, 'MM') as data,
                                    sum(rol) as rol,
                                    sum(lb) as lb,
                                    sum(qtde) as qtde,
                                    round((sum(lb)/sum(rol))*100,2) as mb
                            from vm_skvendaitem_master
                          where 1=1
                          $andSql
                          --and data >= add_months(trunc(to_date('24/08/2021'),'MM'),-11)
                          and data <  trunc(sysdate)
                          -- and emp
                          -- and cod_produto
                          -- and descricao
                          -- and cod_marca
                          -- and marca
                         group by trunc(data, 'MM')) a,
                         VM_SKDIAS_UTEIS du
                    where a.data = du.data
                    order by data";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            // $hydrator->addStrategy('desconto_uni', new ValueStrategy);
            // $hydrator->addStrategy('preco_uni', new ValueStrategy);
            // $hydrator->addStrategy('imposto_uni', new ValueStrategy);
            // $hydrator->addStrategy('rol_uni', new ValueStrategy);
            // $hydrator->addStrategy('custo_uni', new ValueStrategy);
            // $hydrator->addStrategy('lucro_uni', new ValueStrategy);
            // $hydrator->addStrategy('imposto_perc', new ValueStrategy);
            // $hydrator->addStrategy('desconto_perc', new ValueStrategy);
            // $hydrator->addStrategy('rob', new ValueStrategy);
            $hydrator->addStrategy('rol', new ValueStrategy);
            // $hydrator->addStrategy('cmv', new ValueStrategy);
            $hydrator->addStrategy('lb', new ValueStrategy);
            $hydrator->addStrategy('mb', new ValueStrategy);
            $hydrator->addStrategy('qtde', new ValueStrategy);
            // $hydrator->addStrategy('nf', new ValueStrategy);
            // $hydrator->addStrategy('cc', new ValueStrategy);
            // $hydrator->addStrategy('tkm_cc', new ValueStrategy);
            // $hydrator->addStrategy('tkm_nf', new ValueStrategy);
            // $hydrator->addStrategy('lb_cc', new ValueStrategy);
            // $hydrator->addStrategy('lb_nf', new ValueStrategy);
            // $hydrator->addStrategy('rob_dia', new ValueStrategy);
            $hydrator->addStrategy('rol_dia', new ValueStrategy);
            // $hydrator->addStrategy('cmv_dia', new ValueStrategy);
            $hydrator->addStrategy('lb_dia', new ValueStrategy);
            $hydrator->addStrategy('qtde_dia', new ValueStrategy);
            // $hydrator->addStrategy('nf_dia', new ValueStrategy);
            // $hydrator->addStrategy('cc_dia', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            $cont = 0;



            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                
                // $cont = $cont == 12 ? 11 : $cont;
                while($categories[$cont] != $meses[(float)substr($elementos['data'], 3, 2)] && $cont<12){

                    // var_dump($categories[$cont] );
                    // var_dump($meses[(float)substr($elementos['data'], 3, 2)] );

                    $cont++;

                    // if($cont == 12){
                    //     break;
                    // }
                }

                // $cont = $cont == 12 ? 11 : $cont;

                if($categories[$cont] == $meses[(float)substr($elementos['data'], 3, 2)]){

                    // $arrayDesc[$cont]        = (float)$elementos['descontoUni'];
                    // $arrayPreco[$cont]       = (float)$elementos['precoUni'];
                    // $arrayImposto[$cont]     = (float)$elementos['impostoUni'];
                    // $arrayRolUni[$cont]      = (float)$elementos['rolUni'];
                    // $arrayCusto[$cont]       = (float)$elementos['custoUni'];
                    // $arrayLucro[$cont]       = (float)$elementos['lucroUni'];
                    // $arrayImpostoPc[$cont]   = (float)$elementos['impostoPerc'];
                    // $arrayDescPc[$cont]      = (float)$elementos['descontoPerc'];
                    // $arrayRob[$cont]         = (float)$elementos['rob'];
                    $arrayRol[$cont]         = (float)$elementos['rol'];
                    // $arrayCmv[$cont]         = (float)$elementos['cmv'];
                    $arrayLb[$cont]          = (float)$elementos['lb'];
                    $arrayMb[$cont]          = (float)$elementos['mb'];
                    $arrayQtde[$cont]        = (float)$elementos['qtde'];
                    // $arrayNf[$cont]          = (float)$elementos['nf'];
                    // $arrayCc[$cont]          = (float)$elementos['cc'];
                    // $arrayTkmcc[$cont]       = (float)$elementos['tkmCc'];
                    // $arrayTkmnf[$cont]       = (float)$elementos['tkmNf'];
                    // $arrayLbcc[$cont]        = (float)$elementos['lbCc'];
                    // $arrayLbnf[$cont]        = (float)$elementos['lbNf'];
                    // $arrayRobdia[$cont]      = (float)$elementos['robDia'];
                    $arrayRoldia[$cont]      = (float)$elementos['rolDia'];
                    // $arrayCmvdia[$cont]      = (float)$elementos['cmvDia'];
                    $arrayLbdia[$cont]       = (float)$elementos['lbDia'];
                    $arrayQtdedia[$cont]     = (float)$elementos['qtdeDia'];
                    // $arrayNfdia[$cont]       = (float)$elementos['nfDia'];
                    // $arrayCcdia[$cont]       = (float)$elementos['ccDia'];

                }

                $cont++;
            }

            $colors = ["#63b598","#ce7d78","#ea9e70","#a48a9e","#c6e1e8","#648177","#0d5ac1","#f205e6","#1c0365","#14a9ad","#4ca2f9"
                      ,"#a4e43f","#d298e2","#6119d0","#d2737d","#c0a43c","#f2510e","#651be6","#79806e","#61da5e","#cd2f00","#9348af"
                      ,"#01ac53","#c5a4fb","#996635","#b11573","#2f3f94","#2f7b99","#da967d","#34891f","#b0d87b","#4bb473","#75d89e"];

            // $this->setCallbackData($data);
            return new JsonModel(
                array(
                    'success' => true,
                    'data' => array(
                        'categories' => $categories,
                        'series' => array(                            
                            array(
                                'name' => 'Preço Unitário',
                                'yAxis'=> 0,
                                // 'color' => 'rgba(165,170,217,1)',
                                'data' => $arrayPreco,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                ),
                            ),
                            array(
                                'name' => 'Desconto Unitário',
                                'yAxis'=> 0,
                                // 'color' => 'rgba(126,86,134,.9)',
                                'data' => $arrayDesc,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Imposto Unitário',
                                'yAxis'=> 0,
                                // 'color' => 'rgba(46, 36, 183, 1)',
                                'data' => $arrayImposto,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'ROL Unitário',
                                'yAxis'=> 0,
                                // 'color' => 'rgba(221, 117, 85, 1)',
                                'data' => $arrayRolUni,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Custo Unitário',
                                'yAxis'=> 0,
                                // 'color' => 'rgba(221, 117, 85, 1)',
                                'data' => $arrayCusto,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Lucro Unitário',
                                'yAxis'=> 0,
                                // 'color' => 'rgba(221, 117, 85, 1)',
                                'data' => $arrayLucro,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => '% Imposto',
                                'yAxis'=> 6,
                                // 'color' => 'rgba(221, 117, 85, 1)',
                                'data' => $arrayImpostoPc,
                                'vFormat' => '%',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => '% Desconto',
                                'yAxis'=> 7,
                                // 'color' => 'rgba(221, 117, 85, 1)',
                                'data' => $arrayDescPc,
                                'vFormat' => '%',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'ROB',
                                'yAxis'=> 8,
                                // 'color' => 'rgba(221, 117, 85, 1)',
                                'data' => $arrayRob,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'ROL',
                                'yAxis'=> 8,
                                // 'color' => 'rgba(221, 117, 85, 1)',
                                'data' => $arrayRol,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => true,
                                'showInLegend' => true,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'CMV',
                                'yAxis'=> 8,
                                'color' => $colors[0],
                                'data' => $arrayCmv,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'LB',
                                'yAxis'=> 8,
                                'color' => $colors[1],
                                'data' => $arrayLb,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => true,
                                'showInLegend' => true,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'MB',
                                'yAxis'=> 12,
                                'color' => $colors[2],
                                'data' => $arrayMb,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => true,
                                'showInLegend' => true,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Quantidade',
                                'yAxis'=> 13,
                                'color' => $colors[3],
                                'data' => $arrayQtde,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Nota',
                                'yAxis'=> 14,
                                'color' => $colors[4],
                                'data' => $arrayNf,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Cliente',
                                'yAxis'=> 15,
                                'color' => $colors[5],
                                'data' => $arrayCc,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'TKM Cliente',
                                'yAxis'=> 16,
                                'color' => $colors[6],
                                'data' => $arrayTkmcc,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'TKM Nota',
                                'yAxis'=> 17,
                                'color' => $colors[7],
                                'data' => $arrayTkmnf,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'LB Cliente',
                                'yAxis'=> 18,
                                'color' => $colors[8],
                                'data' => $arrayLbcc,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'LB Nota',
                                'yAxis'=> 19,
                                'color' => $colors[9],
                                'data' => $arrayLbnf,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'ROB Dia',
                                'yAxis'=> 20,
                                'color'=> $colors[10],
                                'data' => $arrayRobdia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'ROL Dia',
                                'yAxis'=> 21,
                                'color'=> $colors[11],
                                'data' => $arrayRoldia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'CMV Dia',
                                'yAxis'=> 22,
                                'color'=> $colors[12],
                                'data' => $arrayCmvdia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'LB Dia',
                                'yAxis'=> 23,
                                'color'=> $colors[13],
                                'data' => $arrayLbdia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Qtde Dia',
                                'yAxis'=> 24,
                                'color'=> $colors[14],
                                'data' => $arrayQtdedia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Nota Dia',
                                'yAxis'=> 25,
                                'color'=> $colors[15],
                                'data' => $arrayNfdia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Cliente Dia',
                                'yAxis'=> 26,
                                'color'=> $colors[16],
                                'data' => $arrayCcdia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Estoque Inicial',
                                'yAxis'=> 27,
                                'color'=> $colors[17],
                                'data' => $EstoqueMesInicial,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Estoque Final',
                                'yAxis'=> 28,
                                'color'=> $colors[18],
                                'data' => $EstoqueMesFinal,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Dias de Estoque',
                                'yAxis'=> 29,
                                'color'=> $colors[19],
                                'data' => $EstoqueDias,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Índice Estoque/ROL',
                                'yAxis'=> 30,
                                'color'=> $colors[20],
                                'data' => $EstoqueInRol,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Índice Estoque/LB',
                                'yAxis'=> 31,
                                'color'=> $colors[21],
                                'data' => $EstoqueInLb,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            array(
                                'name' => 'Índice Estoque/Giro',
                                'yAxis'=> 32,
                                'color'=> $colors[22],
                                'data' => $EstoqueInGiro,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            )
                        )
                    )
                )
            );
            
        }  catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        
        return $this->getCallbackModel();
    }
}
