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

        var btnchart = Ext.create('Ext.button.Button',{

            iconCls: 'fa fa-chart-bar',
            tooltip: 'Consultar',
            margin: '1 1 1 4',
            enableToggle: true,
            pressed: true,
            toggleHandler: me.onBtnChart
        });

        Ext.applyIf(me, {

            items : [
                btnFiltro,
                btnConsultar,
                btnchart
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        if(me.up('container').down('#analisemarcafiltro').hidden){
            me.up('container').down('#analisemarcafiltro').setHidden(false);
        }else{
            me.up('container').down('#analisemarcafiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');

    },

    onBtnChart: function(btn, pressed){

        var me = this.up('toolbar');

        var panelChart = me.up('container').down('#analisemarcachart');

        if(pressed){

            panelChart.setHidden(false);
            // this.btnIconEl.addCls('red-text');
            btn.setStyle('background-color: #ff0000 !important');
            
        }else{
            
            panelChart.setHidden(true);
            // this.btnIconEl.addCls('black-text');
            btn.setStyle('background-color: #00ff00 !important');
            
        }
    }

});
