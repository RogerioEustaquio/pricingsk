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

class ExploreController extends AbstractRestfulController
{
    
    /**
     * Construct
     */
    public function __construct()
    {
        
    }
    
    public function funcregionais($id){
        // return array idEmpresas

        $regionais = array();

        // 13/06/2022
        // $regionais[] = ['id'=> 'R1','idEmpresas'=> ['AR','BX','CG','FZ','JN','MA','NA','RE']];
        $regionais[] = ['id'=> 'R1','idEmpresas'=> [8,9,11,12,15,16,20,21]];
        // $regionais[] = ['id'=> 'R2','idEmpresas'=> ['IM','AP','MB','M1','RJ','SL','TE']];
        $regionais[] = ['id'=> 'R2','idEmpresas'=> [2,5,6,14,17,18,23]];
        // $regionais[] = ['id'=> 'R3','idEmpresas'=> ['BH','CB','LE','GO','JF','SA','SN','VC']];
        $regionais[] = ['id'=> 'R3','idEmpresas'=> [3,7,10,13,19,22,24,28]];

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

    public function listartreepvdAction()
    {
        $data = array();
        
        try {

            $emps           = $this->params()->fromQuery('idempresa',null);
            $regional       = $this->params()->fromQuery('regional',null);
            $datareferencia = $this->params()->fromQuery('datareferencia',null);
            $produtos       = $this->params()->fromQuery('produtos',null);
            $marcas         = $this->params()->fromQuery('marcas',null);
            $categorias     = $this->params()->fromQuery('categorias',null);
            $ordem          = $this->params()->fromQuery('ordem',null);

            if($ordem){
                $arrayOrder = json_decode($ordem);
            }

            if($emps){
                $emps   =  implode(",",json_decode($emps));
            }
            if($regional){

                $arrayEmps = json_decode($regional);
                $regional = '';
                foreach($arrayEmps as $idRow){
                    
                    $arrayLinha = $this->funcregionais($idRow);
    
                    $regional .= implode(",",$arrayLinha);
                }
                
                $emps = $emps ? $emps . ($regional ? ",". $regional: "") : $regional;
            }
            if($marcas){
                $marcas =  implode(",",json_decode($marcas));
            }
            if($produtos){
                $produtos =  implode(",",json_decode($produtos));
            }
            if($categorias){
                $categorias = implode("','",json_decode($categorias));
            }

            $andSql = '';
            if($datareferencia){
                $datareferencia = '01/'.$datareferencia;
            }

            if($emps){
                $andSql .= " and e.cod_empresa in ($emps)";
            }

            if($marcas){
                $andSql .= " and p.cod_marca in ($marcas)";
            }

            if($produtos){
                $andSql .= " and e.cod_produto in ($produtos)";
            }

            $pNiveis = $this->params()->fromQuery('niveis',null);
            $lvs = json_decode($pNiveis);

            $pNode = $this->params()->fromQuery('node',null);
            $nodeArr = explode('|', $pNode);
            $nodeId = $nodeArr[0];

            if(count($nodeArr)>2){
                $nodeId = $nodeArr[count($nodeArr)-2];
            }

            $em = $this->getEntityManager();

            if(!$lvs){
                $lvs = ['REDE', 'MARCA', 'EMPRESA'];
            }

            $nodes = array();
            foreach($lvs as $k => $n){
                if($k === 0){
                    $nodes['root'] = array( $lvs[$k], $lvs[$k], "'".$lvs[$k]."'"."||'|'||ID_".$lvs[$k] );
                } else {
                    $cols = array();
                    for($i=0; $i < $k; $i++){
                        $cols[] = $lvs[$i];
                    }

                    $cols[] = $n;

                    $nodes[$lvs[$k-1]] = array(
                        implode(", ", $cols), 
                        $lvs[$k], 
                        ($nodeId === 'root' ? null : "'".$pNode."|'" ) ."||"."'".$lvs[$k]."'"."||'|'||ID_".$lvs[$k]
                    );
                }
            }
     
            $groupBy = $nodes[$nodeId][0];
            $groupDescription = $nodes[$nodeId][1];
            $groupId = $nodes[$nodeId][2];
            $groupAndWhere = "";

            // print "$groupBy - ";
            // print "$groupDescription -";
            // print "$groupId";
            // exit;

            //loop order by//
            $orderBy = '';

            if($arrayOrder){

                $orderBy = ' GRUPO';

                // foreach($arrayOrder as $linha){

                //     if($linha->ordem){
                //         if($linha->campo == $groupDescription){
                //             if($orderBy){
                //                 $orderBy .= ',GRUPO '.$linha->ordem;
                //             }else{
                //                 $orderBy = 'GRUPO '.$linha->ordem;
                //             }
                //         }else{
    
                //             $SemOrder = false;
                //             foreach($lvs as $idGrupo){
                //                 if($linha->campo == $idGrupo){
                //                     $SemOrder = true;
                //                 }
                //             }
                //             // if(!$SemOrder){
                //             //     if($orderBy){
                //             //         $orderBy .= ', '.$linha->campo.' '.$linha->ordem;
                //             //     }else{
                //             //         $orderBy = ' '.$linha->campo.' '.$linha->ordem;
                //             //     }
                //             // }
                            
                //         }
                //     }
                // }
            }
            
            // var_dump($arrayOrder);
            // exit;
            
            if($orderBy){
                $orderBy = 'order by '.$orderBy;
            }else{
                $orderBy = 'order by GRUPO';
            }
            // Fim order by

            for ($i=0; $i < count($nodeArr); $i++) {
                $groupAndWhere .= ($i % 2 == 0 && $nodeArr[$i] !== 'root' ? " and ID_".$nodeArr[$i]." = '".$nodeArr[$i+1] . "'" : "" );
            }
            
            $leaf = ( (count($lvs) === 1 || $nodeId === $lvs[count($lvs)-2]) ? "'true'" : "'false'" );

            $conn = $em->getConnection();

            $andSqlCategoria = '';
            $tableCategoria = '';
            $groupCategoria = '';
            if(strpos($groupId,"CATEGORIA") !== false || $categorias){
                $tableCategoria = ', vw_skproduto_categoria_tmp ctmp';
                $andSqlCategoria = " and e.cod_produto = ctmp.cod_produto";
                $groupCategoria = "
                ctmp.categoria as id_categoria,
                ctmp.categoria as categoria,";

                if($categorias){
                
                    $sqlTmp ="insert into vw_skproduto_categoria_tmp
                    select distinct cod_produto, categoria
                        from vw_skproduto_categoria
                    where categoria in ('$categorias')";
                    
                }else{
                    $sqlTmp ="insert into vw_skproduto_categoria_tmp
                    select distinct cod_produto, categoria
                        from vw_skproduto_categoria";
                }

                $conn->beginTransaction();
                
                $stmt = $conn->prepare($sqlTmp);
                $stmt->execute();

            }

            

            $sql = "select id,
                         grupo,
                         leaf,
                         rol_dia_m0,
                         lb_dia_m0,
                         mb_m0,
                         rol_dia_m1,
                         lb_dia_m1,
                         mb_m1,
                         rol_dia_ac_at,
                         lb_dia_ac_at,
                         rol_dia_ac_an,
                         lb_dia_ac_an,
                         mb_ac_an,
                         var_rd_m0_m1,
                         var_ld_m0_m1,
                         var_mb_m0_m1,
                         var_rd_ac_at_an,
                         var_ld_ac_at_an
                    from (
                        select $groupId as id,
                            $groupDescription as grupo,
                            $leaf as leaf,
                                        
                            sum(rol_dia_m0) as rol_dia_m0,
                            sum(lb_dia_m0) as lb_dia_m0,
                            sum(lb_dia_m0)/sum(rol_dia_m0)*100 as mb_m0,
                            
                            sum(rol_dia_m1) as rol_dia_m1,
                            sum(lb_dia_m1) as lb_dia_m1,
                            sum(lb_dia_m1)/sum(rol_dia_m1)*100 as mb_m1,
                            
                            sum(rol_dia_ac_at) as rol_dia_ac_at,
                            sum(lb_dia_ac_at) as lb_dia_ac_at,
                            sum(lb_dia_ac_at)/sum(rol_dia_ac_at)*100 as mb_ac_at,
                                    
                            sum(rol_dia_ac_an) as rol_dia_ac_an,
                            sum(lb_dia_ac_an) as lb_dia_ac_an,
                            sum(lb_dia_ac_an)/sum(rol_dia_ac_an)*100 as mb_ac_an,
                            
                            /*Rol dia mês atual x mês anterior*/
                            round(case when nvl(sum(rol_dia_m0),0) > 0 then ((sum(rol_dia_m0)/sum(rol_dia_m1))-1)*100 end,2) as var_rd_m0_m1,
                            
                            /*Lb dia mês atual x mês anterior*/
                            round(case when nvl(sum(lb_dia_m0),0) > 0 then ((sum(lb_dia_m0)/sum(lb_dia_m1))-1)*100 end,2) as var_ld_m0_m1,
                            
                            round(case when nvl(sum(rol_dia_m0),0) > 0 and nvl(sum(rol_dia_m1),0) > 0 then ( ( sum(lb_dia_m0)/sum(rol_dia_m0) ) - ( sum(lb_dia_m1)/sum(rol_dia_m1) ) )*100 end,2) as var_mb_m0_m1,
                            
                            /*Rol dia ac ano atual x ac ano anterior*/
                            round(case when nvl(sum(rol_dia_ac_at),0) > 0 then ((sum(rol_dia_ac_at)/sum(rol_dia_ac_an))-1)*100 end,2) as var_rd_ac_at_an,
                            
                            /*Lb dia ac ano atual x ac ano anterior*/
                            round(case when nvl(sum(lb_dia_ac_at),0) > 0 then ((sum(lb_dia_ac_at)/sum(lb_dia_ac_an))-1)*100 end,2) as var_ld_ac_at_an

                        from (
                            with
                        
                            vm0 as (
                                select cod_empresa, cod_produto, du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia 
                                    from vw_skvenda_dia
                                where data = trunc(add_months(sysdate,-0),'MM')
                            ),
                        
                            vm1 as (
                                select cod_empresa, cod_produto, du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia 
                                    from vw_skvenda_dia
                                where data = trunc(add_months(sysdate,-1),'MM')
                            ),
      
                            vac_at as (
                               select cod_empresa, cod_produto, du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia 
                                 from vw_skvenda_dia
                                where data <= trunc(add_months(sysdate,-0),'MM')
                                  and data >= trunc(add_months(sysdate,-0),'RRRR')
                            ),
                      
                            vac_an as (
                               select cod_empresa, cod_produto, du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia 
                                 from vw_skvenda_dia
                                where data <= trunc(add_months(sysdate,-12),'MM')
                                  and data >= trunc(add_months(sysdate,-12),'RRRR')
                            ),
      
                            v12m AS (
                               SELECT cod_empresa, cod_produto, du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia 
                                 FROM vw_skvenda_dia
                                WHERE data <= TRUNC(ADD_MONTHS(SYSDATE,-1),'MM')
                                  AND data >= TRUNC(ADD_MONTHS(SYSDATE,-12),'MM')
                            ),
                            
                            v6m AS (
                               SELECT cod_empresa, cod_produto, du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia 
                                 FROM vw_skvenda_dia
                                WHERE data <= TRUNC(ADD_MONTHS(SYSDATE,-1),'MM')
                                  AND data >= TRUNC(ADD_MONTHS(SYSDATE,-6),'MM')
                            ),
                            
                            v3m AS (
                               SELECT cod_empresa, cod_produto, du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia 
                                 FROM vw_skvenda_dia
                                WHERE data <= TRUNC(ADD_MONTHS(SYSDATE,-1),'MM')
                                  AND data >= TRUNC(ADD_MONTHS(SYSDATE,-3),'MM')
                            )
                        
                            select 'REDE' as id_rede,
                                    'REDE' as rede,  
                                    e.cod_empresa as id_empresa,
                                    em.emp as empresa,
                                    e.cod_produto as id_produto,
                                    p.descricao as produto,
                                    p.cod_marca as id_marca,
                                    m.descricao_marca as marca,
                                    $groupCategoria

                                    e.estoque as qtd_estoque_atual,
                                    e.custo_medio as custo_medio_estoque_atual,
                                    e.valor as valor_estoque_atual,
                                    
                                    vm0.rol_dia as rol_dia_m0,
                                    vm0.lb_dia as lb_dia_m0,
                                    
                                    vm1.rol_dia as rol_dia_m1,
                                    vm1.lb_dia as lb_dia_m1,
                                    
                                    vac_at.rol_dia as rol_dia_ac_at,
                                    vac_at.lb_dia as lb_dia_ac_at,
                                    
                                    vac_an.rol_dia as rol_dia_ac_an,
                                    vac_an.lb_dia as lb_dia_ac_an,
                                    
                                    v12m.rol_dia AS rol_dia_12m,
                                    v12m.lb_dia AS lb_dia_12m,
                                    
                                    v6m.rol_dia AS rol_dia_6m,
                                    v6m.lb_dia AS lb_dia_6m,
                                    
                                    v3m.rol_dia AS rol_dia_3m,
                                    v3m.lb_dia AS lb_dia_3m
                                    
                                from vm_skestoque e,
                                     vw_skempresa em,
                                     vw_skproduto p,
                                     vw_skmarca m,
                                     vm0, vm1,
                                     vac_at, vac_an
                                     , v12m, v6m, v3m
                                     $tableCategoria
                                where e.cod_empresa = em.cod_empresa
                                  and e.cod_produto = p.cod_produto
                                  and p.cod_marca = m.cod_marca
                                  
                                  and e.cod_empresa = vm0.cod_empresa(+)
                                  and e.cod_produto = vm0.cod_produto(+)
                                  
                                  and e.cod_empresa = vm1.cod_empresa(+)
                                  and e.cod_produto = vm1.cod_produto(+)
                                  
                                  and e.cod_empresa = vac_at.cod_empresa(+)
                                  and e.cod_produto = vac_at.cod_produto(+)
                                  
                                  and e.cod_empresa = vac_an.cod_empresa(+)
                                  and e.cod_produto = vac_an.cod_produto(+)
                                  
                                  AND e.cod_empresa = v12m.cod_empresa(+)
                                  AND e.cod_produto = v12m.cod_produto(+)
                                  
                                  AND e.cod_empresa = v6m.cod_empresa(+)
                                  AND e.cod_produto = v6m.cod_produto(+)
                                  
                                  AND e.cod_empresa = v3m.cod_empresa(+)
                                  AND e.cod_produto = v3m.cod_produto(+)
                                  $andSqlCategoria
                                  $andSql
                                --and e.cod_empresa = 7
                                --and e.cod_produto = 397
                        
                        )
                        where 1=1
                        $groupAndWhere
                        group by $groupBy, $groupId)
                    where 1=1
                    $orderBy";

        //   print "$sql";
        //   exit;

            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('rol_dia_m0', new ValueStrategy);
            $hydrator->addStrategy('lb_dia_m0', new ValueStrategy);
            $hydrator->addStrategy('mb_m0', new ValueStrategy);
            $hydrator->addStrategy('rol_dia_m1', new ValueStrategy);
            $hydrator->addStrategy('lb_dia_m1', new ValueStrategy);
            $hydrator->addStrategy('mb_m1', new ValueStrategy);
            $hydrator->addStrategy('rol_dia_ac_at', new ValueStrategy);
            $hydrator->addStrategy('lb_dia_ac_at', new ValueStrategy);
            $hydrator->addStrategy('rol_dia_ac_an', new ValueStrategy);
            $hydrator->addStrategy('lb_dia_ac_an', new ValueStrategy);
            $hydrator->addStrategy('mb_ac_an', new ValueStrategy);
            $hydrator->addStrategy('var_rd_m0_m1', new ValueStrategy);
            $hydrator->addStrategy('var_ld_m0_m1', new ValueStrategy);
            $hydrator->addStrategy('var_mb_m0_m1', new ValueStrategy);
            $hydrator->addStrategy('var_rd_ac_at_an', new ValueStrategy);
            $hydrator->addStrategy('var_ld_ac_at_an', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {

                $l = $hydrator->extract($row);

                $data[] = $l;
            }

            if($categorias){
                $conn->commit();
            }

            // var_dump($data);
            // exit;

            $this->setCallbackData($data);

            $objReturn = $this->getCallbackModel();
            
        } catch (\Exception $e) {
            $objReturn = $this->setCallbackError($e->getMessage());
        }
        
        return $objReturn;
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
            // $hydrator->addStrategy('qt_mes', new ValueStrategy);
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

        $emp         = $this->params()->fromQuery('emp',null);

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

    // public function listargrupomarcaAction(){
    //     // return array idEmpresas

    //     $marcas = array();

    //     $marcas[] = ['id'=> 'G1 EVERTONOPE','idMarcas'=> [181,99,10146,300,542,584,341,1013,10388,538,342,10176,567,289,10396,70,10353,
    //     131,10174,604,211,10143,10021,620,267,10295,10187,10352,89,594,10206,9999,10165,10355,92,
    //     113,104,261,140,10184,10112,121,83]];

    //     $marcas[] = ['id'=> 'G2 MAYKONRS','idMarcas'=> [39,10426,10307,10407,10406,106,270,8,7,10102,
    //     10123,10410,1017,10158,10129,195,10394,10017,3,555,556,10148,10157,
    //     1020,60,319,148,10341,610,10100,10141,10026,10029,288,1001,10201,10200,154,10328,10342,10343,10432,10279,10386,38,75,76,613,22,
    //     10433,351,19,10159,10412,10421,10314,10126,10154,199,61,1012,10133,10405,10444,10300,197,10411,10422,10027,10198,9,10,10372,11,
    //     12,10403,322,10419,23,539,10014,10140,10414,280,519,10404,10375,10440,244,356,10191,255,10409,279,10179,10420
    //     ]];

    //     $marcas[] = ['id'=> 'G3 WELISONOPE','idMarcas'=> [349,293,10436,10389,583,172,10160,10016,117,522,10011,73,10137,582,354,10325,
    //     88,10202,82,10351,214,10023,10321,122,93,81,616,47,10186,134,105,51,161,10135,206,10416,10234,74,560,1015,72,10114,328,10178,
    //     10183,614,163,10101,59,10305,205,10281,10415,302,617,97,10395,10423,10139,10425,10193,150
    //     ]];

    //     $marcas[] = ['id'=> 'G4 INATIVO','idMarcas'=> [226,10306,10144,273,
    //     570,10379,178,87,204,586,309,10297,10149,10292,310,10164,118,10268,10185,1014,216,10177,69,
    //     10348,225,569,10169,10155,1000,336,26,10099,266,559,10166,568,337,15,10018,572,10245,10251,10142,298,132,587,
    //     10329,10175,323,10118,248,251,540,100559,147,10354,2,10236,10326,10376,580,598,602,10103,10020,334,77,175,64,100,10418,
    //     566,330,235,200,
    //     304,10237,10192,13,612,10301,290,10293,10131,169,115,143,553,10274,10235,10441,10400,10319,10196,
    //     146,282,314,10104,10316,10302,10244,10013,10413,10373,10107,346,10153,10345,335
    //     ]];

    //     $this->setCallbackData($marcas);
    //     return $this->getCallbackModel();
    // }

    public function listarcurvaAction()
    {
        $data = array();
        
        try {
            $session = $this->getSession();
            $usuario = $session['info']['usuarioSistema'];

            // $idEmpresa      = $this->params()->fromQuery('idEmpresa',null);

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = "select id_curva_abc from MS.TB_CURVA_ABC";

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

    public function listarElementosAction()
    {
        $data = array();
        
        try {

            $pNode = $this->params()->fromQuery('node',null);
            // $lvs = ['REDE', 'EMPRESA', 'CURVA_NBS', 'MARCA'];
            $data = array();
            $pkey = 'idKey';
            $data[] = [$pkey => 'REDE'];
            $data[] = [$pkey => 'EMPRESA'];
            $data[] = [$pkey => 'MARCA'];
            $data[] = [$pkey => 'CATEGORIA'];
            $data[] = [$pkey => 'PRODUTO'];
            $data[] = [$pkey => 'FAIXA CUSTO'];

            $this->setCallbackData($data);

            $objReturn = $this->getCallbackModel();
            
        } catch (\Exception $e) {
            $objReturn = $this->setCallbackError($e->getMessage());
        }
        
        return $objReturn;
    }

    public function listarordemagrupamentoAction()
    {
        $data = array();
        
        try {

            $pNode = $this->params()->fromQuery('node',null);
            $data = array();
            $data[] = ['campo' => 'REDE', 'ordem' => 'ASC'];
            $data[] = ['campo' => 'EMPRESA', 'ordem' => 'ASC'];
            $data[] = ['campo' => 'MARCA', 'ordem' => 'ASC'];
            $data[] = ['campo' => 'CATEGORIA', 'ordem' => 'ASC'];
            $data[] = ['campo' => 'PRODUTO', 'ordem' => 'ASC'];
            $data[] = ['campo' => 'FAIXA CUSTO', 'ordem' => 'ASC'];

            $this->setCallbackData($data);

            $objReturn = $this->getCallbackModel();
            
        } catch (\Exception $e) {
            $objReturn = $this->setCallbackError($e->getMessage());
        }
        
        return $objReturn;
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
            
            $sql = "select i.cod_item||c.descricao as cod_item,
                           i.descricao,
                           m.descricao as marca
                        from ms.tb_item_categoria ic,
                        ms.tb_marca m,
                        ms.tb_item i,
                        ms.tb_categoria c
                    where ic.id_item = i.id_item
                    and ic.id_categoria = c.id_categoria
                    and ic.id_marca = m.id_marca
                    and i.cod_item||c.descricao $filtroProduto
                    order by cod_item asc";
            


            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('custo_contabil', new ValueStrategy);
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
    
}
