Ext.define('App.view.basepreco.Toolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'bprecotoolbar',
    itemId: 'bprecotoolbar',
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

        var btnNotmarga = Ext.create('Ext.button.Button',{

            name: 'btnnotmarca',
            itemId: 'btnnotmarca',
            iconCls: 'fa fa-cog',
            tooltip: 'Excluir marcas selecionadas',
            margin: '1 1 1 4',
            handler: function(){

                var v = me.up('container').down('#bprecofiltro').down('#notMarca').value;
                btnNotmarga.value = v;

                objWindow = Ext.create('Ext.window.Window',{
                    title: 'Opção',
                    scrollable: true,
                    height: 90,
                    width: 210,
                    items: [
                        {
                            xtype: 'checkboxfield',
                            name: 'bxnotmarca',
                            itemId: 'bxnotmarca',
                            checked: this.value,
                            boxLabel: 'Excluir marcas selecionadas',
                            labelWidth: '70%',
                            labelAlign: 'right',
                            // margin: '2 2 2 2',
                            handler: function(){

                                vnotmarca = this.checked ? 1 : 0;
                                btnNotmarga.value = vnotmarca;
                                me.up('container').down('#bprecofiltro').down('#notMarca').value = vnotmarca;
                               
                                setTimeout(function(){
                                    objWindow.close();
                                },300);
                            }
                        }
                    ]

                });

                objWindow.show();

            }
        });
        
        Ext.applyIf(me, {

            items : [
                btnFiltro,
                btnConsultar,
                btnNotmarga
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        var vnotmarca = me.down('#btnnotmarca').value ? 1 : 0 ;
        // console.log(vnotmarca);
        me.up('container').down('#bprecofiltro').down('#notMarca').value = vnotmarca;

        if(me.up('container').down('#bprecofiltro').hidden){
            me.up('container').down('#bprecofiltro').setHidden(false);
        }else{
            me.up('container').down('#bprecofiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');

        var idEmpresas      = me.up('container').down('#bprecofiltro').down('#elEmp').getValue();
        var notMarca        = me.up('container').down('#bprecofiltro').down('#notMarca').value;
        var idMarcas        = me.up('container').down('#bprecofiltro').down('#elMarca').getValue();
        var produtos        = me.up('container').down('#bprecofiltro').down('#elProduto').getValue();
        var codTabPreco     = me.up('container').down('#bprecofiltro').down('#eltagtabpreco').getValue();
        var idProduto       = me.up('container').down('#bprecofiltro').down('#eltagidproduto').getValue();
        var grupoDesconto   = me.up('container').down('#bprecofiltro').down('#eltaggrupodesconto').getValue();

        var fieldset =  me.up('container').down('#bprecofiltro').down('fieldset').collapsed  ? '' : 1;

        var checkEstoque    = fieldset ? me.up('container').down('#bprecofiltro').down('#elestoque').getValue() : '';
        var checkpreco      = fieldset ? me.up('container').down('#bprecofiltro').down('#elpreco').getValue(): '';
        var checkmargem     = fieldset ? me.up('container').down('#bprecofiltro').down('#elmargem').getValue(): '';
        var checktipoprecificacao   = fieldset ? me.up('container').down('#bprecofiltro').down('#eltipoprecificacao').getValue(): '';
        var checkgrupodesconto      = fieldset ? me.up('container').down('#bprecofiltro').down('#elgrupodesconto').getValue(): '';
        var checktabelapreco        = fieldset ? me.up('container').down('#bprecofiltro').down('#eltabelapreco').getValue(): '';
        var checkcustounitario      = fieldset ? me.up('container').down('#bprecofiltro').down('#elcustounitario').getValue(): '';

        var grid = me.up('container').down('#panelcenter').down('grid');
        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            notMarca: notMarca,
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
