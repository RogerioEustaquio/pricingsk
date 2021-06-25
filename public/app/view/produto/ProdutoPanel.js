Ext.define('App.view.produto.ProdutoPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'produtopanel',
    itemId: 'produtopanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.produto.Toolbar',
        'App.view.produto.ProdutoFiltro',
        'App.view.produto.ProdutoGrid'
    ],
    
    title: 'Produto',
    layout: 'fit',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {

            items: [
                {
                    xtype: 'container',
                    layout: 'border',
                    width: '100%',
                    items:[
                        {
                            xtype: 'produtotoolbar'
                        },
                        {
                            xtype: 'produtofiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            text: 'Produto',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'produtogrid'
                                }
                            ]

                        }
                    ]
                }

            ]

        });

        me.callParent(arguments);
    }
    
});
