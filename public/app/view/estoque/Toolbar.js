Ext.define('App.view.estoque.Toolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'estoquetoolbar',
    itemId: 'estoquetoolbar',
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

                var v = me.up('container').down('#estoquefiltro').down('#notmarca').value;
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
                                me.up('container').down('#estoquefiltro').down('#notmarca').value = vnotmarca;
                               
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
                    text: 'Export',
                    iconCls: 'fa fa-table',
                    handler: function(){

                        var win = open('','forml');
                        var link = BASEURL + '/api/estoque/gerarexcel';
                        var dados = this.dado;

                        var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                        input +=  " <input type='hidden' name='nome' value='exportestoque'></input>";
                        input +=  " <input type='hidden' name='total' value='"+this.total+"'></input>";

                        var html = "<html><body><form id='forml' method='POST' action='"+link+"'> " +input+" </form></body></html>"

                        win.document.write(html);
                        win.document.close();
                        win.document.getElementById('forml').submit();

                    }
                }
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        var vnotmarca = me.down('#btnnotmarca').value ? 1 : 0 ;
        me.up('container').down('#estoquefiltro').down('#notmarca').value = vnotmarca;

        if(me.up('container').down('#estoquefiltro').hidden){
            me.up('container').down('#estoquefiltro').setHidden(false);
        }else{
            me.up('container').down('#estoquefiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');

        var idEmpresas  = me.up('container').down('#estoquefiltro').down('#elEmp').getValue();
        var notMarca        = me.up('container').down('#estoquefiltro').down('#notmarca').value;
        var idMarcas       = me.up('container').down('#estoquefiltro').down('#elMarca').getValue();
        var idProduto   = me.up('container').down('#estoquefiltro').down('#eltagidproduto').getValue();
        var grupoDesconto   = me.up('container').down('#estoquefiltro').down('#eltaggrupodesconto').getValue();

        var fieldset =  me.up('container').down('#estoquefiltro').down('fieldset').collapsed  ? '' : 1;

        var checkEstoque   = fieldset ? me.up('container').down('#estoquefiltro').down('#elestoque').getValue() : '';
        var checkMbAlvo    = fieldset ? me.up('container').down('#estoquefiltro').down('#elmbalvo').getValue() : '';

        var grid = me.up('container').down('#panelcenter').down('grid');
        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            notMarca: notMarca,
            idMarcas: Ext.encode(idMarcas),
            idProduto: Ext.encode(idProduto),
            grupoDesconto: Ext.encode(grupoDesconto),
            checkEstoque: checkEstoque,
            checkMbAlvo: checkMbAlvo
        };
    
        grid.getStore().getProxy().setExtraParams(params);
        grid.getStore().loadPage(1);

        

    }

});
