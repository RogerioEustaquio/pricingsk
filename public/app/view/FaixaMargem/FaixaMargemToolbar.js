Ext.define('App.view.faixamargem.FaixaMargemToolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'faixamargemtoolbar',
    itemId: 'faixamargemtoolbar',
    region: 'north',
    requires:[
        // 'App.view.fii.ContainerHighCharts'
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

        // var btnNotmarga = Ext.create('Ext.button.Button',{

        //     name: 'btnnotmarca',
        //     itemId: 'btnnotmarca',
        //     iconCls: 'fa fa-cog',
        //     tooltip: 'Excluir marcas selecionadas',
        //     margin: '1 1 1 4',
        //     handler: function(){

        //         var v = me.up('container').down('#produtofiltro').down('#notmarca').value;
        //         btnNotmarga.value = v;

        //         objWindow = Ext.create('Ext.window.Window',{
        //             title: 'Opção',
        //             scrollable: true,
        //             height: 90,
        //             width: 210,
        //             items: [
        //                 {
        //                     xtype: 'checkboxfield',
        //                     name: 'bxnotmarca',
        //                     itemId: 'bxnotmarca',
        //                     checked: this.value,
        //                     boxLabel: 'Excluir marcas selecionadas',
        //                     labelWidth: '70%',
        //                     labelAlign: 'right',
        //                     // margin: '2 2 2 2',
        //                     handler: function(){

        //                         vnotmarca = this.checked ? 1 : 0;
        //                         btnNotmarga.value = vnotmarca;
        //                         me.up('container').down('#produtofiltro').down('#notmarca').value = vnotmarca;
                               
        //                         setTimeout(function(){
        //                             objWindow.close();
        //                         },300);
        //                     }
        //                 }
        //             ]

        //         });

        //         objWindow.show();

        //     }
        // });
        
        Ext.applyIf(me, {

            items : [
                btnFiltro,
                btnConsultar,
                // btnNotmarga
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        // var vnotmarca = me.down('#btnnotmarca').value ? 1 : 0 ;
        // me.up('container').down('#faixamargemfiltro').down('#notmarca').value = vnotmarca;

        if(me.up('container').down('#faixamargemfiltro').hidden){
            me.up('container').down('#faixamargemfiltro').setHidden(false);
        }else{
            me.up('container').down('#faixamargemfiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');

        var faixamargemfiltro = me.up('container').down('#faixamargemfiltro') ;

        var codEmpresa      = faixamargemfiltro.down('#elEmp').getValue();
        var dataInicio      = faixamargemfiltro.down('#datainicio').getRawValue();
        var dataInicio      = faixamargemfiltro.down('#datainicio').getRawValue();
        var dataFinal       = faixamargemfiltro.down('#datafinal').getRawValue();

        // var notMarca        = faixamargemfiltro.down('#notmarca').value;
        var idMarcas        = faixamargemfiltro.down('#elMarca').getValue();
        var produtos        = faixamargemfiltro.down('#elProduto').getValue();
        var idProduto       = faixamargemfiltro.down('#eltagidproduto').getValue();

        var params = {
            codEmpresa: Ext.encode(codEmpresa),
            dataInicio: dataInicio,
            dataFinal: dataFinal,
            // notMarca: notMarca,
            idMarcas: Ext.encode(idMarcas),
            produtos: Ext.encode(produtos),
            idProduto: Ext.encode(idProduto)
        };

        var charts = me.up('panel').down('#chartsfaixamargem');

        charts.setLoading({msg: 'Carregando...'});

        var seriesLength = (charts.chart.series) ? charts.chart.series.length : 0 ;

        for(var i = seriesLength - 1; i > -1; i--)
        {
            charts.chart.series[i].remove();
        }

        charts.chart.update(false,false);

        Ext.Ajax.request({
            url: BASEURL +'/api/faixamargem/faixamargem',
            method: 'POST',
            params: params,
            async: true,
            timeout: 1080000,
            success: function (response) {
                var result = Ext.decode(response.responseText);

                charts.setLoading(false);
                charts.chart.update(false,false);
                // charts.chart.hideLoading();
                if(result.success){
                    xaxis       = result.xCategories;
                    yaxis       = result.yCategories;
                    arraySerie  = result.data;

                    // var extraUpdate = {
                    //     xAxis: {
                    //         categories: xaxis,
                    //             title: null,
                    //             reversed: false
                    //     },
                    
                    //     yAxis: {
                    //         categories: yaxis,
                    //         title: null,
                    //         reversed: true
                    //     },
                    // };

                    // charts.chart.update(extraUpdate);

                    charts.chart.xAxis[0].setCategories(xaxis);
                    charts.chart.yAxis[0].setCategories(yaxis);

                    var vSerie = {
                        name: '',
                        borderWidth: 0,
                        data: arraySerie,
                        dataLabels: {
                            enabled: true,
                            // borderWidth:0,
                            // borderColor: '#000000',
                            color: '#000000',
                            style: {
                                'border-width': '0px !important',
                                'border-color': '#000000 !important'
                            }
                        }
                    };
                    charts.chart.addSeries(vSerie);

                    setTimeout(function(){
                        charts.chart.redraw();
                    },250);

                }else{
                    arraySerie = [];
                    yaxis       = [];
                    xaxis       = [];

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

                charts.setLoading(false);
                arraySerie = [];
                yaxis       = [];
                xaxis       = [];

                var extraUpdate = {
                    xAxis: {
                        categories: xaxis,
                    },
                
                    yAxis: {
                        categories: yaxis
                    },
                    series: [{
                        data: arraySerie
                    }]
                };

                charts.chart.update(extraUpdate);

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
