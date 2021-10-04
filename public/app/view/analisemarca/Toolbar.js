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
            hidden: false,
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

        var optionWindow = {
            title: 'Indicadores Adicionais',
            scrollable: true,
            height: 200,
            width: 200,
            items: [
                // {
                //     xtype: 'checkboxfield',
                //     margin: '2 2 2 2',
                //     labelWidth: 90,
                //     fieldLabel: 'Faixa Custo',
                //     name: 'faixaCusto',
                //     idItem: 'faixaCusto',
                //     // checked: false
                // },
                {
                    xtype: 'checkboxfield',
                    margin: '2 2 2 2',
                    labelWidth: 90,
                    fieldLabel: 'Estoque',
                    name: 'estoque',
                    idItem: 'estoque',
                    // checked: false
                }
            ],
            bbar:[
                '->',
                {
                    xtype:'button',
                    text: 'Salvar',
                    handler: function(){
                        var meWindow = this.up('window');
                        var array = new Array();

                        array.push({
                            name : 'estoque',
                            value: meWindow.down('checkboxfield[name=estoque]').getValue()
                        });

                        me.indicadoresAdd = array;

                        meWindow.close();
                    },
                    listeners: {
                        afterrender: function(){
                            var meWindow = this.up('window');

                            if(me.indicadoresAdd){
                                for (let index = 0; index < me.indicadoresAdd.length; index++) {

                                    if(me.indicadoresAdd[index].name == "estoque"){
                                        if(me.indicadoresAdd[index].value)
                                            meWindow.down('checkboxfield[name=estoque]').setValue(true);
                                    }
                                }
                            }
                            
                        }
                    }

                }
            ]
        };

        Ext.applyIf(me, {

            items : [
                btnFiltro,
                btnConsultar,
                btnchart,
                '->',
                {
                    xtype: 'button',
                    text: 'Indicadores Adicionais',
                    tooltip: 'Indicadores Adicionais',
                    margin: '1 1 1 4',
                    hidden: false,
                    handler: function(){
                        Ext.create('Ext.window.Window',optionWindow).show();
                    }
                }
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
        
        var charts = me.up('container').down('#panelcenter').down('#analisemarcachart');

        charts.setLoading({msg: 'Carregando...'});

        var idEmpresas  = me.up('container').down('#analisemarcafiltro').down('#elEmp').getValue();
        var regional    = me.up('container').down('#analisemarcafiltro').down('#elRegional').getValue();
        var data        = me.up('container').down('#analisemarcafiltro').down('#data').getRawValue();
        var meses24     = me.up('container').down('#analisemarcafiltro').down('radiofield[inputValue=24]');
        var meses36     = me.up('container').down('#analisemarcafiltro').down('radiofield[inputValue=36]');
        var idproduto   = me.up('container').down('#analisemarcafiltro').down('#eltagidproduto').getValue();
        var produto     = me.up('container').down('#analisemarcafiltro').down('#elProduto').getValue();
        var marca       = me.up('container').down('#analisemarcafiltro').down('#elMarca').getValue();
        var montadora   = me.up('container').down('#analisemarcafiltro').down('#elMontadora').getValue();

        var qtdemeses = 12;

        if(meses24.checked){
            qtdemeses = 24;
        }
        if(meses36.checked){
            qtdemeses = 36;
        }
        
        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            regional: Ext.encode(regional),
            data: data,
            qtdemeses : qtdemeses,
            idProduto:  Ext.encode(idproduto),
            produto:  Ext.encode(produto),
            marca: Ext.encode(marca),
            indicadoresAdd: Ext.encode(me.indicadoresAdd),
            montadora : Ext.encode(montadora)
        };

        var seriesOrig = Array();
        var seriesCores= Array();
        var seriesLength = (charts.chart.series) ? charts.chart.series.length : 0 ;

        for (let index = 0; index < seriesLength; index++) {

            seriesCores.push(charts.chart.series[index].color);

            if(charts.chart.series[index].visible){
                seriesOrig.push({visible: true});
            }else{
                seriesOrig.push({visible: false});
            }
            
        }
  
        for(var i = seriesLength - 1; i > -1; i--)
        {
            charts.chart.series[i].remove();
        }

        charts.chart.update(false,false);

        Ext.Ajax.request({
            url: BASEURL +'/api/analisemarca/listarfichaitemgrafico',
            method: 'POST',
            params: params,
            async: true,
            timeout: 240000,
            success: function (response) {
                var result = Ext.decode(response.responseText);

                charts.setLoading(false);
                // charts.chart.hideLoading();
                if(result.success){

                    rsarray = result.data;
                    var cont = 0;
                    
                    charts.chart.xAxis[0].setCategories(rsarray.categories);

                    rsarray.series.forEach(function(record){

                        record.visible      = seriesOrig[cont].visible;
                        record.color        = seriesCores[cont];
                        record.showInLegend = charts.showLegend[cont];
                        charts.chart.addSeries(record);
                        cont++;
                    });

                }else{
                    rsarray = [];

                    new Noty({
                        theme: 'relax',
                        layout: 'bottomRight',
                        type: 'error',
                        closeWith: [],
                        text: 'Erro sistema: '+ result.message.substr(0,20)
                    }).show();
                }
                
            },
            error: function() {
                rsarray = [];
                charts.setLoading(false);
                // charts.chart.hideLoading();

                new Noty({
                    theme: 'relax',
                    layout: 'bottomRight',
                    type: 'error',
                    closeWith: [],
                    text: 'Erro sistema: '+ result.message.substr(0,20)
                }).show();
            }
        });        

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
