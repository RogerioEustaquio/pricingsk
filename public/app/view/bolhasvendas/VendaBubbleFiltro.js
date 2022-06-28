Ext.define('App.view.bolhasvendas.VendaBubbleFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'vendasbubblefiltro',
    itemId: 'vendasbubblefiltro',
    title: 'Filtro',
    region: 'west',
    width: 220,
    hidden: false,
    scrollable: true,
    layout: 'vbox',
    requires:[
    ],

    constructor: function() {
        var me = this;

        var dataPosicionamento = [
            {"id":1, "name":"Filial"},
            {"id":2, "name":"Marca"},
            {"id":3, "name":"Categoria"},
            {"id":4, "name":"Produto"}
        ];

        var elPosicionamento = Ext.create('Ext.form.field.ComboBox',{
            name: 'elposicionamento',
            itemId: 'elposicionamento',
            multiSelect: false,
            labelAlign: 'top',
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [
                    { name: 'name', type: 'string' },
                    { name: 'id', type: 'number' }
                ],
                data: dataPosicionamento
            }),
            queryParam: 'name',
            queryMode: 'local',
            displayField: 'name',
            valueField: 'id',
            emptyText: 'Posicionamento',
            fieldLabel: 'Posicionamento',
            filterPickList: true,
            publishes: 'value',
            disabled: false,
            listeners : {
            }
        });
        
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
                    url: BASEURL + '/api/bolhasvendas/listarEmpresas',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'emp',
            queryMode: 'local',
            displayField: 'emp',
            valueField: 'idEmpresa',
            emptyText: 'Empresa',
            fieldLabel: 'Empresas',
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
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
                    url: BASEURL + '/api/bolhasvendas/listarregional',
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

        var fielDataInicio = Ext.create('Ext.form.field.Date',{
            name: 'datainicio',
            itemId: 'datainicio',
            labelAlign: 'top',
            fieldLabel: 'Data Inícial',
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
            format: 'd/m/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            // value: sysdate
        });

        var fielDataFim = Ext.create('Ext.form.field.Date',{
            name: 'datafim',
            itemId: 'datafim',
            labelAlign: 'top',
            fieldLabel: 'Data Final',
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
            format: 'd/m/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            // value: sysdate
        });

        var elTagMarca = Ext.create('Ext.form.field.Tag',{
            name: 'elmarca',
            itemId: 'elmarca',
            multiSelect: true,
            labelAlign: 'top',
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [
                    { name: 'marca', type: 'string' },
                    { name: 'idMarca', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/bolhasvendas/listarmarca',
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
            valueField: 'marca',
            emptyText: 'Marca',
            fieldLabel: 'Marcas',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagMarca.store.load(
            function(){
                elTagMarca.setDisabled(false);
            }
        );

        var elTagIdProduto = Ext.create('Ext.form.field.Tag',{
            name: 'eltagidproduto',
            itemId: 'eltagidproduto',
            multiSelect: true,
            labelAlign: 'top',
            margin: '1 1 1 1',
            padding: 1,
            width: 180,
            labelWidth: 60,
            minChars: 2,
            store: Ext.data.Store({
                fields: [{ name: 'idProduto' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/bolhasvendas/listaridproduto',
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

        var elTagCategoria = Ext.create('Ext.form.field.Tag',{
            name: 'elcategoria',
            itemId: 'elcategoria',
            multiSelect: true,
            labelAlign: 'top',
            store: Ext.data.Store({
                fields: [
                    { name: 'categoria', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/bolhasvendas/listarcategoria',
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
            padding: 1,
            width: 180,
            labelWidth: 60,
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
                        elPosicionamento,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
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
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        fielDataFim,
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
                                // form.up('toolbar').up('panel').down('tagfield[name=filialelmarca]').setValue(null);
                                var filtromarca = form.up('toolbar').up('panel');

                                filtromarca.down('#elposicionamento').setValue(null);
                                filtromarca.down('#elEmp').setValue(null);
                                filtromarca.down('#elregional').setValue(null);
                                filtromarca.down('datefield[name=datainicio]').setValue(null);
                                filtromarca.down('datefield[name=datafim]').setValue(null);
                                filtromarca.down('#elmarca').setValue(null);
                                filtromarca.down('#eltagidproduto').setValue(null);
                                filtromarca.down('#elcategoria').setValue(null);
                            }
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);

    }

});
