Ext.define('App.view.basepreco.BaseprecoPanel', {
    extend: 'Ext.tab.Panel',
    xtype: 'baseprecopanel',
    itemId: 'baseprecopanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.basepreco.Toolbar',
        'App.view.basepreco.BprecoGrid',
        'App.view.basepreco.BprecoFiltro'
    ],
    
    title: 'Base Preços',
    layout: 'fit',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {

            items: [
                {
                    xtype: 'container',
                    layout: 'border',
                    width: '100%',
                    title: 'Parâmetros de Preço',
                    items:[
                        {
                            xtype: 'bprecotoolbar'
                        },
                        {
                            xtype: 'bprecofiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            text: 'Base Preço',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'bprecogrid'
                                }
                            ]

                        }
                    ]
                },
                {
                    xtype: 'panel',
                    title: 'Geração de Preço'
                    
                }

            ]

        });

        me.callParent(arguments);
    }
    
});
