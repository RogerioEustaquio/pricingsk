Ext.define('App.view.basepreco.GridPreco', {
    extend: 'Ext.grid.Panel',
    xtype: 'gridpreco',
    itemId: 'gridpreco',
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
                                {name:'empresa', type: 'string'},
                                {name:'codTabela', type: 'string'},
                                // {name:'nomeTabPreco', type: 'string'},
                                {name:'codProduto', type: 'number' },
                                {name:'descricao', type: 'string' },
                                {name:'marca', type: 'string' },
                                {name:'codNbs', type: 'string' },
                                {name:'estoque', type: 'number' },
                                {name:'fxCusto', type: 'string' },
                                {name:'tipoPrecificacao', type: 'string' },
                                {name:'curva', type: 'string' },
                                {name:'custoMedio', type: 'number' },
                                {name:'valor', type: 'number' },
                                {name:'pis', type: 'number' },
                                {name:'cofins', type: 'number' },
                                {name:'icms', type: 'number' },
                                {name:'grupoDesconto', type: 'string' },
                                {name:'precVendedor', type: 'number' },
                                {name:'ccMed12m', type: 'number' },
                                {name:'ccMed6m', type: 'number' },
                                {name:'ccMed3m', type: 'number' },
                                {name:'ccM3', type: 'number' },
                                {name:'ccM2', type: 'number' },
                                {name:'ccM1', type: 'number' },
                                {name:'mb_12m', type: 'number' },
                                {name:'mb_6m', type: 'number' },
                                {name:'mb_3m', type: 'number' },
                                {name:'mbM3', type: 'number' },
                                {name:'mbM2', type: 'number' },
                                {name:'mbM1', type: 'number' },
                                {name:'paramMargem', type: 'string' },
                                {name:'margemPrecoAtual', type: 'number' },
                                {name:'precoAtual', type: 'number' },
                                {name:'precoAtualMin', type: 'number' },
                                {name:'precoAtualLiq', type: 'number' },
                                {name:'precoMargemParam', type: 'number' }
                            ]
                        }),
                        pageSize: 50,
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/basepreco/listargpreco',
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
                    dataIndex: 'empresa',
                    width: 68,
                    align: 'right'
                },
                {
                    text: 'Cód. Tab. Preço',
                    dataIndex: 'codTabela',
                    width: 120,
                    hidden: false
                },
                // {
                //     text: 'Tabela Preço',
                //     dataIndex: 'nomeTabPreco',
                //     width: 200,
                //     align: 'right',
                //     hidden: true
                // },
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
                    dataIndex: 'codNbs',
                    width: 120,
                    hidden: false
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
                    text: 'FX. Custo',
                    dataIndex: 'fxCusto',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'Tipo Precificação',
                    dataIndex: 'tipoPrecificacao',
                    width: 150,
                    align: 'right'
                },
                {
                    text: 'Curva',
                    dataIndex: 'curva',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'Custo Médio',
                    dataIndex: 'custoMedio',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Valor',
                    dataIndex: 'valor',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'PIS',
                    dataIndex: 'pis',
                    width: 60,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'COFINS',
                    dataIndex: 'cofins',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'ICMS',
                    dataIndex: 'icms',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Grupo Desconto',
                    dataIndex: 'grupoDesconto',
                    width: 130
                },
                {
                    text: 'Prec. Vendedor',
                    dataIndex: 'precVendedor',
                    width: 126,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 12M',
                    dataIndex: 'ccMed12m',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 6M',
                    dataIndex: 'ccMed6m',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 3M',
                    dataIndex: 'ccMed3m',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. 3M',
                    dataIndex: 'ccM3',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. 2M',
                    dataIndex: 'ccM2',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. 1M',
                    dataIndex: 'ccM1',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 12M',
                    dataIndex: 'mb_12m',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 6M',
                    dataIndex: 'mb_6m',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 3M',
                    dataIndex: 'mb_3m',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M3',
                    dataIndex: 'mbM3',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M2',
                    dataIndex: 'mbM2',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M1',
                    dataIndex: 'mbM1',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Parametro Margem',
                    dataIndex: 'paramMargem',
                    width: 140,
                    hidden: false
                },
                {
                    text: 'Margem Preço Atual',
                    dataIndex: 'margemPrecoAtual',
                    width: 150,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual',
                    dataIndex: 'precoAtual',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual',
                    dataIndex: 'precoAtual',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual Min.',
                    dataIndex: 'precoAtualMin',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual Min.',
                    dataIndex: 'precoAtualMin',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual Liq.',
                    dataIndex: 'precoAtualLiq',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Margem Param.',
                    dataIndex: 'precoMargemParam',
                    width: 160,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
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
