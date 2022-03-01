Ext.define('App.view.faixamargem.FaixaMargemPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'faixamargempanel',
    itemId: 'faixamargempanel',
    // title: 'Home',
    requires: [
        'App.view.faixamargem.FaixaMargemToolbar',
        'App.view.faixamargem.FaixaMargemFiltro',
        'App.view.faixamargem.ChartsFaixaMargem'
    ],
    
    layout: 'fit',
    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {
            items: [
                {
                    xtype:'panel',
                    layout: 'border',
                    width: '100%',
                    items: [
                        {
                            xtype: 'faixamargemtoolbar'
                        },
                        {
                            xtype: 'faixamargemfiltro'
                        },
                        {
                            xtype: 'chartsfaixamargem',
                            region: 'center'
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }
    
});
