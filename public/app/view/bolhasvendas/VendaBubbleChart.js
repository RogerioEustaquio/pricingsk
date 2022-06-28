Ext.define('App.view.bolhasvendas.VendaBubbleChart', {
    extend: 'Ext.Container',
    xtype: 'vendabubblechart',
    itemId: 'vendabubblechart',
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

                            var idEixos = {
                                x: 'rol',
                                y: 'mb',
                                z: 'cc'
                            }
                            
                            Ext.Ajax.request({
                                url: BASEURL +'/api/bolhasvendas/posicionamento',
                                method: 'POST',
                                params: me.params,
                                async: true,
                                timeout: 240000,
                                success: function (response) {
                                    
                                    me.setLoading(false);
                                    var result = Ext.decode(response.responseText);
                                    if(result.success){

                                        rsarray = result.data;

                                        var x='',y='',z='';
                                        var iCont = 0
                                        rsarray.forEach(function(rowSerie){

                                            x = rowSerie[idEixos.x];
                                            y = rowSerie[idEixos.y];
                                            z = rowSerie[idEixos.z];

                                            rsarray[iCont].x = parseFloat(x);
                                            rsarray[iCont].y = parseFloat(y);
                                            rsarray[iCont].z = parseFloat(z);

                                            iCont++;
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

                                    me.buildChartContainer(el,rsarray);

                                    textSubtitle = {
                                        subtitle:{
                                            text: result.referencia.incio + ' até ' + result.referencia.fim
                                        }
                                    };

                                    me.chart.update(textSubtitle);
                                },
                                error: function() {
                                    
                                    me.setLoading(false);
                                    rsarray = [];

                                    me.buildChartContainer(el,rsarray);

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

    buildChartContainer: function(el,series){
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');

        me.chart =  Highcharts.chart(el.id, {

            chart: {
                type: 'bubble',
                plotBorderWidth: 1,
                zoomType: 'xy'
            },
        
            legend: {
                enabled: false
            },
            credits:{
                enabled: false
            },
        
            title: {
                text: 'Posicionamento de Filial',
                style: {
                    fontSize: '14px'
                }
            },
        
            subtitle: {
                text: ' '
            },
            // accessibility: {
            //     point: {
            //         valueDescriptionFormat: '{index}. {point.desc}, MB: {point.x}, ROL: {point.y}, cc: {point.z}'
            //     }
            // },
        
            xAxis: {
                min: 0,
                gridLineWidth: 1,
                title: {
                    text: 'ROL'
                },
                labels: {
                   formatter: function () {
                    return utilFormat.Value2(this.value,0);
                   }
                },
                // },
                // plotLines: [{
                //     color: 'black',
                //     dashStyle: 'dot',
                //     width: 2,
                //     value: 65,
                //     label: {
                //         rotation: 0,
                //         y: 15,
                //         style: {
                //             fontStyle: 'italic'
                //         },
                //         text: 'Safe fat intake 65g/day'
                //     },
                //     zIndex: 3
                // }],
                // accessibility: {
                //     rangeDescription: 'Range: 60 to 100 grams.'
                // }
            },
        
            yAxis: {
                min: 0,
                // max: 100,
                startOnTick: false,
                endOnTick: false,
                title: {
                    text: 'MB'
                },
                labels: {
                    format: '{value} MB'
                },
                maxPadding: 0.2,
                
                labels: {
                    // formatter: function () {
                    //     return utilFormat.Value2(this.value,0);
                    // },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        // color: Highcharts.getOptions().colors[1],
                        fontSize: '10px',
                        border: '0px'
                    }
                },
            },
        
            tooltip: {
                useHTML: true,
                formatter: function () {

                    var pointFormat = '<table>';
                    pointFormat += '<tr><th colspan="2">'+this.point.descricao+'</th></tr>';
                    pointFormat += '<tr><th align="left">ROL:</th><td  align="left">'+utilFormat.Value2(this.point.x,0)+'</td></tr>';
                    pointFormat += '<tr><th align="left">MB:</th><td  align="left">'+utilFormat.Value2(this.point.y,2)+'</td></tr>';
                    pointFormat += '<tr><th align="left">CC:</th><td  align="left">'+utilFormat.Value2(this.point.z,0)+'</td></tr>';
                    pointFormat += '</table>';

                    return pointFormat;
                },
                followPointer: true
            },
        
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.ds}'
                    },
                    events: {
                        click: function (event) {
                            console.log(' ');
                        }
                    }
                }
            },
        
            series: [{
                data: series
                // data: [
                //     { x: 95, y: 95, z: 13.8, name: 'BE', country: 'Belgium' },
                //     { x: 86.5, y: 102.9, z: 14.7, name: 'DE', country: 'Germany' },
                //     { x: 80.8, y: 91.5, z: 15.8, name: 'FI', country: 'Finland' },
                //     { x: 80.4, y: 102.5, z: 12, name: 'NL', country: 'Netherlands' },
                //     { x: 80.3, y: 86.1, z: 11.8, name: 'SE', country: 'Sweden' },
                //     { x: 78.4, y: 70.1, z: 16.6, name: 'ES', country: 'Spain' },
                //     { x: 74.2, y: 68.5, z: 14.5, name: 'FR', country: 'France' },
                //     { x: 73.5, y: 83.1, z: 10, name: 'NO', country: 'Norway' },
                //     { x: 71, y: 93.2, z: 24.7, name: 'UK', country: 'United Kingdom' },
                //     { x: 69.2, y: 57.6, z: 10.4, name: 'IT', country: 'Italy' },
                //     { x: 68.6, y: 20, z: 16, name: 'RU', country: 'Russia' },
                //     { x: 65.5, y: 126.4, z: 35.3, name: 'US', country: 'United States' },
                //     { x: 65.4, y: 50.8, z: 28.5, name: 'HU', country: 'Hungary' },
                //     { x: 63.4, y: 51.8, z: 15.4, name: 'PT', country: 'Portugal' },
                //     { x: 64, y: 82.9, z: 31.3, name: 'NZ', country: 'New Zealand' }
                // ]
            }]
        
        });

    }
});
