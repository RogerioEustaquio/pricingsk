Ext.define('App.view.analiseperformance.TabExplore', {
    extend: 'Ext.panel.Panel',
    xtype: 'tabexplore',
    itemId: 'tabexplore',
    closable: false,
    requires: [
        'App.view.analiseperformance.ToolbarExplore',
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
                    layout: 'fit',
                    items: [
                        {
                            xtype: 'treegridexplore'
                        }
                    ]
                }
            ]

        });

        me.callParent(arguments);
    }
    
});
