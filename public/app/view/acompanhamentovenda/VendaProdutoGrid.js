Ext.define('App.view.acompanhamentovenda.VendaProdutoGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'vendaprodutogrid',
    itemId: 'vendaprodutogrid',
    layout: 'fit',
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

        Ext.applyIf(this, {

            store: Ext.create('Ext.data.Store', {
                model: Ext.create('Ext.data.Model', {
                        fields:[{name:'codEmpresa', type: 'number'},
                            {name:'empresa', type: 'string'},
                            {name:'data', type: 'string'},
                            {name:'nroNota', type: 'string'},
                            {name:'codParceiro', type: 'string' },
                            {name:'parceiro', type: 'number' },
                            {name:'idProduto', type: 'number' },
                            {name:'nmProduto', type: 'string' },
                            {name:'idMarca', type: 'number' },
                            {name:'marca', type: 'string' },
                            {name:'codVendedor', type: 'number' },
                            {name:'vendedor', type: 'string' },
                            {name:'usuarioDesconto', type: 'string' },
                            {name:'pis', type: 'number' },
                            {name:'cofins', type: 'number' },
                            {name:'icms', type: 'number' },
                            {name:'precoTabela', type: 'number' },
                            {name:'descontoUnitario', type: 'number' },
                            {name:'pcDesconto', type: 'number' },
                            {name:'pcImposto', type: 'number' },
                            {name:'precoLiquido', type: 'number' },
                            {name:'qtde', type: 'number' },
                            {name:'rol', type: 'number' },
                            {name:'lb', type: 'number' },
                            {name:'mb', type: 'number' }
                            ]
                    }),
                pageSize: 50,
                autoLoad: false,
                proxy: {
                    type: 'ajax',
                    method:'POST',
                    url : BASEURL + '/api/acompanhamentovenda/listarvendaproduto',
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
                    text: 'Cod. Filial',
                    dataIndex: 'codEmpresa',
                    width: 30,
                    hidden: true
                },
                {
                    text: 'Filial',
                    dataIndex: 'empresa',
                    width: 68,
                    align: 'right'
                },
                {
                    text: 'Data',
                    dataIndex: 'data',
                    width: 80,
                    hidden: false
                },
                {
                    text: 'Nro Nota',
                    dataIndex: 'nroNota',
                    width: 110,
                    align: 'right'
                },
                {
                    text: 'Cód. Parceiro',
                    dataIndex: 'codParceiro',
                    width: 110,
                    align: 'left',
                    hidden: true
                },
                {
                    text: 'Parceiro',
                    dataIndex: 'parceiro',
                    width: 90,
                    align: 'right'
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
                    text: 'Cód. Marca',
                    dataIndex: 'idMarca',
                    width: 110,
                    align: 'right',
                    hidden: true
                },
                {
                    text: 'Marca',
                    dataIndex: 'marca',
                    width: 110,
                    align: 'right'
                },
                {
                    text: 'Cod. Vendedor',
                    dataIndex: 'codVendedor',
                    width: 30,
                    hidden: true
                },
                {
                    text: 'Vendedor',
                    dataIndex: 'vendedor',
                    width: 110,
                    align: 'right'
                },
                {
                    text: 'Usuário Desconto',
                    dataIndex: 'usuarioDesconto',
                    width: 150,
                    align: 'right'
                },
                {
                    text: 'PIS',
                    dataIndex: 'pis',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,0);
                    },
                },
                {
                    text: 'COFINS',
                    dataIndex: 'cofins',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,0);
                    },
                },
                {
                    text: 'ICMS',
                    dataIndex: 'icms',
                    width: 80,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'Preço Tabela',
                    dataIndex: 'precoTabela',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'Desconto Unitário',
                    dataIndex: 'descontoUnitario',
                    width: 140,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'Desconto %',
                    dataIndex: 'pcDesconto',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'Imposto',
                    dataIndex: 'pcImposto',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'Preço Líquido',
                    dataIndex: 'precoLiquido',
                    width: 120,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'Quantidade',
                    dataIndex: 'qtde',
                    width: 110,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,0);
                    },
                },
                {
                    text: 'ROL',
                    dataIndex: 'rol',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'LB',
                    dataIndex: 'lb',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                },
                {
                    text: 'MB',
                    dataIndex: 'mb',
                    width: 90,
                    align: 'right',
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    },
                }
            ]
        });

        this.callParent(arguments);
    }
});
