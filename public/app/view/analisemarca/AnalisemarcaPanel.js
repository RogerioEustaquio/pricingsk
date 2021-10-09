Ext.define('App.view.analisemarca.AnalisemarcaPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'analisemarcapanel',
    itemId: 'analisemarcapanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.analisemarca.Toolbar',
        'App.view.analisemarca.AnalisemarcaFiltro',
        'App.view.analisemarca.ContainerHighCharts',
        'App.view.analisemarca.ProdutoTPanel'
    ],
    
    title: 'Análise Marca',
    layout: 'fit',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {

            items: [
                {
                    xtype: 'container',
                    layout: 'border',
                    width: '100%',
                    items:[
                        {
                            xtype: 'analisemarcatoolbar'
                        },
                        {
                            xtype: 'analisemarcafiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            region: 'center',
                            layout: 'border',
                            items:[
                                {
                                    xtype: 'analisemarcachart',
                                    hidden: false,
                                    region: 'north'
                                },
                                {
                                    xtype: 'tabpanel',
                                    itemId: 'listaspanel',
                                    region: 'center',
                                    items:[
                                        {
                                            xtype: 'produtotpanel'
                                        }
                                    ]
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
