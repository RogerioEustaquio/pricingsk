Ext.define('App.view.analisegrafica.AnalisegraficaPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'analisegraficapanel',
    itemId: 'analisegraficapanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.analisegrafica.Toolbar',
        'App.view.analisegrafica.ContainerHighCharts',
        'App.view.analisegrafica.AnalisegraficaFiltro'
    ],
    
    title: 'Análise Gráfica de Indicadores',
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
                            xtype: 'analisegraficatoolbar'
                        },
                        {
                            xtype: 'analisegraficafiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            region: 'center',
                            layout: 'border',
                            items:[
                                {
                                    xtype: 'analisegraficachart',
                                    region: 'north'
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
