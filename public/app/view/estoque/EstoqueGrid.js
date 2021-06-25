Ext.define('App.view.estoque.EstoqueGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'estoquegrid',
    itemId: 'estoquegrid',
    columnLines: true,
    margin: '1 1 1 1',
    requires: [
        'Ext.toolbar.Paging',
        'Ext.ux.util.Format'
    ],
    bbar: {
        xtype: 'pagingtoolbar',
        displayInfo: true,
        displayMsg: 'Exibindo solicitações {0} - {1} de {2}',
        inputItemWidth : 40,
        emptyMsg: "Não há solicitações a serem exibidos"
    },
    constructor: function() {
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');

        Ext.applyIf(me, {
    
            store: Ext.create('Ext.data.Store', {
                        model: Ext.create('Ext.data.Model', {
                        fields:[{name:'codEmpresa', type: 'number'},
                                {name:'nomeEmpresa', type: 'string'},
                                {name:'codProduto', type: 'number'},
                                {name:'descricaoProduto', type: 'string'},
                                {name:'codMarca', type: 'number'},
                                {name:'descricaoMarca', type: 'string'},
                                {name:'estoque', type: 'string'},
                                {name:'custoMedio', type: 'number'},
                                {name:'valor', type: 'number'},
                                {name:'custoOperacao', type: 'number'},
                                {name:'pis', type: 'number'},
                                {name:'cofins', type: 'number'},
                                {name:'icms', type: 'number'},
                                {name:'curva', type: 'string'},
                                {name:'clientes', type: 'string'},
                            ]
                        }),
                        pageSize: 50,
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/estoque/listarestoque',
                            encode: true,
                            timeout: 240000,
                            format: 'json',
                            reader: {
                                type: 'json',
                                rootProperty: 'data',
                                totalProperty: 'total'
                            }
                        }
            }),
            columns: [
                {
                    text: 'Cód. Filial',
                    dataIndex: 'codEmpresa',
                    width: 80,
                    hidden: true
                },
                {
                    text: 'Filial',
                    dataIndex: 'nomeEmpresa',
                    width: 68,
                    align: 'right'
                },
                {
                    text: 'Cód. Produto',
                    dataIndex: 'codProduto',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'Descrição',
                    dataIndex: 'descricaoProduto',
                    width: 120,
                    align: 'right'
                },
                {
                    text: 'Cód. Marca',
                    dataIndex: 'codMarca',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'Descrição Marca',
                    dataIndex: 'descricaoMarca',
                    width: 130,
                    align: 'right'
                },
                {
                    text: 'Estoque',
                    dataIndex: 'estoque',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    }
                },
                {
                    text: 'Custo Médio',
                    dataIndex: 'custoMedio',
                    width: 110,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Valor',
                    dataIndex: 'valor',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Custo Operação',
                    dataIndex: 'custoOperacao',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'PIS',
                    dataIndex: 'pis',
                    width: 60,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'COFINS',
                    dataIndex: 'cofins',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'ICMS',
                    dataIndex: 'icms',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Curva',
                    dataIndex: 'curva',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Cliente',
                    dataIndex: 'cliente',
                    // width: 90,
                    minWidth: 70,
                    flex: 1,
                    // align: 'right'
                }
               
            ]
        });

        me.callParent(arguments);

    }
});
