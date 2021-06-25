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

class ProdutoController extends AbstractRestfulController
{
    
    /**
     * Construct
     */
    public function __construct()
    {
        
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
            
            // $sql = "select i.cod_item||c.descricao as cod_item,
            //                i.descricao,
            //                m.descricao as marca
            //             from ms.tb_item_categoria ic,
            //             ms.tb_marca m,
            //             ms.tb_item i,
            //             ms.tb_categoria c
            //         where ic.id_item = i.id_item
            //         and ic.id_categoria = c.id_categoria
            //         and ic.id_marca = m.id_marca
            //         and i.cod_item||c.descricao $filtroProduto
            //         order by cod_item asc";
            
            $sql = "select distinct COD_ITEM_NBS cod_item, descricao 
            from SK_PRODUTO_TABELA_TMP
            where 1 =1 
            and COD_ITEM_NBS $filtroProduto";

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
                $filtroProduto = "like upper('".$pCod."%')";
            }else{
                $produtos =  implode(",",json_decode($pCod));
                $filtroProduto = "in ($produtos)";
            }

            $sql = "select distinct nvl(COD_PRODUTO,'') ID_PRODUTO, DESCRICAO
            from SK_PRODUTO_TABELA_TMP
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

            $sql = 'select distinct marca as id_marca, marca 
            from SK_PRODUTO_TABELA_TMP order by marca';
            
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

    public function listarprodutoAction(){

        $dataInicio     = $this->params()->fromQuery('dataInicio',null);
        $dataFinal      = $this->params()->fromQuery('dataFinal',null);
        $notMarca       = $this->params()->fromQuery('notMarca',null);
        $marcas         = $this->params()->fromQuery('idMarcas',null);
        $produtos       = $this->params()->fromQuery('produtos',null);
        $idProduto      = $this->params()->fromQuery('idProduto',null);

        $inicio     = $this->params()->fromQuery('start',null);
        $final      = $this->params()->fromQuery('limit',null);

        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        if($marcas){
            $marcas = implode("','",json_decode($marcas));
        }
        if($produtos){
            $produtos = implode("','",json_decode($produtos));
        }
        if($idProduto){
            $idProduto = implode(",",json_decode($idProduto));
        }

        $andSql = '';

        if($marcas){
            $notMarca = !$notMarca? '': 'not';
            $andSql .= " and MARCA $notMarca in ('$marcas')";
        }
        if($produtos){
            $andSql .= " and COD_ITEM_NBS in ('$produtos')";
        }
        if($idProduto){
            $andSql .= " and nvl(COD_PRODUTO,'') in ($idProduto)";
        }

        if($dataInicio){
            $andSql .= " and DT_VIGOR >= '$dataInicio'";
        }
        if($dataFinal){
            $andSql .= " and DT_VIGOR <= '$dataFinal'";
        }

        $sql = " select distinct COD_PRODUTO,
                        DESCRICAO,
                        DESCRICAO DESCRICAO_JS,
                        'YES' ATIVO,
                        COD_ITEM_NBS,
                        COD_TAB_PRECO COD_GRUPO,
                        COD_TAB_PRECO DESCRICAO_GRUPO,
                        PARTNUMBER,
                        DT_VIGOR DT_CADASTRO,
                        MARCA COD_MARCA,
                        MARCA DESCRICAO_MARCA,
                        GRUPO_DESCONTO USADO_COMO                 
                    from SK_PRODUTO_TABELA_TMP 
                 where 1 = 1
                 $andSql ";

        $session = $this->getSession();
        $session['exportproduto'] = "$sql";

        $this->setSession($session);

        $sql1 = "select count(*) as totalCount from ($sql)";
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
        // $hydrator->addStrategy('preco', new ValueStrategy);
        $stdClass = new StdClass;
        $resultSet = new HydratingResultSet($hydrator, $stdClass);
        $resultSet->initialize($results);

        $data = array();
        foreach ($resultSet as $row) {
            $data[] = $hydrator->extract($row);
        }

        $this->setCallbackData($data);

        $objReturn = $this->getCallbackModel();

        $objReturn->total = $resultCount[0]['TOTALCOUNT'];

        return $objReturn;
    }

    public function gerarexcelAction()
    {
        $data = array();
        
        try {

            $session = $this->getSession();

            if($session['exportproduto']){

                ini_set('memory_limit', '5120M' );

                $em = $this->getEntityManager();
                $conn = $em->getConnection();

                $sql = $session['exportproduto'] ;
                
                $conn = $em->getConnection();
                $stmt = $conn->prepare($sql);
                
                $stmt->execute();
                $results = $stmt->fetchAll();

                $hydrator = new ObjectProperty;
                // $hydrator->addStrategy('preco', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $data = array();
                
                $output = 'COD_PRODUTO;DESCRICAO;DESCRICAO_JS;ATIVO;COD_ITEM_NBS;COD_GRUPO;PARTNUMBER;DT_CADASTRO;COD_MARCA;USADO_COMO'."\n";

                $i=0;
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $output  .= $data[$i]['codProduto'].';'.
                                $data[$i]['descricao'].';'.
                                $data[$i]['descricaoJs'].';'.
                                $data[$i]['ativo'].';'.
                                $data[$i]['codItemNbs'].';'.
                                $data[$i]['codGrupo'].';'.
                                $data[$i]['partnumber'].';'.
                                $data[$i]['dtCadastro'].';'.
                                $data[$i]['codMarca'].';'.
                                $data[$i]['usadoComo'].';'.
                                "\n";
                    $i++;
                }

                $response = new \Zend\Http\Response();
                $response->setContent($output);
                $response->setStatusCode(200);

                $headers =[
                        'Pragma' => 'public',
                        'Cache-control' => 'must-revalidate, post-check=0, pre-check=0',
                        'Cache-control' => 'private',
                        'Expires' => '0000-00-00',
                        'Content-Type' => 'application/CSV; charset=utf-8',
                        'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Produto.csv',
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
}
