Ext.define('App.view.analiseperformance.TabExplore', {
    extend: 'Ext.panel.Panel',
    xtype: 'tabexplore',
    itemId: 'tabexplore',
    closable: false,
    requires: [
        'App.view.analiseperformance.ToolbarExplore',
        'App.view.analiseperformance.ExploreFiltro',
        'App.view.analiseperformance.TreeGridExplore'
    ],
    
    title: 'Explore',
    layout: 'border',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {
            items: [
                {
                    xtype : 'toolbarexplore'
                },
                {
                    xtype: 'panel',
                    region: 'center',
                    layout: 'border',
                    items: [
                        {
                            xtype:'explorefiltro'
                        },
                        {
                            xtype: 'treegridexplore',
                            region: 'center'
                        }
                    ]
                }
            ]

        });

        me.callParent(arguments);
    }
    
});
