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
                           round(rol_m0,4) as rol_m0
                           ,round(rol_m1,4) as rol_m1
                           ,round(rol_m2,4) as rol_m2
                           ,round(rol_m3,4) as rol_m3
                           ,round(rol_3m,4) as rol_3m
                           ,round(rol_6m,4) as rol_6m
                           ,round(rol_12m,4) as rol_12m
                           ,round(rol_acat,4) as rol_acat
                           ,round(rol_acan,4) as rol_acan

                           ,round(cmv_m0,4) as cmv_m0
                           ,round(cmv_m1,4) as cmv_m1
                           ,round(cmv_m2,4) as cmv_m2
                           ,round(cmv_m3,4) as cmv_m3
                           ,round(cmv_3m,4) as cmv_3m
                           ,round(cmv_6m,4) as cmv_6m
                           ,round(cmv_12m,4) as cmv_12m
                           ,round(cmv_acat,4) as cmv_acat
                           ,round(cmv_acan,4) as cmv_acan

                           ,round(lb_m0,4) as lb_m0
                           ,round(lb_m1,4) as lb_m1
                           ,round(lb_m2,4) as lb_m2
                           ,round(lb_m3,4) as lb_m3
                           ,round(lb_3m,4) as lb_3m
                           ,round(lb_6m,4) as lb_6m
                           ,round(lb_12m,4) as lb_12m
                           ,round(lb_acat,4) as lb_acat
                           ,round(lb_acan,4) as lb_acan

                           ,round(qtd_m0,4) as qtd_m0
                           ,round(qtd_m1,4) as qtd_m1
                           ,round(qtd_m2,4) as qtd_m2
                           ,round(qtd_m3,4) as qtd_m3
                           ,round(qtd_3m,4) as qtd_3m
                           ,round(qtd_6m,4) as qtd_6m
                           ,round(qtd_12m,4) as qtd_12m
                           ,round(qtd_acat,4) as qtd_acat
                           ,round(qtd_acan,4) as qtd_acan

                           ,round(roldia_m0,4) as roldia_m0
                           ,round(roldia_m1,4) as roldia_m1
                           ,round(roldia_m2,4) as roldia_m2
                           ,round(roldia_m3,4) as roldia_m3
                           ,round(roldia_3m,4) as roldia_3m
                           ,round(roldia_6m,4) as roldia_6m
                           ,round(roldia_12m,4) as roldia_12m
                           ,round(roldia_acat,4) as roldia_acat
                           ,round(roldia_acan,4) as roldia_acan

                           ,round(cmvdia_m0,4) as cmvdia_m0
                           ,round(cmvdia_m1,4) as cmvdia_m1
                           ,round(cmvdia_m2,4) as cmvdia_m2
                           ,round(cmvdia_m3,4) as cmvdia_m3
                           ,round(cmvdia_3m,4) as cmvdia_3m
                           ,round(cmvdia_6m,4) as cmvdia_6m
                           ,round(cmvdia_12m,4) as cmvdia_12m
                           ,round(cmvdia_acat,4) as cmvdia_acat
                           ,round(cmvdia_acan,4) as cmvdia_acan

                           ,round(lbdia_m0,4) as lbdia_m0
                           ,round(lbdia_m1,4) as lbdia_m1
                           ,round(lbdia_m2,4) as lbdia_m2
                           ,round(lbdia_m3,4) as lbdia_m3
                           ,round(lbdia_3m,4) as lbdia_3m
                           ,round(lbdia_6m,4) as lbdia_6m
                           ,round(lbdia_12m,4) as lbdia_12m
                           ,round(lbdia_acat,4) as lbdia_acat
                           ,round(lbdia_acan,4) as lbdia_acan

                           ,round(qtddia_m0,4) as qtddia_m0
                           ,round(qtddia_m1,4) as qtddia_m1
                           ,round(qtddia_m2,4) as qtddia_m2
                           ,round(qtddia_m3,4) as qtddia_m3
                           ,round(qtddia_3m,4) as qtddia_3m
                           ,round(qtddia_6m,4) as qtddia_6m
                           ,round(qtddia_12m,4) as qtddia_12m
                           ,round(qtddia_acat,4) as qtddia_acat
                           ,round(qtddia_acan,4) as qtddia_acan

                            ,round(var_rold_m0_m1,4) as var_rold_m0_m1
                            ,round(var_lbd_m0_m1,4) as var_lbd_m0_m1
                            ,round(var_mbd_m0_m1,4) as var_mbd_m0_m1
                            ,round(var_rold_acat_acan,4) as var_rold_acat_acan
                            ,round(var_lbd_acat_acan,4) as var_lbd_acat_acan

                    from (
                        select $groupId as id,
                                $groupDescription as grupo,
                                $leaf as leaf

                                ,sum(rol_m0) as rol_m0
                                ,sum(rol_m1) as rol_m1
                                ,sum(rol_m2) as rol_m2
                                ,sum(rol_m3) as rol_m3
                                ,sum(rol_3m) as rol_3m
                                ,sum(rol_6m) as rol_6m
                                ,sum(rol_12m) as rol_12m
                                ,sum(rol_acat) as rol_acat
                                ,sum(rol_acan) as rol_acan
                                
                                ,sum(cmv_m0) as cmv_m0
                                ,sum(cmv_m1) as cmv_m1
                                ,sum(cmv_m2) as cmv_m2
                                ,sum(cmv_m3) as cmv_m3
                                ,sum(cmv_3m) as cmv_3m
                                ,sum(cmv_6m) as cmv_6m
                                ,sum(cmv_12m) as cmv_12m
                                ,sum(cmv_acat) as cmv_acat
                                ,sum(cmv_acan) as cmv_acan

                                ,sum(lb_m0) as lb_m0
                                ,sum(lb_m1) as lb_m1
                                ,sum(lb_m2) as lb_m2
                                ,sum(lb_m3) as lb_m3
                                ,sum(lb_3m) as lb_3m
                                ,sum(lb_6m) as lb_6m
                                ,sum(lb_12m) as lb_12m
                                ,sum(lb_acat) as lb_acat
                                ,sum(lb_acan) as lb_acan

                                ,sum(qtd_m0) as qtd_m0
                                ,sum(qtd_m1) as qtd_m1
                                ,sum(qtd_m2) as qtd_m2
                                ,sum(qtd_m3) as qtd_m3
                                ,sum(qtd_3m) as qtd_3m
                                ,sum(qtd_6m) as qtd_6m
                                ,sum(qtd_12m) as qtd_12m
                                ,sum(qtd_acat) as qtd_acat
                                ,sum(qtd_acan) as qtd_acan

                                ,sum(roldia_m0) as roldia_m0
                                ,sum(roldia_m1) as roldia_m1
                                ,sum(roldia_m2) as roldia_m2
                                ,sum(roldia_m3) as roldia_m3
                                ,sum(roldia_3m) as roldia_3m
                                ,sum(roldia_6m) as roldia_6m
                                ,sum(roldia_12m) as roldia_12m
                                ,sum(roldia_acat) as roldia_acat
                                ,sum(roldia_acan) as roldia_acan
                            
                                ,sum(cmvdia_m0) as cmvdia_m0
                                ,sum(cmvdia_m1) as cmvdia_m1
                                ,sum(cmvdia_m2) as cmvdia_m2
                                ,sum(cmvdia_m3) as cmvdia_m3
                                ,sum(cmvdia_3m) as cmvdia_3m
                                ,sum(cmvdia_6m) as cmvdia_6m
                                ,sum(cmvdia_12m) as cmvdia_12m
                                ,sum(cmvdia_acat) as cmvdia_acat
                                ,sum(cmvdia_acan) as cmvdia_acan

                                ,sum(lbdia_m0) as lbdia_m0
                                ,sum(lbdia_m1) as lbdia_m1
                                ,sum(lbdia_m2) as lbdia_m2
                                ,sum(lbdia_m3) as lbdia_m3
                                ,sum(lbdia_3m) as lbdia_3m
                                ,sum(lbdia_6m) as lbdia_6m
                                ,sum(lbdia_12m) as lbdia_12m
                                ,sum(lbdia_acat) as lbdia_acat
                                ,sum(lbdia_acan) as lbdia_acan

                                ,sum(qtddia_m0) as qtddia_m0
                                ,sum(qtddia_m1) as qtddia_m1
                                ,sum(qtddia_m2) as qtddia_m2
                                ,sum(qtddia_m3) as qtddia_m3
                                ,sum(qtddia_3m) as qtddia_3m
                                ,sum(qtddia_6m) as qtddia_6m
                                ,sum(qtddia_12m) as qtddia_12m
                                ,sum(qtddia_acat) as qtddia_acat
                                ,sum(qtddia_acan) as qtddia_acan
                            
                                /*Rol dia mês atual x mês anterior*/
                                ,round(case when nvl(sum(roldia_m0),0) > 0 then ((sum(roldia_m0)/sum(roldia_m1))-1)*100 end,2) as var_rold_m0_m1
                                /*Lb dia mês atual x mês anterior*/
                                ,round(case when nvl(sum(lbdia_m0),0) > 0 then ((sum(lbdia_m0)/sum(lbdia_m1))-1)*100 end,2) as var_lbd_m0_m1
                                /* MB diamês atual x mês anterior*/
                                ,round(case when nvl(sum(roldia_m0),0) > 0 and nvl(sum(roldia_m1),0) > 0 then ( ( sum(lbdia_m0)/sum(roldia_m0) ) - ( sum(lbdia_m1)/sum(roldia_m1) ) )*100 end,2) as var_mbd_m0_m1
                                /*Rol dia ac ano atual x ac ano anterior*/
                                ,round(case when nvl(sum(roldia_acat),0) > 0 then ((sum(roldia_acat)/sum(roldia_acan))-1)*100 end,2) as var_rold_acat_acan
                                /*Lb dia ac ano atual x ac ano anterior*/
                                ,round(case when nvl(sum(lbdia_acat),0) > 0 then ((sum(lbdia_acat)/sum(lbdia_acan))-1)*100 end,2) as var_lbd_acat_acan

                                
                        from (

                            with

                            vx as (
                                    select cod_empresa, cod_produto, 
                                            --du, rol, lb, qtd, rol_dia, lb_dia, qtd_dia,
                                            
                                            -- Rol
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then rol end) as rol_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then rol end) as rol_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then rol end) as rol_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then rol end) as rol_m3,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then rol end) as rol_3m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then rol end) as rol_6m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then rol end) as rol_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then rol end) as rol_acat,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then rol end) as rol_acan,
                                            
                                            -- Custo
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then cmv end) as cmv_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then cmv end) as cmv_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then cmv end) as cmv_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then cmv end) as cmv_m3,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then cmv end) as cmv_3m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then cmv end) as cmv_6m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then cmv end) as cmv_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then cmv end) as cmv_acat,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then cmv end) as cmv_acan,
                                            
                                            -- Lucro bruto              
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then lb end) as lb_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then lb end) as lb_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then lb end) as lb_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then lb end) as lb_m3,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then lb end) as lb_3m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then lb end) as lb_6m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then lb end) as lb_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then lb end) as lb_acat,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then lb end) as lb_acan,
                                            
                                            -- Qtd 
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then qtd end) as qtd_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then qtd end) as qtd_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then qtd end) as qtd_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then qtd end) as qtd_m3,

                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then qtd end) as qtd_3m,

                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then qtd end) as qtd_6m,

                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then qtd end) as qtd_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then qtd end) as qtd_acat,

                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then qtd end) as qtd_acan,
                                            
                                            -- Rol Dia
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then rol_dia end) as roldia_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then rol_dia end) as roldia_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then rol_dia end) as roldia_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then rol_dia end) as roldia_m3,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then rol_dia end) as roldia_3m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then rol_dia end) as roldia_6m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then rol_dia end) as roldia_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then rol_dia end) as roldia_acat,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then rol_dia end) as roldia_acan,
                                            
                                            -- Custo
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then cmv_dia end) as cmvdia_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then cmv_dia end) as cmvdia_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then cmv_dia end) as cmvdia_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then cmv_dia end) as cmvdia_m3,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then cmv_dia end) as cmvdia_3m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then cmv_dia end) as cmvdia_6m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then cmv_dia end) as cmvdia_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then cmv_dia end) as cmvdia_acat,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then cmv_dia end) as cmvdia_acan,
                                            
                                            -- Lucro bruto              
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then lb_dia end) as lbdia_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then lb_dia end) as lbdia_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then lb_dia end) as lbdia_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then lb_dia end) as lbdia_m3,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then lb_dia end) as lbdia_3m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then lb_dia end) as lbdia_6m,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then lb_dia end) as lbdia_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then lb_dia end) as lbdia_acat,
                                            
                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then lb_dia end) as lbdia_acan,
                                            
                                            -- Qtd Dia
                                            sum(case when data = trunc(add_months(sysdate,-0),'MM') then qtd_dia end) as qtddia_m0,
                                            sum(case when data = trunc(add_months(sysdate,-1),'MM') then qtd_dia end) as qtddia_m1,
                                            sum(case when data = trunc(add_months(sysdate,-2),'MM') then qtd_dia end) as qtddia_m2,
                                            sum(case when data = trunc(add_months(sysdate,-3),'MM') then qtd_dia end) as qtddia_m3,

                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-3),'MM') then qtd_dia end) as qtddia_3m,

                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-6),'MM') then qtd_dia end) as qtddia_6m,

                                            sum(case when data <= trunc(add_months(sysdate,-1),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'MM') then qtd_dia end) as qtddia_12m,
                                                        
                                            sum(case when data <= trunc(add_months(sysdate,-0),'MM') and
                                                        data >= trunc(add_months(sysdate,-0),'RRRR') then qtd_dia end) as qtddia_acat,

                                            sum(case when data <= trunc(add_months(sysdate,-12),'MM') and
                                                        data >= trunc(add_months(sysdate,-12),'RRRR') then qtd_dia end) as qtddia_acan
                                                        
                                        from vw_skvenda_dia
                                    where data <= trunc(add_months(sysdate,-0),'MM')
                                        and trunc(data,'RRRR') >= trunc(add_months(sysdate,-12),'RRRR')
                                    group by cod_empresa, cod_produto
                                )

                                select 'REDE' as id_rede,
                                        'REDE' as rede,  
                                        e.cod_empresa id_empresa,
                                        em.emp as empresa,
                                        e.cod_produto id_produto,
                                        p.descricao as produto,
                                        p.cod_marca id_marca,
                                        m.descricao_marca marca,
                                        $groupCategoria
                                        
                                        e.estoque as qtd_estoque_atual,
                                        e.custo_medio as custo_medio_estoque_atual,
                                        e.valor as vlr_estoque_atual,
                                        
                                        vx.rol_m0,
                                        vx.rol_m1,
                                        vx.rol_m2,
                                        vx.rol_m3,
                                        vx.rol_3m,
                                        vx.rol_6m,
                                        vx.rol_12m,
                                        vx.rol_acat,
                                        vx.rol_acan,
                                        vx.cmv_m0,
                                        vx.cmv_m1,
                                        vx.cmv_m2,
                                        vx.cmv_m3,
                                        vx.cmv_3m,
                                        vx.cmv_6m,
                                        vx.cmv_12m,
                                        vx.cmv_acat,
                                        vx.cmv_acan,
                                        vx.lb_m0,
                                        vx.lb_m1,
                                        vx.lb_m2,
                                        vx.lb_m3,
                                        vx.lb_3m,
                                        vx.lb_6m,
                                        vx.lb_12m,
                                        vx.lb_acat,
                                        vx.lb_acan,
                                        vx.qtd_m0,
                                        vx.qtd_m1,
                                        vx.qtd_m2,
                                        vx.qtd_m3,
                                        vx.qtd_3m,
                                        vx.qtd_6m,
                                        vx.qtd_12m,
                                        vx.qtd_acat,
                                        vx.qtd_acan,
                                        vx.roldia_m0,
                                        vx.roldia_m1,
                                        vx.roldia_m2,
                                        vx.roldia_m3,
                                        vx.roldia_3m,
                                        vx.roldia_6m,
                                        vx.roldia_12m,
                                        vx.roldia_acat,
                                        vx.roldia_acan,
                                        vx.cmvdia_m0,
                                        vx.cmvdia_m1,
                                        vx.cmvdia_m2,
                                        vx.cmvdia_m3,
                                        vx.cmvdia_3m,
                                        vx.cmvdia_6m,
                                        vx.cmvdia_12m,
                                        vx.cmvdia_acat,
                                        vx.cmvdia_acan,
                                        vx.lbdia_m0,
                                        vx.lbdia_m1,
                                        vx.lbdia_m2,
                                        vx.lbdia_m3,
                                        vx.lbdia_3m,
                                        vx.lbdia_6m,
                                        vx.lbdia_12m,
                                        vx.lbdia_acat,
                                        vx.lbdia_acan,
                                        vx.qtddia_m0,
                                        vx.qtddia_m1,
                                        vx.qtddia_m2,
                                        vx.qtddia_m3,
                                        vx.qtddia_3m,
                                        vx.qtddia_6m,
                                        vx.qtddia_12m,
                                        vx.qtddia_acat,
                                        vx.qtddia_acan
                                        
                                    from vm_skestoque e,
                                         vw_skempresa em,
                                         vw_skproduto p,
                                         vw_skmarca m, vx
                                         $tableCategoria
                                where e.cod_empresa = em.cod_empresa
                                and e.cod_produto = p.cod_produto
                                and p.cod_marca = m.cod_marca
                                --nd e.cod_produto = pc.cod_produto(+)
                                $andSqlCategoria
                                
                                and e.cod_empresa = vx.cod_empresa(+)
                                and e.cod_produto = vx.cod_produto(+)
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
            $hydrator->addStrategy('rol_m0', new ValueStrategy);
            $hydrator->addStrategy('rol_m1', new ValueStrategy);
            $hydrator->addStrategy('rol_m2', new ValueStrategy);
            $hydrator->addStrategy('rol_m3', new ValueStrategy);
            $hydrator->addStrategy('rol_3m', new ValueStrategy);
            $hydrator->addStrategy('rol_6m', new ValueStrategy);
            $hydrator->addStrategy('rol_12m', new ValueStrategy);
            $hydrator->addStrategy('rol_acat', new ValueStrategy);
            $hydrator->addStrategy('rol_acan', new ValueStrategy);

            $hydrator->addStrategy('cmv_m0', new ValueStrategy);
            $hydrator->addStrategy('cmv_m1', new ValueStrategy);
            $hydrator->addStrategy('cmv_m2', new ValueStrategy);
            $hydrator->addStrategy('cmv_m3', new ValueStrategy);
            $hydrator->addStrategy('cmv_3m', new ValueStrategy);
            $hydrator->addStrategy('cmv_6m', new ValueStrategy);
            $hydrator->addStrategy('cmv_12m', new ValueStrategy);
            $hydrator->addStrategy('cmv_acat', new ValueStrategy);
            $hydrator->addStrategy('cmv_acan', new ValueStrategy);
            
            $hydrator->addStrategy('lb_m0', new ValueStrategy);
            $hydrator->addStrategy('lb_m1', new ValueStrategy);
            $hydrator->addStrategy('lb_m2', new ValueStrategy);
            $hydrator->addStrategy('lb_m3', new ValueStrategy);
            $hydrator->addStrategy('lb_3m', new ValueStrategy);
            $hydrator->addStrategy('lb_6m', new ValueStrategy);
            $hydrator->addStrategy('lb_12m', new ValueStrategy);
            $hydrator->addStrategy('lb_acat', new ValueStrategy);
            $hydrator->addStrategy('lb_acan', new ValueStrategy);
            
            $hydrator->addStrategy('qtd_m0', new ValueStrategy);
            $hydrator->addStrategy('qtd_m1', new ValueStrategy);
            $hydrator->addStrategy('qtd_m2', new ValueStrategy);
            $hydrator->addStrategy('qtd_m3', new ValueStrategy);
            $hydrator->addStrategy('qtd_3m', new ValueStrategy);
            $hydrator->addStrategy('qtd_6m', new ValueStrategy);
            $hydrator->addStrategy('qtd_12m', new ValueStrategy);
            $hydrator->addStrategy('qtd_acat', new ValueStrategy);
            $hydrator->addStrategy('qtd_acan', new ValueStrategy);
            
            $hydrator->addStrategy('roldia_m0', new ValueStrategy);
            $hydrator->addStrategy('roldia_m1', new ValueStrategy);
            $hydrator->addStrategy('roldia_m2', new ValueStrategy);
            $hydrator->addStrategy('roldia_m3', new ValueStrategy);
            $hydrator->addStrategy('roldia_3m', new ValueStrategy);
            $hydrator->addStrategy('roldia_6m', new ValueStrategy);
            $hydrator->addStrategy('roldia_12m', new ValueStrategy);
            $hydrator->addStrategy('roldia_acat', new ValueStrategy);
            $hydrator->addStrategy('roldia_acan', new ValueStrategy);
            
            $hydrator->addStrategy('cmvdia_m0', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_m1', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_m2', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_m3', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_3m', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_6m', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_12m', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_acat', new ValueStrategy);
            $hydrator->addStrategy('cmvdia_acan', new ValueStrategy);
            
            $hydrator->addStrategy('lbdia_m0', new ValueStrategy);
            $hydrator->addStrategy('lbdia_m1', new ValueStrategy);
            $hydrator->addStrategy('lbdia_m2', new ValueStrategy);
            $hydrator->addStrategy('lbdia_m3', new ValueStrategy);
            $hydrator->addStrategy('lbdia_3m', new ValueStrategy);
            $hydrator->addStrategy('lbdia_6m', new ValueStrategy);
            $hydrator->addStrategy('lbdia_12m', new ValueStrategy);
            $hydrator->addStrategy('lbdia_acat', new ValueStrategy);
            $hydrator->addStrategy('lbdia_acan', new ValueStrategy);

            
            $hydrator->addStrategy('qtddia_m0', new ValueStrategy);
            $hydrator->addStrategy('qtddia_m1', new ValueStrategy);
            $hydrator->addStrategy('qtddia_m2', new ValueStrategy);
            $hydrator->addStrategy('qtddia_m3', new ValueStrategy);
            $hydrator->addStrategy('qtddia_3m', new ValueStrategy);
            $hydrator->addStrategy('qtddia_6m', new ValueStrategy);
            $hydrator->addStrategy('qtddia_12m', new ValueStrategy);
            $hydrator->addStrategy('qtddia_acat', new ValueStrategy);
            $hydrator->addStrategy('qtddia_acan', new ValueStrategy);

            $hydrator->addStrategy('var_rold_m0_m1', new ValueStrategy);
            $hydrator->addStrategy('var_lbd_m0_m1', new ValueStrategy);
            $hydrator->addStrategy('var_mbd_m0_m1', new ValueStrategy);
            $hydrator->addStrategy('var_rold_acat_acan', new ValueStrategy);
            $hydrator->addStrategy('var_lbd_acat_acan', new ValueStrategy);
            $stdClass = new StdClass;
            $resultSet = new HydratingResultSet($hydrator, $stdClass);
            $resultSet->initialize($results);

            $data = array();
            foreach ($resultSet as $row) {

                $l = $hydrator->extract($row);

                $l['varMbdM0M1'] = (float) $l['varMbdM0M1'];

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
