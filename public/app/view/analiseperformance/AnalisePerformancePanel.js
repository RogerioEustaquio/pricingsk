Ext.define('App.view.analiseperformance.AnalisePerformancePanel', {
    extend: 'Ext.panel.Panel',
    xtype: 'analiseperformancepanel',
    itemId: 'analiseperformancepanel',
    height: Ext.getBody().getHeight() * 0.9,
    width: Ext.getBody().getWidth() * 0.9,
    requires: [
        'App.view.analiseperformance.TabExplore',
        'App.view.analiseperformance.TabFilial',
        'App.view.analiseperformance.TabMarca',
        'App.view.analiseperformance.TabCategoria'
    ],
    
    title: 'Análise Performance',
    border: false,
    layout: 'border',

    initComponent: function() {
        var me = this;
        
        Ext.applyIf(me, {

            items: [
                {
                    xtype:'tabpanel',
                    region: 'center',
                    items:[
                        {
                            xtype: 'tabexplore'
                        },
                        {
                            xtype: 'tabmarca'
                        },
                        {
                            xtype: 'tabcategoria'
                        }
                    ]
                   
                }

            ]

        });

        me.callParent(arguments);
    },

    // ontabs: function(tab){

    //     var me = this;
    //     var tabSelec = me.down('#'+tab);

    //     if(tabSelec){
    //         me.down('tabpanel').setActiveItem(tabSelec);
    //     }else{

    //         if(tab == 'tabloja'){
    //             var tabAdd = Ext.create('App.view.rpe.TabLoja');
    //         }
    //         if(tab == 'tabmarca'){
    //             var tabAdd = Ext.create('App.view.rpe.TabMarca');
    //         }
    //         if(tab == 'tabproduto'){ 
    //             var tabAdd = Ext.create('App.view.rpe.TabProduto');
    //         }

    //         me.down('tabpanel').add(tabAdd);
    //         me.down('tabpanel').setActiveItem(tabAdd);
    //     }
    // }
    
});
