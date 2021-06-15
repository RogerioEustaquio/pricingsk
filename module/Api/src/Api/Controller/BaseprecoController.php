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

class BaseprecoController extends AbstractRestfulController
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

            $sql = 'select distinct nome_empresa emp, cod_empresa id_empresa
                    from SK_PRODUTO_TABELA_TMP';

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
            
            $sql = 'select distinct cod_produto cod_item, descricao 
            from SK_PRODUTO_TABELA_TMP';

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
            from SK_PRODUTO_TABELA_TMP';
            
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


    public function listarprecoAction(){

        $idEmpresas     = $this->params()->fromQuery('idEmpresas',null);
        $notMarca       = $this->params()->fromQuery('notMarca',null);
        $marcas         = $this->params()->fromQuery('idMarcas',null);
        $produtos       = $this->params()->fromQuery('produtos',null);
        $grupoDesconto  = $this->params()->fromQuery('grupoDesconto',null);

        $checkEstoque           = $this->params()->fromQuery('checkEstoque',null);
        $checkpreco             = $this->params()->fromQuery('checkpreco',null);
        $checkmargem            = $this->params()->fromQuery('checkmargem',null);
        $checktipoprecificacao  = $this->params()->fromQuery('checktipoprecificacao',null);
        $checkgrupodesconto     = $this->params()->fromQuery('checkgrupodesconto',null);
        $checktabelapreco       = $this->params()->fromQuery('checktabelapreco',null);

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
        if($produtos){
            $produtos = implode("','",json_decode($produtos));
        }
        if($grupoDesconto){
            $grupoDesconto = implode("','",json_decode($grupoDesconto));
        }

        $andSql = '';
        if($idEmpresas){
            $andSql = " and COD_EMPRESA in ($idEmpresas)";
        }
        if($marcas){
            $notMarca = !$notMarca? '': 'not';
            $andSql .= " and MARCA $notMarca in ('$marcas')";
        }
        if($produtos){
            $andSql .= " and COD_ITEM_NBS in ('$produtos')";
        }
        if($grupoDesconto){
            $andSql .= " and GRUPO_DESCONTO in ('$grupoDesconto')";
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
        switch ($checkpreco) {
            case 'Com':
                $andSql .= " and nvl(PRECO,0) > 0";
                break;
            case 'Sem':
                $andSql .= " and nvl(PRECO,0) = 0";
                break;
        }
        switch ($checkmargem) {
            case 'Com':
                $andSql .= " and nvl(MARGEM,0) > 0";
                break;
            case 'Sem':
                $andSql .= " and nvl(MARGEM,0) = 0";
                break;
        }
        switch ($checktipoprecificacao) {
            case 'Com':
                $andSql .= " and trim(TIPO_PRECIFICACAO) is not null";
                break;
            case 'Sem':
                $andSql .= " and trim(TIPO_PRECIFICACAO) is null";
                break;
        }
        switch ($checkgrupodesconto) {
            case 'Com':
                $andSql .= " and trim(GRUPO_DESCONTO) is not null";
                break;
            case 'Sem':
                $andSql .= " and trim(GRUPO_DESCONTO) is null";
                break;
        }
        switch ($checktabelapreco) {
            case 'Com':
                $andSql .= " and trim(NOME_TAB_PRECO) is not null";
                break;
            case 'Sem':
                $andSql .= " and trim(NOME_TAB_PRECO) is null";
                break;
        }

        $sql = " select COD_EMPRESA,
                        NOME_EMPRESA,
                        COD_TAB_PRECO,
                        NOME_TAB_PRECO,
                        to_char(DT_VIGOR,'dd/mm/yyyy') dt_vigor,
                        PRECO,
                        TIPO,
                        COD_PRODUTO,
                        DESCRICAO,
                        MARCA,
                        COD_FORNECEDOR,
                        NOME_FORNECEDOR,
                        COD_ITEM_NBS,
                        PARTNUMBER,
                        MARGEM mb,
                        DESP_VARIAVEL,
                        TIPO_PRECIFICACAO,
                        NIVEL_MARGEM,
                        GRUPO_DESCONTO,
                        ESTOQUE,
                        CUSTO_MEDIO,
                        VALOR_ESTOQUE,
                        CUSTO_OPE,
                        PIS_COFINS,
                        ICMS                       
                    from SK_PRODUTO_TABELA_TMP 
                 where 1 = 1
                 $andSql
                 ";

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
        $hydrator->addStrategy('preco', new ValueStrategy);
        $hydrator->addStrategy('estoque', new ValueStrategy);
        $hydrator->addStrategy('mb', new ValueStrategy);
        $hydrator->addStrategy('custo_medio', new ValueStrategy);
        $hydrator->addStrategy('valor_estoque', new ValueStrategy);
        $hydrator->addStrategy('custo_ope', new ValueStrategy);
        $hydrator->addStrategy('pis_cofins', new ValueStrategy);
        $hydrator->addStrategy('icms', new ValueStrategy);
        $stdClass = new StdClass;
        $resultSet = new HydratingResultSet($hydrator, $stdClass);
        $resultSet->initialize($results);

        $data = array();
        foreach ($resultSet as $row) {
            $data[] = $hydrator->extract($row);
        }

        // $data[] = [ 'idEmpresa'=> 2,
        //             'filial'=> 'M1',
        //             'idTabPreco'=> 2001,
        //             'nmTabPreco'=> 'MARITUBA-PA-BASE',
        //             'dtVigor'=> '26/05/2021',
        //             'preco'=> 46.78,
        //             'idProduto'=> 43425,
        //             'nmProduto'=> 'ABAFADOR RUIDOS 0043425',
        //             'marca'=> 'CUMMINS',
        //             'codItem'=> '2T2103487.0',
        //             'mb'=> 42.36,
        //             'tpPrecificacao'=> 'Margem x Custo Médio',
        //             'grupoDesc'=> 'MWM1',
        //             'estoque'=> 9,
        //             'custoMedioUnit'=> 26.9622,
        //             'vlEstoque'=> 242.6598,
        //             'custo'=> 26.9622,
        //             'pisCofins'=> 9.25,
        //             'icms'=> 17,
        // ];

        $this->setCallbackData($data);

        $objReturn = $this->getCallbackModel();

        $objReturn->total = $resultCount[0]['TOTALCOUNT'];

        return $objReturn;
    }
}
