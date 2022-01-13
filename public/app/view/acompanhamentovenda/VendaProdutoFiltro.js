Ext.define('App.view.acompanhamentovenda.VendaProdutoFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'vendaprodutofiltro',
    itemId: 'vendaprodutofiltro',
    title: 'Filtro',
    region: 'west',
    width: 290,
    hidden: true,
    scrollable: true,
    layout: 'vbox',
    requires:[
        // 'App.view.basepreco.PluginToggle'
    ],

    initComponent: function() {
        var me = this;

        var elTagEmpresa = Ext.create('Ext.form.field.Tag',{
            name: 'elEmp',
            itemId: 'elEmp',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [
                    { name: 'emp', type: 'string' },
                    { name: 'idEmpresa', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listarEmpresas',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            width: 230,
            queryParam: 'emp',
            queryMode: 'local',
            displayField: 'emp',
            valueField: 'idEmpresa',
            emptyText: 'Empresa',
            fieldLabel: 'Empresas',
            labelWidth: 60,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        elTagEmpresa.store.load(
            function(){
                elTagEmpresa.setDisabled(false);
            }
        );

        var fielDataInicio = Ext.create('Ext.form.field.Date',{
            name: 'datainicio',
            itemId: 'datainicio',
            labelAlign: 'top',
            fieldLabel: 'Data Início',
            margin: '1 1 1 8',
            padding: 1,
            width: 230,
            labelWidth: 60,
            format: 'd/m/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            // value: sysdate
        });
        
        var fielDataFinal = Ext.create('Ext.form.field.Date',{
            name: 'datafinal',
            itemId: 'datafinal',
            labelAlign: 'top',
            fieldLabel: 'Data Final',
            margin: '1 1 1 8',
            padding: 1,
            width: 230,
            labelWidth: 60,
            format: 'd/m/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            // value: sysdate
        });

        var elTagMarca = Ext.create('Ext.form.field.Tag',{
            name: 'elMarca',
            itemId: 'elMarca',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'marca', type: 'string' },
                    { name: 'idMarca', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listarmarca',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'marca',
            queryMode: 'local',
            displayField: 'marca',
            valueField: 'idMarca',
            emptyText: 'Marca',
            fieldLabel: 'Marcas',
            // labelWidth: 60,
            margin: '1 1 1 8',
            // padding: 1,
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagMarca.store.load(
            function(){
                elTagMarca.setDisabled(false);
            }
        );

        var elTagCurva = Ext.create('Ext.form.field.Tag',{
            name: 'elCurva',
            itemId: 'elCurva',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [
                    { name: 'idCurvaAbc', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listarcurvas',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'idCurvaAbc',
            queryMode: 'local',
            displayField: 'idCurvaAbc',
            valueField: 'idCurvaAbc',
            emptyText: 'Curva',
            fieldLabel: 'Curvas',
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagCurva.store.load(
            function(){
                elTagCurva.setDisabled(false);
            }
        );

        var elTagProduto = Ext.create('Ext.form.field.Tag',{
            name: 'elProduto',
            itemId: 'elProduto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [{ name: 'coditem' }, { name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listarprodutos',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'codItem',
            queryMode: 'remote',
            displayField: 'codItem',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{codItem} {descricao} {marca}',
                '</tpl>'), 
            valueField: 'codItem',
            // emptyText: 'Produto',
            fieldLabel: 'Código Produto NBS',
            emptyText: 'Código Produto NBS',
            // matchFieldWidth: false,
            // padding: 1,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',

            listeners: {
                
            },
            
            // allowBlank: false,
            listConfig: {
                loadingText: 'Carregando...',
                emptyText: '<div class="notificacao-red">Nenhuma produto encontrado!</div>',
                getInnerTpl: function() {
                    return '{[ values.codItem]} {[ values.descricao]} {[ values.marca]}';
                }
            }
        });

        var elTagDescProduto = Ext.create('Ext.form.field.Tag',{
            name: 'elDescProduto',
            itemId: 'elDescProduto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            minChars: 3,
            store: Ext.data.Store({
                fields: [{ name: 'idProduto' }, { name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listardescricaoprodutos',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'descricao',
            queryMode: 'remote',
            displayField: 'descricao',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{idProduto} {descricao} {marca}',
                '</tpl>'), 
            valueField: 'idProduto',
            fieldLabel: 'Descrição Produto',
            emptyText: 'Descrição Produto',
            // matchFieldWidth: false,
            // padding: 1,
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            enableKeyEvents: true,
            listeners: {
            },
            
            // allowBlank: false,
            listConfig: {
                loadingText: 'Carregando...',
                emptyText: '<div class="notificacao-red">Nenhuma produto encontrado!</div>',
                getInnerTpl: function() {
                    return '{[ values.idProduto]} {[ values.descricao]} {[ values.marca]}';
                }
            }
        });

        var elTagTabPreco = Ext.create('Ext.form.field.Tag',{
            name: 'eltagtabpreco',
            itemId: 'eltagtabpreco',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            minChars: 2,
            store: Ext.data.Store({
                fields: [{ name: 'codTabPreco' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listartabelapreco',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'codTabPreco',
            queryMode: 'remote',
            displayField: 'codTabPreco',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{codTabPreco}',
                '</tpl>'), 
            valueField: 'codTabPreco',
            // emptyText: 'Produto',
            fieldLabel: 'Código Table Preço',
            emptyText: 'Código Table Preço',
            // matchFieldWidth: false,
            // padding: 1,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',

            listeners: {
                
            },
            
            // allowBlank: false,
            listConfig: {
                loadingText: 'Carregando...',
                emptyText: '<div class="notificacao-red">Nenhuma produto encontrado!</div>',
                getInnerTpl: function() {
                    return '{[ values.codTabPreco]} {[ values.descricao]} {[ values.marca]}';
                }
            }
        });

        var elTpPrecificacao = Ext.create('Ext.form.field.Tag',{
            name: 'elTpPrecificacao',
            itemId: 'elTpPrecificacao',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [
                    { name: 'tipoPrecificacao', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listartipoprecificacao',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'tipoPrecificacao',
            queryMode: 'local',
            displayField: 'tipoPrecificacao',
            valueField: 'tipoPrecificacao',
            emptyText: 'Tipo Precificaçao',
            fieldLabel: 'Tipo Precificaçao',
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTpPrecificacao.store.load(
            function(){
                elTpPrecificacao.setDisabled(false);
            }
        );

        var elTagIdProduto = Ext.create('Ext.form.field.Tag',{
            name: 'eltagidproduto',
            itemId: 'eltagidproduto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            minChars: 2,
            store: Ext.data.Store({
                fields: [{ name: 'idProduto' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listaridproduto',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'idProduto',
            queryMode: 'remote',
            displayField: 'idProduto',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{idProduto}',
                '</tpl>'), 
            valueField: 'idProduto',
            // emptyText: 'Produto',
            fieldLabel: 'Código Produto Sankhya',
            emptyText: 'Código Produto Sankhya',
            // matchFieldWidth: false,
            // padding: 1,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',

            listeners: {
                
            },
            
            // allowBlank: false,
            listConfig: {
                loadingText: 'Carregando...',
                emptyText: '<div class="notificacao-red">Nenhuma produto encontrado!</div>',
                getInnerTpl: function() {
                    return '{[ values.idProduto]} {[ values.descricao]} {[ values.marca]}';
                }
            }
        });

        var elTagFaixaCusto = Ext.create('Ext.form.field.Tag',{
            name: 'eltagfaixacusto',
            itemId: 'eltagfaixacusto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'fxCusto', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listarfaixacusto',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'fxCusto',
            queryMode: 'local',
            displayField: 'fxCusto',
            valueField: 'fxCusto',
            emptyText: 'Faixa de Custo',
            fieldLabel: 'Faixa de Custo',
            // labelWidth: 60,
            margin: '1 1 1 8',
            // padding: 1,
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagFaixaCusto.store.load(
            function(){
                elTagFaixaCusto.setDisabled(false);
            }
        );

        var elTagGrupodesconto = Ext.create('Ext.form.field.Tag',{
            name: 'eltaggrupodesconto',
            itemId: 'eltaggrupodesconto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'grupoDesconto', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/acompanhamentovenda/listargrupodesconto',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'grupoDesconto',
            queryMode: 'local',
            displayField: 'grupoDesconto',
            valueField: 'grupoDesconto',
            emptyText: 'Grupo Desconto',
            fieldLabel: 'Grupo Desconto',
            // labelWidth: 60,
            margin: '1 1 1 8',
            // padding: 1,
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagGrupodesconto.store.load(
            function(){
                elTagGrupodesconto.setDisabled(false);
            }
        );

        var elSlidMargem = Ext.create('Ext.slider.Multi', {
            itemId: 'slidmargem',
            name: 'slidmargem',
            labelAlign: 'top',
            width: 230,
            margin: '1 1 1 8',
            values: [0, 80],
            increment: 1,
            minValue: 0,
            maxValue: 100,
            fieldLabel: 'Margem',
            valueField: 'slidmargem',
            // this defaults to true, setting to false allows the thumbs to pass each other
            constrainThumbs: false,
            // tipText: 'tipText'
        });

        var elEstoque = Ext.create('Ext.form.field.ComboBox',{
            name: 'elestoque',
            itemId: 'elestoque',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'elestoque', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"elestoque":"", "name":""},
                    {"elestoque":"Com", "name":"Com"},
                    {"elestoque":"Sem", "name":"Sem"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'elestoque',
            emptyText: '',
            fieldLabel: 'Estoque',
            labelWidth: 110,
            margin: '10 1 1 0',
            filterPickList: true,
            disabled: false
        });

        var elPreco = Ext.create('Ext.form.field.ComboBox',{
            name: 'elpreco',
            itemId: 'elpreco',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'elpreco', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"elpreco":"", "name":""},
                    {"elpreco":"Com", "name":"Com"},
                    {"elpreco":"Sem", "name":"Sem"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'elpreco',
            emptyText: '',
            fieldLabel: 'Preço Atual',
            labelWidth: 110,
            margin: '1 1 1 0',
            filterPickList: true,
            disabled: false
        });

        var elTabelaPreco = Ext.create('Ext.form.field.ComboBox',{
            name: 'eltabelapreco',
            itemId: 'eltabelapreco',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'eltabelapreco', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"eltabelapreco":"", "name":""},
                    {"eltabelapreco":"Com", "name":"Com"},
                    {"eltabelapreco":"Sem", "name":"Sem"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'eltabelapreco',
            emptyText: '',
            fieldLabel: 'Tabela Preço',
            labelWidth: 110,
            margin: '1 1 1 0',
            filterPickList: true,
            disabled: false
        });

        var elMargem = Ext.create('Ext.form.field.ComboBox',{
            name: 'elmargem',
            itemId: 'elmargem',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'elmargem', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"elmargem":"", "name":""},
                    {"elmargem":"Com", "name":"Com"},
                    {"elmargem":"Sem", "name":"Sem"},
                    {"elmargem":">10", "name":"MB > 10"},
                    {"elmargem":">5", "name":"MB > 5"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'elmargem',
            emptyText: '',
            fieldLabel: 'Margem',
            labelWidth: 110,
            margin: '1 1 1 0',
            filterPickList: true,
            disabled: false
        });

        var elTipoPrecificacao = Ext.create('Ext.form.field.ComboBox',{
            name: 'eltipoprecificacao',
            itemId: 'eltipoprecificacao',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'eltipoprecificacao', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"eltipoprecificacao":"", "name":""},
                    {"eltipoprecificacao":"Com", "name":"Com"},
                    {"eltipoprecificacao":"Sem", "name":"Sem"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'eltipoprecificacao',
            emptyText: '',
            fieldLabel: 'Tipo Precificação',
            labelWidth: 110,
            margin: '1 1 1 0',
            filterPickList: true,
            disabled: false
        });

        var elGrupoDesconto = Ext.create('Ext.form.field.ComboBox',{
            name: 'elgrupodesconto',
            itemId: 'elgrupodesconto',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'elgrupodesconto', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"elgrupodesconto":"", "name":""},
                    {"elgrupodesconto":"Com", "name":"Com"},
                    {"elgrupodesconto":"Sem", "name":"Sem"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'elgrupodesconto',
            emptyText: '',
            fieldLabel: 'Grupo Desconto',
            labelWidth: 110,
            margin: '1 1 1 0',
            filterPickList: true,
            disabled: false
        });

        var elCustoUnitario = Ext.create('Ext.form.field.ComboBox',{
            name: 'elcustounitario',
            itemId: 'elcustounitario',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'elcustounitario', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"elcustounitario":"", "name":""},
                    {"elcustounitario":"Com", "name":"Com"},
                    {"elcustounitario":"Sem", "name":"Sem"},
                    {"elcustounitario":"c<p", "name":"Custo < Preço"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'elcustounitario',
            emptyText: '',
            fieldLabel: 'Custo Unitário',
            labelWidth: 110,
            margin: '1 1 1 0',
            filterPickList: true,
            disabled: false
        });

        var elCustoUnitario = Ext.create('Ext.form.field.ComboBox',{
            name: 'elcustounitario',
            itemId: 'elcustounitario',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'elcustounitario', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"elcustounitario":"", "name":""},
                    {"elcustounitario":"Com", "name":"Com"},
                    {"elcustounitario":"Sem", "name":"Sem"},
                    {"elcustounitario":"c<p", "name":"Custo < Preço"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'elcustounitario',
            emptyText: '',
            fieldLabel: 'Custo Unitário',
            labelWidth: 110,
            margin: '1 1 1 0',
            filterPickList: true,
            disabled: false
        });
        
        var elparamMargem = Ext.create('Ext.form.field.ComboBox',{
            name: 'elparammargem',
            itemId: 'elparammargem',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'parametromargem', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"parametromargem":"", "name":""},
                    {"parametromargem":"Com", "name":"Com"},
                    {"parametromargem":"Sem", "name":"Sem"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'parametromargem',
            emptyText: '',
            fieldLabel: 'Param. Margem',
            labelWidth: 110,
            margin: '1 1 10 0',
            filterPickList: true,
            disabled: false
        });

        Ext.applyIf(me, {

            items : [
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        elTagEmpresa,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        fielDataInicio,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        fielDataFinal,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        elTagMarca,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        elTagCurva,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        elTagProduto,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        elTagDescProduto,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        elTagIdProduto,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        elTagTabPreco,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        elTpPrecificacao,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        elTagFaixaCusto,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items: [
                        elTagGrupodesconto,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 10 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items: [
                        elSlidMargem,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 10 1',
                            handler: function(form) {
                                form.up('panel').down('multislider').setValue([0,80]);
                            }
                        }
                    ]
                },
                {
                xtype:'fieldset',
                title: 'Com/Sem Parâmetro', // title or checkboxToggle creates fieldset header
                // columnWidth: 0.5,
                checkboxToggle: true,
                collapsed: true, // fieldset initially collapsed
                layout:'anchor',
                hidden: false,
                items :[
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            items:[
                                elEstoque,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '10 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            hidden: false,
                            items:[
                                elPreco,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '1 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            hidden: false,
                            items:[
                                elTabelaPreco,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '1 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            hidden: true,
                            items:[
                                elMargem,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '1 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            hidden: false,
                            items:[
                                elTipoPrecificacao,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '1 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            hidden: false,
                            items:[
                                elGrupoDesconto,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '1 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            hidden: true,
                            items:[
                                elCustoUnitario,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '1 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            hidden: false,
                            items:[
                                elparamMargem,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '1 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'toolbar',
                    width: '100%',
                    border: false,
                    items:[
                        '->',
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            text: 'Limpar Filtros',
                            tooltip: 'Limpar Filtros',
                            handler: function(form) {
                               
                                form.up('toolbar').up('panel').down('tagfield[name=elEmp]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elMarca]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elProduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagtabpreco]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagidproduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltaggrupodesconto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elTpPrecificacao]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagfaixacusto]').setValue(null);
                                form.up('toolbar').up('panel').down('multislider[name=slidmargem]').setValue([0,80]);
                                form.up('toolbar').up('panel').down('fieldset').setCollapsed(true);
                            }
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        if(me.up('container').down('#bprecofiltro').hidden){
            me.up('container').down('#bprecofiltro').setHidden(false);
        }else{
            me.up('container').down('#bprecofiltro').setHidden(true);
        }

    },

    onBtnConsultar: function(btn){
        var me = this.up('toolbar');

    }

});
