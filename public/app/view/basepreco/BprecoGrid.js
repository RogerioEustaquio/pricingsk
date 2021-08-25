Ext.define('App.view.basepreco.BprecoGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'bprecogrid',
    itemId: 'bprecogrid',
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
                                {name:'filial', type: 'string'},
                                {name:'codTabPreco', type: 'string'},
                                {name:'nomeTabPreco', type: 'string'},
                                {name:'nmTabPreco', type: 'string'},
                                {name:'dtVigor', type: 'string' },
                                {name:'preco', type: 'number' },
                                {name:'codProduto', type: 'number' },
                                {name:'descricao', type: 'string' },
                                {name:'marca', type: 'string' },
                                {name:'codItemNbs', type: 'string' },
                                {name:'mb', type: 'number' },
                                {name:'tipoPrecificacao', type: 'string' },
                                {name:'grupoDesconto', type: 'string' },
                                {name:'estoque', type: 'number' },
                                {name:'custoMedio', type: 'number' },
                                {name:'valorEstoque', type: 'number' },
                                {name:'custoOpe', type: 'number' },
                                {name:'pisCofins', type: 'number' },
                                {name:'icms', type: 'number' }
                            ]
                        }),
                        pageSize: 50,
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/basepreco/listarpreco',
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
                    text: 'Cód. Tab. Preço',
                    dataIndex: 'codTabPreco',
                    width: 120,
                    hidden: true
                },
                {
                    text: 'Tabela Preço',
                    dataIndex: 'nomeTabPreco',
                    width: 200,
                    align: 'right',
                    hidden: true
                },
                {
                    text: 'Vigor',
                    dataIndex: 'dtVigor',
                    width: 90,
                    align: 'right'
                },
                {
                    text: 'Preço',
                    dataIndex: 'preco',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Id Produto',
                    dataIndex: 'codProduto',
                    width: 90,
                    hidden: false
                },
                {
                    text: 'Produto',
                    dataIndex: 'descricao',
                    width: 120,
                    // align: 'right'
                },
                {
                    text: 'Marca',
                    dataIndex: 'marca',
                    width: 110,
                    // align: 'right'
                },
                {
                    text: 'Cód. Item NBS',
                    dataIndex: 'codItemNbs',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'MB',
                    dataIndex: 'mb',
                    width: 74,
                    align: 'right',
                    renderer: function (v) {

                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Tipo Precificação',
                    dataIndex: 'tipoPrecificacao',
                    width: 150,
                    align: 'right'
                },
                {
                    text: 'Grupo Desconto',
                    dataIndex: 'grupoDesconto',
                    width: 130,
                    align: 'right'
                },
                {
                    text: 'Estoque',
                    dataIndex: 'estoque',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Custo Unitário',
                    dataIndex: 'custoMedio',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Valor Estoque',
                    dataIndex: 'valorEstoque',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Custo Operacional',
                    dataIndex: 'custoOpe',
                    width: 150,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Pis Cofins',
                    dataIndex: 'pisCofins',
                    width: 110,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'ICMS',
                    dataIndex: 'icms',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                }
            ],
            // features: [
            //     {
            //         groupHeaderTpl: "{name} | Total: {[values.rows[0].data.valorNota]}",
            //         ftype: 'groupingsummary'
            //     }
            // ]
        });

        me.callParent(arguments);

    }
});
