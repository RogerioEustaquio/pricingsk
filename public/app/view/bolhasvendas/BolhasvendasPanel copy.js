Ext.define('App.view.bolhasvendas.BolhasvendasPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'bolhasvendaspanel',
    itemId: 'bolhasvendaspanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.bolhasvendas.BolhasvendasToolbar',
        'App.view.bolhasvendas.BolhasvendasFiltro',
        'App.view.bolhasvendas.BolhasVendasHighCharts'
    ],
    
    title: 'Bolhas Vendas',
    layout: 'fit',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {

            items: [
                {
                    xtype: 'panel',
                    layout: 'border',
                    width: '100%',
                    items:[
                        {
                            xtype: 'bolhasvendastoolbar'
                        },
                        {
                            xtype: 'bolhasvendasfiltro'
                        },
                        {
                            xtype: 'bolhasvendaschart',
                            region: 'center'

                        }
                    ]
                }

            ]

        });

        me.callParent(arguments);
    }
    
});
