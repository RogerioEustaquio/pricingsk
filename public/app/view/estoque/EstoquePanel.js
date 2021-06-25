Ext.define('App.view.estoque.EstoquePanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'estoquepanel',
    itemId: 'estoquepanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.estoque.Toolbar',
        'App.view.estoque.EstoqueFiltro',
        'App.view.estoque.EstoqueGrid'
    ],
    
    title: 'Estoque',
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
                            xtype: 'estoquetoolbar'
                        },
                        {
                            xtype: 'estoquefiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            text: 'Estoque',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'estoquegrid'
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
