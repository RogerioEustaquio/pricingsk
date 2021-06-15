Ext.define('App.view.home.PanelIndicadores',{
    extend: 'Ext.container.Container',
    xtype: 'panelindicadores',
    itemId: 'panelindicadores',
    margin: '1 1 1 1',
    requires: [
        // 'App.view.bpreco.ContainerHighCharts',
    ],
    
    // title: 'Painel de Indicadores',
    layout: {
        type: 'table',
        columns: 3,
        tableAttrs: {
            style: {
                width: '100%'
            }
        }
    },

    defaults: {
        margin: '1 1 1 1',
        height: 220,
        layout: 'border',
        ui: 'light'
    },
    height: Ext.getBody().getHeight() * 0.9,
    scrollable: true,

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {

            items: [
                {
                    // html: 'Cell B content',

                    title: 'Base Preços',
                    items: [ { xtype: 'basepreco', region: 'center', flex: 1, margin: '0 0 0 0' }],
                    tools: [
                        // {
                        //     type: 'restore',
                        //     handler: function() {
                        //         // show help here
                        //     }
                        // }
                    ]
                }
            ]

        });

        me.callParent(arguments);
    }
    
});
