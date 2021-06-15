Ext.define('App.view.Viewport', {
    extend: 'Ext.Viewport',
    layout: 'border',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {
            items: [
                {
                    region: 'north',
                    xtype: 'apptoolbar'
                }
            ]
        });

        me.callParent(arguments);
    }
});