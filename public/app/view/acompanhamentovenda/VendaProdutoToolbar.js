Ext.define('App.view.acompanhamentovenda.VendaProdutoToolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'vendaprodutotoolbar',
    itemId: 'vendaprodutotoolbar',
    region: 'north',
    requires:[
        // 'App.view.fii.ContainerHighCharts'
    ],
    // indicadoresAdd: null,

    initComponent: function() {
        var me = this;

        var btnFiltro = Ext.create('Ext.button.Button',{
            
            iconCls: 'fa fa-filter',
            tooltip: 'Filtro',
            margin: '1 1 1 4',
            handler: me.onBtnFiltros
        });

        var btnConsultar = Ext.create('Ext.button.Button',{

            iconCls: 'fa fa-search',
            tooltip: 'Consultar',
            margin: '1 1 1 4',
            handler: me.onBtnConsultar
        });
        
        Ext.applyIf(me, {

            items : [
                btnFiltro,
                btnConsultar,
                '->',
                {
                    xtype: 'button',
                    text: 'Excel',
                    iconCls: 'fa fa-download',
                    arrowAlign: 'right',
                    menu: [
                        {
                            text: 'CSV',
                            // handler: function(){

                            //     var win = open('','forml');
                            //     var link = BASEURL + '/api/basepreco/gerarexcel';
                            //     var dados = this.dado;

                            //     var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                            //     input +=  " <input type='hidden' name='nome' value='baseprecoexport'></input>";
                            //     input +=  " <input type='hidden' name='total' value='"+this.total+"'></input>";

                            //     var html = "<html><body><form id='forml' method='POST' action='"+link+"'> " +input+" </form></body></html>"

                            //     win.document.write(html);
                            //     win.document.close();
                            //     win.document.getElementById('forml').submit();
                            // }
                        },
                        {
                            text: 'XLS',
                            // handler: function(){

                            //     var win = open('','forml');
                            //     var link = BASEURL + '/api/basepreco/gerarexcel2';
                            //     var dados = this.dado;

                            //     var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                            //     input +=  " <input type='hidden' name='nome' value='baseprecoexport'></input>";
                            //     input +=  " <input type='hidden' name='total' value='"+this.total+"'></input>";

                            //     var html = "<html><body><form id='forml' method='POST' action='"+link+"'> " +input+" </form></body></html>"

                            //     win.document.write(html);
                            //     win.document.close();
                            //     win.document.getElementById('forml').submit();
                            // }
                        }
                    ],
                    
                }
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        if(me.up('container').down('#vendaprodutofiltro').hidden){
            me.up('container').down('#vendaprodutofiltro').setHidden(false);
        }else{
            me.up('container').down('#vendaprodutofiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');

        var idEmpresas      = me.up('container').down('#vendaprodutofiltro').down('#elEmp').getValue();
        var datainicio        = me.up('container').down('#vendaprodutofiltro').down('#datainicio').getRawValue();
        var datafinal        = me.up('container').down('#vendaprodutofiltro').down('#datafinal').getRawValue();
        var idMarcas        = me.up('container').down('#vendaprodutofiltro').down('#elMarca').getValue();
        var produtos        = me.up('container').down('#vendaprodutofiltro').down('#elProduto').getValue();
        var codTabPreco     = me.up('container').down('#vendaprodutofiltro').down('#eltagtabpreco').getValue();
        var idProduto       = me.up('container').down('#vendaprodutofiltro').down('#eltagidproduto').getValue();
        var grupoDesconto   = me.up('container').down('#vendaprodutofiltro').down('#eltaggrupodesconto').getValue();

        var fieldset =  me.up('container').down('#vendaprodutofiltro').down('fieldset').collapsed  ? '' : 1;

        var checkEstoque    = fieldset ? me.up('container').down('#vendaprodutofiltro').down('#elestoque').getValue() : '';
        var checkpreco      = fieldset ? me.up('container').down('#vendaprodutofiltro').down('#elpreco').getValue(): '';
        var checkmargem     = fieldset ? me.up('container').down('#vendaprodutofiltro').down('#elmargem').getValue(): '';
        var checktipoprecificacao   = fieldset ? me.up('container').down('#vendaprodutofiltro').down('#eltipoprecificacao').getValue(): '';
        var checkgrupodesconto      = fieldset ? me.up('container').down('#vendaprodutofiltro').down('#elgrupodesconto').getValue(): '';
        var checktabelapreco        = fieldset ? me.up('container').down('#vendaprodutofiltro').down('#eltabelapreco').getValue(): '';
        var checkcustounitario      = fieldset ? me.up('container').down('#vendaprodutofiltro').down('#elcustounitario').getValue(): '';

        var grid = me.up('container').down('#panelcenter').down('grid');
        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            datainicio: datainicio,
            datafinal: datafinal,
            idMarcas: Ext.encode(idMarcas),
            produtos: Ext.encode(produtos),
            codTabPreco: Ext.encode(codTabPreco),
            idProduto: Ext.encode(idProduto),
            grupoDesconto: Ext.encode(grupoDesconto),
            checkEstoque: checkEstoque,
            checkpreco: checkpreco,
            checkmargem: checkmargem,
            checktipoprecificacao: checktipoprecificacao,
            checkgrupodesconto: checkgrupodesconto,
            checktabelapreco: checktabelapreco,
            checkcustounitario: checkcustounitario
        };
    
        grid.getStore().getProxy().setExtraParams(params);
        grid.getStore().loadPage(1);

        

    }

});
