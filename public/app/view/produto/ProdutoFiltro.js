Ext.define('App.view.produto.ProdutoFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'produtofiltro',
    itemId: 'produtofiltro',
    title: 'Filtro',
    region: 'west',
    width: 290,
    hidden: true,
    scrollable: true,
    layout: 'vbox',
    requires:[
    ],

    initComponent: function() {
        var me = this;

        var fielDataInicio = Ext.create('Ext.form.field.Date',{
            name: 'datainicio',
            itemId: 'datainicio',
            labelAlign: 'top',
            fieldLabel: 'Data Cadastro Início',
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
            fieldLabel: 'Data Cadastro Final',
            margin: '1 1 1 8',
            padding: 1,
            width: 230,
            labelWidth: 60,
            format: 'd/m/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            // value: sysdate
        });

        var btnNotMarca = Ext.create('Ext.Button', {
            name: 'notmarca',
            itemId: 'notmarca',
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
                    url: BASEURL + '/api/produto/listarmarca',
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
                    url: BASEURL + '/api/produto/listarprodutos',
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
            fieldLabel: 'Produtos',
            emptyText: 'Código Item NBS',
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
                    url: BASEURL + '/api/produto/listaridproduto',
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
            fieldLabel: 'Código Produto',
            emptyText: 'Código Produto',
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

        Ext.applyIf(me, {

            items : [
                
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
                                form.up('panel').down('datefield').setValue(null);
                            }
                        }
                    ]
                },{
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
                                form.up('panel').down('datefield').setValue(null);
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
                               
                                form.up('toolbar').up('panel').down('button[name=notmarca]').value = 0;
                                form.up('toolbar').up('panel').down('tagfield[name=elMarca]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elProduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagidproduto]').setValue(null);
                            }
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);

    }

});
