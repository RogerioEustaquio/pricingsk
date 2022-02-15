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

    public function replaceAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }

    public function listardescricaoprodutosAction()
    {
        $data = array();
        
        try {

            $pEmp    = $this->params()->fromQuery('emp',null);
            $pCod    = $this->params()->fromQuery('descricao',null);
            $tipoSql = $this->params()->fromQuery('tipoSql',null);

            if(!$pCod){
                throw new \Exception('Parâmetros não informados.');
            }

            $pCod = $this->replaceAcentos($pCod);

            $em = $this->getEntityManager();

            if(!$tipoSql){
                $filtroProduto = "like upper('".$pCod."%')";
            }else{
                $produtos =  implode("','",json_decode($pCod));
                $filtroProduto = "in ('".$produtos."')";
            }
            
            $sql = "select distinct nvl(COD_PRODUTO,'') ID_PRODUTO, descricao 
            from SK_PRODUTO_TABELA_TMP
            where 1 =1 
            and descricao $filtroProduto";

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
                $filtroProduto = "in (".$pCod.")";
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

    public function listartipoprecificacaoAction()
    {
        $data = array();
        
        try {
            $session = $this->getSession();
            $usuario = $session['info']['usuarioSistema'];

            // $idEmpresa      = $this->params()->fromQuery('idEmpresa',null);

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = "select distinct tipo_precificacao from tmp_skbase_preco_teste";

            $stmt = $conn->prepare($sql);
            // $stmt->bindParam(':idEmpresa', $idEmpresa);
            $stmt->execute();
            $results = $stmt->fetchAll();

            $hydrator = new ObjectProperty;
            $hydrator->addStrategy('tipo_precificacao', new ValueStrategy);
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

    
    public function listarfaixacustoAction()
    {
        $data = array();

        $emp = $this->params()->fromQuery('emp',null);

        try {

            $session = $this->getSession();
            $usuario = $session['info'];

            // $em = $this->getEntityManager();

            // $sql = 'select distinct marca as id_marca, marca 
            // from SK_PRODUTO_TABELA_TMP order by marca';
            
            // $conn = $em->getConnection();
            // $stmt = $conn->prepare($sql);
            
            // $stmt->execute();
            // $results = $stmt->fetchAll();

            // $hydrator = new ObjectProperty;
            // $stdClass = new StdClass;
            // $resultSet = new HydratingResultSet($hydrator, $stdClass);
            // $resultSet->initialize($results);

            // $data = array();
            // foreach ($resultSet as $row) {
            //     $data[] = $hydrator->extract($row);
            // }

            $data[] = ['fxCusto'=> '10001-X'];
            $data[] = ['fxCusto'=> '5001-10000'];
            $data[] = ['fxCusto'=> '1001-5000'];
            $data[] = ['fxCusto'=> '501-1000'];
            $data[] = ['fxCusto'=> '251-500'];
            $data[] = ['fxCusto'=> '101-250'];
            $data[] = ['fxCusto'=> '51-100'];
            $data[] = ['fxCusto'=> '26-50'];
            $data[] = ['fxCusto'=> '11-25'];
            $data[] = ['fxCusto'=> '6-10'];
            $data[] = ['fxCusto'=> '1-5'];
            $data[] = ['fxCusto'=> '0'];

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

            $sql = "select distinct grupo_desconto
                         from VW_SKPRODUTO_DESCONTO_GRUPO
                    order by grupo_desconto";

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
        $codTabPreco    = $this->params()->fromQuery('codTabPreco',null);
        $idProduto      = $this->params()->fromQuery('idProduto',null);
        $grupoDesconto  = $this->params()->fromQuery('grupoDesconto',null);

        $checkEstoque           = $this->params()->fromQuery('checkEstoque',null);
        $checkpreco             = $this->params()->fromQuery('checkpreco',null);
        $checkmargem            = $this->params()->fromQuery('checkmargem',null);
        $checktipoprecificacao  = $this->params()->fromQuery('checktipoprecificacao',null);
        $checkgrupodesconto     = $this->params()->fromQuery('checkgrupodesconto',null);
        $checktabelapreco       = $this->params()->fromQuery('checktabelapreco',null);
        $checkcustounitario     = $this->params()->fromQuery('checkcustounitario',null);

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
        if($codTabPreco){
            $codTabPreco = implode(",",json_decode($codTabPreco));
        }
        if($idProduto){
            $idProduto = implode(",",json_decode($idProduto));
        }
        if($grupoDesconto){
            $grupoDesconto = implode("','",json_decode($grupoDesconto));
        }

        $andSql = '';
        $andSqlEmp = "";
        if($idEmpresas){
            $andSqlEmp = " and es.cod_empresa in ($idEmpresas)";
        }
        $andSqlMarca = "";
        if($marcas){
            $notMarca = !$notMarca? '': 'not';
            $andSqlMarca = " and descricao_marca $notMarca in ('$marcas')";
        }
        $andSqlProduto ='';
        if($produtos){
            $andSqlProduto = " and cod_nbs in ('$produtos')";
        }
        if($idProduto){
            $andSqlProduto .= " and nvl(es.cod_produto,'') in ($idProduto)";
        }
        $andSqlPreco = "";
        if($codTabPreco){
            $andSqlPreco = " and nvl(bs.cod_tabela,'') in ($codTabPreco)";
        }
        $andSqlGrupo = "";
        if($grupoDesconto){
            $andSqlGrupo = " and pd.grupo_desconto in ('$grupoDesconto')";
        }
        $andSqlCkEstoque = "";
        switch ($checkEstoque) {
            case 'Com':
                $andSqlCkEstoque = " and nvl(es.ESTOQUE,0) > 0";
                break;
            case 'Sem':
                $andSqlCkEstoque = " and nvl(es.ESTOQUE,0) = 0";
                break;
            default:
               break;
        }
        $andSqlCkPreco = "";
        switch ($checkpreco) {
            case 'Com':
                $andSqlCkPreco = " and nvl(PRECO,0) > 0";
                break;
            case 'Sem':
                $andSqlCkPreco = " and nvl(PRECO,0) = 0";
                break;
        }
        $andSqlCkTbPreco = "";
        switch ($checktabelapreco) {
            case 'Com':
                $andSqlCkTbPreco = " and trim(bs.cod_tabela) is not null";
                break;
            case 'Sem':
                $andSqlCkTbPreco = " and trim(bs.cod_tabela) is null";
                break;
        }
        $andSqlCkMb = "";
        switch ($checkmargem) {
            case 'Com':
                $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) > 0";
                break;
            case 'Sem':
                $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) = 0";
                break;
            case '>10':
                $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) > 10";
                break;
            case '>5':
                $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) > 5";
                break;
        }
        // switch ($checktipoprecificacao) {
        //     case 'Com':
        //         $andSql .= " and trim(TIPO_PRECIFICACAO) is not null";
        //         break;
        //     case 'Sem':
        //         $andSql .= " and trim(TIPO_PRECIFICACAO) is null";
        //         break;
        // }
        $andSqlCkGdesc = "";
        switch ($checkgrupodesconto) {
            case 'Com':
                $andSqlCkGdesc = " and trim(es.grupo_desconto) is not null";
                break;
            case 'Sem':
                $andSqlCkGdesc = " and trim(es.grupo_desconto) is null";
                break;
        }
        $andSqlCkUnitario = "";
        switch ($checkcustounitario) {
            case 'Com':
                $andSqlCkUnitario = " and trim(custo_medio) is not null";
                break;
            case 'Sem':
                $andSqlCkUnitario = " and trim(custo_medio) is null";
                break;
            case 'c<p':
                $andSqlCkUnitario = " and nvl(custo_medio,0) < nvl(PRECO,0)";
                break;
        }

        $sql = " select cod_empresa,
                        empresa nome_empresa,
                        cod_tabela cod_tab_preco,
                        trim(nome_tabela_preco) nome_tab_preco,
                        '' dt_vigor,
                        preco,
                        '' tipo,
                        cod_produto,
                        descricao,
                        marca,
                        '' cod_fornecedor,
                        '' nome_fornecedor,
                        cod_nbs COD_ITEM_NBS,
                        '' PARTNUMBER,
                        round((preco_liq - custo_medio) / preco_liq *100,2) mb,
                        '' DESP_VARIAVEL,
                        '' TIPO_PRECIFICACAO,
                        '' NIVEL_MARGEM,
                        grupo_desconto,
                        estoque,
                        custo_medio,
                        custo_medio custo_ope,
                        '' valor_estoque,
                        '' custo_oper,
                        pis + cofins PIS_COFINS,
                        icms,
                        fx_custo,
                        param_margem_minima,
                        --custo_medio,
                        valor,
                        pis,
                        cofins,
                        --icms,
                        --grupo_desconto,
                        perc_vendedor,
                        --round((preco_liq - custo_medio) / preco_liq *100,2) margem,
                        --preco,
                        preco_min,
                        preco_liq,
                        round(((custo_medio/(1-(param_margem_minima/100)))/(1-((nvl(pis,0)+nvl(cofins,0)+nvl(icms,0))/100)))/(1-(perc_vendedor/100)), 2) as preco_ideal
                        
                    from (
                        select a.cod_empresa,
                               a.empresa,
                               a.cod_tabela,
                               pv.nome nome_tabela_preco,
                               a.cod_produto,
                               a.descricao,
                               a.marca,
                               a.cod_nbs, 
                               a.estoque,
                               get_fx_custo(a.custo_medio) as fx_custo,
                               get_fx_custo_mb(a.custo_medio) as param_margem_minima, 
                               a.custo_medio, a.valor, a.pis, a.cofins, a.icms,
                               a.grupo_desconto, ad.perc_vendedor, 
                               pv.preco, 
                               pv.preco*(1-(ad.perc_vendedor/100)) as preco_min, -- Preço vendedor desconto
                               (pv.preco*(1-(ad.perc_vendedor/100)))*(1-((nvl(a.pis,0)+nvl(a.cofins,0)+nvl(a.icms,0))/100)) as preco_liq -- Preço final sem impostos
                            from (select es.cod_empresa,
                                         es.empresa,
                                         bs.cod_tabela,
                                         es.cod_produto,
                                         es.descricao,
                                         es.marca,
                                         es.cod_nbs,
                                         es.estoque,
                                         es.custo_medio,
                                         es.valor,
                                         es.pis,
                                         es.cofins,
                                         es.icms,
                                         es.grupo_desconto         
                                    from (select es.cod_empresa,
                                                 e.emp as empresa,
                                                 es.cod_produto,
                                                 p.descricao,
                                                 m.descricao_marca as marca,
                                                 p.partnumber,
                                                 p.cod_nbs as cod_nbs,
                                                 es.estoque,
                                                 es.custo_medio,
                                                 round(es.estoque*es.custo_medio,2) as valor, 
                                                 pi.pis,
                                                 pi.cofins,
                                                 pi.icms,
                                                 nvl(pd.grupo_desconto,'GERAL') as grupo_desconto
                                            from vw_skestoque_base es,
                                                 vw_skempresa e,
                                                 vw_skproduto p,
                                                 vw_skmarca m,
                                                 vw_skproduto_desconto_grupo pd,
                                                 vw_skproduto_imposto pi
                                            where es.cod_empresa = e.cod_empresa
                                            $andSqlEmp
                                            $andSqlMarca
                                            $andSqlProduto
                                            $andSqlGrupo
                                            $andSqlCkEstoque
                                            and es.cod_produto = p.cod_produto
                                            and p.cod_marca = m.cod_marca
                                            and es.cod_empresa = pd.cod_empresa(+)
                                            and es.cod_produto = pd.cod_produto(+)
                                            and es.cod_empresa = pi.cod_empresa
                                            and es.cod_produto = pi.cod_produto
                                    ) es, 
                                    vw_sktabela_config_base bs
                            where es.cod_empresa = bs.cod_empresa(+)
                            $andSqlPreco
                            $andSqlCkTbPreco
                            $andSqlCkGdesc) a,
                            vw_skalcada_desconto ad,
                            vw_sktabela_preco pv
                        where a.cod_tabela = ad.cod_tabela(+)
                        and a.grupo_desconto = ad.agrupamento_produto(+)
                        and a.cod_tabela = pv.cod_tabela(+)
                        and a.cod_produto = pv.cod_produto(+)
                        $andSqlCkPreco
                        $andSqlCkUnitario
                        and a.marca not in ('MWM','MWM IESA','MWM OPCIONAL','CASCO BATERIA','CASCO EATON CX.CAMBIO','CASCO OUTROS','CASCO EATON EMB.', 'EMERGENCIAL')
                        and a.cod_empresa not in (28/*LE*/ ,25/*CD*/ ,27 /*TL*/)
                        --and a.cod_empresa = 5
                        order by a.cod_empresa, a.cod_tabela, grupo_desconto
                    )
                where 1=1
                and estoque > 0
                and nvl(custo_medio,0) > 0
                $andSqlCkMb
                --and preco is null
                --and marca not in ('BATERIAS MOURA','BATERIAS','MOURA-TROCA','ZETTA','ZETTA-TROCA')
                --and cod_produto not in (397)
                --and cod_produto = 19887
                --and marca in ('BATERIAS MOURA','BATERIAS','MOURA-TROCA','ZETTA','ZETTA-TROCA')
                --and cod_empresa = 24
                --and marca in('CUMMINS-REMAN')
                --and cod_empresa = 12
                --and empresa = 'BH'
                --and cod_produto in (61958, 61918, 61955, 61957, 16461, 60752)
                --and cod_produto = 397
                --and cod_produto = 58938
                --and cod_produto in(7717,38808,40942,7719,49732,19887,50561,40805,40940,38807)
          ";

        $session = $this->getSession();
        $session['exportbasepreco'] = "$sql";
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

            if($session['exportbasepreco']){

                ini_set('memory_limit', '5120M' );

                $em = $this->getEntityManager();
                $conn = $em->getConnection();

                $sql = $session['exportbasepreco'] ;
                
                $conn = $em->getConnection();
                $stmt = $conn->prepare($sql);
                
                $stmt->execute();
                $results = $stmt->fetchAll();

                $hydrator = new ObjectProperty;
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $data = array();
                
                $output = 'COD_EMPRESA;NOME_EMPRESA;COD_TAB_PRECO;NOME_TAB_PRECO;DT_VIGOR;PRECO'.
                                 ';TIPO;COD_PRODUTO;DESCRICAO;MARCA;COD_FORNECEDOR;NOME_FORNECEDOR;COD_ITEM_NBS'.
                                 ';PARTNUMBER;MARGEM;DESP_VARIAVEL;TIPO_PRECIFICACAO;NIVEL_MARGEM;GRUPO_DESCONTO'.
                                 ';ESTOQUE;CUSTO_MEDIO;VALOR_ESTOQUE;CUSTO_OPE;PIS_COFINS;ICMS'."\n";

                $i=0;
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $codEmpresa     = $data[$i]['codEmpresa'];
                    $nomeEmpresa    = $data[$i]['nomeEmpresa'];
                    $codTabPreco    = $data[$i]['codTabPreco'];

                    $preco          = $data[$i]['preco'] >0 ? $data[$i]['preco'] : null ;
                    $mb             = $data[$i]['mb'] >0 ? $data[$i]['mb'] : null ;
                    $despVariavel   = $data[$i]['despVariavel'] >0 ? $data[$i]['despVariavel'] : null ;
                    $custoMedio     = $data[$i]['custoMedio'] >0 ? $data[$i]['custoMedio'] : null ;
                    $valorEstoque   = $data[$i]['valorEstoque'] >0 ? $data[$i]['valorEstoque'] : null ;
                    $custoOpe       = $data[$i]['custoOpe'] >0 ? $data[$i]['custoOpe'] : null ;
                    $pisCofins      = $data[$i]['pisCofins'] >0 ? $data[$i]['pisCofins'] : null ;
                    $icms           = $data[$i]['icms'] >0 ? $data[$i]['icms'] : null ;

                    $output  .= $codEmpresa.';'.
                                $nomeEmpresa.';'.
                                $codTabPreco.';'.
                                $data[$i]['nomeTabPreco'].';'.
                                $data[$i]['dtVigor'].';'.
                                $preco.';'.
                                $data[$i]['tipo'].';'.
                                $data[$i]['codProduto'].';'.
                                $data[$i]['descricao'].';'.
                                $data[$i]['marca'].';'.
                                $data[$i]['codFornecedor'].';'.
                                $data[$i]['nomeFornecedor'].';'.
                                $data[$i]['codItemNbs'].';'.
                                (string) $data[$i]['partnumber'].';'.
                                $mb.';'.
                                $despVariavel.';'.
                                $data[$i]['tipoPrecificacao'].';'.
                                $data[$i]['nivelMargem'].';'.
                                $data[$i]['grupoDesconto'].';'.
                                $data[$i]['estoque'].';'.
                                $custoMedio.';'.
                                $valorEstoque.';'.
                                $custoOpe.';'.
                                $pisCofins.';'.
                                $icms."\n";
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
                        'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Base Preço.csv',
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

            $sql = $session['exportbasepreco'] ;
            
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
            $arqFile = '.\data\exportbasepreco_'.$session['info']['usuarioSistema'].'1.xlsx';
            fopen($arqFile,'w'); // Paramentro $phpExcel somente retorno

            $phpExcel = $excelService->createPHPExcelObject($arqFile);
            $phpExcel->getActiveSheet()->setCellValue('A'.'1', 'COD_EMPRESA')
                                       ->setCellValue('B'.'1', 'NOME_EMPRESA')
                                       ->setCellValue('C'.'1', 'COD_TAB_PRECO')
                                       ->setCellValue('D'.'1', 'NOME_TAB_PRECO')
                                       ->setCellValue('E'.'1', 'DT_VIGOR')
                                       ->setCellValue('F'.'1', 'PRECO')
                                       ->setCellValue('G'.'1', 'TIPO')
                                       ->setCellValue('H'.'1', 'COD_PRODUTO')
                                       ->setCellValue('I'.'1', 'DESCRICAO')
                                       ->setCellValue('J'.'1', 'MARCA')
                                       ->setCellValue('K'.'1', 'COD_FORNECEDOR')
                                       ->setCellValue('L'.'1', 'NOME_FORNECEDOR')
                                       ->setCellValue('M'.'1', 'COD_ITEM_NBS')
                                       ->setCellValue('N'.'1', 'PARTNUMBER')
                                       ->setCellValue('O'.'1', 'MARGEM')
                                       ->setCellValue('P'.'1', 'DESP_VARIAVEL')
                                       ->setCellValue('Q'.'1', 'TIPO_PRECIFICACAO')
                                       ->setCellValue('R'.'1', 'NIVEL_MARGEM')
                                       ->setCellValue('S'.'1', 'GRUPO_DESCONTO')
                                       ->setCellValue('T'.'1', 'ESTOQUE')
                                       ->setCellValue('U'.'1', 'CUSTO_MEDIO')
                                       ->setCellValue('V'.'1', 'VALOR_ESTOQUE')
                                       ->setCellValue('W'.'1', 'CUSTO_OPE')
                                       ->setCellValue('X'.'1', 'PIS_COFINS')
                                       ->setCellValue('Y'.'1', 'ICMS');

                $i=0;
                $ix=2;
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $codEmpresa     = $data[$i]['codEmpresa'];
                    $nomeEmpresa    = $data[$i]['nomeEmpresa'];
                    $codTabPreco    = $data[$i]['codTabPreco'];

                    $preco          = $data[$i]['preco'] >0 ? $data[$i]['preco'] : null ;
                    $mb             = $data[$i]['mb'] >0 ? $data[$i]['mb'] : null ;
                    $despVariavel   = $data[$i]['despVariavel'] >0 ? $data[$i]['despVariavel'] : null ;
                    $custoMedio     = $data[$i]['custoMedio'] >0 ? $data[$i]['custoMedio'] : null ;
                    $valorEstoque   = $data[$i]['valorEstoque'] >0 ? $data[$i]['valorEstoque'] : null ;
                    $custoOpe       = $data[$i]['custoOpe'] >0 ? $data[$i]['custoOpe'] : null ;
                    $pisCofins      = $data[$i]['pisCofins'] >0 ? $data[$i]['pisCofins'] : null ;
                    $icms           = $data[$i]['icms'] >0 ? $data[$i]['icms'] : null ;

                    $phpExcel->getActiveSheet()->setCellValue('A'.$ix, $codEmpresa)
                                           ->setCellValue('B'.$ix, $nomeEmpresa)
                                           ->setCellValue('C'.$ix, $codTabPreco)
                                           ->setCellValue('D'.$ix, $data[$i]['nomeTabPreco'])
                                           ->setCellValue('E'.$ix, $data[$i]['dtVigor'])
                                           ->setCellValue('F'.$ix, $preco)
                                           ->setCellValue('G'.$ix, $data[$i]['tipo'])
                                           ->setCellValue('H'.$ix, $data[$i]['codProduto'])
                                           ->setCellValue('I'.$ix, $data[$i]['descricao'])
                                           ->setCellValue('J'.$ix, $data[$i]['marca'])
                                           ->setCellValue('K'.$ix, $data[$i]['codFornecedor'])
                                           ->setCellValue('L'.$ix, $data[$i]['nomeFornecedor'])
                                           ->setCellValue('M'.$ix, $data[$i]['codItemNbs'])
                                           ->setCellValue('N'.$ix,  $data[$i]['partnumber'])
                                           ->setCellValue('O'.$ix, $mb)
                                           ->setCellValue('P'.$ix, $despVariavel)
                                           ->setCellValue('Q'.$ix, $data[$i]['tipoPrecificacao'])
                                           ->setCellValue('R'.$ix, $data[$i]['nivelMargem'])
                                           ->setCellValue('S'.$ix, $data[$i]['grupoDesconto'])
                                           ->setCellValue('T'.$ix, $data[$i]['estoque'])
                                           ->setCellValue('U'.$ix, $custoMedio)
                                           ->setCellValue('V'.$ix, $valorEstoque)
                                           ->setCellValue('W'.$ix, $custoOpe)
                                           ->setCellValue('X'.$ix, $pisCofins)
                                           ->setCellValue('Y'.$ix, $icms);
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
                'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Base Preço.xls',
            ]);

            return $response;

        } catch (\Exception $e) {
            $this->setCallbackError($e->getMessage());
        }

        $this->setCallbackData($data);
        $this->setMessage("Solicitação enviada com sucesso.");
        return $this->getCallbackModel();
        
    }

    public function camposgerapreco(){

        $campos['codEmpresa'] ='vx.cod_empresa ';
        $campos['empresa'] ='vx.empresa ';
        $campos['codTabela'] ='vx.cod_tabela ';
        $campos['codProduto'] ='vx.cod_produto ';
        $campos['descricao'] ='vx.descricao ';
        $campos['marca'] ='vx.marca ';
        $campos['codNbs'] ='vx.cod_nbs ';
        $campos['estoque'] ='vx.estoque';
        $campos['fxCusto'] ='vx.fx_custo';
        $campos['tipoPrecificacao'] ='vx.tipo_precificacao';
        $campos['curva'] ='curva';
        $campos['custoMedio'] ='vx.custo_medio ';
        $campos['valor'] ='vx.valor';
        $campos['pis'] ='vx.pis';
        $campos['cofins'] =' vx.cofins';
        $campos['icms'] =' icms';
        $campos['grupoDesconto'] ='vx.grupo_desconto';
        $campos['percVendedor'] ='vx.perc_vendedor ';
        $campos['ccMed12mRd'] ='pdr.cc_med12m';
        $campos['ccMed6mRd'] ='pdr.cc_med6m';
        $campos['ccMed3mRd'] ='pdr.cc_med3m';
        $campos['ccM3Rd'] ='pdr.cc_m3';
        $campos['ccM2Rd'] ='pdr.cc_m2';
        $campos['ccM1Rd'] ='pdr.cc_m1';
        $campos['ccMed12m'] ='vx.cc_med12m';
        $campos['ccMed6m'] ='vx.cc_med6m';
        $campos['ccMed3m'] ='vx.cc_med3m';
        $campos['ccM3'] ='vx.cc_m3';
        $campos['ccM2'] ='vx.cc_m2';
        $campos['ccM1'] ='vx.cc_m1';
        // $campos[''] ='pdr2.mb_3m_median';
        $campos['mb_12mRd'] ='pdr.mb_12m';
        $campos['mb_6mRd'] ='pdr.mb_6m';
        $campos['mb_3mRd'] ='pdr.mb_3m';
        $campos['mbM3Rd'] ='pdr.mb_m3';
        $campos['mbM2Rd'] ='pdr.mb_m2';
        $campos['mbM1Rd'] ='pdr.mb_m1';
        $campos['mb_12mMc'] ='emm.mb_12m';
        $campos['mb_6mMc'] ='emm.mb_6m';
        $campos['mb_3mMc'] ='emm.mb_3m';
        $campos['mb_12m'] ='vx.mb_12m';
        $campos['mb_6m'] ='vx.mb_6m';
        $campos['mb_3m'] ='vx.mb_3m';
        $campos['mbM3'] ='vx.mb_m3';
        $campos['mbM2'] ='vx.mb_m2';
        $campos['mbM1'] ='vx.mb_m1';
        $campos['paramMargem'] ='vx.param_margem';
        $campos['margemPrecoAtual'] ='vx.margem_preco_atual';
        $campos['precoAtual'] ='vx.preco_atual';
        $campos['precoAtualMin'] ='vx.preco_atual_min';
        $campos['precoAtualLiq'] ='vx.preco_atual_liq';
        $campos['precoMargemParam'] ='vx.preco_margem_param';

        return $campos;

    }

    public function listargeraprecoAction(){

        $idEmpresas         = $this->params()->fromQuery('idEmpresas',null);
        $notMarca           = $this->params()->fromQuery('notMarca',null);
        $marcas             = $this->params()->fromQuery('idMarcas',null);
        $curvas             = $this->params()->fromQuery('idCurvaAbc',null);
        $produtos           = $this->params()->fromQuery('produtos',null);
        $codTabPreco        = $this->params()->fromQuery('codTabPreco',null);
        $idProduto          = $this->params()->fromQuery('idProduto',null);
        $tipoprecificacao   = $this->params()->fromQuery('tipoprecificacao',null);
        $faixaCusto         = $this->params()->fromQuery('faixaCusto',null);
        $grupoDesconto      = $this->params()->fromQuery('grupoDesconto',null);
        $slidMargem         = $this->params()->fromQuery('slidMargem',null);

        $checkEstoque           = $this->params()->fromQuery('checkEstoque',null);
        $checkpreco             = $this->params()->fromQuery('checkpreco',null);
        $checkmargem            = $this->params()->fromQuery('checkmargem',null);
        $checktipoprecificacao  = $this->params()->fromQuery('checktipoprecificacao',null);
        $checkgrupodesconto     = $this->params()->fromQuery('checkgrupodesconto',null);
        $checktabelapreco       = $this->params()->fromQuery('checktabelapreco',null);
        $checkcustounitario     = $this->params()->fromQuery('checkcustounitario',null);
        $checkparammargem       = $this->params()->fromQuery('checkparammargem',null);

        $inicio     = $this->params()->fromQuery('start',null);
        $final      = $this->params()->fromQuery('limit',null);
        $sort       = $this->params()->fromQuery('sort',null);

        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        $orderBy = 'ORDER BY cod_empresa, cod_tabela, grupo_desconto';
        if($sort){

            $camposOrder = $this->camposgerapreco();

            $sort = json_decode($sort);
            $orderBy = 'ORDER BY '. $camposOrder[$sort[0]->property]." ".$sort[0]->direction;

        }

        if($idEmpresas){
            $idEmpresas =  implode(",",json_decode($idEmpresas));
        }
        if($marcas){
            $marcas = implode("','",json_decode($marcas));
        }
        if($curvas){
            $curvas = implode("','",json_decode($curvas));
        }
        if($produtos){
            $produtos = implode("','",json_decode($produtos));
        }
        if($codTabPreco){
            $codTabPreco = implode(",",json_decode($codTabPreco));
        }
        if($idProduto){
            $idProduto = implode(",",json_decode($idProduto));
        }
        if($tipoprecificacao){
            $tipoprecificacao = implode("','",json_decode($tipoprecificacao));
        }
        if($faixaCusto){
            $faixaCusto = implode("','",json_decode($faixaCusto));
        }
        if($grupoDesconto){
            $grupoDesconto = implode("','",json_decode($grupoDesconto));
        }

        $andSql = '';
        $andSqlEmp = "";
        if($idEmpresas){
            $andSqlEmp = " and vx.cod_empresa in ($idEmpresas)";
        }
        $andSqlMarca = "";
        if($marcas){
            $notMarca = !$notMarca? '': 'not';
            $andSqlMarca = " and vx.marca $notMarca in ('$marcas')";
        }
        $andSqlCurva = "";
        if($curvas){
            // $andSqlCurva = " and curva in ('$curvas')";
            $andSqlCurva = "\n AND (vx.cod_empresa, vx.cod_produto) IN (SELECT c.codemp, c.codprod
                                                                            FROM vw_skproduto_curva_exp c
                                                                        WHERE vx.cod_empresa = c.codemp
                                                                        and codprod = vx.cod_produto
                                                                        AND curva IN ('$curvas'))";
        }
        
        $andSqlProduto ='';
        if($produtos){
            $andSqlProduto = " and vx.cod_nbs in ('$produtos')";
        }
        if($idProduto){
            $andSqlProduto .= " and nvl(vx.cod_produto,'') in ($idProduto)";
        }
        $andSqlPreco = "";
        if($codTabPreco){
            $andSqlPreco = " and nvl(vx.cod_tabela,'') in ($codTabPreco)";
        }
        $andSqlTipoPrecificicao ='';
        if($tipoprecificacao){
            $andSqlTipoPrecificicao = " and vx.tipo_precificacao in ('$tipoprecificacao')";
        }
        $andSqlFxcusto = "";
        if($faixaCusto){
            $andSqlFxcusto = " and to_char(vx.fx_custo) in ('$faixaCusto')";
        }
        $andSqlGrupo = "";
        if($grupoDesconto){
            $andSqlGrupo = " and vx.grupo_desconto in ('$grupoDesconto')";
        }
        
        if($slidMargem){
            $slidMargem =  json_decode($slidMargem);
        }
        $andSlidMargem = '';
        if($slidMargem){
            $andSlidMargem = "and nvl(vx.margem_preco_atual,0) >= $slidMargem[0] and nvl(vx.margem_preco_atual,0) <= $slidMargem[1]";
        }else{
            $andSlidMargem = "and nvl(vx.margem_preco_atual,0) >= 0 and nvl(vx.margem_preco_atual,0) <= 80";
        }

        $andSqlCkEstoque = "";
        switch ($checkEstoque) {
            case 'Com':
                $andSqlCkEstoque = " and nvl(vx.estoque,0) > 0";
                break;
            case 'Sem':
                $andSqlCkEstoque = " and nvl(vx.estoque,0) = 0";
                break;
            default:
               break;
        }
        $andSqlCkPreco = "";
        switch ($checkpreco) {
            case 'Com':
                $andSqlCkPreco = " and nvl(vx.preco_atual,0) > 0";
                break;
            case 'Sem':
                $andSqlCkPreco = " and nvl(vx.preco_atual,0) = 0";
                break;
        }
        $andSqlCkTbPreco = "";
        switch ($checktabelapreco) {
            case 'Com':
                $andSqlCkTbPreco = " and trim(vx.cod_tabela) is not null";
                break;
            case 'Sem':
                $andSqlCkTbPreco = " and trim(vx.cod_tabela) is null";
                break;
        }
        // $andSqlCkMb = "";
        // switch ($checkmargem) {
        //     case 'Com':
        //         $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) > 0";
        //         break;
        //     case 'Sem':
        //         $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) = 0";
        //         break;
        //     case '>10':
        //         $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) > 10";
        //         break;
        //     case '>5':
        //         $andSqlCkMb = " and nvl(round((preco_liq - custo_medio) / preco_liq *100,2),0) > 5";
        //         break;
        // }
        $andSqlTpPrecificacao = '';
        switch ($checktipoprecificacao) {
            case 'Com':
                $andSqlTpPrecificacao = " and trim(vx.tipo_precificacao) is not null";
                break;
            case 'Sem':
                $andSqlTpPrecificacao = " and trim(vx.tipo_precificacao) is null";
                break;
        }
        $andSqlCkGdesc = "";
        switch ($checkgrupodesconto) {
            case 'Com':
                $andSqlCkGdesc = " and trim(vx.grupo_desconto) is not null";
                break;
            case 'Sem':
                $andSqlCkGdesc = " and trim(vx.grupo_desconto) is null";
                break;
        }
        $andSqlCkParamMargem = "";
        switch ($checkparammargem) {
            case 'Com':
                $andSqlCkParamMargem = " and trim(vx.param_margem) is not null";
                break;
            case 'Sem':
                $andSqlCkParamMargem = " and trim(vx.param_margem) is null";
                break;
        }
        // $andSqlCkUnitario = "";
        // switch ($checkcustounitario) {
        //     case 'Com':
        //         $andSqlCkUnitario = " and trim(custo_medio) is not null";
        //         break;
        //     case 'Sem':
        //         $andSqlCkUnitario = " and trim(custo_medio) is null";
        //         break;
        //     case 'c<p':
        //         $andSqlCkUnitario = " and nvl(custo_medio,0) < nvl(PRECO,0)";
        //         break;
        // }

        $sql = "WITH
        
                -- Empresa marca
                emm AS (
                        SELECT cod_empresa, marca,
                            CASE WHEN cc_med12m > 0 THEN cc_med12m END AS cc_med12m,
                            CASE WHEN cc_med6m > 0 THEN cc_med6m END AS cc_med6m,
                            CASE WHEN cc_med3m > 0 THEN cc_med3m END AS cc_med3m,
                            cc_m3, cc_m2, cc_m1,
                            (CASE WHEN NVL(rol_12m,0) > 0 AND NVL(lb_12m,0) > 0 THEN ROUND((lb_12m/rol_12m)*100,2) END) AS mb_12m,
                            (CASE WHEN NVL(rol_6m,0) > 0 AND NVL(lb_6m,0) > 0 THEN ROUND((lb_6m/rol_6m)*100,2) END) AS mb_6m,
                            (CASE WHEN NVL(rol_3m,0) > 0 AND NVL(lb_3m,0) > 0 THEN ROUND((lb_3m/rol_3m)*100,2) END) AS mb_3m,
                            (CASE WHEN NVL(rol_m3,0) > 0 AND NVL(lb_m3,0) > 0 THEN ROUND((lb_m3/rol_m3)*100,2) END) AS mb_m3,
                            (CASE WHEN NVL(rol_m2,0) > 0 AND NVL(lb_m2,0) > 0 THEN ROUND((lb_m2/rol_m2)*100,2) END) AS mb_m2,
                            (CASE WHEN NVL(rol_m1,0) > 0 AND NVL(lb_m1,0) > 0 THEN ROUND((lb_m1/rol_m1)*100,2) END) AS mb_m1
                        FROM (SELECT cod_empresa, marca,
                                    ROUND(SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END)/12) AS cc_med12m,
                                    ROUND(SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-6) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END)/6) AS cc_med6m,
                                    ROUND(SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END)/3) AS cc_med3m,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) THEN 1 END) AS cc_m3,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-2) THEN 1 END) AS cc_m2,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END) AS cc_m1,
                                    SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_12m,
                                    SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-6) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_6m,
                                    SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_3m,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) THEN rol END) AS rol_m3,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-2) THEN rol END) AS rol_m2,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_m1,
                                    SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_12m,
                                    SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-6) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_6m,
                                    SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_3m,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) THEN lb END) AS lb_m3,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-2) THEN lb END) AS lb_m2,
                                    SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_m1
                                FROM (SELECT TRUNC(data,'MM') AS data, cod_empresa, marca, cnpj_parceiro, SUM(rol) AS rol, SUM(lb) AS lb
                                        FROM vm_skvendanota
                                        WHERE data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12)
                                        GROUP BY TRUNC(data,'MM'), cod_empresa, marca, cnpj_parceiro)
                              GROUP BY cod_empresa, marca)
                ),
                -- Produto rede
                pdr AS (
                    SELECT cod_produto,
                        CASE WHEN cc_med12m > 0 THEN cc_med12m END AS cc_med12m,
                        CASE WHEN cc_med6m > 0 THEN cc_med6m END AS cc_med6m,
                        CASE WHEN cc_med3m > 0 THEN cc_med3m END AS cc_med3m,
                        cc_m3, cc_m2, cc_m1,
                        (CASE WHEN NVL(rol_12m,0) > 0 AND NVL(lb_12m,0) > 0 THEN ROUND((lb_12m/rol_12m)*100,2) END) AS mb_12m,
                        (CASE WHEN NVL(rol_6m,0) > 0 AND NVL(lb_6m,0) > 0 THEN ROUND((lb_6m/rol_6m)*100,2) END) AS mb_6m,
                        (CASE WHEN NVL(rol_3m,0) > 0 AND NVL(lb_3m,0) > 0 THEN ROUND((lb_3m/rol_3m)*100,2) END) AS mb_3m,
                        (CASE WHEN NVL(rol_m3,0) > 0 AND NVL(lb_m3,0) > 0 THEN ROUND((lb_m3/rol_m3)*100,2) END) AS mb_m3,
                        (CASE WHEN NVL(rol_m2,0) > 0 AND NVL(lb_m2,0) > 0 THEN ROUND((lb_m2/rol_m2)*100,2) END) AS mb_m2,
                        (CASE WHEN NVL(rol_m1,0) > 0 AND NVL(lb_m1,0) > 0 THEN ROUND((lb_m1/rol_m1)*100,2) END) AS mb_m1
                    FROM (SELECT cod_produto,
                                ROUND(SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END)/12) AS cc_med12m,
                                ROUND(SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-6) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END)/6) AS cc_med6m,
                                ROUND(SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END)/3) AS cc_med3m,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) THEN 1 END) AS cc_m3,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-2) THEN 1 END) AS cc_m2,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN 1 END) AS cc_m1,
                                SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_12m,
                                SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-6) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_6m,
                                SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_3m,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) THEN rol END) AS rol_m3,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-2) THEN rol END) AS rol_m2,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN rol END) AS rol_m1,
                                SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_12m,
                                SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-6) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_6m,
                                SUM(CASE WHEN data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_3m,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3) THEN lb END) AS lb_m3,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-2) THEN lb END) AS lb_m2,
                                SUM(CASE WHEN data = ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1) THEN lb END) AS lb_m1
                            FROM (SELECT TRUNC(data,'MM') AS data, cod_produto, cnpj_parceiro, SUM(rol) AS rol, SUM(lb) AS lb
                                    FROM vm_skvendanota
                                    WHERE data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-12)
                                    --and emp = 'SA' and cod_produto = 397
                                    GROUP BY TRUNC(data,'MM'), cod_produto, cnpj_parceiro)
                          GROUP BY cod_produto)
                ),
                -- Produto mediana rede
                pdr2 AS (
                    SELECT cod_produto, median(mb) AS mb_3m_median
                    FROM (SELECT emp, cod_produto, (CASE WHEN NVL(rol,0) > 0 AND NVL(lb,0) > 0 THEN ROUND((lb/rol)*100,2) END) AS mb
                            FROM (SELECT emp, cod_produto, SUM(rol) AS rol, SUM(lb) AS lb
                                    FROM vm_skvendanota
                                    WHERE data >= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-3)
                                    AND data <= ADD_MONTHS(TRUNC(SYSDATE,'MM'),-1)
                                    --and cod_produto = 397
                                    GROUP BY emp, cod_produto))
                    GROUP BY cod_produto
                ),
                -- Dados base
                vx AS (
                    SELECT cod_empresa, empresa, cod_tabela, cod_produto, descricao, marca, cod_nbs,
                           estoque,
                           fx_custo,
                           tipo_precificacao,
                           custo_medio,
                           valor,
                           pis, cofins, icms,
                           grupo_desconto,
                           perc_vendedor,
                           cc_med12m,
                           cc_med6m,
                           cc_med3m,
                           cc_m3,
                           cc_m2,
                           cc_m1,
                           mb_12m,
                           mb_6m,
                           mb_3m,
                           mb_m3,
                           mb_m2,
                           mb_m1,
                           param_margem,
                           ROUND((preco_liq - custo_medio) / preco_liq *100,2) AS margem_preco_atual,
                           preco AS preco_atual,
                           preco_min AS preco_atual_min,
                           preco_liq AS preco_atual_liq,
                           ROUND(((custo_medio/(1-(param_margem/100)))/(1-((NVL(pis,0)+NVL(cofins,0)+NVL(icms,0))/100)))/(1-(perc_vendedor/100)), 2) AS preco_margem_param
                    FROM (SELECT a.cod_empresa, a.empresa, a.cod_tabela, a.cod_produto, a.descricao, a.marca, a.cod_nbs,
                                 a.estoque,
                                 get_fx_custo(a.custo_medio) AS fx_custo,
                                 a.tipo_precificacao,
                                 ROUND(a.margem,2) AS param_margem,
                                 a.custo_medio,
                                 a.valor,
                                 a.pis, a.cofins, a.icms,
                                 a.grupo_desconto,
                                 ad.perc_vendedor,
                                 pd.cc_med12m,
                                 pd.cc_med6m,
                                 pd.cc_med3m,
                                 pd.cc_m3,
                                 pd.cc_m2,
                                 pd.cc_m1,
                                 pd.mb_12m,
                                 pd.mb_6m,
                                 pd.mb_3m,
                                 pd.mb_m3,
                                 pd.mb_m2,
                                 pd.mb_m1,
                                 ROUND(pv.preco,2) AS preco,
                                 ROUND(pv.preco*(1-(ad.perc_vendedor/100)),2) AS preco_min, -- Preço vendedor desconto
                                 ROUND((pv.preco*(1-(ad.perc_vendedor/100)))*(1-((NVL(a.pis,0)+NVL(a.cofins,0)+NVL(a.icms,0))/100)),2) AS preco_liq -- Preço final sem impostos
                            FROM (SELECT es.cod_empresa, es.empresa, bs.cod_tabela, es.cod_produto, es.descricao, es.marca, es.cod_nbs,
                                         es.estoque,
                                         es.custo_medio,
                                         es.valor,
                                         es.pis, es.cofins, es.icms,
                                         es.grupo_desconto,
                                         es.margem,
                                         es.tipo_precificacao
                                    FROM (SELECT es.cod_empresa, e.emp AS empresa, es.cod_produto, p.descricao, m.descricao_marca AS marca, p.partnumber, p.cod_nbs AS cod_nbs,
                                                 es.estoque,
                                                 ROUND(es.custo_medio,2) AS custo_medio,
                                                 ROUND(es.estoque*es.custo_medio,2) AS valor,
                                                 pi.pis, pi.cofins, pi.icms,
                                                 NVL(pd.grupo_desconto,'GERAL') AS grupo_desconto,
                                                 pe.margem,
                                                 pe.tipo_precificacao
                                            FROM vw_skestoque_base es,
                                                vw_skempresa e,
                                                vw_skproduto p,
                                                vw_skmarca m,
                                                vw_skproduto_desconto_grupo pd,
                                                vw_skproduto_imposto pi,
                                                vw_skproduto_param_empresa pe
                                          WHERE es.cod_empresa = e.cod_empresa
                                          AND es.cod_produto = p.cod_produto
                                          AND p.cod_marca = m.cod_marca
                                          AND es.cod_empresa = pd.cod_empresa(+)
                                          AND es.cod_produto = pd.cod_produto(+)
                                          AND es.cod_empresa = pi.cod_empresa
                                          AND es.cod_produto = pi.cod_produto
                                          AND es.cod_empresa = pe.cod_empresa(+)
                                          AND es.cod_produto = pe.cod_produto(+)
                                         ) es,
                                         vw_sktabela_config_base bs
                                 WHERE es.cod_empresa = bs.cod_empresa(+)) a,
                                vw_skalcada_desconto ad,
                                vw_sktabela_preco2 pv,
                                VW_SKBI_PRODUTO_DEMANDA pd
                          WHERE a.cod_tabela = ad.cod_tabela(+)
                          AND a.grupo_desconto = ad.agrupamento_produto(+)
                          AND a.cod_tabela = pv.cod_tabela(+)
                          AND a.cod_produto = pv.cod_produto(+)
                          AND a.cod_empresa = pd.cod_empresa(+)
                          AND a.cod_produto = pd.cod_produto(+)
                          AND a.marca NOT IN ('MWM','MWM IESA','MWM OPCIONAL','CASCO BATERIA','CASCO EATON CX.CAMBIO','CASCO OUTROS','CASCO EATON EMB.', 'EMERGENCIAL')
                          AND a.cod_empresa NOT IN (28/*LE*/ ,25/*CD*/ ,27/*TL*/)
                          --and a.cod_empresa = 5
                        )
                )
                
                SELECT vx.cod_empresa, 
                       vx.empresa, 
                       vx.cod_tabela, 
                       vx.cod_produto, 
                       vx.descricao, 
                       vx.marca, 
                       vx.cod_nbs, 
                       vx.estoque,
                       vx.fx_custo,
                       vx.tipo_precificacao,
                       '' curva,
                       vx.custo_medio, 
                       vx.valor,
                       vx.pis, vx.cofins, icms,
                       vx.grupo_desconto,
                       vx.perc_vendedor, 
                       pdr.cc_med12m AS cc_med12m_rd,
                       pdr.cc_med6m AS cc_med6m_rd,
                       pdr.cc_med3m AS cc_med3m_rd,
                       pdr.cc_m3 AS cc_m3_rd,
                       pdr.cc_m2 AS cc_m2_rd,
                       pdr.cc_m1 AS cc_m1_rd,
                       vx.cc_med12m,
                       vx.cc_med6m,
                       vx.cc_med3m,
                       vx.cc_m3,
                       vx.cc_m2,
                       vx.cc_m1,
                       pdr2.mb_3m_median AS mb_3m_median_rd,
                       pdr.mb_12m AS mb_12m_rd,
                       pdr.mb_6m AS mb_6m_rd,
                       pdr.mb_3m AS mb_3m_rd,
                       pdr.mb_m3 AS mb_m3_rd,
                       pdr.mb_m2 AS mb_m2_rd,
                       pdr.mb_m1 AS mb_m1_rd,
                       emm.mb_12m AS mb_12m_mc,
                       emm.mb_6m AS mb_6m_mc,
                       emm.mb_3m AS mb_3m_mc,
                       vx.mb_12m,
                       vx.mb_6m,
                       vx.mb_3m,
                       vx.mb_m3,
                       vx.mb_m2,
                       vx.mb_m1,
                       vx.param_margem,
                       vx.margem_preco_atual,
                       vx.preco_atual,
                       vx.preco_atual_min,
                       vx.preco_atual_liq,
                       vx.preco_margem_param
                        
                    FROM vx,
                         pdr,
                         pdr2,
                         emm
                WHERE vx.cod_produto = pdr.cod_produto(+)
                AND vx.cod_produto = pdr2.cod_produto(+)
                AND vx.cod_empresa = emm.cod_empresa(+)
                AND vx.marca = emm.marca(+)
                $andSqlEmp
                $andSqlProduto
                $andSqlMarca
                $andSqlCurva
                $andSqlPreco
                $andSqlTipoPrecificicao
                $andSqlFxcusto
                $andSqlGrupo
                $andSlidMargem
                $andSqlCkEstoque
                $andSqlCkPreco
                $andSqlCkTbPreco
                $andSqlTpPrecificacao
                $andSqlCkGdesc
                $andSqlCkParamMargem
                $orderBy
          ";

        $session = $this->getSession();
        $session['exportgerapreco'] = "$sql";
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
        $hydrator->addStrategy('preco', new ValueStrategy);
        $hydrator->addStrategy('estoque', new ValueStrategy);
        $hydrator->addStrategy('custo_medio', new ValueStrategy);
        $hydrator->addStrategy('valor', new ValueStrategy);
        $hydrator->addStrategy('fx_custo', new ValueStrategy);
        $hydrator->addStrategy('pis', new ValueStrategy);
        $hydrator->addStrategy('cofins', new ValueStrategy);
        $hydrator->addStrategy('icms', new ValueStrategy);
        $hydrator->addStrategy('prec_vendedor', new ValueStrategy);
        $hydrator->addStrategy('cc_med12m', new ValueStrategy);
        $hydrator->addStrategy('cc_med6m', new ValueStrategy);
        $hydrator->addStrategy('cc_med3m', new ValueStrategy);
        $hydrator->addStrategy('cc_m3', new ValueStrategy);
        $hydrator->addStrategy('cc_m2', new ValueStrategy);
        $hydrator->addStrategy('cc_m1', new ValueStrategy);
        $hydrator->addStrategy('mb_6m', new ValueStrategy);
        $hydrator->addStrategy('mb_3m', new ValueStrategy);
        $hydrator->addStrategy('mb_m3', new ValueStrategy);
        $hydrator->addStrategy('mb_m2', new ValueStrategy);
        $hydrator->addStrategy('mb_m1', new ValueStrategy);

        $hydrator->addStrategy('mb_3m_median_rd', new ValueStrategy);
        $hydrator->addStrategy('mb_12m_rd', new ValueStrategy);
        $hydrator->addStrategy('mb_6m_rd', new ValueStrategy);
        $hydrator->addStrategy('mb_3m_rd', new ValueStrategy);
        $hydrator->addStrategy('mb_m3_rd', new ValueStrategy);
        $hydrator->addStrategy('mb_m2_rd', new ValueStrategy);
        $hydrator->addStrategy('mb_m1_rd', new ValueStrategy);
        $hydrator->addStrategy('mb_12m_mc', new ValueStrategy);
        $hydrator->addStrategy('mb_6m_mc', new ValueStrategy);
        $hydrator->addStrategy('mb_3m_mc', new ValueStrategy);
        $hydrator->addStrategy('mb_12m', new ValueStrategy);
        $hydrator->addStrategy('mb_6m', new ValueStrategy);
        $hydrator->addStrategy('mb_3m', new ValueStrategy);
        $hydrator->addStrategy('mb_m3', new ValueStrategy);
        $hydrator->addStrategy('mb_m2', new ValueStrategy);
        $hydrator->addStrategy('mb_m1', new ValueStrategy);

        $hydrator->addStrategy('param_margem', new ValueStrategy);
        $hydrator->addStrategy('margem_preco_atual', new ValueStrategy);
        $hydrator->addStrategy('preco_atual', new ValueStrategy);
        $hydrator->addStrategy('preco_atual_min', new ValueStrategy);
        $hydrator->addStrategy('preco_atual_liq', new ValueStrategy);
        $hydrator->addStrategy('preco_margem_param', new ValueStrategy);
        $stdClass = new StdClass;
        $resultSet = new HydratingResultSet($hydrator, $stdClass);
        $resultSet->initialize($results);

        $data = array();
        $cont = 0;
        foreach ($resultSet as $row) {
            $data[] = $hydrator->extract($row);

            $data[$cont]['estoque'] = (float) $data[$cont]['estoque'];

            $data[$cont]['ccMed12mRd'] = (float) $data[$cont]['ccMed12mRd'];
            $data[$cont]['ccMed6mRd'] = (float) $data[$cont]['ccMed6mRd'];
            $data[$cont]['ccMed3mRd'] = (float) $data[$cont]['ccMed3mRd'];
            $data[$cont]['ccM3Rd'] = (float) $data[$cont]['ccM3Rd'];
            $data[$cont]['ccM2Rd'] = (float) $data[$cont]['ccM2Rd'];
            $data[$cont]['ccM1Rd'] = (float) $data[$cont]['ccM1Rd'];
            $data[$cont]['ccMed12m'] = (float) $data[$cont]['ccMed12m'];
            $data[$cont]['ccMed6m'] = (float) $data[$cont]['ccMed6m'];
            $data[$cont]['ccMed3m'] = (float) $data[$cont]['ccMed3m'];
            $data[$cont]['ccM3'] = (float) $data[$cont]['ccM3'];
            $data[$cont]['ccM2'] = (float) $data[$cont]['ccM2'];
            $data[$cont]['ccM1'] = (float) $data[$cont]['ccM1'];
            $data[$cont]['mb_12mRd'] = (float) $data[$cont]['mb_12mRd'];
            $data[$cont]['mb_6mRd'] = (float) $data[$cont]['mb_6mRd'];
            $data[$cont]['mb_3mRd'] = (float) $data[$cont]['mb_3mRd'];
            $data[$cont]['mbM3Rd'] = (float) $data[$cont]['mbM3Rd'];
            $data[$cont]['mbM2Rd'] = (float) $data[$cont]['mbM2Rd'];
            $data[$cont]['mbM1Rd'] = (float) $data[$cont]['mbM1Rd'];

            $data[$cont]['mb_12mMc'] = (float) $data[$cont]['mb_12mMc'];
            $data[$cont]['mb_6mMc'] = (float) $data[$cont]['mb_6mMc'];
            $data[$cont]['mb_3mMc'] = (float) $data[$cont]['mb_3mMc'];
            $data[$cont]['mb_12m'] = (float) $data[$cont]['mb_12m'];
            $data[$cont]['mb_6m'] = (float) $data[$cont]['mb_6m'];
            $data[$cont]['mb_3m'] = (float) $data[$cont]['mb_3m'];
            $data[$cont]['mbM3'] = (float) $data[$cont]['mbM3'];
            $data[$cont]['mbM2'] = (float) $data[$cont]['mbM2'];
            $data[$cont]['mbM1'] = (float) $data[$cont]['mbM1'];

            $data[$cont]['custoMedio'] = (float) $data[$cont]['custoMedio'];
            $data[$cont]['valor'] = (float) $data[$cont]['valor'];
            $data[$cont]['pis'] = (float) $data[$cont]['pis'];
            $data[$cont]['cofins'] = (float) $data[$cont]['cofins'];
            $data[$cont]['icms'] = (float) $data[$cont]['icms'];
            $data[$cont]['paramMargem'] = (float) $data[$cont]['paramMargem'];
            $data[$cont]['margemPrecoAtual'] = (float) $data[$cont]['margemPrecoAtual'];
            $data[$cont]['precoAtual'] = (float) $data[$cont]['precoAtual'];
            $data[$cont]['precoAtualMin'] = (float) $data[$cont]['precoAtualMin'];
            $data[$cont]['precoAtualLiq'] = (float) $data[$cont]['precoAtualLiq'];
            $data[$cont]['precoMargemParam'] = (float) $data[$cont]['precoMargemParam'];

            $cont++;
        }

        $this->setCallbackData($data);

        $objReturn = $this->getCallbackModel();

        $objReturn->total = $resultCount[0]['TOTALCOUNT'];

        return $objReturn;
    }
    
    public function gerarprecoexcelAction()
    {
        $data = array();
        
        try {

            $session = $this->getSession();

            if($session['exportgerapreco']){

                ini_set('memory_limit', '5120M' );

                $em = $this->getEntityManager();
                $conn = $em->getConnection();

                $sql = $session['exportgerapreco'] ;
                
                $conn = $em->getConnection();
                $stmt = $conn->prepare($sql);
                
                $stmt->execute();
                $results = $stmt->fetchAll();

                $hydrator = new ObjectProperty;
                $hydrator->addStrategy('tipo_precificacao', new ValueStrategy);
                $stdClass = new StdClass;
                $resultSet = new HydratingResultSet($hydrator, $stdClass);
                $resultSet->initialize($results);

                $data = array();
                
                $output = 'COD_EMPRESA;NOME_EMPRESA;COD_TABELA;COD_PRODUTO;DESCRICAO'.
                          ';MARCA;COD_NBS;ESTOQUE;FX_CUSTO;TIPO_PRECIFICACAO;CURVA;CUSTO_MEDIO'.
                          ';VALOR;PIS;COFINS;ICMS;GRUPO_DESCONTO;PERC_VENDEDOR'.
                          ';CC_MED12M_RD;CC_MED6M_RD;CC_MED3M_RD'.
                          ';CC_M3_RD;CC_M2_RD;CC_M1_RD'.
                          ';CC_MED12M;CC_MED6M;CC_MED3M'.
                          ';CC_M3;CC_M2;CC_M1'.
                          ';MB_12M_RD;MB_6M_RD;MB_3M_RD'.
                          ';MB_M3_RD;MB_M2_RD;MB_M1_RD'.
                          ';MB_12M_MC;MB_6M_MC;MB_3M_MC'.
                          ';MB_12M;MB_6M;MB_3M'.
                          ';MB_M3;MB_M2;MB_M1'.
                          ';PARAM_MARGEM;MARGEM_PRECO_ATUAL;PRECO_ATUAL;PRECO_ATUAL_MIN;PRECO_ATUAL_LIQ'.
                          ';PRECO_MARGEM_PARAM'."\n";

                $i=0;
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $codEmpresa     = $data[$i]['codEmpresa'];
                    $nomeEmpresa    = $data[$i]['empresa'];
                    $codTabPreco    = $data[$i]['codTabela'];

                    $estoque        = $data[$i]['estoque'] >0 ? $data[$i]['estoque'] : null ;
                    $custoMedio     = $data[$i]['custoMedio'] >0 ? $data[$i]['custoMedio'] : null ;
                    $valor          = $data[$i]['valor'] >0 ? $data[$i]['valor'] : null ;
                    $pis            = $data[$i]['pis'] >0 ? $data[$i]['pis'] : null ;
                    $cofins         = $data[$i]['cofins'] >0 ? $data[$i]['cofins'] : null ;
                    $icms           = $data[$i]['icms'] >0 ? $data[$i]['icms'] : null ;
                    $percVendedor   = $data[$i]['percVendedor'] >0 ? $data[$i]['percVendedor'] : null ;

                    $ccMed12mRd     = $data[$i]['ccMed12mRd'] >0 ? $data[$i]['ccMed12mRd'] : null ;
                    $ccMed6mRd      = $data[$i]['ccMed6mRd'] >0 ? $data[$i]['ccMed6mRd'] : null ;
                    $ccMed3mRd      = $data[$i]['ccMed3mRd'] >0 ? $data[$i]['ccMed3mRd'] : null ;
                    $ccM3Rd         = $data[$i]['ccM3Rd'] >0 ? $data[$i]['ccM3Rd'] : null ;
                    $ccM2Rd         = $data[$i]['ccM2Rd'] >0 ? $data[$i]['ccM2Rd'] : null ;
                    $ccM1Rd         = $data[$i]['ccM1Rd'] >0 ? $data[$i]['ccM1Rd'] : null ;
                    $ccMed12m       = $data[$i]['ccMed12m'] >0 ? $data[$i]['ccMed12m'] : null ;
                    $ccMed6m        = $data[$i]['ccMed6m'] >0 ? $data[$i]['ccMed6m'] : null ;
                    $ccMed3m        = $data[$i]['ccMed3m'] >0 ? $data[$i]['ccMed3m'] : null ;
                    $ccM3           = $data[$i]['ccM3'] >0 ? $data[$i]['ccM3'] : null ;
                    $ccM2           = $data[$i]['ccM2'] >0 ? $data[$i]['ccM2'] : null ;
                    $ccM1           = $data[$i]['ccM1'] >0 ? $data[$i]['ccM1'] : null ;

                    $mb_12mRd       = $data[$i]['mb_12mRd'] >0 ? $data[$i]['mb_12mRd'] : null ;
                    $mb_6mRd        = $data[$i]['mb_6mRd'] >0 ? $data[$i]['mb_6mRd'] : null ;
                    $mb_3mRd        = $data[$i]['mb_3mRd'] >0 ? $data[$i]['mb_3mRd'] : null ;
                    $mbM3Rd         = $data[$i]['mbM3Rd'] >0 ? $data[$i]['mbM3Rd'] : null ;
                    $mbM2Rd         = $data[$i]['mbM2Rd'] >0 ? $data[$i]['mbM2Rd'] : null ;
                    $mbM1Rd         = $data[$i]['mbM1Rd'] >0 ? $data[$i]['mbM1Rd'] : null ;

                    $mb_12mMc       = $data[$i]['mb_12mMc'] >0 ? $data[$i]['mb_12mMc'] : null ;
                    $mb_6mMc        = $data[$i]['mb_6mMc'] >0 ? $data[$i]['mb_6mMc'] : null ;
                    $mb_3mMc        = $data[$i]['mb_3mMc'] >0 ? $data[$i]['mb_3mMc'] : null ;
                    $mb_12m         = $data[$i]['mb_12m'] >0 ? $data[$i]['mb_12m'] : null ;
                    $mb_6m          = $data[$i]['mb_6m'] >0 ? $data[$i]['mb_6m'] : null ;
                    $mb_3m          = $data[$i]['mb_3m'] >0 ? $data[$i]['mb_3m'] : null ;
                    $mbM3           = $data[$i]['mbM3'] >0 ? $data[$i]['mbM3'] : null ;
                    $mbM2           = $data[$i]['mbM2'] >0 ? $data[$i]['mbM2'] : null ;
                    $mbM1           = $data[$i]['mbM1'] >0 ? $data[$i]['mbM1'] : null ;

                    $margemPrecoAtual   = $data[$i]['margemPrecoAtual'] >0 ? $data[$i]['margemPrecoAtual'] : null ;
                    $precoAtual         = $data[$i]['precoAtual'] >0 ? $data[$i]['precoAtual'] : null ;
                    $precoAtualMin      = $data[$i]['precoAtualMin'] >0 ? $data[$i]['precoAtualMin'] : null ;
                    $precoAtualLiq      = $data[$i]['precoAtualLiq'] >0 ? $data[$i]['precoAtualLiq'] : null ;
                    $precoMargemParam               = $data[$i]['precoMargemParam'] >0 ? $data[$i]['precoMargemParam'] : null ;


                    $output  .= $codEmpresa.';'.
                                $nomeEmpresa.';'.
                                $codTabPreco.';'.
                                $data[$i]['codProduto'].';'.
                                $data[$i]['descricao'].';'.
                                $data[$i]['marca'].';'.
                                $data[$i]['codNbs'].';'.
                                $estoque.';'.
                                $data[$i]['fxCusto'].';'.
                                $data[$i]['tipoPrecificacao'].';'.
                                $data[$i]['curva'].';'.
                                $custoMedio.';'.
                                $valor.';'.
                                $pis.';'.
                                $cofins.';'.
                                $icms.';'.
                                $data[$i]['grupoDesconto'].';'.
                                $percVendedor.';'.

                                $ccMed12mRd.';'.
                                $ccMed6mRd.';'.
                                $ccMed3mRd.';'.
                                $ccM3Rd.';'.
                                $ccM2Rd.';'.
                                $ccM1Rd.';'.
                                $ccMed12m.';'.
                                $ccMed6m.';'.
                                $ccMed3m.';'.
                                $ccM3.';'.
                                $ccM2.';'.
                                $ccM1.';'.

                                $mb_12mRd.';'.
                                $mb_6mRd.';'.
                                $mb_3mRd.';'.
                                $mbM3Rd.';'.
                                $mbM2Rd.';'.
                                $mbM1Rd.';'.

                                $mb_12mMc.';'.
                                $mb_6mMc.';'.
                                $mb_3mMc.';'.
                                $mb_12m.';'.
                                $mb_6m.';'.
                                $mb_3m.';'.
                                $mbM3.';'.
                                $mbM2.';'.
                                $mbM1.';'.

                                $data[$i]['paramMargem'].';'.
                                $margemPrecoAtual.';'.
                                $precoAtual.';'.
                                $precoAtualMin.';'.
                                $precoAtualLiq.';'.
                                $precoMargemParam.';'."\n";
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
                        'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Gerar Preço.csv',
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

    public function gerarprecoexcel2Action()
    {

        $data = array();

        try {

            $session = $this->getSession();
            $usuario = $session['info']['usuarioSistema'];

            ini_set('memory_limit', '5120M' );

            $em = $this->getEntityManager();
            $conn = $em->getConnection();

            $sql = $session['exportgerapreco'] ;
            
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
            $arqFile = '.\data\exportgerapreco_'.$session['info']['usuarioSistema'].'1.xlsx';
            fopen($arqFile,'w'); // Paramentro $phpExcel somente retorno

            $phpExcel = $excelService->createPHPExcelObject($arqFile);
            $phpExcel->getActiveSheet()->setCellValue('A'.'1', 'COD_EMPRESA')
                                       ->setCellValue('B'.'1', 'NOME_EMPRESA')
                                       ->setCellValue('C'.'1', 'COD_TABELA')
                                       ->setCellValue('D'.'1', 'COD_PRODUTO')
                                       ->setCellValue('E'.'1', 'DESCRICAO')
                                       ->setCellValue('F'.'1', 'MARCA')
                                       ->setCellValue('G'.'1', 'COD_NBS')
                                       ->setCellValue('H'.'1', 'ESTOQUE')
                                       ->setCellValue('I'.'1', 'FX_CUSTO')
                                       ->setCellValue('J'.'1', 'TIPO_PRECIFICACAO')
                                       ->setCellValue('K'.'1', 'CURVA')
                                       ->setCellValue('L'.'1', 'CUSTO_MEDIO')
                                       ->setCellValue('M'.'1', 'VALOR')
                                       ->setCellValue('N'.'1', 'PIS')
                                       ->setCellValue('O'.'1', 'COFINS')
                                       ->setCellValue('P'.'1', 'ICMS')
                                       ->setCellValue('Q'.'1', 'GRUPO_DESCONTO')
                                       ->setCellValue('R'.'1', 'PERC_VENDEDOR')
                                       ->setCellValue('S'.'1', 'CC_MED12M_RD')
                                       ->setCellValue('T'.'1', 'CC_MED6M_RD')
                                       ->setCellValue('U'.'1', 'CC_MED3M_RD')
                                       ->setCellValue('V'.'1', 'CC_M3_RD')
                                       ->setCellValue('W'.'1', 'CC_M2_RD')
                                       ->setCellValue('X'.'1', 'CC_M1_RD')
                                       ->setCellValue('Y'.'1', 'CC_MED12M')
                                       ->setCellValue('Z'.'1', 'CC_MED6M')
                                       ->setCellValue('AA'.'1', 'CC_MED3M')
                                       ->setCellValue('AB'.'1', 'CC_M3')
                                       ->setCellValue('AC'.'1', 'CC_M2')
                                       ->setCellValue('AD'.'1', 'CC_M1')
                                       ->setCellValue('AE'.'1', 'MB_12M_RD')
                                       ->setCellValue('AF'.'1', 'MB_6M_RD')
                                       ->setCellValue('AG'.'1', 'MB_3M_RD')
                                       ->setCellValue('AH'.'1', 'MB_M3_RD')
                                       ->setCellValue('AI'.'1', 'MB_M2_RD')
                                       ->setCellValue('AJ'.'1', 'MB_M1_RD')
                                       ->setCellValue('AK'.'1', 'MB_12M_MC')
                                       ->setCellValue('AL'.'1', 'MB_6M_MC')
                                       ->setCellValue('AM'.'1', 'MB_3M_MC')
                                       ->setCellValue('AN'.'1', 'MB_12M')
                                       ->setCellValue('AO'.'1', 'MB_6M')
                                       ->setCellValue('AP'.'1', 'MB_3M')
                                       ->setCellValue('AQ'.'1', 'MB_M3')
                                       ->setCellValue('AR'.'1', 'MB_M2')
                                       ->setCellValue('AS'.'1', 'MB_M1')
                                       ->setCellValue('AT'.'1', 'PARAM_MARGEM')
                                       ->setCellValue('AU'.'1', 'MARGEM_PRECO_ATUAL')
                                       ->setCellValue('AV'.'1', 'PRECO_ATUAL')
                                       ->setCellValue('AW'.'1', 'PRECO_ATUAL_MIN')
                                       ->setCellValue('AX'.'1', 'PRECO_ATUAL_LIQ')
                                       ->setCellValue('AY'.'1', 'PRECO_MARGEM_PARAM');

                $i=0;
                $ix=2;
                foreach ($resultSet as $row) {
                    $data[] = $hydrator->extract($row);

                    $codEmpresa     = $data[$i]['codEmpresa'];
                    $nomeEmpresa    = $data[$i]['empresa'];
                    $codTabPreco    = $data[$i]['codTabela'];

                    $estoque        = $data[$i]['estoque'] >0 ? $data[$i]['estoque'] : null ;
                    $custoMedio     = $data[$i]['custoMedio'] >0 ? $data[$i]['custoMedio'] : null ;
                    $valor          = $data[$i]['valor'] >0 ? $data[$i]['valor'] : null ;
                    $pis            = $data[$i]['pis'] >0 ? $data[$i]['pis'] : null ;
                    $cofins         = $data[$i]['cofins'] >0 ? $data[$i]['cofins'] : null ;
                    $icms           = $data[$i]['icms'] >0 ? $data[$i]['icms'] : null ;
                    $percVendedor   = $data[$i]['percVendedor'] >0 ? $data[$i]['percVendedor'] : null ;

                    $ccMed12mRd     = $data[$i]['ccMed12mRd'] >0 ? $data[$i]['ccMed12mRd'] : null ;
                    $ccMed6mRd      = $data[$i]['ccMed6mRd'] >0 ? $data[$i]['ccMed6mRd'] : null ;
                    $ccMed3mRd      = $data[$i]['ccMed3mRd'] >0 ? $data[$i]['ccMed3mRd'] : null ;
                    $ccM3Rd         = $data[$i]['ccM3Rd'] >0 ? $data[$i]['ccM3Rd'] : null ;
                    $ccM2Rd         = $data[$i]['ccM2Rd'] >0 ? $data[$i]['ccM2Rd'] : null ;
                    $ccM1Rd         = $data[$i]['ccM1Rd'] >0 ? $data[$i]['ccM1Rd'] : null ;
                    $ccMed12m       = $data[$i]['ccMed12m'] >0 ? $data[$i]['ccMed12m'] : null ;
                    $ccMed6m        = $data[$i]['ccMed6m'] >0 ? $data[$i]['ccMed6m'] : null ;
                    $ccMed3m        = $data[$i]['ccMed3m'] >0 ? $data[$i]['ccMed3m'] : null ;
                    $ccM3           = $data[$i]['ccM3'] >0 ? $data[$i]['ccM3'] : null ;
                    $ccM2           = $data[$i]['ccM2'] >0 ? $data[$i]['ccM2'] : null ;
                    $ccM1           = $data[$i]['ccM1'] >0 ? $data[$i]['ccM1'] : null ;

                    $mb_12mRd       = $data[$i]['mb_12mRd'] >0 ? $data[$i]['mb_12mRd'] : null ;
                    $mb_6mRd        = $data[$i]['mb_6mRd'] >0 ? $data[$i]['mb_6mRd'] : null ;
                    $mb_3mRd        = $data[$i]['mb_3mRd'] >0 ? $data[$i]['mb_3mRd'] : null ;
                    $mbM3Rd         = $data[$i]['mbM3Rd'] >0 ? $data[$i]['mbM3Rd'] : null ;
                    $mbM2Rd         = $data[$i]['mbM2Rd'] >0 ? $data[$i]['mbM2Rd'] : null ;
                    $mbM1Rd         = $data[$i]['mbM1Rd'] >0 ? $data[$i]['mbM1Rd'] : null ;

                    $mb_12mMc       = $data[$i]['mb_12mMc'] >0 ? $data[$i]['mb_12mMc'] : null ;
                    $mb_6mMc        = $data[$i]['mb_6mMc'] >0 ? $data[$i]['mb_6mMc'] : null ;
                    $mb_3mMc        = $data[$i]['mb_3mMc'] >0 ? $data[$i]['mb_3mMc'] : null ;
                    $mb_12m         = $data[$i]['mb_12m'] >0 ? $data[$i]['mb_12m'] : null ;
                    $mb_6m          = $data[$i]['mb_6m'] >0 ? $data[$i]['mb_6m'] : null ;
                    $mb_3m          = $data[$i]['mb_3m'] >0 ? $data[$i]['mb_3m'] : null ;
                    $mbM3           = $data[$i]['mbM3'] >0 ? $data[$i]['mbM3'] : null ;
                    $mbM2           = $data[$i]['mbM2'] >0 ? $data[$i]['mbM2'] : null ;
                    $mbM1           = $data[$i]['mbM1'] >0 ? $data[$i]['mbM1'] : null ;
                    $margemPrecoAtual   = $data[$i]['margemPrecoAtual'] >0 ? $data[$i]['margemPrecoAtual'] : null ;
                    $precoAtual         = $data[$i]['precoAtual'] >0 ? $data[$i]['precoAtual'] : null ;
                    $precoAtualMin      = $data[$i]['precoAtualMin'] >0 ? $data[$i]['precoAtualMin'] : null ;
                    $precoAtualLiq      = $data[$i]['precoAtualLiq'] >0 ? $data[$i]['precoAtualLiq'] : null ;
                    $precoMargemParam   = $data[$i]['precoMargemParam'] >0 ? $data[$i]['precoMargemParam'] : null ;

                    $phpExcel->getActiveSheet()->setCellValue('A'.$ix, $codEmpresa)
                                               ->setCellValue('B'.$ix, $nomeEmpresa)
                                               ->setCellValue('C'.$ix, $codTabPreco)
                                               ->setCellValue('D'.$ix, $data[$i]['codProduto'])
                                               ->setCellValue('E'.$ix, $data[$i]['descricao'])
                                               ->setCellValue('F'.$ix, $data[$i]['marca'])
                                               ->setCellValue('G'.$ix, $data[$i]['codNbs'])
                                               ->setCellValue('H'.$ix, $estoque)
                                               ->setCellValue('I'.$ix, $data[$i]['fxCusto'])
                                               ->setCellValue('J'.$ix, $data[$i]['tipoPrecificacao'])
                                               ->setCellValue('K'.$ix, $data[$i]['curva'])
                                               ->setCellValue('L'.$ix, $custoMedio)
                                               ->setCellValue('M'.$ix, $valor)
                                               ->setCellValue('N'.$ix, $pis)
                                               ->setCellValue('O'.$ix, $cofins)
                                               ->setCellValue('P'.$ix, $icms)
                                               ->setCellValue('Q'.$ix, $data[$i]['grupoDesconto'])
                                               ->setCellValue('R'.$ix, $percVendedor)
                                               ->setCellValue('S'.$ix, $ccMed12mRd)
                                               ->setCellValue('T'.$ix, $ccMed6mRd)
                                               ->setCellValue('U'.$ix, $ccMed3mRd)
                                               ->setCellValue('V'.$ix, $ccM3Rd)
                                               ->setCellValue('W'.$ix, $ccM2Rd)
                                               ->setCellValue('X'.$ix, $ccM1Rd)
                                               ->setCellValue('Y'.$ix, $ccMed12m)
                                               ->setCellValue('Z'.$ix, $ccMed6m)
                                               ->setCellValue('AA'.$ix, $ccMed3m)
                                               ->setCellValue('AB'.$ix, $ccM3)
                                               ->setCellValue('AC'.$ix, $ccM2)
                                               ->setCellValue('AD'.$ix, $ccM1)
                                               ->setCellValue('AE'.$ix, $mb_12mRd)
                                               ->setCellValue('AF'.$ix, $mb_6mRd)
                                               ->setCellValue('AG'.$ix, $mb_3mRd)
                                               ->setCellValue('AH'.$ix, $mbM3Rd)
                                               ->setCellValue('AI'.$ix, $mbM2Rd)
                                               ->setCellValue('AJ'.$ix, $mbM1Rd)
                                               ->setCellValue('AK'.$ix, $mb_12mMc)
                                               ->setCellValue('AL'.$ix, $mb_6mMc)
                                               ->setCellValue('AM'.$ix, $mb_3mMc)
                                               ->setCellValue('AN'.$ix, $mb_12m)
                                               ->setCellValue('AO'.$ix, $mb_6m)
                                               ->setCellValue('AP'.$ix, $mb_3m)
                                               ->setCellValue('AQ'.$ix, $mbM3)
                                               ->setCellValue('AR'.$ix, $mbM2)
                                               ->setCellValue('AS'.$ix, $mbM1)
                                               ->setCellValue('AT'.$ix, $data[$i]['paramMargem'])
                                               ->setCellValue('AU'.$ix, $margemPrecoAtual)
                                               ->setCellValue('AV'.$ix, $precoAtual)
                                               ->setCellValue('AW'.$ix, $precoAtualMin)
                                               ->setCellValue('AX'.$ix, $precoAtualLiq)
                                               ->setCellValue('AY'.$ix, $precoMargemParam);
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
                'Content-Disposition' => 'attachment; filename=' . 'JS Peças - Gerar Preço.xls',
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
