Ext.define('App.view.basepreco.BaseprecoPanel', {
    extend: 'Ext.tab.Panel',
    xtype: 'baseprecopanel',
    itemId: 'baseprecopanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.basepreco.Toolbar',
        'App.view.basepreco.BprecoGrid',
        'App.view.basepreco.BprecoFiltro',
        'App.view.basepreco.ToolbarPreco',
        'App.view.basepreco.GridPreco',
        'App.view.basepreco.FiltroPreco',
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
                    title: 'Geração de Preço',
                    layout: 'border',
                    width: '100%',
                    items:[
                        {
                            xtype: 'toolbarpreco'
                        },
                        {
                            xtype: 'filtropreco'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenterpreco',
                            text: 'Base Preço',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'gridpreco'
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
