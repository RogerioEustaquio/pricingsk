Ext.define('App.view.analisemarca.ProdutoTPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'produtotpanel',
    itemId: 'produtotpanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.analisemarca.RankGrid'
    ],
    
    title: 'Produto',
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
                            xtype: 'toolbar',
                            region: 'north',
                            items:[
                                {
                                    xtype: 'button',
                                    text: 'Rank',
                                    handler: function(){
                                        this.up('panel').down('#centerproduto').setActiveItem(0);
                                    }
                                },
                                {
                                    xtype: 'button',
                                    text: 'Tipo',
                                    handler: function(){
                                        this.up('panel').down('#centerproduto').setActiveItem(1);
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'panel',
                            itemId:'centerproduto',
                            region: 'center',
                            layout: 'card',
                            items: [
                                {
                                    xtype:'rankgrid'
                                },
                                {
                                    xtype:'panel',
                                    title: 'Tipo'
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
