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

class AnalisemarcaController extends AbstractRestfulController
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

        // $regionais[] = ['id'=> 'R1','idEmpresas'=> [9,2,29,23,25,24,13,19]];
        // $regionais[] = ['id'=> 'R2','idEmpresas'=> [12,10,15,16,21,3,22,8]];
        // $regionais[] = ['id'=> 'R3','idEmpresas'=> [6,4,5,17,18,14,7]];

        $regionais[] = ['id'=> 'R1','idEmpresas'=> ['AR','BX','CG','JN','MA','NA','RE','SA']];
        $regionais[] = ['id'=> 'R2','idEmpresas'=> ['FZ','IM','AP','MB','M1','SL','TE']];
        $regionais[] = ['id'=> 'R3','idEmpresas'=> ['BH','CB','LE','GO','JF','RJ','SN','VC']];

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

    public function listarmontadoraAction()
    {
        $data = array();

        $emp = $this->params()->fromQuery('emp',null);

        try {

            $session = $this->getSession();
            $usuario = $session['info'];

            $em = $this->getEntityManager();
            
            $sql = 'select distinct montadora
             from tb_sk_produto_montadora
            order by montadora';
            
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

    public function faixacusto($idEmpresas,$data,$qtdemeses,$codNbs,$codProdutos,$idMarcas,$montadora)
    {
        $data1 = array();

        $andSql = '';
        // $andSql2 = '';
        if($idEmpresas){
            $andSql  = " and emp in ('$idEmpresas')";
        }

        if($idMarcas){
            $andSql .= " and cod_marca in ($idMarcas)";
        }


        if($montadora){
            $andSql .= " and m.montadora in ('$montadora')";
        }

        if($codProdutos){
            $andSql .= " and i.cod_produto in ('$codProdutos')";
        }

        $qtdemeses = !$qtdemeses ? 12: $qtdemeses;
        $andSqlPeriodo = '';
        $sqlMeses = "";

        if($data){
            $sysdate = "to_date('01/".$data."')";
        }else{
            $sysdate = 'sysdate';
        }

        if($data){
            $andSqlPeriodo .= " and du.data >= add_months(trunc($sysdate,'MM'),-".($qtdemeses-1).")";
            $andSqlPeriodo .= " and du.data <= add_months(trunc($sysdate,'MM'),0)";
        }else{
            $andSqlPeriodo .= " and du.data >= add_months(trunc(sysdate,'MM'),-".($qtdemeses-1).")";
        }

        if($qtdemeses>12){

            for($int = $qtdemeses; $int > 12; $int--){

                $sqlMeses .= "select add_months(trunc($sysdate,'MM'),-".($int-1).") as id from dual union all \n";
                
            }
        }

        try {

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $mesesFaixa = [null,
                          'Jan',
                          'Fev',
                          'Mar',
                          'Abr',
                          'Mai',
                          'Jun',
                          'Jul',
                          'Ago',
                          'Set',
                          'Out',
                          'Nov',
                          'Dez'];

            $sql = "$sqlMeses
                    select add_months(trunc($sysdate,'MM'),-11) as id from dual union all
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
                    order by 1
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data1 = array();
            $mesSelecao = array();
            $FaixaCusto  = array();
            $FaixaCusto2  = array();

            foreach ($resultSet as $row) {
                $data1 = $hydrator->extract($row);
                
                $mesSelecao[] = $mesesFaixa[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);

                $FaixaCusto[]    = 0;
                $FaixaCusto2[]   = 0;

            }

            $sql = "select a.data, a.fx_custo, a.rol, a.lb, a.qtde, a.mb, du.dias,
                            round(a.rol/du.dias,2) as rol_dia, round(a.lb/du.dias,2) as lb_dia, round(a.qtde/du.dias,0) as qtde_dia
                    from (select trunc(data, 'MM') as data,
                                    get_fx_custo(sum(custo)/sum(qtde)) as fx_custo,
                                    sum(rol) as rol,
                                    sum(lb) as lb,
                                    sum(qtde) as qtde,
                                    round((sum(lb)/sum(rol))*100,2) as mb
                            from vm_skvendaitem_master i,
                                 tb_sk_produto_montadora m
                            where 1=1
                            $andSql
                            and data <  trunc(sysdate)
                            and i.cod_produto = m.cod_produto(+)
                            group by trunc(data, 'MM')) a,
                            VM_SKDIAS_UTEIS du
                    where du.data = a.data(+)
                    and fx_custo in ('101-250','251-500')
                    $andSqlPeriodo
                    order by data
                    ";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $hydrator->addStrategy('fx_custo', new ValueStrategy);
            $hydrator->addStrategy('rol', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data2 = array();
            $contMes = 0;

            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $mesesFaixa[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);

                while($mesSelecao[$contMes] != $elementos['data'] && $contMes< $qtdemeses){
                    $contMes++;
                }

                if($mesSelecao[$contMes] == $elementos['data']){

                    $FaixaCusto[$contMes] = (float) $elementos['rol'];

                    if($elementos['fxCusto'] == '251-500'){
                        $FaixaCusto2[$contMes]    = (float) $elementos['rol'];
                    }
                    
                    $contMes++;
                }
                
            }

        } catch (\Exception $e) {
            $FaixaCusto  = null;
        }

        $arrayFaixaCusto[] = $FaixaCusto;
        $arrayFaixaCusto[] = $FaixaCusto2;

        return $arrayFaixaCusto;
    }

    public function listarfichaitemgraficoAction()
    {
        $data = array();
        
        try {
            
            $emp     = $this->params()->fromPost('idEmpresas',null);
            $regional       = $this->params()->fromPost('regional',null);
            $data           = $this->params()->fromPost('data',null);
            $qtdemeses      = $this->params()->fromPost('qtdemeses',null);
            $codProdutos    = $this->params()->fromPost('idProduto',null);
            $produtos       = $this->params()->fromPost('produto',null);
            $idMarcas       = $this->params()->fromPost('marca',null);
            $montadora       = $this->params()->fromPost('montadora',null);
            $indicadoresAdd = $this->params()->fromPost('indicadoresAdd',null);
                                    
            $meses = [null,
                     'Jan',
                     'Fev',
                     'Mar',
                     'Abr',
                     'Mai',
                     'Jun',
                     'Jul',
                     'Ago',
                     'Set',
                     'Out',
                     'Nov',
                     'Dez'];

            $indicadoresAdd = json_decode($indicadoresAdd);
            if($emp){
                $emp =  implode("','",json_decode($emp));
            }
            if($regional){

                $arrayEmps = json_decode($regional);
                $regional = '';
                foreach($arrayEmps as $idRow){
                    
                    $arrayLinha = $this->funcregionais($idRow);
                    $regional .= implode("','",$arrayLinha);
                }
            }
            
            if($idMarcas){
                $idMarcas = implode(",",json_decode($idMarcas));
            }
            if($montadora){
                $montadora = implode("','",json_decode($montadora));
            }
            if($produtos){
                $produtos =  implode("','",json_decode($produtos));
            }
            if($codProdutos){
                $codProdutos =  implode(",",json_decode($codProdutos));
            }

            $andSql = '';
            if($emp){
                $andSql = " and emp in ('$emp')";
            }
            if($regional){
                $andSql = " and emp in ('$regional')";
            }

            if($idMarcas){
                $andSql .= " and cod_marca in ($idMarcas)";
            }

            if($montadora){
                $andSql .= " and m.montadora in ('$montadora')";
            }
            
            if($produtos){
                $andSql .= " and cod_nbs in ('$produtos')";
            }

            if($codProdutos){
                $andSql .= " and i.cod_produto in ($codProdutos)";
            }
            
            if($data){
                $sysdate = "to_date('01/".$data."')";
            }else{
                $sysdate = 'sysdate';
            }

            $qtdemeses = !$qtdemeses ? 12: $qtdemeses;

            $andSqlPeriodo = '';
            $sqlMeses = "";

            if($data){
                $andSqlPeriodo .= " and du.data >= add_months(trunc($sysdate,'MM'),-".($qtdemeses-1).")";
                $andSqlPeriodo .= " and du.data <= add_months(trunc($sysdate,'MM'),0)";

            }else{
                $andSqlPeriodo .= " and du.data >= add_months(trunc(sysdate,'MM'),-".($qtdemeses-1).")";
            }
            
            if($qtdemeses>12){

                for($int = $qtdemeses; $int > 12; $int--){

                    $sqlMeses .= "select add_months(trunc($sysdate,'MM'),-".($int-1).") as id from dual union all \n";
                    
                }
            }

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = "$sqlMeses
                    select add_months(trunc($sysdate,'MM'),-11) as id from dual union all
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
            $categoriesView = array();

            $arrayRol       = array();
            $arrayLb        = array();
            $arrayMb        = array();
            $arrayQtde      = array();
            $arrayCMV       = array();
            $arrayRoldia    = array();
            $arrayLbdia     = array();
            $arrayQtdedia   = array();
            $arrayCmvDia    = array();

            foreach ($resultSet as $row) {
                $data1 = $hydrator->extract($row);
                $categories[] = $meses[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);

                if((float) substr($data1['id'], 3, 2) == 1){ # 1 = mes de janeiro

                    $categoriesView[] = $meses[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);
                }else{

                    $categoriesView[] = $meses[(float) substr($data1['id'], 3, 2)];
                }

                $arrayRol[]         = 0;
                $arrayLb[]          = 0;
                $arrayMb[]          = 0;
                $arrayQtde[]        = 0;
                $arrayCMV[]         = 0;
                $arrayRoldia[]      = 0;
                $arrayLbdia[]       = 0;
                $arrayQtdedia[]     = 0;
                $arrayCmvDia[]      = 0;

            }

            $consultaFaixaCusto = false;
            $FxCusto  = array();
            $FxCusto2  = array();

            if($indicadoresAdd){

                for ($i=0; $i < count($indicadoresAdd); $i++) {
            
                    if($indicadoresAdd[$i]->value){
                        $consultaFaixaCusto = true;
                    }
                }
            }

            if($consultaFaixaCusto){

                $FaixaCusto = $this->faixacusto($emp,$data,$qtdemeses,$produtos,$codProdutos,$idMarcas,$montadora);
                $FxCusto  = $FaixaCusto[0];
                $FxCusto2  = $FaixaCusto[1];
            }

            $sql = "select du.data,
                        a.rol,
                        a.lb,
                        a.qtde,
                        a.cmv,
                        a.mb,
                        du.dias,
                        round(a.rol / du.dias, 2) as rol_dia,
                        round(a.lb / du.dias, 2) as lb_dia,
                        round(a.qtde / du.dias, 0) as qtde_dia,
                        round(a.cmv / du.dias, 2) as cmv_dia
                    from VM_SKDIAS_UTEIS du,
                        (select trunc(data, 'MM') as data,
                        round(sum(rol),2) as rol,
                        round(sum(lb),2) as lb,
                        sum(qtde) as qtde,
                        round(sum(custo),2) as cmv,
                        round((sum(lb) / sum(rol)) * 100, 2) as mb
                        from vm_skvendaitem_master i,
                             tb_sk_produto_montadora m
                        where 1 = 1
                        $andSql
                        and i.cod_produto = m.cod_produto(+)
                        group by trunc(data, 'MM')) a
                    where du.data = a.data(+)
                    $andSqlPeriodo
                    order by data";
            // print "$sql";
            // exit;
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('rol', new ValueStrategy);
            $hydrator->addStrategy('lb', new ValueStrategy);
            $hydrator->addStrategy('mb', new ValueStrategy);
            $hydrator->addStrategy('qtde', new ValueStrategy);
            $hydrator->addStrategy('cmv', new ValueStrategy);
            $hydrator->addStrategy('rol_dia', new ValueStrategy);
            $hydrator->addStrategy('lb_dia', new ValueStrategy);
            $hydrator->addStrategy('qtde_dia', new ValueStrategy);
            $hydrator->addStrategy('cmv_dia', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            $cont = 0;
            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $meses[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);

                while($categories[$cont] != $elementos['data'] && $cont< $qtdemeses){
                    $cont++;
                }

                if($categories[$cont] == $elementos['data']){

                    $arrayRol[$cont]         = (float)$elementos['rol'];
                    $arrayLb[$cont]          = (float)$elementos['lb'];
                    $arrayMb[$cont]          = (float)$elementos['mb'];
                    $arrayQtde[$cont]        = (float)$elementos['qtde'];
                    $arrayCMV[$cont]         = (float)$elementos['cmv'];
                    $arrayRoldia[$cont]      = (float)$elementos['rolDia'];
                    $arrayLbdia[$cont]       = (float)$elementos['lbDia'];
                    $arrayQtdedia[$cont]     = (float)$elementos['qtdeDia'];
                    $arrayCmvDia[$cont]      = (float)$elementos['cmvDia'];

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
                        'categories' => $categoriesView,
                        'series' => array(                            
                            array(
                                'name' => 'ROL',
                                'yAxis'=> 0,
                                'color' => $colors[0],
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
                                'name' => 'LB',
                                'yAxis'=> 1,
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
                                'yAxis'=> 2,
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
                                'name' => 'QTDE',
                                'yAxis'=> 3,
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
                                'name' => 'CMV',
                                'yAxis'=> 4,
                                'color' => $colors[4],
                                'data' => $arrayCMV,
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
                                'name' => 'ROL Dia',
                                'yAxis'=> 5,
                                'color'=> $colors[5],
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
                                'name' => 'LB Dia',
                                'yAxis'=> 6,
                                'color'=> $colors[6],
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
                                'name' => 'QTDE Dia',
                                'yAxis'=> 7,
                                'color'=> $colors[7],
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
                                'name' => 'CMV Dia',
                                'yAxis'=> 8,
                                'color'=> $colors[8],
                                'data' => $arrayCmvDia,
                                'vFormat' => '',
                                'vDecimos' => '2',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            // array(
                            //     'name' => 'ROL Fx 101-250',
                            //     'yAxis'=> 9,
                            //     'color'=> $colors[9],
                            //     'data' => $FxCusto,
                            //     'vFormat' => '',
                            //     'vDecimos' => '0',
                            //     'visible' => false,
                            //     'showInLegend' => false,
                            //     'dataLabels' => array(
                            //          'enabled' => true,
                            //          'style' => array( 'fontSize' => '10')
                            //         )
                            // ),
                            // array(
                            //     'name' => 'ROL Fx 251-500',
                            //     'yAxis'=> 10,
                            //     'color'=> $colors[10],
                            //     'data' => $FxCusto2,
                            //     'vFormat' => '',
                            //     'vDecimos' => '0',
                            //     'visible' => false,
                            //     'showInLegend' => false,
                            //     'dataLabels' => array(
                            //          'enabled' => true,
                            //          'style' => array( 'fontSize' => '10')
                            //         )
                            // )
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
