Ext.define('App.view.grupodesconto.GrupodescontoPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'grupodescontopanel',
    itemId: 'grupodescontopanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.grupodesconto.Toolbar',
        'App.view.grupodesconto.GrupodescontoFiltro',
        'App.view.grupodesconto.GrupodescontoGrid'
    ],
    
    title: 'Grupo Desconto',
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
                            xtype: 'grupodescontotoolbar'
                        },
                        {
                            xtype: 'grupodescontofiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            text: 'Grupo Desconto',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'grupodescontogrid'
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
