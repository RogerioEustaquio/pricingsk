Ext.define('App.view.analiseperformance.TabFilial', {
    extend: 'Ext.panel.Panel',
    xtype: 'tabfilial',
    itemId: 'tabfilial',
    closable: false,
    requires: [
        'App.view.analiseperformance.FiltroFilial',
        'App.view.analiseperformance.GridFilialOverview'
    ],
    title: 'Filial',
    layout: 'card',
    border: false,
    tbar: {
        border: false,
        items:[
            {
                xtype: 'button',    
                text: 'Overview',
                handler: function(){
                
                    this.up('panel').setActiveItem(0);
                }
            }
        ]
    },
    items:[
        {
            xtype: 'container',
            layout:'border',
            itemId: 'containerbolha',
            items:[
                {
                    xtype:'filtrofilial',
                    region: 'west'
                },
                {
                    xtype: 'panel',
                    region: 'center',
                    layout: 'fit',
                    itemId: 'panelbolha',
                    tbar:[
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-filter',
                            handler: function() {
                                var filtro =  this.up('panel').up('container').down('#filtrofilial');
                                var hidden = (filtro.hidden) ? false : true;
                                filtro.setHidden(hidden);
                            }
                        },
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-search',
                            margin: '0 0 0 2',
                            tooltip: 'Consultar',
                            handler: function() {

                                var filtro =  this.up('panel').up('container').down('#gridfilialoverview');
                                // var empresas = filtro.down('#elEmpresa').getValue();
                                // var data = filtro.down('#data').getRawValue();
                                // var marcas = filtro.down('#elmarca').getValue();
                                // var grupomarcas = filtro.down('#elgrupomarca').getValue();

                                // if(grupomarcas.length > 0){
                                //     marcas = marcas.concat(grupomarcas);
                                // }
                                
                                var params = {
                                    // idEmpresas: Ext.encode(empresas),
                                    // data : data,
                                    // idMarcas: Ext.encode(marcas)
                                };
                
                                var gridStore = this.up('panel').down('grid').getStore();
                
                                gridStore.getProxy().setExtraParams(params);
                                gridStore.load();
                
                            }
                        },
                        '->',
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-cog',
                            handler: function() {

                              
                            }
                        }
                    ],
                    items:[
                        {
                            xtype: 'gridfilialoverview'
                        }
                    ]
                    
                }
            ]
        }
    ],

    onConsultar: function(panelBolha,idEixos,textEixos){
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');

        var filtromarca =  panelBolha.up('container').down('#filtrofilial');
        var datainicio = filtromarca.down('#filialdatainicio').getRawValue();
        var datafim = filtromarca.down('#filialdatafim').getRawValue();
        var marcas = filtromarca.down('#filialelmarca').getValue();
        
        var params = {
            datainicio : datainicio,
            datafim: datafim,
            idMarcas: Ext.encode(marcas)
        };

        if(!idEixos){

            idEixos = {
                x: 'rol',
                y: 'mb',
                z: 'cc'
            };
            
        }
        if(!textEixos){

            textEixos = {
                x: 'ROL',
                y: 'MB',
                z: 'CC'
            };
            
        }
        
        var xtext = textEixos.x;
        var ytext = textEixos.y;
        var ztext = textEixos.z;

        var charts = panelBolha.down('#chartsfilialposicionamento');

        var seriesLength = (charts.chart.series) ? charts.chart.series.length : 0 ;

        for(var i = seriesLength - 1; i > -1; i--)
        {
            charts.chart.series[i].remove();
        }
        charts.setLoading(true);
        charts.chart.update(false,false);

        // Ext.Ajax.request({
        //     url: BASEURL +'/api/filialposicionamento/filialposicionamento',
        //     method: 'POST',
        //     params: params,
        //     async: true,
        //     timeout: 240000,
        //     success: function (response) {
        //         var result = Ext.decode(response.responseText);

        //         charts.setLoading(false);
        //         // charts.chart.hideLoading();
        //         if(result.success){

        //             rsarray = result.data;
        //             var cont = 0;
                    
        //             // charts.chart.xAxis[0].setCategories(rsarray.categories);

        //             var vSerie = Object();
        //             var vData = Array();

        //             var x='',y='',z='';
        //             var decX = 0,decY = 2,decZ = 0;
        //             rsarray.forEach(function(record){

        //                 x = record[idEixos.x];
        //                 y = record[idEixos.y];
        //                 z = record[idEixos.z];
        //                 decX = record['dec'+idEixos.x];
        //                 decY = record['dec'+idEixos.y];
        //                 decZ = record['dec'+idEixos.z];

        //                 vData.push({
        //                         x: parseFloat(x),
        //                         y: parseFloat(y),
        //                         z: parseFloat(z),
        //                         ds: record.ds,
        //                         descricao: record.descricao
        //                 });

        //                 cont++;
        //             });

        //             vSerie = {data: vData};
        //             charts.chart.addSeries(vSerie);

        //             var extraUpdate = {

        //                 subtitle:{
        //                     text: result.referencia.incio + ' até ' + result.referencia.fim
        //                 },
        //                 tooltip: {
        //                     formatter: function () {
        
        //                         var pointFormat = '<table>';
        //                         pointFormat += '<tr><th colspan="2">'+this.point.descricao+'</th></tr>';
        //                         pointFormat += '<tr><th align="left">'+xtext+':</th><td  align="left">'+utilFormat.Value2(this.point.x,parseFloat(decX))+'</td></tr>';
        //                         pointFormat += '<tr><th align="left">'+ytext+':</th><td  align="left">'+utilFormat.Value2(this.point.y,parseFloat(decY))+'</td></tr>';
        //                         pointFormat += '<tr><th align="left">'+ztext+':</th><td  align="left">'+utilFormat.Value2(this.point.z,parseFloat(decZ))+'</td></tr>';
        //                         pointFormat += '</table>';
            
        //                         return pointFormat;
        //                     }
        //                 },
        //                 xAxis : {
        //                     title:{
        //                         text: xtext
        //                     },
        //                     labels: {
        //                        formatter: function () {
        //                             return utilFormat.Value2(this.value,parseFloat(decX));
        //                        }
        //                     }
        //                 },
        //                 yAxis: {
        //                     title:{
        //                         text: ytext
        //                     },
        //                     labels: {
        //                        formatter: function () {
        //                             return utilFormat.Value2(this.value,parseFloat(decY));
        //                        }
        //                     }
        //                 }

        //             };

        //             charts.chart.update(extraUpdate);

        //         }else{
        //             rsarray = [];

        //             new Noty({
        //                 theme: 'relax',
        //                 layout: 'bottomRight',
        //                 type: 'error',
        //                 closeWith: [],
        //                 text: 'Erro sistema: '+ result.message.substr(0,20)
        //             }).show();
        //         }
                
        //     },
        //     error: function() {
        //         rsarray = [];
        //         charts.setLoading(false);
        //         // charts.chart.hideLoading();

        //         new Noty({
        //             theme: 'relax',
        //             layout: 'bottomRight',
        //             type: 'error',
        //             closeWith: [],
        //             text: 'Erro sistema: '+ result.message.substr(0,20)
        //         }).show();
        //     }
        // });

    }
})
