Ext.define('App.view.faixamargem.ChartsFaixaMargem', {
    extend: 'Ext.Container',
    xtype: 'chartsfaixamargem',
    itemId: 'chartsfaixamargem',
    // id: 'chartsbrandpositioning',
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
                                // me.chart.setSize(el.getWidth(), el.getHeight())
								me.chart.reflow();
                            }
                        },
                        afterrender: function(el){

                            me.setLoading({msg: 'Carregando...'});

                            Ext.Ajax.request({
                                url: BASEURL +'/api/faixamargem/faixamargem',
                                method: 'POST',
                                params: me.params,
                                async: true,
                                timeout: 240000,
                                success: function (response) {
                                    
                                    me.setLoading(false);
                                    var result = Ext.decode(response.responseText);
                                    if(result.success){
                                        nmPrincipal = result.nmPrincipal;
                                        xaxis       = result.xCategories;
                                        yaxis       = result.yCategories;
                                        zMinMax     = result.zMinMax;
                                        arraySerie  = result.data;

                                    }else{
                                        arraySerie = [];

                                        new Noty({
                                            theme: 'relax',
                                            layout: 'bottomRight',
                                            type: 'error',
                                            closeWith: [],
                                            text: 'Erro sistema: '+ result.message.substr(0,20)
                                        }).show();
                                    }
                                    
                                    me.buildChartContainer(el,arraySerie,yaxis,xaxis,zMinMax,nmPrincipal);
                                },
                                error: function() {
                                    
                                    me.setLoading(false);
                                    arraySerie = [];
                                    yaxis       = [];
                                    xaxis       = [];
                                    zMinMax     = [];
                                    nmPrincipal ='';

                                    me.buildChartContainer(el,arraySerie,yaxis,xaxis,zMinMax,nmPrincipal)

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

    buildChartContainer: function(el,series,yaxis,xaxis,zMinMax,nmPrincipal){
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');

        // console.log(series);
        
        function getPointCategoryName(point, dimension) {
            var series = point.series,
                isY = dimension === 'y',
                axis = series[isY ? 'yAxis' : 'xAxis'];
            return axis.categories[point[isY ? 'y' : 'x']];
        }

        me.chart =  Highcharts.chart(el.id, {

            credits:{
                enabled: false
            },
            chart: {
                type: 'heatmap',
                zoomType: 'xy',
                inverted: false
            },
            
            plotOptions: {
                series: {
                    dataLabels: {
                        // format: '<b>{point.value:.0f}',
                        formatter: function (el) {
                            var valor = this.point.options.value ;
                            valor = !valor ? '' : utilFormat.Value2(valor,0) ;

                            var obj = '<p>'+valor+'</p>';
                            return obj;
                        }
                    }
                }
            },

            title: {
                text: ''
            },
        
            xAxis: {
                categories: xaxis,
                title: null,
                reversed: false,
                dataLabels: {
                    enabled: false
                }
            },
        
            yAxis: {
                categories: yaxis,
                title: {
                    text: 'Margem'
                },
                reversed: true,
                dataLabels: {
                    enabled: false
                }
            },

            colorAxis: {
                stops: [
                    [0, '#ff0000'],
                    [0.5, '#ffff00'],
                    [0.9, '#00ff00']
                ],
                min: Number(zMinMax[0]),
                max: Number(zMinMax[1]),
                // startOnTick: false,
                // endOnTick: false,
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,0);
                    }
                },
                // tickPositions: [0, 0.2, 0.9]
                tickPositioner: function () {
                    var positions = [],
                        min = Number(zMinMax[0]),
                        max = Number(zMinMax[1]);
        
                    var med = zMinMax[1] / 5 ;

                    var n2 =  Number(min + med),
                        n3 =  Number(min + (med*2)),
                        n4 =  Number(min + (med*3)),
                        n5 =  Number(min + (med*4));

                    positions.push(max);
                    positions.push(n5);
                    positions.push(n4);
                    positions.push(n3);
                    positions.push(n2);
                    positions.push(min);

                    return positions;
                }
            },
        
            accessibility: {
                point: {
                    descriptionFormatter: function (point) {
                        var ix = point.index + 1,
                            xName = getPointCategoryName(point, 'x'),
                            yName = getPointCategoryName(point, 'y'),
                            val = point.value;

                        return ix + '. ' + xName + ' sales ' + yName + ', ' + val + '.';
                    }
                }
            },

            legend: {
                align: 'right',
                layout: 'vertical',
                margin: 4,
                verticalAlign: 'top',
                y: 40,
                symbolHeight: 300,
                width: 80
            },
        
            tooltip: {
                formatter: function () {
                    
                    var nValorPrincipal = this.point.series.options.nmPrincipal;
                    return '<b> Filial: </b>' +getPointCategoryName(this.point, 'x') + ' <br><b>'+nValorPrincipal.toUpperCase()+': </b>'+
                    utilFormat.Value2(this.point.value,0) + '<br><b>Margem: </b>' + getPointCategoryName(this.point, 'y') ;
                }
            },
        
            series: [{
                nmPrincipal: nmPrincipal,
                type: 'heatmap',
                borderWidth: 0,
                nullColor: '#EFEFEF',
                data: series,
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    style: {
                        fontSize: '8px',
                        textOutline: 'none'
                    }
                }
            }],
        
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        yAxis: {
                            labels: {
                                formatter: function () {
                                    return this.value.charAt(0);
                                }
                            }
                        }
                    }
                }]
            }        
        
        });

    }
});
