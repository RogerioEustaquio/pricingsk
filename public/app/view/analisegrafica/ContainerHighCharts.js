Ext.define('App.view.analisegrafica.ContainerHighCharts', {
    extend: 'Ext.Container',
    xtype: 'analisegraficachart',
    itemId: 'analisegraficachart',
    width: '100%',
    height: '100%',
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
        me.showLegend = Array();

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
                                url: BASEURL +'/api/analisegrafico/listarfichaitemgrafico',
                                method: 'POST',
                                params: me.params,
                                async: true,
                                timeout: 240000,
                                success: function (response) {
                                    
                                    me.setLoading(false);
                                    var result = Ext.decode(response.responseText);
                                    if(result.success){

                                        rsarray = result.data;

                                        if(rsarray.series){

                                            rsarray.series.forEach(function(record){
                                                if(me.showLegend){
                                                    me.showLegend.push(record.showInLegend);
                                                }
                                            })
                                        }

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

                                    me.buildChartContainer(el,rsarray.categories,rsarray.series)
                                },
                                error: function() {
                                    
                                    me.setLoading(false);
                                    rsarray = [];

                                    me.buildChartContainer(el,rsarray.categories,rsarray.series)

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

    buildChartContainer: function(el,meses,series){
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');
        colors = ["#63b598","#ce7d78","#ea9e70","#a48a9e","#c6e1e8","#648177","#0d5ac1","#f205e6","#1c0365","#14a9ad","#4ca2f9"
                 ,"#a4e43f","#d298e2","#6119d0","#d2737d","#c0a43c","#f2510e","#651be6","#79806e","#61da5e","#cd2f00","#9348af"
                 ,"#01ac53","#c5a4fb","#996635","#b11573","#2f3f94","#2f7b99","#da967d","#34891f","#b0d87b","#4bb473","#75d89e"];

        me.chart =  Highcharts.chart(el.id, {
            loading: {
                labelStyle: {
                    color: 'gray'
                },
                // style: {
                //     backgroundColor: 'gray'
                // }
            },
            credits:{
                enabled: false
            },
            exporting: {
                menuItemDefinitions: {
                    fullscreen: {
                        onclick: function() {
                        //   Highcharts.FullScreen.prototype.open(this.renderTo);
                            // this.fullscreen.prototype.open();
                            this.fullscreen.toggle();
                        },
                        text: 'Full screen'
                    },
                    indicadores: {
                        onclick: function () {
                            var meChart = this;
                            var lista = [];
                            var element = '';
                            meChart.series.forEach(function(record){

                                var recordSeries = record;

                                var indicadoresAdd = me.up('panel').up('container').down('#analisegraficatoolbar').indicadoresAdd;
                                
                                element = {
                                    xtype: 'checkboxfield',
                                    margin: '2 2 2 2',
                                    labelWidth: 120,
                                    fieldLabel: record.name,
                                    name: record.name,
                                    checked: recordSeries.options.showInLegend,
                                    handler: function(record,index){
                                        
                                        let cont = 0;
                                        if(index){

                                            var listaCheck = record.up('window').items;

                                                for (let i = 0; i < listaCheck.length; i++) {
                                                    const element = listaCheck.items[i];

                                                    cont = (element.checked) ? cont+1 : cont;
                                                    
                                                }
                                        }

                                        if(cont > 8){

                                            Ext.Msg.alert('Alerta','Permitido selecionar 8 indicadores.');
                                            record.setValue(false);
                                            me.showLegend[recordSeries.index] = false ;
                                            recordSeries.update({showInLegend: false, visible: false},false);
                                            meChart.yAxis[recordSeries.index].update({visible: false},false);
                                            cont--;

                                        }else{

                                            me.showLegend[recordSeries.index] = index ;
                                            recordSeries.update({showInLegend: index, visible: index},false);
                                            meChart.yAxis[recordSeries.index].update({visible: index},false);

                                            var iColor = 0
                                            var iCont = 0
                                            meChart.series.forEach(function(rowSerie){
                                                if(rowSerie.visible){
                                                    const color = colors[iColor]; //Highcharts.getOptions().colors[iColor];

                                                    rowSerie.update({color:color},false);
                                                    meChart.yAxis[iCont].update(
                                                        {
                                                            title:{
                                                                style: {
                                                                    color: color
                                                                }
                                                            },
                                                            labels:{
                                                                style: {
                                                                    color: color
                                                                }
                                                            }
                                                        }
                                                        ,false);
                                                        
                                                    iColor++;
                                                }

                                                iCont++;
                                            });
                                        }

                                        meChart.redraw();
                                        record.up('window').down('displayfield[name=contCheck]').setValue(cont);
                                    }
                                };
                                
                                if(indicadoresAdd){
                                    for (let e = 0; e < indicadoresAdd.length; e++) {
                                        if(indicadoresAdd[e].name == record.name){
                                            element = (!indicadoresAdd[e].value) ? null: element;
                                        }
                                    }
                                }

                                var serieExtras = ['Estoque Inicial','Estoque Final','Dias de Estoque','Índice Estoque/ROL','Índice Estoque/LB','Índice Estoque/Giro'];

                                for (let e = 0; e < serieExtras.length; e++) {
                                    if(serieExtras[e] == record.name){

                                        element = (recordSeries.yData.length > 0) ? element : null;
                                    }
                                }

                                if(element)
                                    lista.push(element);
                                
                            });

                            Ext.create('Ext.window.Window', {
                                title: 'Habilitar/Desabilitar Indicadores',
                                // renderTo: me,
                                scrollable: true,
                                height: 300,
                                width: 260,
                                // padding: '1 1 1 1',
                                // layout: 'fit',
                                tbar: [
                                    {
                                        xtype: 'displayfield',
                                        name: 'contCheck',
                                        itemId: 'contCheck',
                                        renderer: function(){
                                            let cont =0;
                                            me.showLegend.forEach(function(record){
                                                if(record){
                                                    cont++
                                                }
                                            })
                                            return cont;
                                        }
                                    },
                                    '->',
                                    {
                                        xtype: 'panel',
                                        items: {
                                            xtype: 'button',
                                            iconCls: 'fa fa-file',
                                            tooltip: 'Limpar seleção',
                                            handler: function(){
        
                                                var listaCheck = this.up('panel').up('window').items;


                                                for (let i = 0; i < listaCheck.length; i++) {
                                                    const element = listaCheck.items[i];

                                                    element.setValue(false);
                                                    me.showLegend[i] = false ;
                                                    meChart.series[i].setVisible(false, false);
                                                    meChart.yAxis[i].update({visible: false},false);
                                                    
                                                }

                                                meChart.redraw();

                                                this.up('panel').up('window').down('displayfield[name=contCheck]').setValue(0);
                                            }
                                        }
                                        
                                    }
                                ],
                                items: lista
                            }).show();
                        },
                        text: 'Selecionar Indicadores'
                    },
                    ocultar: {
                        onclick: function () {
                            var meChart = this;

                            $(meChart.series).each(function(){
                                //this.hide();
                                this.setVisible(false, false);
                            });
                            meChart.redraw();

                        },
                        text: 'Ocultar Indicadores'
                    }

                },
                buttons: {
                    contextButton: {
                        menuItems: ['viewFullscreen','downloadPNG', 'downloadXLS', 'indicadores', 'ocultar']
                    }
                }
            },

            chart: {
                type: 'line',
                zoomType: 'xy'
            },
            plotOptions: {
                series: {
                    events: {

                        hide: function(){
                            this.chart.yAxis[this.index].update({visible: false},false);
                            this.chart.redraw();
                        },
                        show: function(){
                            this.chart.yAxis[this.index].update({visible: true},false);
                            this.chart.redraw();
                        },
                        // legendItemClick: function () {

                        //     var index = this.index;

                        //     if(this.chart.yAxis[index].visible){
                        //         this.chart.yAxis[index].update({visible: false});
                        //     }else{
                        //         this.chart.yAxis[index].update({visible: true});
                        //     }

                        //     return this;
                        // }
                    },
                    dataLabels: {
                        // format: '{series}'
                        formatter: function () {

                            var options  = this.point.series.options;
                            var vFormat  = options.vFormat.toString();
                            var vDecimos = options.vDecimos.toString();

                            if(vFormat == 'N'){
                                return this.y;
                            }

                            return vFormat+' '+utilFormat.Value2(this.y,vDecimos);
                        }
                    }
                }
            },
            title: {
                text: 'Ficha de Indicadores',
                style: {
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: meses,
                crosshair: true
            },
            yAxis: [
                    {
                        title: {
                            text: 'ROL',
                            style: {
                                color: colors[0], //Highcharts.getOptions().colors[0],
                                fontSize: '10px'
                            }
                        },
                        labels: {
                                formatter: function () {
                                    var v = utilFormat.ValueZero(this.value);
                                    return v;
                                },
                                x: 0,
                                y: 0,
                                padding: 0,
                                style: {
                                    color: colors[0],
                                    fontSize: '10px'
                                }
                        },
                        opposite: true,
                        visible: true
                    },
                    {
                        title: {
                            text: 'LB',
                            style: {
                                color: colors[1],
                                fontSize: '10px'
                            }
                        },
                        labels: {
                        formatter: function () {
                            return utilFormat.ValueZero(this.value);
                        },
                        x: 0,
                        y: 0,
                        padding: 0,
                        style: {
                            color: colors[1],
                            fontSize: '10px'
                        }
                        },
                        opposite: true,
                        visible: true
                    },
                    {
                        title: {
                            text: 'MB',
                            style: {
                                color: colors[2],
                                fontSize: '10px'
                            }
                        },
                        labels: {
                            formatter: function () {
                            return utilFormat.Value2(this.value,this.chart.options.series[this.chart.index].vDecimos);
                            },
                            x: 0,
                            y: 0,
                            padding: 0,
                            style: {
                                color: colors[2],
                                fontSize: '10px'
                            }
                        },
                        opposite: true,
                        visible: true
                    },
                    {
                        title: {
                            text: 'Quantidade',
                            style: {
                                color: colors[3],
                                fontSize: '10px'
                            }
                        },
                        labels: {
                            formatter: function () {
                            return utilFormat.ValueZero(this.value);
                            },
                            x: 0,
                            y: 0,
                            padding: 0,
                            style: {
                                color: colors[3],
                                fontSize: '10px'
                            }
                        },
                        opposite: true,
                        visible: false
                    },
                    {
                        title: {
                            text: 'ROL Dia',
                            style: {
                                color: colors[4],
                                fontSize: '10px'
                            }
                        },
                        labels: {
                            formatter: function () {
                                return utilFormat.ValueZero(this.value);
                            },
                            x: 0,
                            y: 0,
                            padding: 0,
                            style: {
                                color: colors[4],
                                fontSize: '10px'
                            }
                        },
                        opposite: true,
                        visible: false
                    },
                    {
                        title: {
                            text: 'LB Dia',
                            style: {
                                color: colors[5],
                                fontSize: '10px'
                            }
                        },
                        labels: {
                            formatter: function () {
                                return utilFormat.ValueZero(this.value);
                            },
                            x: 0,
                            y: 0,
                            padding: 0,
                            style: {
                                color: colors[5],
                                fontSize: '10px'
                            }
                        },
                        opposite: true,
                        visible: false
                    },
                    {
                        title: {
                            text: 'Qtde Dia',
                            style: {
                                color: colors[6],
                                fontSize: '10px'
                            }
                        },
                        labels: {
                            formatter: function () {
                                return utilFormat.ValueZero(this.value);
                            },
                            x: 0,
                            y: 0,
                            padding: 0,
                            style: {
                                color: colors[6],
                                fontSize: '10px'
                            }
                        },
                        opposite: true,
                        visible: false
                    },
                    // {
                    //     title: {
                    //         text: 'ROL Fx 101-250',
                    //         style: {
                    //             color: colors[7],
                    //             fontSize: '10px'
                    //         }
                    //     },
                    //     labels: {
                    //         formatter: function () {
                    //             return utilFormat.ValueZero(this.value);
                    //         },
                    //         x: 0,
                    //         y: 0,
                    //         padding: 0,
                    //         style: {
                    //             color: colors[7],
                    //             fontSize: '10px'
                    //         }
                    //     },
                    //     opposite: true,
                    //     visible: false
                    // },
                    // {
                    //     title: {
                    //         text: 'ROL Fx 251-500',
                    //         style: {
                    //             color: colors[7],
                    //             fontSize: '10px'
                    //         }
                    //     },
                    //     labels: {
                    //         formatter: function () {
                    //             return utilFormat.ValueZero(this.value);
                    //         },
                    //         x: 0,
                    //         y: 0,
                    //         padding: 0,
                    //         style: {
                    //             color: colors[7],
                    //             fontSize: '10px'
                    //         }
                    //     },
                    //     opposite: true,
                    //     visible: false
                    // }
                ],
                tooltip: {
                    // shared: true,
                    // outside: true
                },
                series: series
        });

    }
});
