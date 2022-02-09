Ext.define('App.view.basepreco.GeraPrecoToolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'geraprecotoolbar',
    itemId: 'geraprecotoolbar',
    region: 'north',
    requires:[
        
    ],

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

                var v = me.up('container').down('#geraprecofiltro').down('#notMarca').value;
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
                                me.up('container').down('#geraprecofiltro').down('#notMarca').value = vnotmarca;
                               
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
                btnNotmarga,
                '->',
                {
                    xtype: 'button',
                    text: 'Excel',
                    iconCls: 'fa fa-download',
                    arrowAlign: 'right',
                    menu: [
                        {
                            text: 'CSV',
                            handler: function(){

                                var win = open('','forml');
                                var link = BASEURL + '/api/basepreco/gerarprecoexcel';
                                var dados = this.dado;

                                var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                                input +=  " <input type='hidden' name='nome' value='exportgerapreco'></input>";
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
                                var link = BASEURL + '/api/basepreco/gerarprecoexcel2';
                                var dados = this.dado;

                                var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                                input +=  " <input type='hidden' name='nome' value='exportgerapreco'></input>";
                                input +=  " <input type='hidden' name='total' value='"+this.total+"'></input>";

                                var html = "<html><body><form id='forml' method='POST' action='"+link+"'> " +input+" </form></body></html>"

                                win.document.write(html);
                                win.document.close();
                                win.document.getElementById('forml').submit();
                            }
                        }
                    ],
                    
                }
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        var vnotmarca = me.down('#btnnotmarca').value ? 1 : 0 ;
        // console.log(vnotmarca);
        me.up('container').down('#geraprecofiltro').down('#notMarca').value = vnotmarca;

        if(me.up('container').down('#geraprecofiltro').hidden){
            me.up('container').down('#geraprecofiltro').setHidden(false);
        }else{
            me.up('container').down('#geraprecofiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');

        var idEmpresas      = me.up('container').down('#geraprecofiltro').down('#elEmp').getValue();
        var notMarca        = me.up('container').down('#geraprecofiltro').down('#notMarca').value;
        var idMarcas        = me.up('container').down('#geraprecofiltro').down('#elMarca').getValue();
        var idCurvaAbc      = me.up('container').down('#geraprecofiltro').down('#elCurva').getValue();
        var produtos        = me.up('container').down('#geraprecofiltro').down('#elProduto').getValue();
        var codTabPreco     = me.up('container').down('#geraprecofiltro').down('#eltagtabpreco').getValue();
        var idProduto       = me.up('container').down('#geraprecofiltro').down('#eltagidproduto').getValue();
        var iddescProduto   = me.up('container').down('#geraprecofiltro').down('#elDescProduto').getValue();
        var tipoprecificacao= me.up('container').down('#geraprecofiltro').down('#elTpPrecificacao').getValue();
        var faixaCusto      = me.up('container').down('#geraprecofiltro').down('#eltagfaixacusto').getValue();
        var grupoDesconto   = me.up('container').down('#geraprecofiltro').down('#eltaggrupodesconto').getValue();
        var slidMargem      = me.up('container').down('#geraprecofiltro').down('#slidmargem').getValue();

        var fieldset =  me.up('container').down('#geraprecofiltro').down('fieldset').collapsed  ? '' : 1;

        var checkEstoque    = fieldset ? me.up('container').down('#geraprecofiltro').down('#elestoque').getValue() : '';
        var checkpreco      = fieldset ? me.up('container').down('#geraprecofiltro').down('#elpreco').getValue(): '';
        var checkmargem     = fieldset ? me.up('container').down('#geraprecofiltro').down('#elmargem').getValue(): '';
        var checktipoprecificacao   = fieldset ? me.up('container').down('#geraprecofiltro').down('#eltipoprecificacao').getValue(): '';
        var checkgrupodesconto      = fieldset ? me.up('container').down('#geraprecofiltro').down('#elgrupodesconto').getValue(): '';
        var checktabelapreco        = fieldset ? me.up('container').down('#geraprecofiltro').down('#eltabelapreco').getValue(): '';
        var checkcustounitario      = fieldset ? me.up('container').down('#geraprecofiltro').down('#elcustounitario').getValue(): '';
        var checkparammargem        = fieldset ? me.up('container').down('#geraprecofiltro').down('#elparammargem').getValue(): '';

        if(iddescProduto){

            iddescProduto.forEach(function(record){

                idProduto.push(record);
            });
        }

        var grid = me.up('container').down('#panelcenterpreco').down('grid');
        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            notMarca: notMarca,
            idMarcas: Ext.encode(idMarcas),
            idCurvaAbc: Ext.encode(idCurvaAbc),
            produtos: Ext.encode(produtos),
            codTabPreco: Ext.encode(codTabPreco),
            idProduto: Ext.encode(idProduto),
            // descProduto: Ext.encode(descProduto),
            tipoprecificacao: Ext.encode(tipoprecificacao),
            faixaCusto: Ext.encode(faixaCusto),
            grupoDesconto: Ext.encode(grupoDesconto),
            slidMargem: Ext.encode(slidMargem),
            checkEstoque: checkEstoque,
            checkpreco: checkpreco,
            checkmargem: checkmargem,
            checktipoprecificacao: checktipoprecificacao,
            checkgrupodesconto: checkgrupodesconto,
            checktabelapreco: checktabelapreco,
            checkcustounitario: checkcustounitario,
            checkparammargem: checkparammargem
        };
    
        grid.getStore().getProxy().setExtraParams(params);
        grid.getStore().loadPage(1);        

    }

});
