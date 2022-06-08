Ext.define('App.view.dispersaovenda.ChartsDispersaoVenda', {
    extend: 'Ext.Container',
    xtype: 'chartsdispersaovenda',
    itemId: 'chartsdispersaovenda',
    width: '100%',
    height: '60%',
    // margin: '10 2 2 2',
    style: {
        background: '#ffffff'
    },
    requires: [ 
    ],
    showLegend: [],
    // controller: 'chart',
    layout: 'border',
    border: true,

    chart: null,

    constructor: function(config) {
        var me = this;
        // var utilFormat = Ext.create('Ext.ux.util.Format');
        me.showLegend = [];

        Ext.applyIf(me, {
            items: [
                {
                    region: 'center',
                    xtype: 'container',
								  
                    flex: 1,
                    listeners: {
                        afterLayout: function(el){
                            if(me.chart){
								me.chart.reflow();
                            }
                        },
                        afterrender: function(el){

                            me.setLoading({msg: 'Carregando...'});

                            Ext.Ajax.request({
                                url: BASEURL +'/api/dispersaovenda/dispersaovenda2',
                                method: 'POST',
                                params: '',
                                async: true,
                                timeout: 240000,
                                success: function (response) {
                                    
                                    me.setLoading(false);
                                    var result = Ext.decode(response.responseText);

                                    var arraySerie = '';
                                    if(result.success){

                                        var dataInicio = result.referencia.inicio;
                                        var dataFim    = result.referencia.fim;
                                        var rsarray = result.data;

                                        var turboThred = result.contTotal.total + 10 ;

                                        arraySerie = [
                                            {
                                                // boostThreshold: 0,
                                                turboThreshold : result.contTotal.produto+10,
                                                type: 'scatter',
                                                name: 'Produto',
                                                color: Highcharts.getOptions().colors[2],
                                                data : rsarray[2].data,
                                                marker: {
                                                    radius: 3
                                                }
                                            },
                                            {
                                                // boostThreshold: 0,
                                                turboThreshold : result.contTotal.nota+10,
                                                type: 'scatter',
                                                name: 'Nota',
                                                color: Highcharts.getOptions().colors[1],
                                                data : rsarray[1].data,
                                                marker: {
                                                    radius: 3
                                                }
                                            },
                                            {
                                                // boostThreshold: 0,
                                                turboThreshold : result.contTotal.cliente+10,
                                                type: 'scatter',
                                                name: 'Cliente',
                                                color: Highcharts.getOptions().colors[0],
                                                data : rsarray[0].data,
                                                marker: {
                                                    radius: 3
                                                }
                                            },
                                            {
                                                // boostThreshold: 0,
                                                turboThreshold : result.contTotal.categoria+10,
                                                type: 'scatter',
                                                name: 'Categoria',
                                                color: Highcharts.getOptions().colors[3],
                                                data : rsarray[3].data,
                                                marker: {
                                                    radius: 3
                                                }
                                            },
                                            {
                                                // boostThreshold: 0,
                                                turboThreshold : result.contTotal.marca+10,
                                                type: 'scatter',
                                                name: 'Marca',
                                                color: Highcharts.getOptions().colors[4],
                                                data : rsarray[4].data,
                                                marker: {
                                                    radius: 3
                                                }
                                            },
                                            {
                                                // boostThreshold: 0,
                                                turboThreshold : result.contTotal.loja+10,
                                                type: 'scatter',
                                                name: 'Loja',
                                                color: Highcharts.getOptions().colors[5],
                                                data : rsarray[5].data,
                                                marker: {
                                                    radius: 3
                                                }
                                            }
                                        ];

                                    }else{

                                        
                                        arraySerie = [
                                            {
                                                name: 'Nota',
                                                color: Highcharts.getOptions().colors[0],
                                                data : []
                                            },
                                            {
                                                name: 'Cliente',
                                                color: Highcharts.getOptions().colors[1],
                                                data : []
                                            },
                                            {
                                                name: 'Produto',
                                                color: Highcharts.getOptions().colors[2],
                                                data : []
                                            },
                                            {
                                                name: 'Categoria',
                                                color: Highcharts.getOptions().colors[3],
                                                data : []
                                            },
                                            {
                                                name: 'Marca',
                                                color: Highcharts.getOptions().colors[4],
                                                data : []
                                            },
                                            {
                                                name: 'Loja',
                                                color: Highcharts.getOptions().colors[5],
                                                data : []
                                            }
                                        ];

                                        new Noty({
                                            theme: 'relax',
                                            layout: 'bottomRight',
                                            type: 'error',
                                            closeWith: [],
                                            text: 'Erro sistema: '+ result.message.substr(0,20)
                                        }).show();
                                    }
                                    
                                    textSubtitle = dataInicio + ' até ' + dataFim ;


                                    me.buildChartContainer(el,arraySerie,textSubtitle);

                                },
                                error: function() {
                                    
                                    me.setLoading(false);
                                    
                                    var arraySerie = [
                                        {
                                            name: 'Nota',
                                            color: Highcharts.getOptions().colors[0],
                                            data : []
                                        },
                                        {
                                            name: 'Cliente',
                                            color: Highcharts.getOptions().colors[1],
                                            data : []
                                        },
                                        {
                                            name: 'Produto',
                                            color: Highcharts.getOptions().colors[2],
                                            data : []
                                        },
                                        {
                                            name: 'Categoria',
                                            color: Highcharts.getOptions().colors[3],
                                            data : []
                                        },
                                        {
                                            name: 'Marca',
                                            color: Highcharts.getOptions().colors[4],
                                            data : []
                                        },
                                        {
                                            name: 'Loja',
                                            color: Highcharts.getOptions().colors[5],
                                            data : []
                                        }
                                    ];

                                    var textSubtitle = '';

                                    me.buildChartContainer(el,arraySerie,textSubtitle);

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
                    }
                }
            ]
        });

        me.callParent(arguments);
    },

    buildChartContainer: function(el,series,textSubtitle){
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');
        
        me.chart =  Highcharts.chart(el.id, {

            chart: {
                // type: 'scatter',
                zoomType: 'xy'
            },
        
            credits:{
                enabled: false
            },

            boost: {
                useGPUTranslations: true,
                usePreAllocated: true
            },
        
            title: {
                text: 'Dispersão Venda',
                style: {
                    fontSize: '14px'
                }
            },
        
            subtitle: {
                text: textSubtitle
            },

            xAxis: {
                min: 0,
                gridLineWidth: 1,
                startOnTick: true,
                endOnTick: true,
                title: {
                    text: 'ROL'
                },
                labels: {
                    formatter: function () {
                        return utilFormat.ValueZero(this.value);
                    }
                }
            },
        
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'MB'
                }
            },

            legend: {
                enabled: true,
                // layout: 'vertical',
                layout: 'horizontal',
                width: 200,
                margin: '1 1 1 1',
                align: 'left',
                verticalAlign: 'top',
                x: 100,
                y: 0,
                floating: true,
                backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
                borderWidth: 1
            },
            
            // accessibility: {
            //     screenReaderSection: {
            //         beforeChartFormat: '<{headingTagName}>{chartTitle}</{headingTagName}><div>{chartLongdesc}</div><div>{xAxisDescription}</div><div>{yAxisDescription}</div>'
            //     }
            // },

            // plotOptions: {
            //     series: {
            //         turboThreshold : Number(turboThred)
            //     }
            // },

            tooltip: {
                formatter: function () {

                    var pointFormat = '';
                    var descricao = this.point.descricao ? this.point.descricao : '';

                    pointFormat += '<b>'+this.point.nome+' '+''+'</b><br>';
                    pointFormat += '<p>'+descricao+' '+''+'</p><br>';
                    pointFormat += '<b>ROL: </b><label>'+utilFormat.Value2(this.point.x,0)+'</label><br>';
                    pointFormat += '<b>MB: </b><label>'+utilFormat.Value2(this.point.y,2)+'</label><br>';

                    return pointFormat;
                }
            },

            series: series
        
        });

    }
});
