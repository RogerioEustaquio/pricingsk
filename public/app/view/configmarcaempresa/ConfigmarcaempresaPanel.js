Ext.define('App.view.configmarcaempresa.ConfigmarcaempresaPanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'configmarcaempresapanel',
    itemId: 'configmarcaempresapanel',
    // height: Ext.getBody().getHeight() * 0.9,
    // width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.configmarcaempresa.Toolbar',
        'App.view.configmarcaempresa.ConfigmarcaempresaFiltro',
        'App.view.configmarcaempresa.ConfigmarcaempresaGrid'
    ],
    
    title: 'Config. Marca Empresa',
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
                            xtype: 'configmarcaempresatoolbar'
                        },
                        {
                            xtype: 'configmarcaempresafiltro'
                        },
                        {
                            xtype: 'panel',
                            itemId: 'panelcenter',
                            text: 'Config. Marca Empresa',
                            region: 'center',
                            layout: 'fit',
                            items:[
                                {
                                    xtype: 'configmarcaempresagrid'
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
