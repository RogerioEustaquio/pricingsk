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

        var optionWindow = {
            title: 'Indicadores Adicionais',
            scrollable: true,
            height: 200,
            width: 200,
            items: [
                {
                    xtype: 'checkboxfield',
                    margin: '2 2 2 2',
                    labelWidth: 90,
                    fieldLabel: 'Faixa Custo',
                    name: 'faixaCusto',
                    idItem: 'faixaCusto',
                    // checked: false
                },
                {
                    xtype: 'checkboxfield',
                    margin: '2 2 2 2',
                    labelWidth: 90,
                    fieldLabel: 'Estoque',
                    name: 'estoque',
                    idItem: 'estoque',
                    // checked: false
                },
                {
                    xtype: 'checkboxfield',
                    margin: '2 2 2 2',
                    labelWidth: 90,
                    fieldLabel: 'Cliente',
                    name: 'cliente',
                    idItem: 'cliente',
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
                            name : 'faixaCusto',
                            value: meWindow.down('checkboxfield[name=faixaCusto]').checked
                        });
                        array.push({
                            name : 'estoque',
                            value: meWindow.down('checkboxfield[name=estoque]').checked
                        });
                        array.push({
                            name : 'cliente',
                            value: meWindow.down('checkboxfield[name=cliente]').checked
                        });

                        me.indicadoresAdd = array;

                        meWindow.close();
                    },
                    listeners: {
                        afterrender: function(){
                            var meWindow = this.up('window');

                            if(me.indicadoresAdd){
                                for (let index = 0; index < me.indicadoresAdd.length; index++) {

                                    if(me.indicadoresAdd[index].name == "faixaCusto"){
                                        meWindow.down('checkboxfield[name=faixaCusto]').setValue(me.indicadoresAdd[index].value);
                                        meWindow.down('checkboxfield[name=faixaCusto]').checked = me.indicadoresAdd[index].value;
                                    }
                                    if(me.indicadoresAdd[index].name == "estoque"){
                                        meWindow.down('checkboxfield[name=estoque]').setValue(me.indicadoresAdd[index].value);
                                        meWindow.down('checkboxfield[name=estoque]').checked = me.indicadoresAdd[index].value;
                                    }
                                    if(me.indicadoresAdd[index].name == "cliente"){
                                            meWindow.down('checkboxfield[name=cliente]').setValue(me.indicadoresAdd[index].value);
                                            meWindow.down('checkboxfield[name=cliente]').checked = me.indicadoresAdd[index].value;
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
        var regional    = me.up('container').down('#analisegraficafiltro').down('#elRegional').getValue();
        var data        = me.up('container').down('#analisegraficafiltro').down('#data').getRawValue();
        var meses24     = me.up('container').down('#analisegraficafiltro').down('radiofield[inputValue=24]');
        var meses36     = me.up('container').down('#analisegraficafiltro').down('radiofield[inputValue=36]');
        var idproduto   = me.up('container').down('#analisegraficafiltro').down('#eltagidproduto').getValue();
        var produto     = me.up('container').down('#analisegraficafiltro').down('#elProduto').getValue();
        var marca       = me.up('container').down('#analisegraficafiltro').down('#elMarca').getValue();
        var montadora   = me.up('container').down('#analisegraficafiltro').down('#elMontadora').getValue();

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
