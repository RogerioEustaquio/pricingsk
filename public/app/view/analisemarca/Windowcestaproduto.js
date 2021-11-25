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
            idEmpresa:2
        };

        var myStore = Ext.create('Ext.data.Store', {
            model: Ext.create('Ext.data.Model', {
                    fields:[{name:'idEmpresa',mapping:'idEmpresa'},
                            {name:'emp',mapping:'emp'},
                            {name:'codItem',mapping:'codItem'},
                            {name:'descricao',mapping:'descricao'},
                            {name:'marca',mapping:'marca'},
                            {name:'estoque',mapping:'estoque'},
                            {name:'qtdePendente',mapping:'qtdePendente'},
                            {name:'qtdeTotal_12m',mapping:'qtdeTotal_12m'},
                            {name:'qtdeTotal_6m',mapping:'qtdeTotal_6m'},
                            {name:'qtdeTotal_3m',mapping:'qtdeTotal_3m'},
                            {name:'med_12m',mapping:'med_12m', type: 'float'},
                            {name:'med_6m',mapping:'med_6m', type: 'float'},
                            {name:'med_3m',mapping:'med_3m', type: 'float'}
                           ]
            }),
            proxy: {
                type: 'ajax',
                url : BASEURL + '/api/vp/listaritenscategorias',
                timeout: 240000,
                extraParams: params,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            autoLoad : true
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
                            items: [
                                {
                                    xtype:'displayfield',
                                    value: 'teste'
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
                                            text: 'Emp',
                                            dataIndex: 'emp',
                                            width: 52
                                        },
                                        {
                                            text: 'Código',
                                            dataIndex: 'codItem',
                                            width: 120,
                                            hidden: false
                                        },
                                        {
                                            text: 'Descrição',
                                            dataIndex: 'descricao',
                                            flex: 1,
                                            minWidth: 100
                                        },
                                        {
                                            text: 'Marca',
                                            dataIndex: 'marca',
                                            width: 140
                                        },
                                        {
                                            text: 'Estoque',
                                            dataIndex: 'estoque',
                                            width: 80
                                        },
                                        {
                                            text: 'Qtde Pendente',
                                            dataIndex: 'qtdePendente',
                                            width: 114,
                                            hidden: false
                                        },
                                        {
                                            text: 'Qtde 12M',
                                            dataIndex: 'qtdeTotal_12m',
                                            width: 80,
                                            hidden: false
                                        },
                                        {
                                            text: 'Qtde 6M',
                                            dataIndex: 'qtdeTotal_6m',
                                            width: 80,
                                            hidden: false
                                        },
                                        {
                                            text: 'Qtde 3M',
                                            dataIndex: 'qtdeTotal_3m',
                                            width: 80,
                                            hidden: false
                                        },
                                        {
                                            text: 'Méd. 12M',
                                            dataIndex: 'med_12m',
                                            width: 80,
                                            hidden: false,
                                            renderer: function (v) {
                                                return me.Value(v);
                                            }
                                        },
                                        {
                                            text: 'Méd. 6M',
                                            dataIndex: 'med_6m',
                                            width: 80,
                                            hidden: false,
                                            renderer: function (v) {
                                                return me.Value(v);
                                            }
                                        },
                                        {
                                            text: 'Méd. 3M',
                                            dataIndex: 'med_3m',
                                            width: 80,
                                            hidden: false,
                                            renderer: function (v) {
                                                return me.Value(v);
                                            }
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
