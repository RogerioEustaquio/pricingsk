Ext.define('App.view.basepreco.GeraPrecoGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'geraprecogrid',
    itemId: 'geraprecogrid',
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
                                {name:'estoque', type: 'float'},
                                {name:'fxCusto', type: 'string' },
                                {name:'tipoPrecificacao', type: 'string' },
                                {name:'curva', type: 'string' },
                                {name:'custoMedio', type: 'number' },
                                {name:'valor', type: 'number' },
                                {name:'pis', type: 'number' },
                                {name:'cofins', type: 'number' },
                                {name:'icms', type: 'number' },
                                {name:'grupoDesconto', type: 'string' },
                                {name:'percVendedor', type: 'number' },
                                {name:'ccMed12mRd', type: 'number' },
                                {name:'ccMed6mRd', type: 'number' },
                                {name:'ccMed3mRd', type: 'number' },
                                {name:'ccM3Rd', type: 'number' },
                                {name:'ccM2Rd', type: 'number' },
                                {name:'ccM1Rd', type: 'number' },
                                {name:'ccMed12m', type: 'number' },
                                {name:'ccMed6m', type: 'number' },
                                {name:'ccMed3m', type: 'number' },
                                {name:'ccM3', type: 'number' },
                                {name:'ccM2', type: 'number' },
                                {name:'ccM1', type: 'number' },
                                {name:'mb_12mRd', type: 'number' },
                                {name:'mb_6mRd', type: 'number' },
                                {name:'mb_3mRd', type: 'number' },
                                {name:'mbM3Rd', type: 'number' },
                                {name:'mbM2Rd', type: 'number' },
                                {name:'mbM1Rd', type: 'number' },
                                {name:'mb_12mMc', type: 'number' },
                                {name:'mb_6mMc', type: 'number' },
                                {name:'mb_3mMc', type: 'number' },
                                {name:'mb_12m', type: 'number' },
                                {name:'mb_6m', type: 'number' },
                                {name:'mb_3m', type: 'number' },
                                {name:'mbM3', type: 'number' },
                                {name:'mbM2', type: 'number' },
                                {name:'mbM1', type: 'number' },

                                {name:'paramMargem', type: 'number' },
                                {name:'margemPrecoAtual', type: 'number' },
                                {name:'precoAtual', type: 'number' },
                                {name:'precoAtualMin', type: 'number' },
                                {name:'precoAtualLiq', type: 'number' },
                                {name:'precoMargemParam', type: 'number' }
                            ]
                        }),
                        pageSize: 50,
                        autoLoad: false,
                        remoteSort: true,
                        // sorters: [{ property: 'vendaM6', direction: 'DESC' }],
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/basepreco/listargerapreco',
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
                    align: 'center'
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
                    // xtype: 'numbercolumn',
                    // // format:'0.000,00',
                    renderer: function (v) {

                        v = v != 0 ? utilFormat.Value2(v,2) : null;
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
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Valor',
                    dataIndex: 'valor',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'PIS',
                    dataIndex: 'pis',
                    width: 60,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'COFINS',
                    dataIndex: 'cofins',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'ICMS',
                    dataIndex: 'icms',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
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
                    dataIndex: 'percVendedor',
                    width: 126,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 12M RD',
                    dataIndex: 'ccMed12mRd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 6M RD',
                    dataIndex: 'ccMed6mRd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 3M RD',
                    dataIndex: 'ccMed3mRd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    }
                },
                {
                    text: 'CC. 3M RD',
                    dataIndex: 'ccM3Rd',
                    width: 110,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    }
                },
                {
                    text: 'CC. 2M RD',
                    dataIndex: 'ccM2Rd',
                    width: 110,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    }
                },
                {
                    text: 'CC. 1M RD',
                    dataIndex: 'ccM1Rd',
                    width: 110,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 12M',
                    dataIndex: 'ccMed12m',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 6M',
                    dataIndex: 'ccMed6m',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M. 3M',
                    dataIndex: 'ccMed3m',
                    width: 110,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M3',
                    dataIndex: 'ccM3',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M2',
                    dataIndex: 'ccM2',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'CC. M1',
                    dataIndex: 'ccM1',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 12M RD',
                    dataIndex: 'mb_12mRd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 6M RD',
                    dataIndex: 'mb_6mRd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 3M RD',
                    dataIndex: 'mb_3mRd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M. 3M RD',
                    dataIndex: 'mbM3Rd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M. 2M RD',
                    dataIndex: 'mbM2Rd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M. 1M RD',
                    dataIndex: 'mbM1Rd',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 12M MC',
                    dataIndex: 'mb_12mMc',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 6M MC',
                    dataIndex: 'mb_6mMc',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 3M MC',
                    dataIndex: 'mb_3mMc',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 12M',
                    dataIndex: 'mb_12m',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 6M',
                    dataIndex: 'mb_6m',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB 3M',
                    dataIndex: 'mb_3m',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M3',
                    dataIndex: 'mbM3',

                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M2',
                    dataIndex: 'mbM2',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'MB M1',
                    dataIndex: 'mbM1',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Parametro Margem',
                    dataIndex: 'paramMargem',
                    width: 150,
                    hidden: false,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Margem Preço Atual',
                    dataIndex: 'margemPrecoAtual',
                    width: 150,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual',
                    dataIndex: 'precoAtual',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual Min.',
                    dataIndex: 'precoAtualMin',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Atual Liq.',
                    dataIndex: 'precoAtualLiq',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Preço Margem Param.',
                    dataIndex: 'precoMargemParam',
                    width: 160,
                    align: 'right',
                    renderer: function (v) {
                        v = v != 0 ? utilFormat.Value2(v,2) : null
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
