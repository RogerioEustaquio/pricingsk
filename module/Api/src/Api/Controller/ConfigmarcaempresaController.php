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

class ConfigmarcaempresaController extends AbstractRestfulController
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

            $sql = 'select distinct NVL(EMP,SUBSTR(NOME,0,9)) EMP, cod_empresa id_empresa from VW_SKEMPRESA ORDER BY EMP';

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

        $regionais[] = ['id'=> 'R1','idEmpresas'=> [9,2,29,23,25,24,13,19]];
        $regionais[] = ['id'=> 'R2','idEmpresas'=> [12,10,15,16,21,3,22,8]];
        $regionais[] = ['id'=> 'R3','idEmpresas'=> [6,4,5,17,18,14,7]];

        foreach($regionais as $row){

            if($row['id'] == $id){
                return $row['idEmpresas'];
            }
        }

        return null;
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

    public function listartabelaprecoAction()
    {
        $data = array();
        
        try {

            $pEmp    = $this->params()->fromQuery('emp',null);
            $pCod    = $this->params()->fromQuery('codTabPreco',null);
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

            $sql = "select distinct nvl(COD_TAB_PRECO,'') COD_TAB_PRECO, NOME_TAB_PRECO descricao
            from SK_PRODUTO_TABELA_TMP
            where 1 =1 
            and COD_TAB_PRECO $filtroProduto";

            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            // $stmt->bindValue(1, $pEmp);
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('cod_tab_preco', new ValueStrategy);
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

            $sql = 'select distinct COD_MARCA as ID_MARCA, DESCRICAO_MARCA MARCA from vw_skmarca ORDER BY DESCRICAO_MARCA';
            
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
    
    public function listargrupodescontoAction()
    {
        $data = array();
        
        try {

            // $idEmpresa      = $this->params()->fromQuery('idEmpresa',null);

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = "select distinct nvl(grupo_desconto,'') grupo_desconto
                from SK_PRODUTO_TABELA_TMP";

            $stmt = $conn->prepare($sql);
            // $stmt->bindParam(':idEmpresa', $idEmpresa);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('grupo_desconto', new ValueStrategy);
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


    public function listarconfigmarcaempresaAction(){

        $idEmpresas     = $this->params()->fromQuery('idEmpresas',null);
        $notMarca       = $this->params()->fromQuery('notMarca',null);
        $marcas         = $this->params()->fromQuery('idMarcas',null);
        $idProduto      = $this->params()->fromQuery('idProduto',null);

        $checkEstoque           = $this->params()->fromQuery('checkEstoque',null);

        $inicio     = $this->params()->fromQuery('start',null);
        $final      = $this->params()->fromQuery('limit',null);

        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        if($idEmpresas){
            $idEmpresas =  implode(",",json_decode($idEmpresas));
        }
        if($marcas){
            $marcas = implode("','",json_decode($marcas));
        }
        if($idProduto){
            $idProduto = implode(",",json_decode($idProduto));
        }

        $andSql = '';
        if($idEmpresas){
            $andSql = " and me.COD_EMPRESA in ($idEmpresas)";
        }
        if($marcas){
            $notMarca = !$notMarca? '': 'not';
            $andSql .= " and me.COD_MARCA $notMarca in ('$marcas')";
        }
        if($idProduto){
            $andSql .= " and nvl(COD_PRODUTO,'') in ($idProduto)";
        }
        
        switch ($checkEstoque) {
            case 'Com':
                $andSql .= " and nvl(ESTOQUE,0) > 0";
                break;
            case 'Sem':
                $andSql .= " and nvl(ESTOQUE,0) = 0";
                break;
            default:
               break;
        }

        $sql = " select me.COD_EMPRESA,
                        nvl(e.emp,substr(e.nome,0,9)) as NOME_EMPRESA,
                        me.COD_MARCA,
                        m.DESCRICAO_MARCA,
                        me.COD_PARCEIRO,
                        '' DESCRICAO_PARCEIRO,
                        me.META_MARGEM_MIX,
                        me.MARGEM_PADRAO,
                        me.MARGEM_FX_0,
                        me.MARGEM_FX_1_5,
                        me.MARGEM_FX_6_10,
                        me.MARGEM_FX_11_25,
                        me.MARGEM_FX_26_50,
                        me.MARGEM_FX_51_100,
                        me.MARGEM_FX_101_250,
                        me.MARGEM_FX_251_500,
                        me.MARGEM_FX_501_1000,
                        me.MARGEM_FX_1001_5000,
                        me.MARGEM_FX_5001_10000,
                        me.MARGEM_FX_10001_x
                    from VW_SKMARCA_EMPRESA me,
                         vw_skmarca m,
                         vw_skempresa e
                 where me.cod_marca = m.cod_marca
                 and me.cod_empresa = e.cod_empresa
                 $andSql
                 ";

        $session = $this->getSession();
        $session['exportconfigmarcaempresa'] = "$sql";

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
        $hydrator->addStrategy('meta_margem_mix', new ValueStrategy);
        $hydrator->addStrategy('margem_padrao', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_0', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_1_5', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_6_10', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_11_25', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_26_50', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_51_100', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_101_250', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_251_500', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_501_1000', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_1001_5000', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_5001_10000', new ValueStrategy);
        $hydrator->addStrategy('margem_fx_10001_x', new ValueStrategy);
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

            if($session['exportconfigmarcaempresa']){

                ini_set('memory_limit', '5120M' );

                $em = $this->getEntityManager();
                $conn = $em->getConnection();

                $sql = $session['exportconfigmarcaempresa'] ;
                
                $conn = $em->getConnection();
                $stmt = $conn->prepare($sql);
                
                $stmt->execute();
                $results = $stmt->fetchAll();

                $hydrator = new ObjectProperty;
                // $hydrator->addStrategy('estoque', new ValueStrategy);
                // $hydrator->addStrategy('custo_medio', new ValueStrategy);
                // $hydrator->addStrategy('valor', new ValueStrategy);
                // $hydrator->addStrategy('custo_operacao', new ValueStrategy);
                // $hydrator->addStrategy('pis', new ValueStrategy);
                // $hydrator->addStrategy('cofins', new ValueStrategy);
                // $hydrator->addStrategy('icms', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $data = array();
                
                $output = 'COD_EMPRESA;NOME_EMPRESA;COD_MARCA;DESCRICAO_MARCA;COD_PARCEIRO;DESCRICAO_PARCEIRO;'.
                'META_MARGEM_MIX;MARGEM_PADRAO;MARGEM_FX_0;MARGEM_FX_1_5;MARGEM_FX_6_10;MARGEM_FX_11_25;MARGEM_FX_26_50;MARGEM_FX_51_100;'.
                'MARGEM_FX_101_250;MARGEM_FX_251_500;MARGEM_FX_501_1000;MARGEM_FX_1001_5000;MARGEM_FX_5001_10000;MARGEM_FX_10001_x'
                ."\n";

                $i=0;
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $codEmpresa     = $data[$i]['codEmpresa'];
                    $nomeEmpresa    = $data[$i]['nomeEmpresa'];

                    $metaMargemMix      = $data[$i]['metaMargemMix'] >0 ? $data[$i]['metaMargemMix'] : null ;
                    $margemPadrao       = $data[$i]['margemPadrao'] >0 ? $data[$i]['margemPadrao'] : null ;
                    $margemFx_0         = $data[$i]['margemFx_0'] >0 ? $data[$i]['margemFx_0'] : null ;
                    $margemFx_1_5       = $data[$i]['margemFx_1_5'] >0 ? $data[$i]['margemFx_1_5'] : null ;
                    $margemFx_6_10      = $data[$i]['margemFx_6_10'] >0 ? $data[$i]['margemFx_6_10'] : null ;
                    $margemFx_11_25     = $data[$i]['margemFx_11_25'] >0 ? $data[$i]['margemFx_11_25'] : null ;
                    $margemFx_26_50     = $data[$i]['margemFx_26_50'] >0 ? $data[$i]['margemFx_26_50'] : null ;
                    $margemFx_51_100    = $data[$i]['margemFx_51_100'] >0 ? $data[$i]['margemFx_51_100'] : null ;
                    $margemFx_101_250   = $data[$i]['margemFx_101_250'] >0 ? $data[$i]['margemFx_101_250'] : null ;
                    $margemFx_251_500   = $data[$i]['margemFx_251_500'] >0 ? $data[$i]['margemFx_251_500'] : null ;
                    $margemFx_501_1000  = $data[$i]['margemFx_501_1000'] >0 ? $data[$i]['margemFx_501_1000'] : null ;
                    $margemFx_1001_5000 = $data[$i]['margemFx_1001_5000'] >0 ? $data[$i]['margemFx_1001_5000'] : null ;
                    $margemFx_5001_10000= $data[$i]['margemFx_5001_10000'] >0 ? $data[$i]['margemFx_5001_10000'] : null ;
                    $margemFx_10001X    = $data[$i]['margemFx_10001X'] >0 ? $data[$i]['margemFx_10001X'] : null ;
                    
                    $output  .= $codEmpresa.';'.
                                $nomeEmpresa.';'.
                                $data[$i]['codMarca'].';'.
                                $data[$i]['descricaoMarca'].';'.
                                $data[$i]['codParceiro'].';'.
                                $data[$i]['descricaoParceiro'].';'.
                                $metaMargemMix.';'.
                                $margemPadrao.';'.
                                $margemFx_0.';'.
                                $margemFx_1_5.';'.
                                $margemFx_6_10.';'.
                                $margemFx_11_25.';'.
                                $margemFx_26_50.';'.
                                $margemFx_51_100.';'.
                                $margemFx_101_250.';'.
                                $margemFx_251_500.';'.
                                $margemFx_501_1000.';'.
                                $margemFx_1001_5000.';'.
                                $margemFx_5001_10000.';'.
                                $margemFx_10001X.';'.
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
                        'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Config Marca Empresa.csv',
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
