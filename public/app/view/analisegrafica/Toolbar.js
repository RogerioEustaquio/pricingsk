Ext.define('App.view.analisegrafica.Toolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'analisegraficatoolbar',
    itemId: 'analisegraficatoolbar',
    region: 'north',
    requires:[
        'App.view.analisegrafica.ContainerHighCharts'
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

        console.log(me.up('container').down('#analisegraficafiltro'));

        if(me.up('container').down('#analisegraficafiltro').hidden){
            me.up('container').down('#analisegraficafiltro').setHidden(false);
        }else{
            me.up('container').down('#analisegraficafiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');
        
        var charts = me.up('container').down('#panelcenter').down('#analisegraficachart');

        charts.setLoading({msg: 'Carregando...'});

        var idEmpresas  = me.up('container').down('#analisegraficafiltro').down('#elEmp').getValue();
        var data  = me.up('container').down('#analisegraficafiltro').down('#data').getRawValue();
        var idproduto  = me.up('container').down('#analisegraficafiltro').down('#eltagidproduto').getValue();
        var produto  = me.up('container').down('#analisegraficafiltro').down('#elProduto').getValue();
        var marca  = me.up('container').down('#analisegraficafiltro').down('#elMarca').getValue();
        
        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            data: data,
            idProduto:  Ext.encode(idproduto),
            produto:  Ext.encode(produto),
            marca: Ext.encode(marca)
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
            url: BASEURL +'/api/analisegrafico/listarfichaitemgrafico',
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

    }

});
