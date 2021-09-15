Ext.define('App.view.analisemarca.Toolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'analisemarcatoolbar',
    itemId: 'analisemarcatoolbar',
    region: 'north',
    requires:[
        // 'App.view.analisegrafica.ContainerHighCharts'
    ],
    // indicadoresAdd: null,

    initComponent: function() {
        var me = this;

        var btnFiltro = Ext.create('Ext.button.Button',{
            
            iconCls: 'fa fa-filter',
            tooltip: 'Filtro',
            margin: '1 1 1 4',
            handler: me.onBtnFiltros
        });

        var btnConsultar = Ext.create('Ext.button.Button',{

            iconCls: 'fa fa-search',
            tooltip: 'Consultar',
            margin: '1 1 1 4',
            hidden: true,
            handler: me.onBtnConsultar
        });

        
        Ext.applyIf(me, {

            items : [
                btnFiltro,
                btnConsultar
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        console.log(me.up('container').down('#analisemarcafiltro'));

        if(me.up('container').down('#analisemarcafiltro').hidden){
            me.up('container').down('#analisemarcafiltro').setHidden(false);
        }else{
            me.up('container').down('#analisemarcafiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');


    }

});
