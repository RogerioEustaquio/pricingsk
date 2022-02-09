Ext.define('App.view.basepreco.BaseprecoPanel', {
    extend: 'Ext.tab.Panel',
    xtype: 'baseprecopanel',
    itemId: 'baseprecopanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.basepreco.ParamPrecoToolbar',
        'App.view.basepreco.ParamPrecoGrid',
        'App.view.basepreco.ParamPrecoFiltro',
        'App.view.basepreco.GeraPrecoToolbar',
        'App.view.basepreco.GeraPrecoGrid',
        'App.view.basepreco.GeraPrecoFiltro',
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
                            xtype: 'paramprecotoolbar'
                        },
                        {
                            xtype: 'paramprecofiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'paramprecogrid'
                                }
                            ]

                        }
                    ]
                },
                {
                    xtype: 'panel',
                    title: 'Geração de Preço',
                    layout: 'border',
                    width: '100%',
                    items:[
                        {
                            xtype: 'geraprecotoolbar'
                        },
                        {
                            xtype: 'geraprecofiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenterpreco',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'geraprecogrid'
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
