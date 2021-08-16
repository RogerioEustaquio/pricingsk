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

class GrupodescontoController extends AbstractRestfulController
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
            
            $sql = "select distinct COD_ITEM_NBS cod_item, descricao from vw_skproduto
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

            // $sql = "select distinct nvl(COD_TAB_PRECO,'') COD_TAB_PRECO, NOME_TAB_PRECO descricao
            //     from SK_PRODUTO_TABELA_TMP
            // where 1 =1 
            // and COD_TAB_PRECO $filtroProduto";

            $sql = "select distinct COD_TABELA COD_TAB_PRECO, '' DESCRICAO 
              from vw_sktabela_preco
            where 1 =1 
            and COD_TABELA $filtroProduto";

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

            $sql = "select distinct COD_PRODUTO ID_PRODUTO, DESCRICAO
                     from vw_skproduto
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
    
    public function listargrupodescontosAction()
    {
        $data = array();
        
        try {

            // $idEmpresa      = $this->params()->fromQuery('idEmpresa',null);

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = "select distinct nvl(grupo_desconto,'') cod_grupo_desconto,
                        nvl(grupo_desconto,'') descricao
                from SK_PRODUTO_TABELA_TMP
            where grupo_desconto is not null
            order by 1";
            
            // $sql = " select distinct nvl(cod_grupo,'') cod_grupo_desconto,
            //             nvl(cod_grupo,'') descricao
            //     from vw_skalcada_desconto
            // where cod_grupo is not null
            // order by 1 ";

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

    public function listardescontomargemAction()
    {
        $data = array();
        
        try {

            $pNode = $this->params()->fromQuery('node',null);

            $sql = "select DISTINCT PERC_DESCONTO_MARGEM  DESCONTO_MARGEM from vw_skalcada_desconto";

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

    public function listarmaximoalcadaAction()
    {
        $data = array();
        
        try {

            $pNode = $this->params()->fromQuery('node',null);

            $sql = "select DISTINCT DESCONTO_MAXIMO_ALCADA MAXIMO_ALCADA from vw_skalcada_desconto";

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

    public function listargrupodescontoAction(){

        $idEmpresas     = $this->params()->fromQuery('idEmpresas',null);
        $notMarca       = $this->params()->fromQuery('notMarca',null);
        $marcas         = $this->params()->fromQuery('idMarcas',null);
        $idProduto      = $this->params()->fromQuery('idProduto',null);
        $tabelaPreco    = $this->params()->fromQuery('tabelaPreco',null);
        $grupoDesconto  = $this->params()->fromQuery('grupoDesconto',null);
        $descontoMargem = $this->params()->fromQuery('descontoMargem',null);
        $maximoAlcada   = $this->params()->fromQuery('maximoAlcada',null);

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
        if($tabelaPreco){
            $tabelaPreco = implode(",",json_decode($tabelaPreco));
        }
        if($grupoDesconto){
            $grupoDesconto = implode("','",json_decode($grupoDesconto));
        }
        if($descontoMargem){
            $descontoMargem = implode("','",json_decode($descontoMargem));
        }
        if($maximoAlcada){
            $maximoAlcada = implode("','",json_decode($maximoAlcada));
        }

        $andSql = '';
        // if($idEmpresas){
        //     $andSql = " and COD_EMPRESA in ($idEmpresas)";
        // }
        if($marcas){
            $notMarca = !$notMarca? '': 'not';
            $andSql .= " and MARCA $notMarca in ('$marcas')";
        }
        if($idProduto){
            $andSql .= " and nvl(COD_PRODUTO,'') in ($idProduto)";
        }
        if($tabelaPreco){
            $andSql .= " and nvl(COD_TABELA,'') in ($tabelaPreco)";
        }
        if($grupoDesconto){
            $andSql .= " and nvl(DESCRICAO_GRUPO,'') in ('$grupoDesconto')";
        }
        if($descontoMargem){
            $andSql .= " and nvl(PERC_DESCONTO_MARGEM,'') in ('$descontoMargem')";
        }
        if($maximoAlcada){
            $andSql .= " and nvl(DESCONTO_MAXIMO_ALCADA,'') in ('$maximoAlcada')";
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

        $sql = "select '' COD_EMPRESA,
                        '' NOME_EMPRESA,
                        COD_TABELA COD_TABELA_PRECO,
                        '' DESCRICAO_TABELA_PRECO,
                        COD_GRUPO COD_GRUPO_DESCONTO,
                        DESCRICAO_GRUPO DESCRICAO_GRUPO_DESCONTO,
                        AGRUPAMENTO_PRODUTO,
                        PERC_VENDEDOR,
                        PERC_COORDENADOR,
                        PERC_GERENTE,
                        PERC_GERENTE_REGIONAL,
                        PERC_DIRETOR,
                        PERC_DESCONTO_MARGEM,
                        DESCONTO_MAXIMO_ALCADA
                from vw_skalcada_desconto
                where 1 = 1
                $andSql
                ";

        $session = $this->getSession();
        $session['exportgrupodesconto'] = "$sql";

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
        $hydrator->addStrategy('perc_vendedor', new ValueStrategy);
        $hydrator->addStrategy('perc_coordenador', new ValueStrategy);
        $hydrator->addStrategy('perc_gerente', new ValueStrategy);
        $hydrator->addStrategy('perc_gerente_regional', new ValueStrategy);
        $hydrator->addStrategy('perc_diretor', new ValueStrategy);
        // $hydrator->addStrategy('p_desconto_margem', new ValueStrategy);
        // $hydrator->addStrategy('desconto_maximo_alcada', new ValueStrategy);
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

            if($session['exportgrupodesconto']){

                ini_set('memory_limit', '5120M' );

                $em = $this->getEntityManager();
                $conn = $em->getConnection();

                $sql = $session['exportgrupodesconto'] ;
                
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
                
                $output = 'COD_TABELA_PRECO;DESCRICAO_TABELA_PRECO;COD_GRUPO_DESCONTO;DESCRICAO_GRUPO_DESCONTO;AGRUPAMENTO_PRODUTO;'.
                'P_VENDEDOR;P_COORDENADOR;P_GERENTE;P_GERENTE_REGIONAL;P_DIRETOR;P_DESCONTO_MARGEM;DESCONTO_MAXIMO_ALCADA;'
                ."\n";

                $i=0;
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $pVendedor              = $data[$i]['pVendedor'] >0 ? $data[$i]['pVendedor'] : null ;
                    $pCoordenador           = $data[$i]['pCoordenador'] >0 ? $data[$i]['pCoordenador'] : null ;
                    $pGerente               = $data[$i]['pGerente'] >0 ? $data[$i]['pGerente'] : null ;
                    $pGerenteRegional       = $data[$i]['pGerenteRegional'] >0 ? $data[$i]['pGerenteRegional'] : null ;
                    $pDiretor               = $data[$i]['pDiretor'] >0 ? $data[$i]['pDiretor'] : null ;
                    $pDescontoMargem        = $data[$i]['pDescontoMargem'] >0 ? $data[$i]['pDescontoMargem'] : null ;
                    $descontoMaximoAlcada   = $data[$i]['descontoMaximoAlcada'] >0 ? $data[$i]['descontoMaximoAlcada'] : null ;
    
                    $output  .= $data[$i]['codTabelaPreco'].';'.
                                $data[$i]['descricaoTabelaPreco'].';'.
                                $data[$i]['codGrupoDesconto'].';'.
                                $data[$i]['descricaoGrupoDesconto'].';'.
                                $data[$i]['agrupamentoProduto'].';'.
                                $pVendedor.';'.
                                $pCoordenador.';'.
                                $pGerente.';'.
                                $pGerenteRegional.';'.
                                $pDiretor.';'.
                                $pDescontoMargem.';'.
                                $descontoMaximoAlcada.';'.
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
                        'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Grupo Desconto.csv',
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
