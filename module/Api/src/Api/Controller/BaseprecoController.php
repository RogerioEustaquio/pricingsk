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

        // $sql = " select COD_EMPRESA,
        //                 NOME_EMPRESA,
        //                 COD_TAB_PRECO,
        //                 NOME_TAB_PRECO,
        //                 to_char(DT_VIGOR,'dd/mm/yyyy') dt_vigor,
        //                 PRECO,
        //                 TIPO,
        //                 COD_PRODUTO,
        //                 DESCRICAO,
        //                 MARCA,
        //                 COD_FORNECEDOR,
        //                 NOME_FORNECEDOR,
        //                 COD_ITEM_NBS,
        //                 PARTNUMBER,
        //                 MARGEM mb,
        //                 DESP_VARIAVEL,
        //                 TIPO_PRECIFICACAO,
        //                 NIVEL_MARGEM,
        //                 GRUPO_DESCONTO,
        //                 ESTOQUE,
        //                 CUSTO_MEDIO,
        //                 VALOR_ESTOQUE,
        //                 CUSTO_OPE,
        //                 PIS_COFINS,
        //                 ICMS                       
        //             from SK_PRODUTO_TABELA_TMP 
        //          where 1 = 1
        //          $andSql
        //          ";

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
        // print "$sql";
        // exit;

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
                // $hydrator->addStrategy('preco', new ValueStrategy);
                // $hydrator->addStrategy('estoque', new ValueStrategy);
                // $hydrator->addStrategy('mb', new ValueStrategy);
                // $hydrator->addStrategy('despVariavel', new ValueStrategy);
                // $hydrator->addStrategy('custo_medio', new ValueStrategy);
                // $hydrator->addStrategy('valor_estoque', new ValueStrategy);
                // $hydrator->addStrategy('custo_ope', new ValueStrategy);
                // $hydrator->addStrategy('pis_cofins', new ValueStrategy);
                // $hydrator->addStrategy('icms', new ValueStrategy);
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

                // $arqFile = '.\data\exportbasepreco_'.$session['info']['usuarioSistema'].'.csv';
                // $arquivo = fopen($arqFile,'w'); 
                // fwrite($arquivo, $output);
                // fclose($arquivo);

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
}
