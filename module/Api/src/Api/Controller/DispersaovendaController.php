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

class DispersaovendaController extends AbstractRestfulController 
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

            $session = $this->getSession();
            $usuario = $session['info'];

            $em = $this->getEntityManager();
            
            // $sql = "select  g.id_grupo_marca,
            //                 m.id_marca,
            //                 m.descricao as marca,
            //                 count(*) as skus
            //         from ms.tb_estoque e,
            //                 ms.tb_item i,
            //                 ms.tb_categoria c,
            //                 ms.tb_item_categoria ic,
            //                 ms.tb_marca m,
            //                 ms.tb_grupo_marca g,
            //                 ms.empresa em
            //         where e.id_item = i.id_item
            //         and e.id_categoria = c.id_categoria
            //         and e.id_item = ic.id_item
            //         and e.id_categoria = ic.id_categoria
            //         and ic.id_marca = m.id_marca
            //         and m.id_grupo_marca = g.id_grupo_marca
            //         and e.id_empresa = em.id_empresa
            //         --and e.id_curva_abc = 'E'
            //         and ( e.ultima_compra > add_months(sysdate, -6) or e.estoque > 0 )
            //         group by g.id_grupo_marca, m.id_marca, m.descricao
            //         order by skus desc
            // ";
            
            $sql = "select distinct cod_marca as id_marca, descricao_marca marca
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

    public function vendaAction()
    {
        $data = array();
        
        try {

            $idEmpresa      = $this->params()->fromPost('idEmpresas',null);
            $pDataInicio= $this->params()->fromPost('datainicio',null);
            $pDataFim   = $this->params()->fromPost('datafim',null);
            // $idMarcas   = $this->params()->fromPost('idMarcas',null);
            // $descMarca  = $this->params()->fromPost('descMarca',null);

            if($idEmpresa){
                $idEmpresa = implode(",",json_decode($idEmpresa));
            }
            $andFilial ='';
            if($idEmpresa){
                $andFilial = " and cod_empresa in ($idEmpresa)";
            }

            $andData = '';
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
            }else{
                $sysdateFim = 'sysdate';
                $andData .= " and trunc(data) <= sysdate";
            }

            // if($pData){
            //     $sysdate = "to_date('01/".substr($pData,3,5)."')";
            // }else{
            //     $sysdate = "to_date('01/'||to_char(sysdate,'mm/yyyy'))";
            // }

            // if($idMarcas){
            //     $idMarcas =  implode(",",json_decode($idMarcas));
            // }
            // $andMarca = '';
            // $and_accumulated = '';
            // if($idMarcas){
            //     $andMarca = "and ic.id_marca in ($idMarcas)";
            // }
            
            // $andDescMarca= '';
            // if($descMarca){
            //     $andDescMarca = " and m.descricao = '$descMarca'";
            // }else{
            //     $andDescMarca = " and m.id_marca in (279,23,8)"; // para testes
            // }

            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            
            $sql1 = "select to_char($sysdateInicio,'dd/mm/yyyy')  as datainicio,
                            to_char($sysdateFim,'dd/mm/yyyy') as datafim 
                        from dual";

            $stmt = $conn->prepare($sql1);
            $stmt->execute();
            $resultCount = $stmt->fetchAll();

            $sql = "select to_char(data,'dd/mm/yyyy') as data
                            , 0 as cod_cliente
                            ,count(distinct cnpj_parceiro) as cc
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                            , 2 decmb
                            ,round(sum(nvl(rol,0)),0) as rol
                            ,round(sum(nvl(lb,0)),0) as lb
                        from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    group by data
                    having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END >=0
                    order by data desc";
            // print "$sql";
            // exit;
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $hydrator->addStrategy('rol', new ValueStrategy);
            $hydrator->addStrategy('cc', new ValueStrategy);
            $hydrator->addStrategy('lb', new ValueStrategy);
            $hydrator->addStrategy('mb', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            $data2 = array();
            foreach ($resultSet as $row) {
                $elementos = $hydrator->extract($row);

                // $dataEmissao = $elementos['data'];
                // $timeEmissao = $this->create_data($dataEmissao);
                // $elementos['data'] = $timeEmissao ;
                // $dia = (float) substr($elementos['data'],0,2);
                // $mes = (float) substr($elementos['data'],3,2);
                // $ano = (float) substr($elementos['data'],6,4);

                // $elementos['data'] = 'Date.UTC('.$ano.','.($mes-1).',' .$dia.')';

                $data[] = [
                            'emissao'=> $elementos['data'],
                            'x'=> $elementos['data'],
                            'y'=> $elementos['mb'],
                            'nomez'=> 'ROL',
                            'z'=> $elementos['rol']
                ];

                $data2[] = [
                    'emissao'=> $elementos['data'],
                    'x'=> $elementos['data'],
                    'y'=> $elementos['mb'],
                    'nomez'=> 'CC',
                    'z'=> $elementos['cc']
                ];
            }

            $dataSeries = array(
                array(
                    'name' => 'ROL',
                    'data' => $data
                ),
                array(
                    'name' => 'Cliente',
                    'data' => $data2
                )
            );

            // var_dump( $dataSeries);
            // exit;

            // $this->setCallbackData($data);
            $this->setCallbackData($dataSeries);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        $objReturn = $this->getCallbackModel();
        $objReturn->referencia = array('incio'=> $resultCount[0]['DATAINICIO'],'fim'=> $resultCount[0]['DATAFIM']);

        return $objReturn; 
    }

    public function dispersaovendaAction()
    {
        ini_set('memory_limit', '1024M');

        $data = array();
        
        try {

            $idEmpresa      = $this->params()->fromPost('idEmpresas',null);
            $pDataInicio= $this->params()->fromPost('datainicio',null);
            $pDataFim   = $this->params()->fromPost('datafim',null);
            // $idMarcas   = $this->params()->fromPost('idMarcas',null);
            // $descMarca  = $this->params()->fromPost('descMarca',null);

            $idEmpresa = 18;
            $pDataInicio = '01/05/2022';
            $pDataFim    = '31/05/2022';
            // if($idEmpresa){
            //     $idEmpresa = implode(",",json_decode($idEmpresa));
            // }
            $andFilial ='';
            if($idEmpresa){
                $andFilial = " and cod_empresa in ($idEmpresa)";
            }

            $andData = '';
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
            }else{
                $sysdateFim = 'sysdate';
                $andData .= " and trunc(data) <= sysdate";
            }

            // if($pData){
            //     $sysdate = "to_date('01/".substr($pData,3,5)."')";
            // }else{
            //     $sysdate = "to_date('01/'||to_char(sysdate,'mm/yyyy'))";
            // }

            // if($idMarcas){
            //     $idMarcas =  implode(",",json_decode($idMarcas));
            // }
            // $andMarca = '';
            // $and_accumulated = '';
            // if($idMarcas){
            //     $andMarca = "and ic.id_marca in ($idMarcas)";
            // }
            
            // $andDescMarca= '';
            // if($descMarca){
            //     $andDescMarca = " and m.descricao = '$descMarca'";
            // }else{
            //     $andDescMarca = " and m.id_marca in (279,23,8)"; // para testes
            // }
            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql1 = "select to_char($sysdateInicio,'dd/mm/yyyy')  as datainicio,
                            to_char($sysdateFim,'dd/mm/yyyy') as datafim 
                        from dual";

            $stmt = $conn->prepare($sql1);
            $stmt->execute();
            $resultCount = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSetCont = new HydratingResultSet($hydrator, $stdClass);
            $resultSetCont->initialize($resultCount);

            $sql = "select  to_char(data,'dd/mm/yyyy') as data
                            ,to_char(cnpj_parceiro) codigo
                            ,'CC' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    group by data ,cnpj_parceiro
                    having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    union
                    select  to_char(data,'dd/mm/yyyy') as data
                            ,to_char(nota) codigo
                            ,'NF' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    group by data ,nota
                    having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    union
                    select  to_char(data,'dd/mm/yyyy') as data
                            ,to_char(cod_produto) codigo
                            ,'PD' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    group by data ,cod_produto
                    having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    
                    order by grupo ,data desc";
            // print "$sql";
            // exit;
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $hydrator->addStrategy('codigo', new ValueStrategy);
            $hydrator->addStrategy('grupo', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            $arrayCC = array();
            $arrayNF = array();
            $arrayPD = array();
            foreach ($resultSet as $row) {
                $elementos = $hydrator->extract($row);

                $newData = $elementos['data'];

                switch ($elementos['grupo']) {
                    case 'CC':

                        // $arrayCC[] = array(
                        //     'x'=>  $newData,
                        //     'y'=> (float)$elementos['mb'],
                        //     'codigo' => $elementos['codigo'],
                        //     'nome' => 'Cliente'
                        // );

                        $arrayCC[] = [$newData,(float)$elementos['mb']];
                        break;

                    case 'NF':
                        
                        // $arrayNF[] = array(
                        //     'x'=>  $newData,
                        //     'y'=> (float)$elementos['mb'],
                        //     'codigo' => $elementos['codigo'],
                        //     'nome' => 'Nota'
                        // );
                        $arrayNF[] = [$newData,(float)$elementos['mb']];
                        break;

                    case 'PD':
                        
                        // $arrayPD[] = array(
                        //     'x'=>  $newData,
                        //     'y'=> (float)$elementos['mb'],
                        //     'codigo' => $elementos['codigo'],
                        //     'nome' => 'Produto'
                        // );
                        $arrayPD[] = [$newData,(float)$elementos['mb']];
                        break;
                    default:
                        # code...
                        break;
                }

            }


            $data = array(
                    array(
                        'name' => 'Cliente',
                        'data' => $arrayCC
                    ),
                    array(
                        'name' => 'Nota',
                        'data' => $arrayNF
                    ),
                    array(
                        'name' => 'Produto',
                        'data' => $arrayPD
                    )
                )
            ;

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }
        $objReturn = $this->getCallbackModel();
        $objReturn->referencia = array('incio'=> $resultCount[0]['DATAINICIO'],'fim'=> $resultCount[0]['DATAFIM']);

        return $objReturn; 
    }

    public function dispersaovenda2Action()
    {
        ini_set('memory_limit', '1024M');

        $data = array();
        
        try {

            $idEmpresa  = $this->params()->fromPost('idEmpresas',null);
            $pDataInicio= $this->params()->fromPost('datainicio',null);
            $pDataFim   = $this->params()->fromPost('datafim',null);
            $idProduto  = $this->params()->fromPost('produto',null);
            $marca      = $this->params()->fromPost('marca',null);
            $categoria  = $this->params()->fromPost('categoria',null);

            // $idEmpresa = 18;
            // $pDataInicio = '01/05/2022';
            // $pDataFim    = '31/05/2022';
            if($idEmpresa){
                $idEmpresa = implode(",",json_decode($idEmpresa));
            }
            $andFilial ='';
            if($idEmpresa){
                $andFilial = " and cod_empresa in ($idEmpresa)";
            }

            $andData = '';
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
            }else{
                $sysdateFim = 'sysdate';
                $andData .= " and trunc(data) <= sysdate";
            }

            if($idProduto){
                $idProduto =  implode(",",json_decode($idProduto));
            }
            $andProduto = '';
            if($idProduto){
                $andProduto = "and a.cod_produto in ($idProduto)";
            }

            if($marca){
                $marca =  implode("','",json_decode($marca));
            }
            $andMarca = '';
            if($marca){
                $andMarca = "and a.marca in ('$marca')";
            }
            
            if($categoria){
                $categoria =  implode("','",json_decode($categoria));
            }
            $andCategoria = $andCategoria2 = '';
            if($categoria){
                $andCategoria = "and a.cod_produto in (select cod_produto from vw_skproduto_categoria where categoria in ('$categoria'))";
                $andCategoria2 = "and c.categoria in ('$categoria')";
            }

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql1 = "select to_char($sysdateInicio,'dd/mm/yyyy')  as datainicio,
                            to_char($sysdateFim,'dd/mm/yyyy') as datafim 
                        from dual";

            $stmt = $conn->prepare($sql1);
            $stmt->execute();
            $resultCount = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSetCont = new HydratingResultSet($hydrator, $stdClass);
            $resultSetCont->initialize($resultCount);

            $sql = "select  to_char(cnpj_parceiro) codigo
                            ,nome_parceiro descricao
                            ,'CC' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                            ,round(SUM(nvl(rol,0)),0) as rol
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    $andProduto
                    $andMarca
                    $andCategoria
                    group by cnpj_parceiro, nome_parceiro
                    --having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    --and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    --and round(SUM(nvl(rol,0)),0) < 300000
                    union
                    select  to_char(nota) codigo
                            ,'' descricao
                            ,'NF' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                            ,round(SUM(nvl(rol,0)),0) as rol
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    $andProduto
                    $andMarca
                    $andCategoria
                    group by nota
                    --having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    --and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    --and round(SUM(nvl(rol,0)),0) < 300000
                    union
                    select  to_char(cod_produto) codigo
                            ,descricao
                            ,'PD' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                            ,round(SUM(nvl(rol,0)),0) as rol
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    $andProduto
                    $andMarca
                    $andCategoria
                    group by cod_produto, descricao
                    --having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    --and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    --and round(SUM(nvl(rol,0)),0) < 300000
                    union
                    select  c.categoria codigo
                            ,'' descricao
                            ,'CT' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                            ,round(SUM(nvl(rol,0)),0) as rol
                    from vm_skvendanota a 
                        , vw_skproduto_categoria c
                    WHERE 1 = 1
                    and a.cod_produto = c.cod_produto
                    $andFilial
                    $andData
                    $andProduto
                    $andMarca
                    $andCategoria2
                    group by c.categoria
                    --having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    --and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    --and round(SUM(nvl(rol,0)),0) < 300000
                    union
                    select  marca codigo
                            ,'' descricao
                            ,'MC' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                            ,round(SUM(nvl(rol,0)),0) as rol
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    $andProduto
                    $andMarca
                    $andCategoria
                    group by marca
                    --having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    --and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    --and round(SUM(nvl(rol,0)),0) < 300000
                    union
                    select  emp codigo
                            ,'' descricao
                            ,'LJ' grupo
                            ,CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END AS mb
                            ,round(SUM(nvl(rol,0)),0) as rol
                    from vm_skvendanota a 
                    WHERE 1 = 1
                    $andFilial
                    $andData
                    $andProduto
                    $andMarca
                    $andCategoria
                    group by emp
                    --having CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END > 0
                    --and CASE WHEN SUM(nvl(rol,0))>0 THEN ROUND(SUM(lb)/SUM(rol)*100 ,2) ELSE 0 END < 100
                    --and round(SUM(nvl(rol,0)),0) < 300000
                    order by grupo ";
            // print "$sql";
            // exit;
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            // $hydrator->addStrategy('data', new ValueStrategy);
            $hydrator->addStrategy('codigo', new ValueStrategy);
            $hydrator->addStrategy('grupo', new ValueStrategy);
            $hydrator->addStrategy('mb', new ValueStrategy);
            $hydrator->addStrategy('rol', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data    = array();
            $arrayCC = array();
            $arrayNF = array();
            $arrayPD = array();
            $arrayCT = array();
            $arrayMC = array();
            $arrayLJ = array();
            $contCC = $contNF = $contProd = 0;
            $contCat = $contMarca = $contLoja = 0;
            foreach ($resultSet as $row) {
                $elementos = $hydrator->extract($row);

                switch ($elementos['grupo']) {
                    case 'CC':
                        $arrayCC[] = array(
                            'x'=>  (float)$elementos['rol'],
                            'y'=> (float)$elementos['mb'],
                            'nome' =>  $elementos['codigo'],
                            'descricao' =>  $elementos['descricao']
                        );

                        $contCC++;
                        break;

                    case 'NF':
                        $arrayNF[] = array(
                            'x'=>  (float)$elementos['rol'],
                            'y'=> (float)$elementos['mb'],
                            'nome' =>  $elementos['codigo'],
                            'descricao' =>  $elementos['descricao']
                        );

                        $contNF++;
                        break;

                    case 'PD':
                        $arrayPD[] = array(
                            'x'=>  (float)$elementos['rol'],
                            'y'=> (float)$elementos['mb'],
                            'nome' =>  $elementos['codigo'],
                            'descricao' =>  $elementos['descricao']
                        );

                        $contProd++;
                        break;

                    case 'CT':
                        $arrayCT[] = array(
                            'x'=>  (float)$elementos['rol'],
                            'y'=> (float)$elementos['mb'],
                            'nome' =>  $elementos['codigo'],
                            'descricao' =>  $elementos['descricao']
                        );

                        $contCat++;
                        break;

                    case 'MC':
                    
                        $arrayMC[] = array(
                            'x'=>  (float)$elementos['rol'],
                            'y'=> (float)$elementos['mb'],
                            'nome' =>  $elementos['codigo'],
                            'descricao' =>  $elementos['descricao']
                        );

                        $contMarca++;
                        break;

                    case 'LJ':
                        $arrayLJ[] = array(
                            'x'=>  (float)$elementos['rol'],
                            'y'=> (float)$elementos['mb'],
                            'nome' =>  $elementos['codigo'],
                            'descricao' =>  $elementos['descricao']
                        );

                        $contLoja++;
                        break;
                    default:
                        # code...
                        break;
                }

            }

            $data = array(
                    array(
                        'name' => 'Cliente',
                        'data' => $arrayCC
                    ),
                    array(
                        'name' => 'Nota',
                        'data' => $arrayNF
                    ),
                    array(
                        'name' => 'Produto',
                        'data' => $arrayPD
                    ),
                    array(
                        'name' => 'Categoria',
                        'data' => $arrayCT
                    ),
                    array(
                        'name' => 'Marca',
                        'data' => $arrayMC
                    ),
                    array(
                        'name' => 'Loja',
                        'data' => $arrayLJ
                    )
                )
            ;

            $contTotal = array(
                'cliente'   => $contCC,
                'nota'      => $contNF,
                'produto'   => $contProd,
                'categoria' => $contCat,
                'marca'     => $contMarca,
                'loja'      => $contLoja,
                'total'     => $contCC + $contNF + $contProd + $contCat + $contMarca + $contLoja
            );

            $this->setCallbackData($data);
            
        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }

        $objReturn = $this->getCallbackModel();
        $objReturn->contTotal = $contTotal;
        $objReturn->referencia = array('inicio'=> $resultCount[0]['DATAINICIO'],'fim'=> $resultCount[0]['DATAFIM']);

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

    
}
