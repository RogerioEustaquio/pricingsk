Ext.define('App.view.analisemarca.Windowcestaproduto', {
    extend: 'Ext.window.Window',
    xtype: 'windowcestaproduto',
    itemId: 'windowcestaproduto',
    height: Ext.getBody().getHeight() * 0.9,
    width: Ext.getBody().getWidth() * 0.9,
    title: 'Cesta de Produtos',
    requires:[

    ],
    layout: 'fit',
    vpItem: null,

    initComponent: function() {
        var me = this;

        var params = {
            // idEmpresa:2
        };

        var myStore = Ext.create('Ext.data.Store', {
            model: Ext.create('Ext.data.Model', {
                    fields:[{name:'data',mapping:'data'},
                            {name:'codemp',mapping:'codemp'},
                            {name:'emp',mapping:'emp'},
                            {name:'codprod',mapping:'codprod'},
                            {name:'descricao',mapping:'descricao'},
                            {name:'precoAtual',mapping:'precoAtual', type: 'float'},
                            {name:'precoSugerido',mapping:'precoSugerido', type: 'float'},
                            {name:'alterado',mapping:'alterado'},
                            {name:'dataAlteracao',mapping:'dataAlteracao'}
                           ]
            }),
            proxy: {
                type: 'ajax',
                url : BASEURL + '/api/analisemarca/listarcestadeprodutos',
                timeout: 240000,
                extraParams: params,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            autoLoad : false
        });

        var tagEmpresa = Ext.create('Ext.form.field.Tag',{
            name: 'tagempresa',
            itemId: 'tagempresa',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [
                    { name: 'emp', type: 'string' },
                    { name: 'idEmpresa', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/analisemarca/listarEmpresas',
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
            valueField: 'emp',
            emptyText: 'Empresa',
            fieldLabel: 'Empresas',
            labelWidth: 60,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        tagEmpresa.store.load(
            function(){
                tagEmpresa.setDisabled(false);
            }
        );

        var filtrocesta = Ext.create('Ext.form.field.Tag',{
            name: 'filtrocesta',
            itemId: 'filtrocesta',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [{ name: 'codcesta' }, { name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/analisemarca/listarcesta',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'codcesta',
            queryMode: 'remote',
            displayField: 'codcesta',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{codcesta} {descricao}',
                '</tpl>'), 
            valueField: 'codcesta',
            fieldLabel: 'Cesta de Produtos',
            emptyText: 'Mês/Ano',
            margin: '1 1 1 8',
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

        
        var tagProduto = Ext.create('Ext.form.field.Tag',{
            name: 'tagproduto',
            itemId: 'tagproduto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            minChars: 2,
            store: Ext.data.Store({
                fields: [{ name: 'idProduto' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/analisemarca/listaridproduto',
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

        Ext.applyIf(me, {
            
            items:[
                {
                    xtype:'container',
                    layout: 'border',
                    margin: '0 0 0 0',
                    items:[
                        {
                            xtype : 'form',
                            region: 'north',
                            layout: 'hbox',
                            items: [
                                tagEmpresa,
                                filtrocesta,
                                {
                                    xtype: 'textfield',
                                    name: 'descproduto',
                                    fieldLabel: 'Produto',
                                    labelAlign: 'top',
                                    emptyText: 'Descrição',
                                    labelWidth: 60,
                                    width: 300,
                                    margin: '1 2 4 1'
                                },
                                tagProduto,
                                {
                                    xtype:'button',
                                    iconCls: 'fa fa-search',
                                    tooltip: 'Consultar',
                                    margin: '26 2 4 4',
                                    hidden: false,
                                    handler: function(){

                                        var emp         = me.down('tagfield[name=tagempresa]').getValue();
                                        var dataCesta   = me.down('tagfield[name=filtrocesta]').getValue();
                                        var descProduto = me.down('textfield[name=descproduto]').getValue();
                                        var codproduto  = me.down('tagfield[name=tagproduto]').getValue();

                                        descProduto = descProduto.toLowerCase();                                                         
                                        descProduto = descProduto.replace(new RegExp('[ÁÀÂÃ]','gi'), 'a');
                                        descProduto = descProduto.replace(new RegExp('[ÉÈÊ]','gi'), 'e');
                                        descProduto = descProduto.replace(new RegExp('[ÍÌÎ]','gi'), 'i');
                                        descProduto = descProduto.replace(new RegExp('[ÓÒÔÕ]','gi'), 'o');
                                        descProduto = descProduto.replace(new RegExp('[ÚÙÛ]','gi'), 'u');
                                        descProduto = descProduto.replace(new RegExp('[Ç]','gi'), 'c');

                                        var params = {
                                            emp: Ext.encode(emp),
                                            datacesta : Ext.encode(dataCesta),
                                            descproduto : descProduto,
                                            codproduto : Ext.encode(codproduto)
                                        };

                                        me.down('grid').getStore().getProxy().setExtraParams(params);
                                        me.down('grid').getStore().load();
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'form',
                            region: 'center',
                            border: false,
                            scrollable: true,
                            // padding: 5,
                            bodyPadding: '5 5 5 5',
                            items: [
                                {
                                    xtype: 'grid',
                                    store: myStore,
                                    minHeight: 80,
                                    columns:[
                                        {
                                            text: 'Data',
                                            dataIndex: 'data',
                                            width: 100
                                        },
                                        {
                                            text: 'Cod. Emp',
                                            dataIndex: 'codemp',
                                            width: 80,
                                            hidden: false
                                        },
                                        {
                                            text: 'Emp',
                                            dataIndex: 'emp',
                                            width: 60
                                        },
                                        {
                                            text: 'Cod. Produto',
                                            dataIndex: 'codprod',
                                            width: 140
                                        },
                                        {
                                            text: 'Descrição',
                                            dataIndex: 'descricao',
                                            flex: 1,
                                            minWidth: 80
                                        },
                                        {
                                            text: 'Preço Atual',
                                            dataIndex: 'precoAtual',
                                            width: 120,
                                            hidden: false,
                                            renderer: function (v) {
                                                return me.Value(v);
                                            }
                                        },
                                        {
                                            text: 'Preço Sugerido',
                                            dataIndex: 'precoSugerido',
                                            width: 140,
                                            hidden: false,
                                            renderer: function (v) {
                                                return me.Value(v);
                                            }
                                        },
                                        {
                                            text: 'Alterado',
                                            dataIndex: 'alterado',
                                            width: 60,
                                            hidden: true
                                        },
                                        {
                                            text: 'Data Alteração',
                                            dataIndex: 'dataAlteracao',
                                            width: 120,
                                            hidden: true
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]

        });

        me.callParent(arguments);

    },

    onConcluirClick: function(btn){
        var me = btn.up('window'),
            notyType = 'success',
            notyText = 'Solicitação concluída com sucesso!';

        var param = {
            idEmpresa: 2
        };

        me.setLoading({msg: '<b>Salvando os dados...</b>'});

        Ext.Ajax.request({
            url : BASEURL + '/api/vp/concluir',
            method: 'POST',
            params: param,
            success: function (response) {
                var result = Ext.decode(response.responseText);

                if(!result.success){
                    notyType = 'error';
                    notyText = result.message;
                    window.setLoading(false);
                }

                me.noty(notyType, notyText);

                if(result.success){

                    // Ext.GlobalEvents.fireEvent('vpsolicitacaoconcluida', {
                    //     idEmpresa: me.vpItem.idEmpresa,
                    //     idVendaPerdida: me.vpItem.idVendaPerdida
                    // });
                    
                    me.close();
                }
            }
        });
    },

    noty: function(notyType, notyText){
        new Noty({
            theme: 'relax',
            layout: 'bottomRight',
            type: notyType,
            timeout: 3000,
            text: notyText
        }).show();
    },

    Value: function(v) {
        var val = '';
        if (v) {
            val = Ext.util.Format.currency(v, ' ', 4, false);
        } else {
            val = 0;
        }
        return val;
    }

});
