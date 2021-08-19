Ext.define('App.view.basepreco.BprecoFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'bprecofiltro',
    itemId: 'bprecofiltro',
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
                    url: BASEURL + '/api/analisegrafico/listarEmpresas',
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
        
        var btnNotMarca = Ext.create('Ext.Button', {
            name: 'notMarca',
            itemId: 'notMarca',
            hidden: true
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
                    url: BASEURL + '/api/basepreco/listarmarca',
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
                    url: BASEURL + '/api/basepreco/listarprodutos',
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
                    url: BASEURL + '/api/basepreco/listartabelapreco',
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
                    url: BASEURL + '/api/basepreco/listaridproduto',
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
                    url: BASEURL + '/api/basepreco/listargrupodesconto',
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
            fieldLabel: 'Preço',
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
                        btnNotMarca,
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
                    items:[
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
                xtype:'fieldset',
                title: 'Com/Sem Parâmetro', // title or checkboxToggle creates fieldset header
                // columnWidth: 0.5,
                checkboxToggle: true,
                collapsed: true, // fieldset initially collapsed
                layout:'anchor',
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
                        },,
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
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
                                form.up('toolbar').up('panel').down('button[name=notMarca]').value = 0;
                                form.up('toolbar').up('panel').down('tagfield[name=elMarca]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elProduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagtabpreco]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagidproduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltaggrupodesconto]').setValue(null);
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
