Ext.define('App.view.analiseperformance.ExploreFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'explorefiltro',
    itemId: 'explorefiltro',
    title: 'Filtro',
    region: 'west',
    width: 220,
    hidden: true,
    scrollable: true,
    layout: 'vbox',
    requires:[
    ],

    initComponent: function() {
        var me = this;

        var elTagEmpresa = Ext.create('Ext.form.field.Tag',{
            name: 'elfilial',
            itemId: 'elfilial',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [
                    { name: 'emp', type: 'string' },
                    { name: 'idEmpresa', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/explore/listarEmpresas',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            width: 180,
            queryParam: 'emp',
            queryMode: 'local',
            displayField: 'emp',
            valueField: 'idEmpresa',
            emptyText: 'Filial',
            fieldLabel: 'Filiais',
            labelWidth: 60,
            margin: '1 1 1 1',
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

        var elTagRegional = Ext.create('Ext.form.field.Tag',{
            name: 'elregional',
            itemId: 'elregional',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [
                    { name: 'id', type: 'string' },
                    { name: 'regional', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/explore/listarregional',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
            queryParam: 'regional',
            queryMode: 'local',
            displayField: 'regional',
            valueField: 'id',
            emptyText: 'Regional',
            fieldLabel: 'Regional',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        elTagRegional.store.load(
            function(){
                elTagRegional.setDisabled(false);
            }
        );


        var mes =  (new Date().getUTCMonth() +1 );
        mes = mes < 10 ? '0'+mes : mes ;
        var dataIni = mes +'/' + new Date().getFullYear();
        var fielDatareferencia = Ext.create('Ext.form.field.Date',{
            name: 'datareferencia',
            itemId: 'datareferencia',
            labelAlign: 'top',
            fieldLabel: 'Data Referência',
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
            format: 'm/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            value: dataIni
        });

        var elTagIdProduto = Ext.create('Ext.form.field.Tag',{
            name: 'elproduto',
            itemId: 'elproduto',
            multiSelect: true,
            labelAlign: 'top',
            width: 180,
            labelWidth: 60,
            minChars: 2,
            store: Ext.data.Store({
                fields: [{ name: 'idProduto' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/explore/listaridproduto',
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
            fieldLabel: 'Código Produto',
            emptyText: 'Código Produto',
            margin: '1 1 1 1',
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
                    return '{[ values.idProduto]} {[ values.descricao]}';
                }
            }
        });

        var elTagMarca = Ext.create('Ext.form.field.Tag',{
            name: 'elmarca',
            itemId: 'elmarca',
            multiSelect: true,
            labelAlign: 'top',
            width: 180,
            store: Ext.data.Store({
                fields: [
                    { name: 'marca', type: 'string' },
                    { name: 'idMarca', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/explore/listarmarca',
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
            margin: '1 1 1 1',
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
        
        var elTagCategoria = Ext.create('Ext.form.field.Tag',{
            name: 'elcategoria',
            itemId: 'elcategoria',
            multiSelect: true,
            labelAlign: 'top',
            width: 180,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [
                    { name: 'categoria', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/explore/listarcategoria',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'categoria',
            queryMode: 'local',
            displayField: 'categoria',
            valueField: 'categoria',
            emptyText: 'Categoria',
            fieldLabel: 'Categorias',
            margin: '1 1 1 1',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagCategoria.store.load(
            function(){
                elTagCategoria.setDisabled(false);
            }
        );

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
                        elTagRegional,
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
                        fielDatareferencia,
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
                        elTagCategoria,
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
                                form.up('toolbar').up('panel').down('tagfield[name=elfilial]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elregional]').setValue(null);
                                form.up('toolbar').up('panel').down('datefield[name=datareferencia]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elproduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elmarca]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elcategoria]').setValue(null);
                            }
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);

    },


});
