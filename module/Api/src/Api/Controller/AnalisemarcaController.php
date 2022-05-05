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
use Zend\Console\Prompt\Char;

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
       
    public function listarcurvasAction()
    {
        $data = array();
        
        try {
            $session = $this->getSession();
            $usuario = $session['info']['usuarioSistema'];

            // $idEmpresa      = $this->params()->fromQuery('idEmpresa',null);

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = "select distinct curva id_curva_abc from vm_skestoque where curva is not null order by curva";

            $stmt = $conn->prepare($sql);
            // $stmt->bindParam(':idEmpresa', $idEmpresa);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('id_curva_abc', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);
            $this->setMessage("Solicitação enviada com sucesso.");
            
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

    public function listarcestaAction()
    {
        $data = array();
        
        try {

            $pEmp    = $this->params()->fromQuery('emp',null);
            $pCod    = $this->params()->fromQuery('codcesta',null);
            $tipoSql = $this->params()->fromQuery('tipoSql',null);

            if(!$pCod){
                throw new \Exception('Parâmetros não informados.');
            }

            $em = $this->getEntityManager();

            if(!$tipoSql){
                $filtroCesta = "like upper('".$pCod."%')";
            }else{
                $codigos =  implode("','",json_decode($pCod));
                $filtroCesta = "= '".$codigos."'";
            }
            
            $sql = "select distinct data codcesta, data descricao
                         from tb_skprodutocesta
                    where 1 = 1 
                    and to_char(to_date(data),'MM/YYYY') $filtroCesta";

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
    
    public function listarcestasAction()
    {
        $data = array();
        
        try {

            $em = $this->getEntityManager();
            
            $sql = "select distinct data codcesta, data descricao
                         from tb_skprodutocesta
                    where 1 = 1 
                    order by data desc";

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

    public function listarcestadeprodutosAction()
    {
        $data = array();
        
        try {

            $pNode      = $this->params()->fromQuery('node',null);
            $emp        = $this->params()->fromQuery('emp',null);
            $datacesta  = $this->params()->fromQuery('datacesta',null);
            $descproduto= $this->params()->fromQuery('descproduto',null);
            $codproduto = $this->params()->fromQuery('codproduto',null);

            $andsql = '';
            $datacesta= json_decode($datacesta);
            if($datacesta){
                $datacesta =  implode("','",$datacesta);
                $andsql = " and c.data in ('$datacesta')";
            }
            $emp= json_decode($emp);
            if($emp){
                $emp =  implode("','",$emp);
                $andsql .= " and e.emp in ('$emp') ";
            }

            

            if($descproduto){
                
                $andsql .= " and upper(p.descricao) like upper('%$descproduto%') ";
            }


            $codproduto= json_decode($codproduto);
            if($codproduto){
                $codproduto =  implode(",",$codproduto);
                $andsql .= " and c.codprod in ($codproduto) ";
            }

            $sql = "select c.data,
                           codemp,
                           e.emp,
                           c.codprod,
                           p.descricao,
                           preco_atual,
                           preco_sugerido,
                           alterado,
                           data_alteracao
                        from tb_skprodutocesta c,
                            VW_SKEMPRESA e,
                            vw_skproduto p
                    where c.codemp = e.cod_empresa
                    and c.codprod  = p.cod_produto
                    $andsql ";
            // print "$sql";
            // exit;
            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('preco_atual', new ValueStrategy);
            $hydrator->addStrategy('preco_sugerido', new ValueStrategy);
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

    public function listarespecialprodutoAction()
    {
        $data = array();
        
        try {

            $em = $this->getEntityManager();

            $sql = "select distinct titulo descricao, id, data
                         from tb_skprodutoselecaoespecial
                    where 1 = 1
                    order by data desc";
  
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

    public function faixacusto($idEmpresas,$data,$qtdemeses,$curvas,$codNbs,$codProdutos,$idMarcas,$montadora,$notmontadora,$cesta,$especialproduto,$categoria)
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
            $inmont = $notmontadora == 'true' ? 'not' : '';
            $andSql .= " and m.montadora $inmont in ('$montadora')";
        }

        if($codProdutos){
            $andSql .= " and i.cod_produto in ('$codProdutos')";
        }

        if($categoria){
            $andSql .= " and i.cod_produto in (select cod_produto
                                                 from vw_skproduto_categoria
                                               where categoria in ('$categoria'))";
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
    
    public function estoquemes($emp,$data,$qtdemeses,$curvas,$codNbs,$codProdutos,$idMarcas,$notmarca,$montadora,$notmontadora,$cesta,$especialproduto,$categoria)
    {
        $data1 = array();

        $andSql = '';
        $andSql2 = '';
        if($emp){
            $andSql  = " and a.emp in ('$emp')";
        }
        $andSqlCurva = "";
        $andSqltCurva = "";
        if($curvas){

            $andSqlCurva = "AND (a.cod_empresa, a.cod_produto)
             IN (SELECT codemp, codprod FROM vw_skproduto_curva_exp WHERE codprod = a.cod_produto AND curva IN ('$curvas'))";
        }
        if($especialproduto && $codNbs == 'SN'){
            $andSql .= " and a.cod_produto in (0)";

        }elseif($cesta && $codNbs == 'SN'){
            $andSql .= " and a.cod_produto in (0)";

        }else{
            if($codProdutos){

                if($especialproduto){

                    $andSql .= " and (a.cod_empresa, a.cod_produto) in (select distinct codemp, codprod
                                                        from tb_skprodutoselecaoespecial
                                                        where 1 = 1
                                                        and id in ($especialproduto))";
                }else{
                    $andSql .= " and a.cod_produto in ($codProdutos)";
                }
            }

        }
        
        if($idMarcas){
            $inmarca = $notmarca == 'true' ? 'not' : '';

            $andSql .= "AND a.marca $inmarca IN ( SELECT descricao_marca FROM vw_skmarca WHERE cod_marca IN ($idMarcas) )";
        }
        $sqlMotadora = '';
        $sqlMotadoraRelaciona = '';
        if($montadora){
            $inmont = $notmontadora == 'true' ? 'not' : '';
            $andSql .= " and m2.montadora $inmont in ('$montadora')";

            $sqlMotadora = ',tb_sk_produto_montadora m2';
            $sqlMotadoraRelaciona = 'and a.cod_produto = m2.cod_produto(+)';

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

            $conn->beginTransaction();

            try {

                if($categoria){
                    $andSql .= " and a.cod_produto in (select cod_produto
                                                         from vw_skproduto_categoria_tmp
                                                       where categoria in ('$categoria'))";
                }

                $sqlTmp ="insert into vw_skproduto_categoria_tmp
                    select cod_produto, categoria
                    from vw_skproduto_categoria where categoria in ('$categoria')";

                $stmt = $conn->prepare($sqlTmp);
                $stmt->execute();
            
                $sql = "select a.data,
                            round(sum(estoque),2) estoque,
                            case when sum(estoque) > 0 and sum(valor) > 0 then round(sum(valor)/sum(estoque),2) else 0 end custo_medio,
                            round(sum(valor),2) valor,
                            sum(case when nvl(estoque,0) > 0 then 1 end) sku_disp
                        from vw_skestoque_master a
                            $sqlMotadora
                            $andSqltCurva
                        where 1 = 1
                        $sqlMotadoraRelaciona
                        $andSql
                        $andSqlCurva
                        group by a.data
                        order by a.data";
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
                $conn->commit();

            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }

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

    public function clientemes($emp,$data,$qtdemeses,$curvas,$codNbs,$codProdutos,$idMarcas,$notmarca,$montadora,$notmontadora,$cesta,$especialproduto,$categoria)
    {
        $data1 = array();

        $andSql = '';
        $andSql2 = '';
        if($emp){
            $andSql  = " and emp in ('$emp')";
        }
        $andSqlCurva = "";
        $andSqltCurva = "";
        if($curvas){

            $andSqlCurva = "AND (v.cod_empresa, v.cod_produto) IN (SELECT c.codemp, to_char(codprod)
                                                                    FROM vw_skproduto_curva_exp c
                                                                   WHERE c.codemp = v.cod_empresa
                                                                   AND to_char(codprod) = v.cod_produto
                                                                   AND curva IN ('$curvas'))";
        }

        if($especialproduto && $codNbs == 'SN'){
            $andSql .= " and cod_produto in (0)";

        }elseif($cesta && $codNbs == 'SN'){
            $andSql .= " and cod_produto in (0)";

        }else{
            if($codProdutos){

                $codProdutos =  implode("','",explode(',',$codProdutos));

                if($especialproduto){

                    $andSql .= " and (cod_produto, v.cod_empresa) in (select distinct to_char(p.codprod), p.codemp
                                                                        from tb_skprodutoselecaoespecial p
                                                                      where 1 = 1
                                                                      and p.codemp = v.cod_empresa
                                                                      and to_char(codprod) = v.cod_produto
                                                                      and id in ($especialproduto) )";
                }else{

                    $andSql .= " and cod_produto in ('$codProdutos')";
                }
            }
        }

        if($idMarcas){
            $inmarca = $notmarca == 'true' ? 'not' : '';
            $andSql .= " and marca $inmarca in (select descricao_marca from vw_skmarca m where m.cod_marca in ($idMarcas))";
        }
        if($montadora){
            $inmont = $notmontadora == 'true' ? 'not' : '';
            $andSql .= " and cod_produto $inmont in (select distinct to_char(cod_produto) from tb_sk_produto_montadora where to_char(cod_produto) = v.cod_produto and montadora in ('$montadora'))";
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
            $andSqlData .= " and v.data >= add_months(trunc($sysdate,'MM'),-".($qtdemeses-1).")";
            $andSqlData .= " and v.data <= add_months(trunc($sysdate,'MM'),0)";
        }else{
            $andSqlData .= " and v.data >= add_months(trunc(sysdate,'MM'),-".($qtdemeses-1).")";
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

            $conn->beginTransaction();

            try {

                if($categoria){
                    $andSql .= " and cod_produto in (select cod_produto
                                                         from vw_skproduto_categoria_tmp
                                                       where categoria in ('$categoria'))";
                }

                $sqlTmp ="insert into vw_skproduto_categoria_tmp
                    select cod_produto, categoria
                    from vw_skproduto_categoria where categoria in ('$categoria')";

                $stmt = $conn->prepare($sqlTmp);
                $stmt->execute();

                $sql = "select  to_date('01'||to_char(a.data,'/mm/yyyy')) as data, 
                                sum(b.cc) as cc, 
                                sum(c.nota ) as nf,
                                sum(round(a.rol/b.cc,2)) tkm
                                -- incluir cc dia por fora no php
                        from (select data, sum(rol) as rol 
                                from vm_skvendanota 
                                where 1=1
                                group by data) a,
                                (select data, count(*) as cc
                                from (select v.data, emp, cnpj_parceiro, sum(rol) as rol 
                                        from vm_skvendanota v
                                            $andSqltCurva
                                        where 1=1
                                        $andSql
                                        $andSqlData
                                        $andSqlCurva
                                        group by v.data, emp, cnpj_parceiro)
                                where rol > 0
                                group by data) b,
                                (select data, count(*) as nota
                                from (select v.data, emp, nota, sum(rol) as rol 
                                        from vm_skvendanota v
                                            $andSqltCurva
                                        where 1=1
                                        $andSql
                                        $andSqlData
                                        $andSqlCurva
                                        group by v.data, emp, nota)
                                where rol > 0
                                group by data) c
                        where a.data = b.data
                        and a.data = c.data
                        group by '01'||to_char(a.data,'/mm/yyyy')
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

                $conn->commit();

            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            $data2 = array();
            $contMes = 0;

            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $mesesCliente[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);
                
                // var_dump($elementos['data'] );
                // var_dump($mesSelecao[$contMes]);
                // exit;

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

    public function indicesmes($emp,$data,$qtdemeses,$curvas,$codNbs,$codProdutos,$idMarcas,$notmarca,$montadora,$notmontadora,$cesta,$especialproduto,$categoria)
    {
        $data1 = array();

        $andSql = '';
        $andSql2 = '';
        $andSqlEmp  = '';
        if($emp){
            $andSql  = " and a.emp in ('$emp')";
            $andSqlEmp  = " and emp in ('$emp')";
        }
        
        $andSqlCurva = "";
        $andSqltCurva = "";
        if($curvas){

            $andSqlCurva = "AND (a.cod_empresa, a.cod_produto) IN (SELECT c.codemp, c.codprod
                                            FROM vw_skproduto_curva_exp c
                                            WHERE codprod = a.cod_produto
                                            AND curva IN ('$curvas'))";
        }

        if($especialproduto && $codNbs == 'SN'){
            $andSql  .= " and a.COD_PRODUTO in (0)";
            $andSql2 .= " and COD_PRODUTO in (0)";

        }elseif($cesta && $codNbs == 'SN'){
            $andSql  .= " and a.COD_PRODUTO in (0)";
            $andSql2 .= " and COD_PRODUTO in (0)";

        }else{
            if($codProdutos){

                if($especialproduto){
                    if($emp){
                        $andespecial = "and p.codemp in (select cod_empresa from VW_SKEMPRESA where emp  in ('$emp'))";
                    }
                    $sqlprodesp = "select distinct codprod, codemp
                                        from tb_skprodutoselecaoespecial p
                                    where 1 = 1
                                    and id in ($especialproduto)
                                    $andespecial ";

                    $andSql  .= " and (a.COD_PRODUTO, a.emp) in (select distinct to_char(p.codprod), e.emp
                                                                    from tb_skprodutoselecaoespecial p,
                                                                    VW_SKEMPRESA e
                                                                 where 1 = 1
                                                                 and p.codemp = e.cod_empresa
                                                                 and id in ($especialproduto)
                                                                 $andespecial )";

                    $andSql2 .= " and (COD_PRODUTO,cod_empresa) in ($sqlprodesp)";

                }else{

                    $andSql  .= " and a.COD_PRODUTO in ($codProdutos)";
                    $andSql2 .= " and COD_PRODUTO in ($codProdutos)";
                }
            }
        }

        $andSqlmarca = '';
        if($idMarcas){
            $inmarca = $notmarca == 'true' ? 'not' : '';
            $andSql  .= " and m.cod_marca $inmarca in ($idMarcas)";
            $andSqlmarca .= " and marca $inmarca in (select descricao_marca from vw_skmarca m where m.cod_marca in ($idMarcas))";
        }
        if($montadora){
            $inmont = $notmontadora == 'true' ? 'not' : '';
            $andSql  .= " and a.cod_produto $inmont in (select distinct to_char(cod_produto) from tb_sk_produto_montadora where montadora in ('$montadora'))";
            $andSql2 .= " and cod_produto $inmont in (select distinct to_char(cod_produto) from tb_sk_produto_montadora where montadora in ('$montadora'))";
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
            $andSqlData .= " and a.data >= add_months(trunc($sysdate,'MM'),-".($qtdemeses-1).")";
            $andSqlData .= " and a.data <= add_months(trunc($sysdate,'MM'),0)";
        }else{
            $andSqlData .= " and a.data >= add_months(trunc(sysdate,'MM'),-".($qtdemeses-1).")";
        }

        if($qtdemeses>12){

            for($int = $qtdemeses; $int > 12; $int--){

                $sqlMeses .= "select add_months(trunc($sysdate,'MM'),-".($int-1).") as id from dual union all \n";
                
            }
        }

        try {

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $mesesIdx = [null,
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

            $arrayIdxEstoque    = array();
            $arrayIdxCompra     = array();

            foreach ($resultSet as $row) {
                $data1 = $hydrator->extract($row);

                $mesSelecao[] = $mesesIdx[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);

                $arrayIdxEstoque[]  = 0;
                $arrayIdxCompra[]   = 0;

            }

            $conn->beginTransaction();

            try {

                if($categoria){
                    $andSql .= " and a.cod_produto in (select cod_produto
                                                        from vw_skproduto_categoria_tmp
                                                    where categoria in ('$categoria'))";

                    $andSql2 .= " and COD_PRODUTO in (select cod_produto
                                                            from vw_skproduto_categoria_tmp
                                                        where categoria in ('$categoria'))";
                }

                $sqlTmp ="insert into vw_skproduto_categoria_tmp
                    select cod_produto, categoria
                    from vw_skproduto_categoria where categoria in ('$categoria')";

                $stmt = $conn->prepare($sqlTmp);
                $stmt->execute();


                $sql = "select data,
                            sum(valor) as estoque_valor, 
                            sum(valor2) as estoque_valor_custo_anterior,
                            --sum(valor2)/sum(valor) as idx,
                            round((sum(valor)/sum(valor2)-1)*100,2) as idx
                        from ( select a.data,
                                    a.estoque as estoque,
                                    round(a.valor/a.estoque,2) custo_medio,
                                    round(a.estoque*a.custo_medio,2) valor,
                                    round(a.estoque*nvl(a2.custo_medio,a.custo_medio),2) valor2
                                from vw_skestoque_master a,
                                    vw_skmarca m,
                                    (select data, emp, cod_produto, estoque, custo_medio, valor
                                        from vw_skestoque_master
                                    where data = '01/12/2020') a2
                                    $andSqltCurva
                                where 1=1
                                and a.marca = m.descricao_marca
                                and a.emp = a2.emp(+)
                                and a.cod_produto = a2.cod_produto(+)
                                and a.data >= '01/01/2021' -- Não alterar essa data
                                $andSql
                                $andSqlCurva
                                -- Aplicar filtro de produtos / marcas / lojas
                                --and a.COD_PRODUTO = 397
                                --and a.emp = 'SA'
                                
                                and a.data < '01012022'
                                order by a.data
                                ) a
                        where 1 = 1 
                        $andSqlData
                        and a.data < '01012022'
                        group by data
                        union all
                        select data,
                            sum(valor) as estoque_valor, 
                            sum(valor2) as estoque_valor_custo_anterior,
                            --sum(valor2)/sum(valor) as idx,
                            round((sum(valor)/sum(valor2)-1)*100,2) as idx
                        from ( select a.data,
                                    a.estoque as estoque,
                                    round(a.valor/a.estoque,2) custo_medio,
                                    round(a.estoque*a.custo_medio,2) valor,
                                    round(a.estoque*nvl(a2.custo_medio,a.custo_medio),2) valor2
                                from vw_skestoque_master a,
                                    vw_skmarca m,
                                    (select data, emp, cod_produto, estoque, custo_medio, valor
                                        from vw_skestoque_master
                                    where data = '01/12/2021') a2
                                    $andSqltCurva
                                where 1=1
                                and a.marca = m.descricao_marca
                                and a.emp = a2.emp(+)
                                and a.cod_produto = a2.cod_produto(+)
                                and a.data >= '01/01/2022' -- Não alterar essa data
                                $andSql
                                $andSqlCurva
                                -- Aplicar filtro de produtos / marcas / lojas
                                --and a.COD_PRODUTO = 397
                                --and a.emp = 'SA'
                                and a.data >= '01012022'
                                order by a.data
                                ) a
                        where 1 = 1 
                        $andSqlData
                        and a.data >= '01012022'
                        group by data
                        order by data";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $hydrator = new ObjectProperty;
                $hydrator->addStrategy('data', new ValueStrategy);
                // $hydrator->addStrategy('estoque_valor', new ValueStrategy);
                // $hydrator->addStrategy('estoque_valor_custo_anterior', new ValueStrategy);
                $hydrator->addStrategy('idx', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);
                // $andSqlEmp;
                // $andSqlmarca;
                // $andSql2;
                // $andSqlData;

                $sql2 = "select a.data,
                            sum(qtd_operacao) as qtd_operacao,
                            round(sum(custo_operacao_pon_u)/sum(qtd_operacao),2) as custo_operacao_med_u,
                            round(sum(custo_operacao_pon_n)/sum(qtd_operacao),2) as custo_operacao_med_n,
                            round(sum(custo_operacao_pon_u),2) as custo_operacao_pon_u,
                            round(sum(custo_operacao_pon_n),2) as custo_operacao_pon_n,
                            round((sum(custo_operacao_pon_n)/sum(custo_operacao_pon_u)-1)*100,2) as idx
                            from vm_idx_inflacao_compra_item a
                                $andSqltCurva
                        where custo_operacao_pon_u is not null
                        -- Aplicar filtro de produtos / marcas / lojas
                        $andSqlEmp
                        $andSqlmarca
                        $andSql2
                        $andSqlData
                        $andSqlCurva
                        group by a.data
                        order by a.data asc";
                $stmt = $conn->prepare($sql2);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $hydrator = new ObjectProperty;
                $hydrator->addStrategy('data', new ValueStrategy);
                // $hydrator->addStrategy('custo_operacao_pon_u', new ValueStrategy);
                // $hydrator->addStrategy('custo_operacao_pon_n', new ValueStrategy);
                $hydrator->addStrategy('idx', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet2 = new HydratingResultSet($hydrator, $stdClass);
                $resultSet2->initialize($results);

                $conn->commit();

            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            $contMes = 0;

            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $mesesIdx[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);

                while($mesSelecao[$contMes] != $elementos['data'] && $contMes< $qtdemeses){
                    $contMes++;
                }

                if($mesSelecao[$contMes] == $elementos['data']){

                    $arrayIdxEstoque[$contMes]  = (float) $elementos['idx'];
                    
                }

                $contMes++;

            }

            $contMes = 0;

            foreach ($resultSet2 as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $mesesIdx[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);

                while($mesSelecao[$contMes] != $elementos['data'] && $contMes< $qtdemeses){
                    $contMes++;
                }

                if($mesSelecao[$contMes] == $elementos['data']){

                    $arrayIdxCompra[$contMes]  = (float) $elementos['idx'];
                    
                }

                $contMes++;

            }
            // $this->setCallbackData($arrayClienteMes);
            
        } catch (\Exception $e) {
            $arrayIdxEstoque    = null;
            $arrayIdxCompra     = null;
        }

        $arrayIndecesMes[] = $arrayIdxEstoque;
        $arrayIndecesMes[] = $arrayIdxCompra;

        return $arrayIndecesMes;
    }

    public function listarfichaitemgraficoAction()
    {
        $data = array();
        
        try {
            
            $emp            = $this->params()->fromPost('idEmpresas',null);
            $regional       = $this->params()->fromPost('regional',null);
            $data           = $this->params()->fromPost('data',null);
            $qtdemeses      = $this->params()->fromPost('qtdemeses',null);
            $curvas         = $this->params()->fromPost('curva',null);
            $codProdutos    = $this->params()->fromPost('idProduto',null);
            $produtos       = $this->params()->fromPost('produto',null);
            $idMarcas       = $this->params()->fromPost('marca',null);
            $notmarca       = $this->params()->fromPost('notmarca',null);
            $montadora      = $this->params()->fromPost('montadora',null);
            $notmontadora   = $this->params()->fromPost('notmontadora',null);
            $cesta          = $this->params()->fromPost('cesta',null);
            $especialproduto= $this->params()->fromPost('especialproduto',null);
            $categoria      = $this->params()->fromPost('categoria',null);
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

            $andSql = '';
            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $indicadoresAdd = json_decode($indicadoresAdd);
            $cesta          = json_decode($cesta);
            $especialproduto= json_decode($especialproduto);
            
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

            if($curvas){
                $curvas = implode("','",json_decode($curvas));
            }

            if($categoria){
                $categoria = implode("','",json_decode($categoria));
            }
            
            if($especialproduto){

                $especialproduto = implode(",",$especialproduto);
                // Tabela de cesta de produtos
                $and = " and id in ($especialproduto)";
                if($emp){
                    $and .= " and codemp in (select cod_empresa from VW_SKEMPRESA where emp  in ('$emp'))";
                }
                $sqlprodesp = " select distinct codprod
                         from tb_skprodutoselecaoespecial
                         where 1 = 1
                         $and";
                $stmt = $conn->prepare($sqlprodesp);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $hydrator = new ObjectProperty;
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $prods = array();
                foreach ($resultSet as $row) {
                    $dataprod = $hydrator->extract($row);
                    $prods[] = $dataprod['codprod'];
                }

                if(count($prods)>0){

                    $codProdutos =  implode(",",$prods);
                    
                }else{
                    $produtos = 'SN';
                    $codProdutos = '0';
                }

            }elseif($cesta){

                $cesta = implode("','",$cesta);
                // Tabela de cesta de produtos
                $andCesta = " and data in ('$cesta')";
                if($emp){
                    $andCesta .= " and codemp in (select cod_empresa from VW_SKEMPRESA where emp  in ('$emp'))";
                }
                $sql = " select distinct codprod
                         from tb_skprodutocesta
                         where 1 = 1
                         $andCesta";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $hydrator = new ObjectProperty;
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $prods = array();
                foreach ($resultSet as $row) {
                    $dataprod = $hydrator->extract($row);
                    $prods[] = $dataprod['codprod'];
                }

                if(count($prods)>0){

                    $codProdutos =  implode(",",$prods);
                    
                }else{
                    $produtos = 'SN';
                    $codProdutos = '0';
                }

            }else{

                if($produtos){
                    $produtos =  implode("','",json_decode($produtos));
                }
                if($codProdutos){
                    $codProdutos =  implode(",",json_decode($codProdutos));
                }
            }

            
            if($emp){
                $andSql = " and emp in ('$emp')";
            }
            if($regional){
                $andSql = " and emp in ('$regional')";
            }

            if($idMarcas){
                $inmarca = $notmarca == 'true' ? 'not' : '';
                $andSql .= " and cod_marca $inmarca in ($idMarcas)";
            }

            if($montadora){
                $inmont = $notmontadora == 'true' ? 'not' : '';
                $andSql .= " and m.montadora $inmont in ('$montadora')";
            }

            $andSqlCurva = "";
            $andSqltCurva = "";
            if($curvas){
                $andSqltCurva = " , vw_skproduto_curva_exp c";
                $andSqlCurva = " and i.cod_emp = c.codemp
                                 and c.codprod = m.cod_produto 
                                 and c.curva in ('$curvas')";
            }
            
            if($produtos && $produtos != '[]'){
                $andSql .= " and cod_nbs in ('$produtos')";
            }

            if($codProdutos && $codProdutos != '[]'){

                if($especialproduto){

                    $andSql .= " and (i.cod_emp, i.cod_produto) in (select distinct codemp, codprod
                                                         from tb_skprodutoselecaoespecial
                                                        where 1 = 1
                                                        and id in ($especialproduto))";

                }else{
                    $andSql .= " and i.cod_produto in ($codProdutos)";
                }
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
            $consultaIndices = false;
            
            if($indicadoresAdd){

                for ($i=0; $i < count($indicadoresAdd); $i++) {
            
                    if($indicadoresAdd[$i]->name == "estoque"){
                        $consultaEstoque = $indicadoresAdd[$i]->value;
                    }

                    if($indicadoresAdd[$i]->name == "cliente"){
                        $consultaCliente = $indicadoresAdd[$i]->value;
                    }

                    if($indicadoresAdd[$i]->name == "indices"){
                        $consultaIndices = $indicadoresAdd[$i]->value;
                    }
                }
            }


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
            $arrayPrecoMedioRob = array();
            $arrayPrecoMedioRol = array();
            $arrayCustoMedio    = array();
            $arrayDias          = array();
            $arrayQtde          = array();
            $arrayCMV           = array();
            $arrayRob           = array();
            $arrayRoldia        = array();
            $arrayLbdia         = array();
            $arrayQtdedia       = array();
            $arrayCmvDia        = array();
            $arrayRobdia        = array();
            $arrayImpostos       = array();
            $arrayCcDia         = array();
            $estoqueFator       = array();
            $estoqueGiro        = array();
            $estoqueDias        = array();
            $idxEstoque         = array();
            $idxCompra          = array();

            foreach ($resultSet as $row) {
                $data1 = $hydrator->extract($row);
                $categories[] = $meses[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);

                if((float) substr($data1['id'], 3, 2) == 1){ # 1 = mes de janeiro

                    $categoriesView[] = $meses[(float) substr($data1['id'], 3, 2)] .'/'. substr($data1['id'], 6, 2);
                }else{

                    $categoriesView[] = $meses[(float) substr($data1['id'], 3, 2)];
                }

                $arrayRol[]             = 0;
                $arrayLb[]              = 0;
                $arrayMb[]              = 0;
                $arrayPrecoMedioRob[]   = 0;
                $arrayPrecoMedioRol[]   = 0;
                $arrayCustoMedio[]      = 0;
                $arrayDias[]            = 0;
                $arrayQtde[]            = 0;
                $arrayCMV[]             = 0;
                $arrayRob[]             = 0;
                $arrayRoldia[]          = 0;
                $arrayLbdia[]           = 0;
                $arrayQtdedia[]         = 0;
                $arrayCmvDia[]          = 0;
                $arrayRobdia[]          = 0;
                $arrayImpostos[]        = 0;

                if($consultaCliente){
                    $arrayCcDia[]   = 0;
                }

                if($consultaEstoque){
                    $estoqueFator[] = 0;
                    $estoqueGiro[]  = 0;
                    $estoqueDias[]  = 0;
                }

                if($consultaIndices){
                    $idxEstoque[]   = 0;
                    $idxCompra[]    = 0;
                }

            }

            $FxCusto  = array();
            $FxCusto2  = array();

            if($regional){
                $emp =  $emp."','".$regional;
            }

            if($consultaFaixaCusto){

                $FaixaCusto = $this->faixacusto($emp,$data,$qtdemeses,$curvas,$produtos,$codProdutos,$idMarcas,$montadora,$notmontadora,$cesta,$especialproduto,$categoria);
                $FxCusto  = $FaixaCusto[0];
                $FxCusto2  = $FaixaCusto[1];
            }

            $estoqueMes         = array();
            $estoque            = array();
            $estoqueCustoMedio  = array();
            $estoqueValor       = array();
            $estoqueSkud        = array();
            
            if($consultaEstoque){

                $estoqueMes = $this->estoquemes($emp,$data,$qtdemeses,$curvas,$produtos,$codProdutos,$idMarcas,$notmarca,$montadora,$notmontadora,$cesta,$especialproduto,$categoria);
                $estoque            = $estoqueMes[0];
                $estoqueCustoMedio  = $estoqueMes[1];
                $estoqueValor       = $estoqueMes[2];
                $estoqueSkud        = $estoqueMes[3];
            }

            $cc     = array();
            $nf     = array();
            $tkm    = array();

            if($consultaCliente){

                $clienteMes = $this->clientemes($emp,$data,$qtdemeses,$curvas,$produtos,$codProdutos,$idMarcas,$notmarca,$montadora,$notmontadora,$cesta,$especialproduto,$categoria);
                $cc         = $clienteMes[0];
                $nf         = $clienteMes[1];
                $tkm        = $clienteMes[2];
            }

            $idxestoque     = array();
            $idxcompra      = array();

            if($consultaIndices){

                $idxMes = $this->indicesmes($emp,$data,$qtdemeses,$curvas,$produtos,$codProdutos,$idMarcas,$notmarca,$montadora,$notmontadora,$cesta,$especialproduto,$categoria);
                $idxestoque         = $idxMes[0];
                $idxcompra          = $idxMes[1];
            }

            $conn->beginTransaction();

            try {

                if($categoria){
                    $andSql .= " and i.cod_produto in (select cod_produto
                                                         from vw_skproduto_categoria_tmp
                                                       where categoria in ('$categoria'))";
                }

                $sqlTmp ="insert into vw_skproduto_categoria_tmp
                    select cod_produto, categoria
                    from vw_skproduto_categoria where categoria in ('$categoria')";

                $stmt = $conn->prepare($sqlTmp);
                $stmt->execute();

                $sql = "select du.data,
                            a.rol,
                            a.lb,
                            a.mb,
                            du.dias,
                            a.qtde,
                            a.cmv,
                            a.rob,
                            round((1-a.rol/a.rob)*100,2) as impostos,
                            case when  du.dias > 0 then round(a.rol / du.dias, 2) else 0  end as rol_dia,
                            case when  du.dias > 0 then round(a.lb / du.dias, 2) else 0  end as lb_dia,
                            case when  du.dias > 0 then round(a.qtde / du.dias, 0) else 0  end as qtde_dia,
                            case when  du.dias > 0 then round(a.cmv / du.dias, 2) else 0  end as cmv_dia,
                            case when  du.dias > 0 then round(a.rob / du.dias, 2) else 0  end as rob_dia
                        from VM_SKDIAS_UTEIS du,
                            (select trunc(i.data, 'MM') as data,
                                    round(sum(rol),2) as rol,
                                    round(sum(lb),2) as lb,
                                    sum(qtde) as qtde,
                                    round(sum(custo),2) as cmv,
                                    --round((sum(lb) / sum(rol)) * 100, 2) as mb
                                    case when sum(rol) <> 0 then round((sum(lb) / sum(rol)) * 100, 2) else 0 end as mb,
                                    round(sum(rob),2) as rob
                            from vm_skvendaitem_master i,
                                tb_sk_produto_montadora m
                                $andSqltCurva
                            where 1 = 1
                            $andSql
                            $andSqlCurva
                            and i.cod_produto = m.cod_produto(+)
                            group by trunc(i.data, 'MM')) a
                        where du.data = a.data(+)
                        $andSqlPeriodo
                        order by data";

                        // print "$sql";
                        // exit;

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
                $hydrator->addStrategy('rob', new ValueStrategy);
                $hydrator->addStrategy('rol_dia', new ValueStrategy);
                $hydrator->addStrategy('lb_dia', new ValueStrategy);
                $hydrator->addStrategy('qtde_dia', new ValueStrategy);
                $hydrator->addStrategy('cmv_dia', new ValueStrategy);
                $hydrator->addStrategy('rob_dia', new ValueStrategy);
                $hydrator->addStrategy('impostos', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $conn->commit();

            } catch (\Exception $e) {
                $conn->rollBack();
                throw $e;
            }

            $data = array();
            $cont = 0;
            foreach ($resultSet as $row) {

                $elementos = $hydrator->extract($row);

                $elementos['data'] = $meses[(float) substr($elementos['data'], 3, 2)] .'/'. substr($elementos['data'], 6, 2);

                while($categories[$cont] != $elementos['data'] && $cont< $qtdemeses){
                    $cont++;
                }

                if($categories[$cont] == $elementos['data']){

                    $precoMedioRob=0;
                    if($elementos['rob']>0){
                        $precoMedioRob =  round( ((float)$elementos['rob']/(float)$elementos['qtde']) ,2);
                    }
                    $precoMedioRol=0;
                    if($elementos['rol']>0){
                        $precoMedioRol =  round( ((float)$elementos['rol']/(float)$elementos['qtde']) ,2);
                    }

                    $custoMedio=0;
                    if($elementos['cmv']>0){
                        $custoMedio =  round( ((float)$elementos['cmv']/(float)$elementos['qtde']) ,2);
                    }

                    $arrayRol[$cont]            = (float)$elementos['rol'];
                    $arrayLb[$cont]             = (float)$elementos['lb'];
                    $arrayMb[$cont]             = (float)$elementos['mb'];
                    $arrayPrecoMedioRob[$cont]  = $precoMedioRob;
                    $arrayPrecoMedioRol[$cont]  = $precoMedioRol;
                    $arrayCustoMedio[$cont]     = $custoMedio;
                    $arrayDias[$cont]           = (float)$elementos['dias'];
                    $arrayQtde[$cont]           = (float)$elementos['qtde'];
                    $arrayCMV[$cont]            = (float)$elementos['cmv'];
                    $arrayRob[$cont]            = (float)$elementos['rob'];
                    $arrayRoldia[$cont]         = (float)$elementos['rolDia'];
                    $arrayLbdia[$cont]          = (float)$elementos['lbDia'];
                    $arrayQtdedia[$cont]        = (float)$elementos['qtdeDia'];
                    $arrayCmvDia[$cont]         = (float)$elementos['cmvDia'];
                    $arrayRobdia[$cont]         = (float)$elementos['robDia'];
                    $arrayImpostos[$cont]       = (float)$elementos['impostos'];

                    if($consultaCliente){

                        if($cc[$cont] > 0){

                            $arrayCcDia[$cont] = $arrayDias[$cont] > 0 ? round($cc[$cont] / $arrayDias[$cont] ,0) : 0;
                        }

                    }

                    if($consultaEstoque){
                        
                        if($estoqueValor[$cont] > 0){

                            $estoqueFator[$cont] = $arrayCMV[$cont] > 0 ? round( $estoqueValor[$cont] / $arrayCMV[$cont] ,2) : 0;
                            $estoqueGiro[$cont]  = $arrayCMV[$cont] > 0 ? round( ($arrayCMV[$cont]*12)/ $estoqueValor[$cont] ,2) : 0;
                            $estoqueDias[$cont] =  $arrayCMV[$cont] > 0 ? round( ($estoqueValor[$cont] / $arrayCMV[$cont])*30 ,2) : 0;

                        }
                    }

                    if($consultaIndices){

                        $idxEstoque[$cont]  = round($idxestoque[$cont] ,2);
                        $idxCompra[$cont]   = round($idxcompra[$cont] ,2);

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
                                'name' => 'ROB',
                                'yAxis'=> 0,
                                'color' => $colors[0],
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
                                'yAxis'=> 0,
                                'color' => $colors[1],
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
                                'yAxis'=> 2,
                                'color' => $colors[2],
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
                                'yAxis'=> 3,
                                'color' => $colors[3],
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
                                'name' => 'PREÇO MÉDIO ROB',
                                'yAxis'=> 4,
                                'color' => $colors[4],
                                'data' => $arrayPrecoMedioRob,
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
                                'name' => 'PREÇO MÉDIO ROL',
                                'yAxis'=> 4,
                                'color' => $colors[5],
                                'data' => $arrayPrecoMedioRol,
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
                                'color' => $colors[6],
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
                                'yAxis'=> 7,
                                'color' => $colors[7],
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
                                'yAxis'=> 8,
                                'color' => $colors[8],
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
                                'color' => $colors[9],
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
                                'name' => 'Impostos',
                                'yAxis'=> 10,
                                'color'=> $colors[10],
                                'data' => $arrayImpostos,
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
                                'name' => 'ROB Dia',
                                'yAxis'=> 11,
                                'color'=> $colors[11],
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
                                'yAxis'=> 11,
                                'color'=> $colors[12],
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
                                'yAxis'=> 13,
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
                                'name' => 'QTD Dia',
                                'yAxis'=> 14,
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
                                'name' => 'CMV Dia',
                                'yAxis'=> 15,
                                'color'=> $colors[15],
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
                                'yAxis'=> 16,
                                'color'=> $colors[16],
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
                                'yAxis'=> 4,
                                'color'=> $colors[17],
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
                                'yAxis'=> 18,
                                'color'=> $colors[18],
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
                                'yAxis'=> 19,
                                'color'=> $colors[19],
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
                                'yAxis'=> 20,
                                'color'=> $colors[20],
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
                                'yAxis'=> 21,
                                'color'=> $colors[21],
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
                                'yAxis'=> 22,
                                'color'=> $colors[22],
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
                                'yAxis'=> 23,
                                'color'=> $colors[23],
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
                                'yAxis'=> 24,
                                'color'=> $colors[24],
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
                                'yAxis'=> 25,
                                'color'=> $colors[25],
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
                                'yAxis'=> 26,
                                'color'=> $colors[26],
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
                            array(
                                'name' => 'Inflação de Estoque',
                                'yAxis'=> 27,
                                'color'=> $colors[27],
                                'data' => $idxEstoque,
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
                                'name' => 'Inflação de Compra',
                                'yAxis'=> 28,
                                'color'=> $colors[28],
                                'data' => $idxCompra,
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

    public function listarrankAction()
    {

        // $emp            = $this->params()->fromPost('idEmpresas',null);
        $emp            = $this->params()->fromQuery('idEmpresas',null);
        $regional       = $this->params()->fromQuery('regional',null);
        $data           = $this->params()->fromQuery('data',null);
        $qtdemeses      = $this->params()->fromQuery('qtdemeses',null);
        $curvas         = $this->params()->fromQuery('curva',null);
        $codProdutos    = $this->params()->fromQuery('idProduto',null);
        $produtos       = $this->params()->fromQuery('produto',null);
        $idMarcas       = $this->params()->fromQuery('marca',null);
        $montadora      = $this->params()->fromQuery('montadora',null);
        $notmontadora   = $this->params()->fromQuery('notmontadora',null);
        $indicadoresAdd = $this->params()->fromQuery('indicadoresAdd',null);

        $inicio     = $this->params()->fromQuery('start',null);
        $final      = $this->params()->fromQuery('limit',null);

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
            
            $emp = $emp ? $emp . ($regional ? "','". $regional: "") : $regional;
        }
        
        if($idMarcas){
            $idMarcas = implode(",",json_decode($idMarcas));
        }
        if($montadora){
            $montadora = implode("','",json_decode($montadora));
        }
        if($curvas){
            $curvas = implode("','",json_decode($curvas));
        }
        if($produtos){
            $produtos =  implode("','",json_decode($produtos));
        }
        if($codProdutos){
            $codProdutos =  implode(",",json_decode($codProdutos));
        }

        $data = array();

        $andSql = '';
        $andSql2 = '';
        if($emp){
            $andSql  = " and es.emp in ('$emp')";
        }
        $andSqlCurva = "";
        if($curvas){
            $andSqlCurva = " and es.curva in ('$curvas')";
        }
        if($codProdutos){

            // $codProdutos =  implode("','",explode(',',$codProdutos));
            $andSql .= " and p.cod_produto in ($codProdutos)";
        }
        if($idMarcas){
            $andSql .= " and m.cod_marca in ($idMarcas)";
        }
        if($montadora){
            $inmont = $notmontadora == 'true' ? 'not' : '';
            $andSql .= " and p.cod_produto $inmont in (select distinct to_char(cod_produto) from tb_sk_produto_montadora where montadora in ('$montadora'))";
        }

        // $qtdemeses = !$qtdemeses ? 12: $qtdemeses;
        // $sqlMeses = "";

        // if($data){
        //     $sysdate = "to_date('01/".$data."')";
        // }else{
        //     $sysdate = 'sysdate';
        // }

        // $andSqlData = '';
        // if($data){
        //     $andSqlData .= " and data >= add_months(trunc($sysdate,'MM'),-".($qtdemeses-1).")";
        //     $andSqlData .= " and data <= add_months(trunc($sysdate,'MM'),0)";
        // }else{
        //     $andSqlData .= " and data >= add_months(trunc(sysdate,'MM'),-".($qtdemeses-1).")";
        // }
        
        try {

            $pNode = $this->params()->fromQuery('node',null);

            $sql = "select es.COD_EMPRESA,
                           es.emp,
                           es.COD_PRODUTO,
                           p.DESCRICAO,
                           p.COD_MARCA,
                           m.DESCRICAO_MARCA,
                           to_char(es.fx_custo) fx_custo,
                           round(es.ESTOQUE,2) estoque,
                           round(es.CUSTO_MEDIO,2) custo_medio,
                           round(es.VALOR,2) valor,
                           es.CURVA,
                           es.CLIENTES
                    from vw_skestoque es,
                         vw_skproduto p,
                         vw_skmarca m
                    where es.COD_PRODUTO = p.COD_PRODUTO
                    and p.COD_MARCA = m.COD_MARCA
                    --and es.cod_produto = 397
                    --and es.emp not in ('SA')
                    $andSql
                    $andSqlCurva
                    and round(es.VALOR,2) > 0
                    --and rownum < 100
                    ";

            $session = $this->getSession();
            $session['exportprodutorank'] = "$sql";
            $this->setSession($session);

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql1 = "select count(*) as total from ($sql)";
            // $stmt = $conn->prepare($sql1);
            // $stmt->execute();
            $stmt = $conn->query($sql1);
            $resultCount = $stmt->fetchAll();

            $sql = "
                SELECT PGN.*
                FROM (SELECT ROWNUM AS RNUM, PGN.*
                        FROM ($sql) PGN) PGN
                WHERE RNUM BETWEEN " . ($inicio +1 ) . " AND " . ($inicio + $final) . "
            ";

            // $stmt = $conn->prepare($sql);
            // $stmt->execute();
            $stmt = $conn->query($sql);
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('cod_empresa', new ValueStrategy);
            $hydrator->addStrategy('cod_produto', new ValueStrategy);
            $hydrator->addStrategy('descricao', new ValueStrategy);
            $hydrator->addStrategy('cod_marca', new ValueStrategy);
            $hydrator->addStrategy('descricao_marca', new ValueStrategy);
            $hydrator->addStrategy('fx_custo', new ValueStrategy);
            $hydrator->addStrategy('estoque', new ValueStrategy);
            $hydrator->addStrategy('custo_medio', new ValueStrategy);
            $hydrator->addStrategy('valor', new ValueStrategy);
            $hydrator->addStrategy('curva', new ValueStrategy);
            $hydrator->addStrategy('clientes', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);
            }

            $this->setCallbackData($data);

            $objReturn = $this->getCallbackModel();
            
            $objReturn->total = $resultCount[0]['TOTAL'];
            
        } catch (\Exception $e) {
            $objReturn = $this->setCallbackError($e->getMessage());
        }

        return $objReturn;
    }

    public function gerarexcelAction()
    {
        $data = array();
        
        try {

            $session = $this->getSession();

            if($session['exportprodutorank']){

                ini_set('memory_limit', '5120M' );

                $em = $this->getEntityManager();
                $conn = $em->getConnection();

                $sql = $session['exportprodutorank'] ;

                // $response = new \Zend\Http\Response();
                // $response->setContent($sql);
                // $response->setStatusCode(200);
                // return $response;
                
                $conn = $em->getConnection();
                $stmt = $conn->prepare($sql);
                
                $stmt->execute();
                $results = $stmt->fetchAll();

                $hydrator = new ObjectProperty;
                $hydrator->addStrategy('cod_empresa', new ValueStrategy);
                $hydrator->addStrategy('cod_produto', new ValueStrategy);
                $hydrator->addStrategy('descricao', new ValueStrategy);
                $hydrator->addStrategy('cod_marca', new ValueStrategy);
                $hydrator->addStrategy('descricao_marca', new ValueStrategy);
                $hydrator->addStrategy('fx_custo', new ValueStrategy);
                $hydrator->addStrategy('estoque', new ValueStrategy);
                $hydrator->addStrategy('custo_medio', new ValueStrategy);
                $hydrator->addStrategy('valor', new ValueStrategy);
                $hydrator->addStrategy('curva', new ValueStrategy);
                $hydrator->addStrategy('clientes', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $data = array();
                
                $output = 'COD_EMPRESA;NOME_EMPRESA;COD_PRODUTO;DESCRICAO;COD_MARCA;DESCRICAO_MARCA'.
                          ';FX_CUSTO;ESTOQUE;CUSTO_MEDIO;VALOR;CURVA;CLIENTE'."\n";

                $i=0;
                $fxCusto = ' ';
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $codEmpresa     = $data[$i]['codEmpresa'];
                    $nomeEmpresa    = $data[$i]['emp'];
                    $codProduto     = $data[$i]['codProduto'];
                    $descricao      = $data[$i]['descricao'];
                    $codMarca       = $data[$i]['codMarca'];
                    $marca          = $data[$i]['descricaoMarca'];
                    $fxCusto        = (string) $data[$i]['fxCusto'];
                    $estoque        = $data[$i]['estoque'];

                    $custoMedio     = $data[$i]['custoMedio'] >0 ? $data[$i]['custoMedio'] : null ;
                    $valor          = $data[$i]['valor'] >0 ? $data[$i]['valor'] : null ;
                    $curva          = (string) $data[$i]['curva'];
                    $clientes       = $data[$i]['clientes'];

                    $output  .= $codEmpresa.';'.
                                $nomeEmpresa.';'.
                                $codProduto.';'.
                                $descricao.';'.
                                $codMarca.';'.
                                $marca.';'.
                                $fxCusto."\";".
                                $estoque.';'.
                                $custoMedio.';'.
                                $valor.';'.
                                $curva.';'.
                                $clientes."\n";
                    $i++;
                }

                $response = new \Zend\Http\Response();
                $response->setContent($output);
                $response->setStatusCode(200);

                $headers = [
                        'Pragma' => 'public',
                        'Cache-control' => 'must-revalidate, post-check=0, pre-check=0',
                        'Cache-control' => 'private',
                        'Expires' => '0000-00-00',
                        'Content-Type' => 'application/CSV; charset=utf-8',
                        'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Produto Rank.csv',
                    ];
                $responseHeaders = new \Zend\Http\Headers();
                $responseHeaders->addHeaders($headers);
                $response->setHeaders($responseHeaders);

                return $response;

            }

            $this->setCallbackData($data);

            $objReturn = $this->getCallbackModel();
            
        } catch (\Exception $e) {
            $objReturn = $this->setCallbackError($e->getMessage());
        }
        
        return $objReturn;
    }

    public function gerarexcel2Action()
    {

        $data = array();

        try {

            $session = $this->getSession();
            $usuario = $session['info']['usuarioSistema'];

            ini_set('memory_limit', '5120M' );

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = $session['exportprodutorank'] ;
            
            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $sm = $this->getEvent()->getApplication()->getServiceManager();
            $excelService = $sm->get('ExcelService');
            $arqFile = '.\data\exportprodutorank_'.$session['info']['usuarioSistema'].'1.xlsx';
            fopen($arqFile,'w'); // Paramentro $phpExcel somente retorno

            $phpExcel = $excelService->createPHPExcelObject($arqFile);
            $phpExcel->getActiveSheet()->setCellValue('A'.'1', 'COD_EMPRESA')
                                       ->setCellValue('B'.'1', 'NOME_EMPRESA')
                                       ->setCellValue('C'.'1', 'COD_PRODUTO')
                                       ->setCellValue('D'.'1', 'DESCRICAO')
                                       ->setCellValue('E'.'1', 'COD_MARCA')
                                       ->setCellValue('F'.'1', 'DESCRICAO_MARCA')
                                       ->setCellValue('G'.'1', 'FX_CUSTO')
                                       ->setCellValue('H'.'1', 'ESTOQUE')
                                       ->setCellValue('I'.'1', 'CUSTO_MEDIO')
                                       ->setCellValue('J'.'1', 'VALOR')
                                       ->setCellValue('K'.'1', 'CURVA')
                                       ->setCellValue('L'.'1', 'CLIENTE');

            $i=0;
            $ix=2;
            foreach ($resultSet as $row) {
                $data[] = $hydrator->extract($row);

                $codEmpresa     = $data[$i]['codEmpresa'];
                $nomeEmpresa    = $data[$i]['emp'];
                $codProduto     = $data[$i]['codProduto'];
                $descricao      = $data[$i]['descricao'];
                $codMarca       = $data[$i]['codMarca'];
                $marca          = $data[$i]['descricaoMarca'];
                $fxCusto        = $data[$i]['fxCusto'] ? $data[$i]['fxCusto'] : null ;
                $estoque        = $data[$i]['estoque'] ? $data[$i]['estoque'] : null ;

                $custoMedio     = $data[$i]['custoMedio'] >0 ? $data[$i]['custoMedio'] : null ;
                $valor          = $data[$i]['valor'] >0 ? $data[$i]['valor'] : null ;
                $curva          = $data[$i]['curva'] ? $data[$i]['curva'] : null ;
                $clientes       = $data[$i]['clientes'] ? $data[$i]['clientes'] : null ;

                $phpExcel->getActiveSheet()->setCellValue('A'.$ix, $codEmpresa)
                                        ->setCellValue('B'.$ix, $nomeEmpresa)
                                        ->setCellValue('C'.$ix, $codProduto)
                                        ->setCellValue('D'.$ix, $descricao)
                                        ->setCellValue('E'.$ix, $codMarca)
                                        ->setCellValue('F'.$ix, $marca)
                                        ->setCellValue('G'.$ix, $fxCusto)
                                        ->setCellValue('H'.$ix, $estoque)
                                        ->setCellValue('I'.$ix, $custoMedio)
                                        ->setCellValue('J'.$ix, $valor)
                                        ->setCellValue('K'.$ix, $curva)
                                        ->setCellValue('L'.$ix, $clientes);
                $i++;
                $ix++;
            }

            $objWriter = $sm->get('ExcelService')->createWriter($phpExcel, 'Excel5');

            $response = $excelService->createHttpResponse($objWriter, 200, [
                'Pragma' => 'public',
                'Cache-control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-control' => 'private',
                'Expires' => '0000-00-00',
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Produto Rank.xls',
            ]);

            return $response;

        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }

        $this->setCallbackData($data);
        $this->setMessage("Solicitação enviada com sucesso.");
        return $this->getCallbackModel();
        
    }

}
