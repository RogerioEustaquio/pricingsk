Ext.define('App.view.analisemarca.RankGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'rankgrid',
    itemId: 'rankgrid',
    columnLines: true,
    margin: '1 1 1 1',
    requires: [
        'Ext.toolbar.Paging',
        'Ext.ux.util.Format'
    ],
    title: 'Rank',
    tools:[
        {
            xtype:'button',
            iconCls: 'fa fa-download',
            tooltip: 'Export',
            menu: [
                {
                    text: 'CSV',
                    handler: function(){

                        var win = open('','forml');
                        var link = BASEURL + '/api/analisemarca/gerarexcel';
                        var dados = this.dado;

                        var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                        input +=  " <input type='hidden' name='nome' value='produtorankexport'></input>";
                        input +=  " <input type='hidden' name='total' value='"+this.total+"'></input>";

                        var html = "<html><body><form id='forml' method='POST' action='"+link+"'> " +input+" </form></body></html>"

                        win.document.write(html);
                        win.document.close();
                        win.document.getElementById('forml').submit();
                    }
                },
                {
                    text: 'XLS',
                    handler: function(){

                        var win = open('','forml');
                        var link = BASEURL + '/api/analisemarca/gerarexcel2';
                        var dados = this.dado;

                        var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                        input +=  " <input type='hidden' name='nome' value='produtorankexport'></input>";
                        input +=  " <input type='hidden' name='total' value='"+this.total+"'></input>";

                        var html = "<html><body><form id='forml' method='POST' action='"+link+"'> " +input+" </form></body></html>"

                        win.document.write(html);
                        win.document.close();
                        win.document.getElementById('forml').submit();
                    }
                }
            ],
            // handler: function(event, toolEl, panel){
            //     // show help here
            //     console.log(toolEl);
            // }
        }
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
                                {name:'emp', type: 'string'},
                                {name:'codProduto', type: 'number' },
                                {name:'descricao', type: 'string' },
                                {name:'codMarca', type: 'number'},
                                {name:'descricaoMarca', type: 'string'},
                                {name:'fxCuso', type: 'number'},
                                {name:'estoque', type: 'number'},
                                {name:'custoMedio', type: 'number'},
                                {name:'valor', type: 'number'},
                                {name:'curva', type: 'string'},
                                {name:'clientes', type: 'number'}

                            ]
                        }),
                        pageSize: 50,
                        // autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/analisemarca/listarrank',
                            encode: true,
                            timeout: 580000,
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
                    hidden: false
                },
                {
                    text: 'Filial',
                    dataIndex: 'emp',
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
                    dataIndex: 'descricao',
                    // width: 200,
                    flex: 1,
                    align: 'right',
                    hidden: false
                },
                {
                    text: 'Cód. Marca',
                    dataIndex: 'codMarca',
                    width: 120,
                    align: 'right'
                },
                {
                    text: 'Marca',
                    dataIndex: 'descricaoMarca',
                    width: 120,
                    align: 'right'
                },
                {
                    text: 'FX. Custo',
                    dataIndex: 'fxCusto',
                    width: 90,
                    hidden: false
                },
                {
                    text: 'Estoque',
                    dataIndex: 'estoque',
                    width: 120,
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,0) : null
                        return v;
                    },
                },
                {
                    text: 'Custo Médio',
                    dataIndex: 'custoMedio',
                    width: 110,
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Valor',
                    dataIndex: 'valor',
                    width: 120,
                    hidden: false,
                    renderer: function (v) {
                        v = v > 0 ? utilFormat.Value2(v,2) : null
                        return v;
                    },
                },
                {
                    text: 'Curva',
                    dataIndex: 'curva',
                    width: 74,
                    align: 'right',
                },
                {
                    text: 'Cliente',
                    dataIndex: 'cliente',
                    width: 150,
                    align: 'right'
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
