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
    
    public function estoquemes($emp,$data,$qtdemeses,$codNbs,$codProdutos,$idMarcas,$montadora)
    {
        $data1 = array();

        $andSql = '';
        $andSql2 = '';
        if($emp){
            $andSql  = " and a.emp in ('$emp')";
        }
        if($codProdutos){
            $andSql .= " and a.cod_produto in ($codProdutos)";
        }
        if($idMarcas){
            $andSql .= " and m.cod_marca in ($idMarcas)";
        }
        if($montadora){
            $andSql .= " and m2.montadora in ('$montadora')";
        }

        $qtdemeses = !$qtdemeses ? 12: $qtdemeses;
        $sqlMeses = "";

        if($data){
            $sysdate = "to_date('01/".$data."')";
        }else{
            $sysdate = 'sysdate';
        }

        if($data){
            $andSql .= " and a.data >= add_months(trunc($sysdate,'MM'),-".($qtdemeses-1).")";
            $andSql .= " and a.data <= add_months(trunc($sysdate,'MM'),0)";
        }else{
            $andSql .= " and a.data >= add_months(trunc(sysdate,'MM'),-".($qtdemeses-1).")";
        }

        if($qtdemeses>12){

            for($int = $qtdemeses; $int > 12; $int--){

                $sqlMeses .= "select add_months(trunc($sysdate,'MM'),-".($int-1).") as id from dual union all \n";
                
            }
        }

        try {

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $mesesEstoque = [null,
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

            $data1      = array();
            $mesSelecao = array();

            $arrayEstoque       = array();
            $EstoqueCustoMedio  = array();
            $EstoqueValor       = array();
            $EstoqueSkud        = array();

            foreach ($resultSet as $row) {
                $data1 = $hydrator->extract($row);

                $mesSelecao[] = $mesesEstoque[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);

                $arrayEstoque[]         = 0;
                $EstoqueCustoMedio[]    = 0;
                $EstoqueValor[]         = 0;
                $EstoqueSkud[]          = 0;

            }

            $sql = "select data,
                            round(sum(estoque),2) estoque,
                            round(sum(valor)/sum(estoque),2) custo_medio,
                            round(sum(valor),2) valor,
                            sum(case when nvl(estoque,0) > 0 then 1 end) sku_disp
                    from vw_skestoque_master a,
                         vw_skmarca m,
                         tb_sk_produto_montadora m2
                    where 1 = 1
                    and a.marca = m.descricao_marca
                    and a.cod_produto = m2.cod_produto(+)
                    $andSql
                    group by data
                    order by data";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $hydrator->addStrategy('estoque', new ValueStrategy);
            $hydrator->addStrategy('custo_medio', new ValueStrategy);
            $hydrator->addStrategy('valor', new ValueStrategy);
            $hydrator->addStrategy('sku_disp', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data2 = array();
            $contMes = 0;

            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $mesesEstoque[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);

                while($mesSelecao[$contMes] != $elementos['data'] && $contMes< $qtdemeses){
                    $contMes++;
                }

                if($mesSelecao[$contMes] == $elementos['data']){

                    $arrayEstoque[$contMes]         = (float) $elementos['estoque'];
                    $EstoqueCustoMedio[$contMes]    = (float) $elementos['custoMedio'];
                    $EstoqueValor[$contMes]         = (float) $elementos['valor'];
                    $EstoqueSkud[$contMes]          = (float) $elementos['skuDisp'];
                    
                }

                $contMes++;

            }
            // $this->setCallbackData($arrayEstoqueMes);
            
        } catch (\Exception $e) {
            $arrayEstoque       = null;
            $EstoqueCustoMedio  = null;
            $EstoqueValor       = null;
            $EstoqueSkud        = null;
        }

        $arrayEstoqueMes[] = $arrayEstoque;
        $arrayEstoqueMes[] = $EstoqueCustoMedio;
        $arrayEstoqueMes[] = $EstoqueValor;
        $arrayEstoqueMes[] = $EstoqueSkud;

        return $arrayEstoqueMes;
    }

    public function clientemes($emp,$data,$qtdemeses,$codNbs,$codProdutos,$idMarcas,$montadora)
    {
        $data1 = array();

        $andSql = '';
        $andSql2 = '';
        if($emp){
            $andSql  = " and emp in ('$emp')";
        }
        if($codProdutos){

            $codProdutos =  implode("','",explode(',',$codProdutos));

            $andSql .= " and cod_produto in ('$codProdutos')";
        }
        if($idMarcas){
            $andSql .= " and marca in (select descricao_marca from vw_skmarca m where m.cod_marca in ($idMarcas))";
        }
        if($montadora){
            $andSql .= " and cod_produto in (select distinct to_char(cod_produto) from tb_sk_produto_montadora where montadora in ('$montadora'))";
        }

        $qtdemeses = !$qtdemeses ? 12: $qtdemeses;
        $sqlMeses = "";

        if($data){
            $sysdate = "to_date('01/".$data."')";
        }else{
            $sysdate = 'sysdate';
        }

        $andSqlData = '';
        if($data){
            $andSqlData .= " and data >= add_months(trunc($sysdate,'MM'),-".($qtdemeses-1).")";
            $andSqlData .= " and data <= add_months(trunc($sysdate,'MM'),0)";
        }else{
            $andSqlData .= " and data >= add_months(trunc(sysdate,'MM'),-".($qtdemeses-1).")";
        }

        if($qtdemeses>12){

            for($int = $qtdemeses; $int > 12; $int--){

                $sqlMeses .= "select add_months(trunc($sysdate,'MM'),-".($int-1).") as id from dual union all \n";
                
            }
        }

        try {

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $mesesCliente = [null,
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

            $data1      = array();
            $mesSelecao = array();

            $arrayCc    = array();
            $arrayNf    = array();
            $arrayTkm   = array();

            foreach ($resultSet as $row) {
                $data1 = $hydrator->extract($row);

                $mesSelecao[] = $mesesCliente[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);

                $arrayCc[]  = 0;
                $arrayNf[]  = 0;
                $arrayTkm[] = 0;

            }

            $sql = "select  a.data, 
                            b.cc as cc, 
                            c.nota as nf,
                            round(a.rol/b.cc,2) tkm
                            -- incluir cc dia por fora no php
                    from (select data, sum(xrol) as rol 
                            from vm_skbi_venda3 
                            where 1=1
                            group by data) a,
                            (select data, count(*) as cc
                            from (select data, emp, cnpj_parceiro, sum(xrol) as rol  
                                    from vm_skbi_venda3
                                    where 1=1
                                    $andSql
                                    $andSqlData
                                    group by data, emp, cnpj_parceiro)
                            where rol > 0
                            group by data) b,
                            (select data, count(*) as nota
                            from (select data, emp, nota, sum(xrol) as rol  
                                    from vm_skbi_venda3
                                    where 1=1
                                    $andSql
                                    $andSqlData
                                    group by data, emp, nota)
                            where rol > 0
                            group by data) c
                    where a.data = b.data
                    and a.data = c.data
                    order by data asc";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $hydrator->addStrategy('cc', new ValueStrategy);
            $hydrator->addStrategy('nf', new ValueStrategy);
            $hydrator->addStrategy('tkm', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data2 = array();
            $contMes = 0;

            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $mesesCliente[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);

                while($mesSelecao[$contMes] != $elementos['data'] && $contMes< $qtdemeses){
                    $contMes++;
                }

                if($mesSelecao[$contMes] == $elementos['data']){

                    $arrayCc[$contMes]      = (float) $elementos['cc'];
                    $arrayNf[$contMes]      = (float) $elementos['nf'];
                    $arrayTkm[$contMes]     = (float) $elementos['tkm'];
                    
                }

                $contMes++;

            }
            // $this->setCallbackData($arrayClienteMes);
            
        } catch (\Exception $e) {
            $arrayCc    = null;
            $arrayNf    = null;
            $arrayTkm   = null;
        }

        $arrayClienteMes[] = $arrayCc;
        $arrayClienteMes[] = $arrayNf;
        $arrayClienteMes[] = $arrayTkm;

        return $arrayClienteMes;
    }

    public function listarfichaitemgraficoAction()
    {
        $data = array();
        
        try {
            
            $emp            = $this->params()->fromPost('idEmpresas',null);
            $regional       = $this->params()->fromPost('regional',null);
            $data           = $this->params()->fromPost('data',null);
            $qtdemeses      = $this->params()->fromPost('qtdemeses',null);
            $codProdutos    = $this->params()->fromPost('idProduto',null);
            $produtos       = $this->params()->fromPost('produto',null);
            $idMarcas       = $this->params()->fromPost('marca',null);
            $montadora      = $this->params()->fromPost('montadora',null);
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

            $consultaFaixaCusto = false;
            $consultaEstoque = false;
            $consultaCliente = false;
            
            if($indicadoresAdd){

                for ($i=0; $i < count($indicadoresAdd); $i++) {
            
                    if($indicadoresAdd[$i]->name == "estoque"){
                        $consultaEstoque = $indicadoresAdd[$i]->value;
                    }

                    if($indicadoresAdd[$i]->name == "cliente"){
                        $consultaCliente = $indicadoresAdd[$i]->value;
                    }
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

            $arrayRol           = array();
            $arrayLb            = array();
            $arrayMb            = array();
            $arrayPrecoMedio    = array();
            $arrayCustoMedio    = array();
            $arrayDias          = array();
            $arrayQtde          = array();
            $arrayCMV           = array();
            $arrayRoldia        = array();
            $arrayLbdia         = array();
            $arrayQtdedia       = array();
            $arrayCmvDia        = array();
            $arrayCcDia         = array();
            $estoqueFator       = array();
            $estoqueGiro        = array();
            $estoqueDias        = array();

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
                $arrayPrecoMedio[]  = 0;
                $arrayCustoMedio[]  = 0;
                $arrayDias[]        = 0;
                $arrayQtde[]        = 0;
                $arrayCMV[]         = 0;
                $arrayRoldia[]      = 0;
                $arrayLbdia[]       = 0;
                $arrayQtdedia[]     = 0;
                $arrayCmvDia[]      = 0;

                if($consultaCliente){
                    $arrayCcDia[]   = 0;
                }

                if($consultaEstoque){
                    $estoqueFator[] = 0;
                    $estoqueGiro[]  = 0;
                    $estoqueDias[]  = 0;
                }

            }

            $FxCusto  = array();
            $FxCusto2  = array();


            if($regional){
                $emp =  $emp."','".$regional;
            }

            if($consultaFaixaCusto){

                $FaixaCusto = $this->faixacusto($emp,$data,$qtdemeses,$produtos,$codProdutos,$idMarcas,$montadora);
                $FxCusto  = $FaixaCusto[0];
                $FxCusto2  = $FaixaCusto[1];
            }

            $estoqueMes         = array();
            $estoque            = array();
            $estoqueCustoMedio  = array();
            $estoqueValor       = array();
            $estoqueSkud        = array();
            
            if($consultaEstoque){

                $estoqueMes = $this->estoquemes($emp,$data,$qtdemeses,$produtos,$codProdutos,$idMarcas,$montadora);
                $estoque            = $estoqueMes[0];
                $estoqueCustoMedio  = $estoqueMes[1];
                $estoqueValor       = $estoqueMes[2];
                $estoqueSkud        = $estoqueMes[3];
            }

            $cc     = array();
            $nf     = array();
            $tkm    = array();

            if($consultaCliente){

                $clienteMes = $this->clientemes($emp,$data,$qtdemeses,$produtos,$codProdutos,$idMarcas,$montadora);
                $cc         = $clienteMes[0];
                $nf         = $clienteMes[1];
                $tkm        = $clienteMes[2];
            }

            $sql = "select du.data,
                        a.rol,
                        a.lb,
                        a.mb,
                        du.dias,
                        a.qtde,
                        a.cmv,
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

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('data', new ValueStrategy);
            $hydrator->addStrategy('rol', new ValueStrategy);
            $hydrator->addStrategy('lb', new ValueStrategy);
            $hydrator->addStrategy('mb', new ValueStrategy);
            $hydrator->addStrategy('dias', new ValueStrategy);
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
                    $arrayPrecoMedio[$cont]  = round( ((float)$elementos['rol']/(float)$elementos['qtde']) ,2);
                    $arrayCustoMedio[$cont]  = round( ((float)$elementos['cmv']/(float)$elementos['qtde']) ,2);
                    $arrayDias[$cont]        = (float)$elementos['dias'];
                    $arrayQtde[$cont]        = (float)$elementos['qtde'];
                    $arrayCMV[$cont]         = (float)$elementos['cmv'];
                    $arrayRoldia[$cont]      = (float)$elementos['rolDia'];
                    $arrayLbdia[$cont]       = (float)$elementos['lbDia'];
                    $arrayQtdedia[$cont]     = (float)$elementos['qtdeDia'];
                    $arrayCmvDia[$cont]      = (float)$elementos['cmvDia'];

                    if($consultaCliente){

                        if($cc[$cont] > 0){

                            $arrayCcDia[$cont] = round($cc[$cont] / $arrayDias[$cont] ,0);
                        }

                    }

                    if($consultaEstoque){
                        
                        if($estoqueValor[$cont] > 0){

                            $estoqueFator[$cont] =  round( $estoqueValor[$cont] / $arrayCMV[$cont] ,2);
                            $estoqueGiro[$cont]  =  round( ($arrayCMV[$cont]*12)/ $estoqueValor[$cont] ,2);
                            $estoqueDias[$cont] =  round( ($estoqueValor[$cont] / $arrayCMV[$cont])*30 ,2);

                        }
                    }

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
                                'name' => 'PREÇO MÉDIO',
                                'yAxis'=> 3,
                                'color' => $colors[3],
                                'data' => $arrayPrecoMedio,
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
                                'name' => 'CUSTO MÉDIO',
                                'yAxis'=> 4,
                                'color' => $colors[4],
                                'data' => $arrayCustoMedio,
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
                                'name' => 'Dias',
                                'yAxis'=> 5,
                                'color' => $colors[5],
                                'data' => $arrayDias,
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
                                'name' => 'QTD',
                                'yAxis'=> 6,
                                'color' => $colors[6],
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
                                'yAxis'=> 0,
                                'color' => $colors[7],
                                'data' => $arrayCMV,
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
                                'yAxis'=> 8,
                                'color'=> $colors[8],
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
                                'yAxis'=> 9,
                                'color'=> $colors[9],
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
                                'name' => 'QTD Dia',
                                'yAxis'=> 10,
                                'color'=> $colors[10],
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
                                'yAxis'=> 11,
                                'color'=> $colors[10],
                                'data' => $arrayCmvDia,
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
                                'name' => 'ES. QTD',
                                'yAxis'=> 12,
                                'color'=> $colors[12],
                                'data' => $estoque,
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
                                'name' => 'ES. CUSTO MÉDIO',
                                'yAxis'=> 13,
                                'color'=> $colors[13],
                                'data' => $estoqueCustoMedio,
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
                                'name' => 'ES. VALOR',
                                'yAxis'=> 14,
                                'color'=> $colors[14],
                                'data' => $estoqueValor,
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
                                'name' => 'ES. FATOR',
                                'yAxis'=> 15,
                                'color'=> $colors[15],
                                'data' => $estoqueFator,
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
                                'name' => 'ES. GIRO',
                                'yAxis'=> 16,
                                'color'=> $colors[16],
                                'data' => $estoqueGiro,
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
                                'name' => 'ES. DIAS',
                                'yAxis'=> 17,
                                'color'=> $colors[17],
                                'data' => $estoqueDias,
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
                                'name' => 'SKUD',
                                'yAxis'=> 18,
                                'color'=> $colors[18],
                                'data' => $estoqueSkud,
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
                                'name' => 'CC',
                                'yAxis'=> 19,
                                'color'=> $colors[19],
                                'data' => $cc,
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
                                'name' => 'NF',
                                'yAxis'=> 20,
                                'color'=> $colors[20],
                                'data' => $nf,
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
                                'name' => 'TKM',
                                'yAxis'=> 21,
                                'color'=> $colors[21],
                                'data' => $tkm,
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
                                'name' => 'CC Dia',
                                'yAxis'=> 22,
                                'color'=> $colors[22],
                                'data' => $arrayCcDia,
                                'vFormat' => '',
                                'vDecimos' => '0',
                                'visible' => false,
                                'showInLegend' => false,
                                'dataLabels' => array(
                                     'enabled' => true,
                                     'style' => array( 'fontSize' => '10')
                                    )
                            ),
                            // array(
                            //     'name' => 'ROL Fx 101-250',
                            //     'yAxis'=> 11,
                            //     'color'=> $colors[11],
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
                            //     'yAxis'=> 12,
                            //     'color'=> $colors[12],
                            //     'data' => $FxCusto2,
                            //     'vFormat' => '',
                            //     'vDecimos' => '0',
                            //     'visible' => false,
                            //     'showInLegend' => false,
                            //     'dataLabels' => array(
                            //          'enabled' => true,
                            //          'style' => array( 'fontSize' => '10')
                            //         )
                            //     ),
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
