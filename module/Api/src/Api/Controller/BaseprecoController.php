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

    public function listargprecoAction(){

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
        // if($grupoDesconto){
        //     $grupoDesconto = implode("','",json_decode($grupoDesconto));
        // }

        $andSql = '';
        $andSqlEmp = "";
        if($idEmpresas){
            $andSqlEmp = " and cod_empresa in ($idEmpresas)";
        }
        $andSqlMarca = "";
        if($marcas){
            $notMarca = !$notMarca? '': 'not';
            $andSqlMarca = " and marca $notMarca in ('$marcas')";
        }
        
        $andSqlProduto ='';
        if($produtos){
            $andSqlProduto = " and cod_nbs in ('$produtos')";
        }
        if($idProduto){
            $andSqlProduto .= " and nvl(cod_produto,'') in ($idProduto)";
        }
        $andSqlPreco = "";
        if($codTabPreco){
            $andSqlPreco = " and nvl(cod_tabela,'') in ($codTabPreco)";
        }
        // $andSqlGrupo = "";
        // if($grupoDesconto){
        //     $andSqlGrupo = " and pd.grupo_desconto in ('$grupoDesconto')";
        // }
        $andSqlCkEstoque = "";
        switch ($checkEstoque) {
            case 'Com':
                $andSqlCkEstoque = " and nvl(ESTOQUE,0) > 0";
                break;
            case 'Sem':
                $andSqlCkEstoque = " and nvl(ESTOQUE,0) = 0";
                break;
            default:
               break;
        }
        $andSqlCkPreco = "";
        switch ($checkpreco) {
            case 'Com':
                $andSqlCkPreco = " and nvl(preco_atual,0) > 0";
                break;
            case 'Sem':
                $andSqlCkPreco = " and nvl(preco_atual,0) = 0";
                break;
        }
        $andSqlCkTbPreco = "";
        switch ($checktabelapreco) {
            case 'Com':
                $andSqlCkTbPreco = " and trim(cod_tabela) is not null";
                break;
            case 'Sem':
                $andSqlCkTbPreco = " and trim(cod_tabela) is null";
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
                $andSqlTpPrecificacao = " and trim(TIPO_PRECIFICACAO) is not null";
                break;
            case 'Sem':
                $andSqlTpPrecificacao = " and trim(TIPO_PRECIFICACAO) is null";
                break;
        }
        $andSqlCkGdesc = "";
        switch ($checkgrupodesconto) {
            case 'Com':
                $andSqlCkGdesc = " and trim(grupo_desconto) is not null";
                break;
            case 'Sem':
                $andSqlCkGdesc = " and trim(grupo_desconto) is null";
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

        $sql = "select cod_empresa,
                        empresa,
                        cod_tabela,
                        cod_produto,
                        descricao,
                        marca,
                        cod_nbs,
                        estoque,
                        fx_custo,
                        tipo_precificacao,
                        curva,
                        custo_medio,
                        valor,
                        pis,
                        cofins,
                        icms,
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
                        margem_preco_atual,
                        preco_atual,
                        preco_atual_min,
                        preco_atual_liq,
                        preco_margem_param
                from tmp_skbase_preco_teste
             where 1= 1 
             $andSqlEmp
             $andSqlProduto
             $andSqlMarca
             $andSqlPreco
             $andSqlCkEstoque
             $andSqlCkPreco
             $andSqlCkTbPreco
             $andSqlTpPrecificacao
             $andSqlCkGdesc
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
        $hydrator->addStrategy('margem_preco_atual', new ValueStrategy);
        $hydrator->addStrategy('preco_atual', new ValueStrategy);
        $hydrator->addStrategy('preco_atual_min', new ValueStrategy);
        $hydrator->addStrategy('preco_atual_liq', new ValueStrategy);
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
                          ';VALOR;PIS;COFINS;ICMS;GRUPO_DESCONTO;PERC_VENDEDOR;CC_MED12M;CC_MED6M;CC_MED3M'.
                          ';CC_M3;CC_M2;CC_M1;MB_12M;MB_6M;MB_3M;MB_M3;MB_M2;MB_M1'.
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
                    $ccMed12m       = $data[$i]['ccMed12m'] >0 ? $data[$i]['ccMed12m'] : null ;
                    $ccMed6m        = $data[$i]['ccMed6m'] >0 ? $data[$i]['ccMed6m'] : null ;
                    $ccMed3m        = $data[$i]['ccMed3m'] >0 ? $data[$i]['ccMed3m'] : null ;
                    $ccM3           = $data[$i]['ccM3'] >0 ? $data[$i]['ccM3'] : null ;
                    $ccM2           = $data[$i]['ccM2'] >0 ? $data[$i]['ccM2'] : null ;
                    $ccM1           = $data[$i]['ccM1'] >0 ? $data[$i]['ccM1'] : null ;
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
                                $ccMed12m.';'.
                                $ccMed6m.';'.
                                $ccMed3m.';'.
                                $ccM3.';'.
                                $ccM2.';'.
                                $ccM1.';'.
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
                                       ->setCellValue('S'.'1', 'CC_MED12M')
                                       ->setCellValue('T'.'1', 'CC_MED6M')
                                       ->setCellValue('U'.'1', 'CC_MED3M')
                                       ->setCellValue('V'.'1', 'CC_M3')
                                       ->setCellValue('W'.'1', 'CC_M2')
                                       ->setCellValue('X'.'1', 'CC_M1')
                                       ->setCellValue('Y'.'1', 'MB_12M')
                                       ->setCellValue('Z'.'1', 'MB_6M')
                                       ->setCellValue('AA'.'1', 'MB_3M')
                                       ->setCellValue('AB'.'1', 'MB_M3')
                                       ->setCellValue('AC'.'1', 'MB_M2')
                                       ->setCellValue('AD'.'1', 'MB_M1')
                                       ->setCellValue('AE'.'1', 'PARAM_MARGEM')
                                       ->setCellValue('AF'.'1', 'MARGEM_PRECO_ATUAL')
                                       ->setCellValue('AG'.'1', 'PRECO_ATUAL')
                                       ->setCellValue('AH'.'1', 'PRECO_ATUAL_MIN')
                                       ->setCellValue('AI'.'1', 'PRECO_ATUAL_LIQ')
                                       ->setCellValue('AJ'.'1', 'PRECO_MARGEM_PARAM');

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
                    $ccMed12m       = $data[$i]['ccMed12m'] >0 ? $data[$i]['ccMed12m'] : null ;
                    $ccMed6m        = $data[$i]['ccMed6m'] >0 ? $data[$i]['ccMed6m'] : null ;
                    $ccMed3m        = $data[$i]['ccMed3m'] >0 ? $data[$i]['ccMed3m'] : null ;
                    $ccM3           = $data[$i]['ccM3'] >0 ? $data[$i]['ccM3'] : null ;
                    $ccM2           = $data[$i]['ccM2'] >0 ? $data[$i]['ccM2'] : null ;
                    $ccM1           = $data[$i]['ccM1'] >0 ? $data[$i]['ccM1'] : null ;
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
                                               ->setCellValue('S'.$ix, $ccMed12m)
                                               ->setCellValue('T'.$ix, $ccMed6m)
                                               ->setCellValue('U'.$ix, $ccMed3m)
                                               ->setCellValue('V'.$ix, $ccM3)
                                               ->setCellValue('W'.$ix, $ccM2)
                                               ->setCellValue('X'.$ix, $ccM1)
                                               ->setCellValue('Y'.$ix, $mb_12m)
                                               ->setCellValue('Z'.$ix, $mb_6m)
                                               ->setCellValue('AA'.$ix, $mb_3m)
                                               ->setCellValue('AB'.$ix, $mbM3)
                                               ->setCellValue('AC'.$ix, $mbM2)
                                               ->setCellValue('AD'.$ix, $mbM1)
                                               ->setCellValue('AE'.$ix, $data[$i]['paramMargem'])
                                               ->setCellValue('AF'.$ix, $margemPrecoAtual)
                                               ->setCellValue('AG'.$ix, $precoAtual)
                                               ->setCellValue('AH'.$ix, $precoAtualMin)
                                               ->setCellValue('AI'.$ix, $precoAtualLiq)
                                               ->setCellValue('AJ'.$ix, $precoMargemParam);
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
