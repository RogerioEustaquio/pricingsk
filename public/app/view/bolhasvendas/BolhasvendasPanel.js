Ext.define('App.view.bolhasvendas.BolhasvendasPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'bolhasvendaspanel',
    itemId: 'bolhasvendaspanel',
    height: Ext.getBody().getHeight() * 0.9,
    width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.bolhasvendas.VendaBubbleTab'
    ],
    
    title: 'Bolhas Vendas',
    border: false,
    layout: 'border',
    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {

            items: [
                {
                    xtype:'tabpanel',
                    region: 'center',
                    // layout: 'fit',
                    items:[
                        {
                            xtype: 'vendabubbletab'
                        },
                        // {
                        //     xtype: 'tabfilial'
                        // }
                    ]
                   
                }

            ]

        });

        me.callParent(arguments);
    }
    
});
