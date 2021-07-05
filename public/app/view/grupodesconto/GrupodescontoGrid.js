Ext.define('App.view.grupodesconto.GrupodescontoGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'grupodescontogrid',
    itemId: 'grupodescontogrid',
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
                                {name:'codTabelaPreco', type: 'number'},
                                {name:'descricaoTabelaPreco', type: 'string'},
                                {name:'codGrupoDesconto', type: 'number'},
                                {name:'descricaoGrupoDesconto', type: 'string'},
                                {name:'agrupamentoProduto', type: 'number'},
                                {name:'pVendedor', type: 'number'},
                                {name:'pCoordenador', type: 'number'},
                                {name:'pGerente', type: 'number'},
                                {name:'pGerenteRegional', type: 'number'},
                                {name:'pDiretor', type: 'number'},
                                {name:'pDescontoMargem', type: 'number'},
                                {name:'descontoMaximoAlcada', type: 'number'}

                            ]
                        }),
                        pageSize: 50,
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/grupodesconto/listargrupodesconto',
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
                    dataIndex: 'codTabelaPreco',
                    width: 140,
                    hidden: true
                },
                {
                    text: 'Tabela Preço',
                    dataIndex: 'descricaoTabelaPreco',
                    width: 120,
                    // align: 'right'
                },
                {
                    text: 'Cód. Grupo Desconto',
                    dataIndex: 'codGrupoDesconto',
                    width: 140,
                    hidden: true
                },
                {
                    text: 'Grupo Desconto',
                    dataIndex: 'descricaoGrupoDesconto',
                    width: 140,
                    hidden: false
                },
                {
                    text: 'Agrupamento Produto',
                    dataIndex: 'agrupamentoProduto',
                    width: 180,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: '% Vendedor',
                    dataIndex: 'pVendedor',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: '% Coordenador',
                    dataIndex: 'pCoordenador',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: '% Gerente',
                    dataIndex: 'pGerente',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: '% Gerente Regional',
                    dataIndex: 'pGerenteRegional',
                    width: 150,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: '% Diretor',
                    dataIndex: 'pDiretor',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: '% Desconto + Margem',
                    dataIndex: 'pDescontoMargem',
                    width: 180,
                    // align: 'right'
                },
                {
                    text: 'Desconto Máximo Alçada',
                    dataIndex: 'descontoMaximoAlcada',
                    width: 180,
                    // align: 'right'
                }
               
            ]
        });

        me.callParent(arguments);

    }
});
