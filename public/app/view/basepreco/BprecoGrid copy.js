Ext.define('App.view.basepreco.BprecoGrid', {
    extend: 'Ext.container.Container',
    xtype: 'bprecogrid',
    itemId: 'bprecogrid',
    // margin: '10 2 2 2',
    layout:'fit',
    // params: [],
    requires: [
        'Ext.toolbar.Paging',
        // 'Ext.grid.feature.GroupingSummary'
    ],
    bbar: {
        xtype: 'pagingtoolbar',
        displayInfo: true,
        displayMsg: 'Exibindo solicitações {0} - {1} de {2}',
        emptyMsg: "Não há solicitações a serem exibidos"
    },

    constructor: function() {
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');

        Ext.define('App.view.basepreco.modelgrid', {
            extend: 'Ext.data.Model',
            fields:[{name:'idEmpresa', type: 'number'},
                    {name:'filial', type: 'string'},
                    {name:'idTabPreco', type: 'string'},
                    {name:'nmTabPreco', type: 'string'},
                    {name:'dtVigor', type: 'string' },
                    {name:'preco', type: 'number' },
                    {name:'idProduto', type: 'number' },
                    {name:'nmProduto', type: 'string' },
                    {name:'marca', type: 'string' },
                    {name:'codItem', type: 'string' },
                    {name:'mb', type: 'number' },
                    {name:'tpPrecificacao', type: 'string' },
                    {name:'grupoDesc', type: 'string' },
                    {name:'estoque', type: 'number' },
                    {name:'custoMedioUnit', type: 'number' },
                    {name:'vlEstoque', type: 'number' },
                    {name:'custo', type: 'number' },
                    {name:'pisCofins', type: 'number' },
                    {name:'icms', type: 'number' }
                    ]
        });

        Ext.applyIf(this, {

            items: [
                {
                    xtype: Ext.create('Ext.grid.Panel',{

                        store: Ext.create('Ext.data.Store', {
                            model: 'App.view.basepreco.modelgrid',
                            proxy: {
                                type: 'ajax',
                                method:'POST',
                                url : BASEURL + '/api/basepreco/listarpreco',
                                encode: true,
                                timeout: 240000,
                                format: 'json',
                                reader: {
                                    type: 'json',
                                    rootProperty: 'data'
                                }
                            },
                            autoLoad: true,
                            // grouper: {
                            //     property: 'grupo'
                            // }
                        }),
                        
                        features: [
                            // {
                            //     groupHeaderTpl: '{name}',
                            //     ftype: 'groupingsummary',
                            //     hideGroupedHeader: false,
                            //     enableGroupingMenu: false,
                            //     startCollapsed: false
                            // },
                            // {
                            //     ftype: 'summary',
                            //     dock: 'bottom'
                            // }
                        ],
                        listeners: {
                        },

                        columns: [
                            {
                                text: 'Cod. Filial',
                                dataIndex: 'idEmpresa',
                                width: 30,
                                hidden: true
                            },
                            {
                                text: 'Filial',
                                dataIndex: 'filial',
                                width: 68,
                                align: 'right'
                            },
                            {
                                text: 'Cod. Preço',
                                dataIndex: 'idTabPreco',
                                width: 30,
                                hidden: true
                            },
                            {
                                text: 'Tabela Preço',
                                dataIndex: 'nmTabPreco',
                                width: 110,
                                align: 'right'
                            },
                            {
                                text: 'Vigor',
                                dataIndex: 'dtVigor',
                                width: 110,
                                align: 'right'
                            },
                            {
                                text: 'Preço',
                                dataIndex: 'preco',
                                width: 90,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,2);
                                },
                            },
                            {
                                text: 'Id Produto',
                                dataIndex: 'idProduto',
                                width: 30,
                                hidden: true
                            },
                            {
                                text: 'Produto',
                                dataIndex: 'nmProduto',
                                width: 110,
                                align: 'right'
                            },
                            {
                                text: 'Marca',
                                dataIndex: 'marca',
                                width: 110,
                                align: 'right'
                            },
                            {
                                text: 'Cod. Item',
                                dataIndex: 'codItem',
                                width: 30,
                                hidden: true
                            },
                            {
                                text: 'MB',
                                dataIndex: 'mb',
                                width: 74,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,2);
                                },
                            },
                            {
                                text: 'Tipo Precificação',
                                dataIndex: 'tpPrecificacao',
                                width: 150,
                                align: 'right'
                            },
                            {
                                text: 'Grupo Desconto',
                                dataIndex: 'grupoDesc',
                                width: 130,
                                align: 'right'
                            },
                            {
                                text: 'Estoque',
                                dataIndex: 'estoque',
                                width: 90,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,0);
                                },
                            },
                            {
                                text: 'Custo Unitário',
                                dataIndex: 'custoMedioUnit',
                                width: 120,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,2);
                                },
                            },
                            {
                                text: 'Valor Estoque',
                                dataIndex: 'vlEstoque',
                                width: 120,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,2);
                                },
                            },
                            {
                                text: 'Custo Operacional',
                                dataIndex: 'custo',
                                width: 150,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,2);
                                },
                            },
                            {
                                text: 'Pis Cofins',
                                dataIndex: 'pisCofins',
                                width: 110,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,2);
                                },
                            },
                            {
                                text: 'ICMS',
                                dataIndex: 'icms',
                                width: 90,
                                align: 'right',
                                renderer: function (v) {
                                    return utilFormat.Value2(v,2);
                                },
                            }
                        ],
                        
                        // features: [
                        //     {
                        //         groupHeaderTpl: "{name} | Total: {[values.rows[0].data.valorNota]}",
                        //         ftype: 'groupingsummary'
                        //     }
                        // ]
                    })
                }
            ]
        });

        this.callParent(arguments);
    }
});
