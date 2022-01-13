Ext.define('App.view.acompanhamentovenda.AcompanhaVendaPanel', {
    extend: 'Ext.tab.Panel',
    xtype: 'acompanhavendapanel',
    itemId: 'acompanhavendapanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.acompanhamentovenda.VendaProdutoToolbar',
        'App.view.acompanhamentovenda.VendaProdutoGrid',
        'App.view.acompanhamentovenda.VendaProdutoFiltro',
    ],
    
    title: 'Acompanhamento de Venda',
    layout: 'fit',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {

            items: [
                {
                    xtype: 'container',
                    layout: 'border',
                    width: '100%',
                    title: 'Venda por Produto',
                    items:[
                        {
                            xtype: 'vendaprodutotoolbar'
                        },
                        {
                            xtype: 'vendaprodutofiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            text: 'Venda por Produto',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'vendaprodutogrid'
                                }
                            ]

                        }
                    ]
                },

            ]

        });

        me.callParent(arguments);
    }
    
});
