Ext.define('App.view.dispersaovenda.DispersaoVendaPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'dispersaovendapanel',
    itemId: 'dispersaovendapanel',
    title: 'Dispersão Venda',
    requires: [
        'App.view.dispersaovenda.DispersaoVendaToolbar',
        'App.view.dispersaovenda.DispersaoVendaFiltro',
        'App.view.dispersaovenda.ChartsDispersaoVenda'
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
                            xtype: 'dispersaovendatoolbar'
                        },
                        {
                            xtype: 'dispersaovendafiltro'
                        },
                        {
                            xtype: 'chartsdispersaovenda',
                            region: 'center'
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);
    }
    
});
