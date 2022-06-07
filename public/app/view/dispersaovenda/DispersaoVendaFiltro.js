Ext.define('App.view.dispersaovenda.DispersaoVendaFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'dispersaovendafiltro',
    itemId: 'dispersaovendafiltro',
    title: 'Filtro',
    region: 'west',
    width: 220,
    hidden: false,
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
                    url: BASEURL + '/api/dispersaovenda/listarEmpresas',
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

        var fielDataInicio = Ext.create('Ext.form.field.Date',{
            name: 'eldatainicio',
            itemId: 'eldatainicio',
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
            name: 'eldatafim',
            itemId: 'eldatafim',
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

        // var elTagMarca = Ext.create('Ext.form.field.Tag',{
        //     name: 'climarca',
        //     itemId: 'climarca',
        //     multiSelect: true,
        //     labelAlign: 'top',
        //     width: 180,
        //     store: Ext.data.Store({
        //         fields: [
        //             { name: 'marca', type: 'string' },
        //             { name: 'idMarca', type: 'string' }
        //         ],
        //         proxy: {
        //             type: 'ajax',
        //             url: BASEURL + '/api/dispersaovenda/listarmarca',
        //             timeout: 120000,
        //             reader: {
        //                 type: 'json',
        //                 root: 'data'
        //             }
        //         }
        //     }),
        //     queryParam: 'marca',
        //     queryMode: 'local',
        //     displayField: 'marca',
        //     valueField: 'idMarca',
        //     emptyText: 'Marca',
        //     fieldLabel: 'Marcas',
        //     // labelWidth: 60,
        //     margin: '1 1 1 1',
        //     // padding: 1,
        //     plugins:'dragdroptag',
        //     filterPickList: true,
        //     publishes: 'value',
        //     disabled: true
        // });
        // elTagMarca.store.load(
        //     function(){
        //         elTagMarca.setDisabled(false);
        //     }
        // );

        // var elTagProduto = Ext.create('Ext.form.field.Tag',{
        //     name: 'cliproduto',
        //     itemId: 'cliproduto',
        //     multiSelect: true,
        //     labelAlign: 'top',
        //     width: 180,
        //     labelWidth: 60,
        //     store: Ext.data.Store({
        //         fields: [{ name: 'coditem' }, { name: 'descricao' }],
        //         proxy: {
        //             type: 'ajax',
        //             url: BASEURL + '/api/dispersaovenda/listarprodutos',
        //             reader: { type: 'json', root: 'data' },
        //             extraParams: { tipoSql: 0}
        //         }
        //     }),
        //     queryParam: 'codItem',
        //     queryMode: 'remote',
        //     displayField: 'codItem',
        //     displayTpl: Ext.create('Ext.XTemplate',
        //         '<tpl for=".">',		                            
        //         '{codItem} {descricao} {marca}',
        //         '</tpl>'), 
        //     valueField: 'codItem',
        //     // emptyText: 'Produto',
        //     fieldLabel: 'Produtos',
        //     emptyText: 'Código do produto',
        //     // matchFieldWidth: false,
        //     // padding: 1,
        //     margin: '1 1 1 1',
        //     plugins:'dragdroptag',
        //     filterPickList: true,
        //     publishes: 'value',

        //     listeners: {
                
        //     },
            
        //     // allowBlank: false,
        //     listConfig: {
        //         loadingText: 'Carregando...',
        //         emptyText: '<div class="notificacao-red">Nenhuma produto encontrado!</div>',
        //         getInnerTpl: function() {
        //             return '{[ values.codItem]} {[ values.descricao]} {[ values.marca]}';
        //         }
        //     }
        // });

        // var elTagCliente = Ext.create('Ext.form.field.Tag',{
        //     name: 'cliente',
        //     itemId: 'cliente',
        //     multiSelect: true,
        //     labelAlign: 'top',
        //     width: 180,
        //     labelWidth: 60,
        //     store: Ext.data.Store({
        //         fields: [{ name: 'idPessoa' }, { name: 'descricao' }],
        //         proxy: {
        //             type: 'ajax',
        //             url: BASEURL + '/api/dispersaovenda/listarclientes',
        //             reader: { type: 'json', root: 'data' },
        //             extraParams: { tipoSql: 0}
        //         }
        //     }),
        //     queryParam: 'idPessoa',
        //     queryMode: 'remote',
        //     displayField: 'idPessoa',
        //     displayTpl: Ext.create('Ext.XTemplate',
        //         '<tpl for=".">',		                            
        //         '{idPessoa} {descricao}',
        //         '</tpl>'), 
        //     valueField: 'idPessoa',
        //     // emptyText: 'Produto',
        //     fieldLabel: 'Clientes',
        //     emptyText: 'CNPJ/CPF/NOME',
        //     // matchFieldWidth: false,
        //     // padding: 1,
        //     margin: '1 1 1 1',
        //     plugins:'dragdroptag',
        //     filterPickList: true,
        //     publishes: 'value',

        //     listeners: {
                
        //     },
            
        //     // allowBlank: false,
        //     listConfig: {
        //         loadingText: 'Carregando...',
        //         emptyText: '<div class="notificacao-red">Nenhuma Cliente encontrado!</div>',
        //         getInnerTpl: function() {
        //             return '{[ values.idPessoa]} {[ values.descricao]}';
        //         }
        //     }
        // });

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
                // {
                //     xtype: 'panel',
                //     layout: 'hbox',
                //     border: false,
                //     items:[
                //         elTagMarca,
                //         {
                //             xtype: 'button',
                //             iconCls: 'fa fa-file',
                //             tooltip: 'Limpar',
                //             margin: '26 1 1 1',
                //             handler: function(form) {
                //                 form.up('panel').down('tagfield').setValue(null);
                //             }
                //         }
                //     ]
                // },
                // {
                //     xtype: 'panel',
                //     layout: 'hbox',
                //     border: false,
                //     items:[
                //         elTagProduto,
                //         {
                //             xtype: 'button',
                //             iconCls: 'fa fa-file',
                //             tooltip: 'Limpar',
                //             margin: '26 1 1 1',
                //             handler: function(form) {
                //                 form.up('panel').down('tagfield').setValue(null);
                //             }
                //         }
                //     ]
                // },
                // {
                //     xtype: 'panel',
                //     layout: 'hbox',
                //     border: false,
                //     items:[
                //         elTagCliente,
                //         {
                //             xtype: 'button',
                //             iconCls: 'fa fa-file',
                //             tooltip: 'Limpar',
                //             margin: '26 1 1 1',
                //             handler: function(form) {
                //                 form.up('panel').down('tagfield').setValue(null);
                //             }
                //         }
                //     ]
                // },
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
                                form.up('toolbar').up('panel').down('datefield[name=eldatainicio]').setValue(null);
                                form.up('toolbar').up('panel').down('datefield[name=eldatafim]').setValue(null);

                                // form.up('toolbar').up('panel').down('tagfield[name=climarca]').setValue(null);
                                // form.up('toolbar').up('panel').down('tagfield[name=cliproduto]').setValue(null);
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

        // if(me.up('container').down('#panelwest').hidden){
        //     me.up('container').down('#panelwest').setHidden(false);
        // }else{
        //     me.up('container').down('#panelwest').setHidden(true);
        // }

    },

    onBtnConsultar: function(btn){
        var me = this.up('toolbar');

    }

});
