Ext.define('App.view.configmarcaempresa.ConfigmarcaempresaGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'configmarcaempresagrid',
    itemId: 'configmarcaempresagrid',
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
                                {name:'codMarca', type: 'number'},
                                {name:'descricaoMarca', type: 'string'},
                                {name:'codParceiro', type: 'number'},
                                {name:'descricaoParceiro', type: 'string'},
                                {name:'metaMargemMix', type: 'number'},
                                {name:'margemPadrao', type: 'number'},
                                {name:'margemFx_0', type: 'number'},
                                {name:'margemFx_1_5', type: 'number'},
                                {name:'margemFx_6_10', type: 'number'},
                                {name:'margemFx_11_25', type: 'number'},
                                {name:'margemFx_26_50', type: 'number'},
                                {name:'margemFx_51_100', type: 'number'},
                                {name:'margemFx_101_250', type: 'number'},
                                {name:'margemFx_251_500', type: 'number'},
                                {name:'margemFx_501_1000', type: 'number'},
                                {name:'margemFx_1001_5000', type: 'number'},
                                {name:'margemFx_5001_10000', type: 'number'},
                                {name:'margemFx_10001X', type: 'number'}
                            ]
                        }),
                        pageSize: 50,
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/configmarcaempresa/listarconfigmarcaempresa',
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
                    text: 'Cód. Parceiro',
                    dataIndex: 'codParceiro',
                    width: 120,
                    hidden: true
                },
                {
                    text: 'Descrição Parceiro',
                    dataIndex: 'descricaoParceiro',
                    width: 146,
                    align: 'right',
                    hidden: true
                },
                {
                    text: 'Meta MB Mix',
                    dataIndex: 'metaMargemMix',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB Padrão',
                    dataIndex: 'margemPadrao',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 0',
                    dataIndex: 'margemFx_0',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 1-5',
                    dataIndex: 'margemFx_1_5',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 6-10',
                    dataIndex: 'margemFx_6_10',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 11-25',
                    dataIndex: 'margemFx_11_25',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 26-50',
                    dataIndex: 'margemFx_26_50',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 51-100',
                    dataIndex: 'margemFx_51_100',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 101-250',
                    dataIndex: 'margemFx_101_250',
                    width: 130,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 251-500',
                    dataIndex: 'margemFx_251_500',
                    width: 140,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 501-1000',
                    dataIndex: 'margemFx_501_1000',
                    width: 140,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 1001-5000',
                    dataIndex: 'margemFx_1001_5000',
                    width: 146,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 5001-10000',
                    dataIndex: 'margemFx_5001_10000',
                    width: 150,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                },
                {
                    text: 'Meta MB 10001-X',
                    dataIndex: 'margemFx_10001X',
                    width: 140,
                    align: 'right',
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    }
                }
               
            ]
        });

        me.callParent(arguments);

    }
});
