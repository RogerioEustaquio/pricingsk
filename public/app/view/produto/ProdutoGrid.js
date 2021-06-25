Ext.define('App.view.produto.ProdutoGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'produtogrid',
    itemId: 'produtogrid',
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
                        fields:[{name:'codProduto', type: 'number'},
                                {name:'descricao', type: 'string'},
                                {name:'descricaoJs', type: 'string'},
                                {name:'ativo', type: 'string'},
                                {name:'codItemNbs', type: 'string'},
                                {name:'codGrupo', type: 'string'},
                                {name:'descricaoGrupo', type: 'string'},
                                {name:'partnumber', type: 'string'},
                                {name:'dtCadastro', type: 'string'},
                                {name:'codMarca', type: 'string'},
                                {name:'descricaoMarca', type: 'string'},
                                {name:'usadoComo', type: 'string'}
                            ]
                        }),
                        pageSize: 50,
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            method:'POST',
                            url : BASEURL + '/api/produto/listarproduto',
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
                    text: 'Cód. Produto',
                    dataIndex: 'codProduto',
                    width: 100,
                    hidden: false
                },
                {
                    text: 'Descrição',
                    dataIndex: 'descricao',
                    // width: 90,
                    flex: 1,
                    hidden: false
                },
                {
                    text: 'Descrição JS',
                    dataIndex: 'descricaoJs',
                    width: 100,
                    hidden: false
                },
                {
                    text: 'Ativo',
                    dataIndex: 'ativo',
                    width: 80,
                    hidden: false
                },
                {
                    text: 'Cód. Item NBS',
                    dataIndex: 'codItemNbs',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'Cód. Grupo',
                    dataIndex: 'codGrupo',
                    width: 110,
                    hidden: true
                },
                {
                    text: 'Desc. Grupo',
                    dataIndex: 'descricaoGrupo',
                    width: 110,
                    hidden: false
                },
                {
                    text: 'Part. Number',
                    dataIndex: 'partnumber',
                    width: 120,
                    hidden: true
                },
                {
                    text: 'Data Cadastro',
                    dataIndex: 'dtCadastro',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'Cód. Marca',
                    dataIndex: 'codMarca',
                    width: 120,
                    hidden: true
                },
                {
                    text: 'Desc. Marca',
                    dataIndex: 'descricaoMarca',
                    width: 120,
                    hidden: false
                },
                {
                    text: 'Usado Como',
                    dataIndex: 'usadoComo',
                    width: 120,
                    hidden: false
                },
             
            ]
        });

        me.callParent(arguments);

    }
});
