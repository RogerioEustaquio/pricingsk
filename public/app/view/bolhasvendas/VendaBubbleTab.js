Ext.define('App.view.bolhasvendas.VendaBubbleTab', {
    extend: 'Ext.panel.Panel',
    xtype: 'vendabubbletab',
    itemId: 'vendabubbletab',
    closable: false,
    requires: [
        'App.view.bolhasvendas.VendaBubbleFiltro',
        'App.view.bolhasvendas.VendaBubbleWindow',
        'App.view.bolhasvendas.VendaBubbleChart',
        // 'App.view.rpe.FiltroMarca',
    ],
    title: 'Venda Bubble',
    layout: 'fit',
    border: false,
    items:[
        {
            xtype: 'container',
            itemId: 'containerbolha',
            layout:'border',
            items:[
                {
                    xtype: 'toolbar',
                    region: 'north',
                    items: [
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-filter',
                            handler: function() {
                                var filtromarca =  this.up('toolbar').up('container').down('#vendasbubblefiltro');
                                var hidden = (filtromarca.hidden) ? false : true;
                                filtromarca.setHidden(hidden);
                            }
                        },
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-search',
                            margin: '0 0 0 2',
                            tooltip: 'Consultar',
                            handler: function() {

                                var me = this.up('toolbar').up('container').up('panel');
                                var panelBolha =  this.up('toolbar').up('container');

                                var idEixos = null;
                                var textEixos = null;

                                var window = Ext.getCmp('vendabubblewindow');
                                if(window){
                                    idEixos = window.idEixos;
                                    textEixos = window.textEixos;
                                }

                                me.onConsultar(panelBolha,idEixos,textEixos);
                
                            }
                        },
                        '->',
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-cog',
                            handler: function() {

                                var me = this.up('toolbar').up('container').up('panel');
                                var panelBolha =  this.up('toolbar').up('container');

                                var window = Ext.getCmp('vendabubblewindow');
                                if(!window){
                                    window = Ext.create('App.view.bolhasvendas.VendaBubbleWindow', {
                                        listeners: {
                                            render: function(w){

                                                w.down('#btnconfirmar').on('click',function(btn){

                                                    var yxz = w.down('#bxElement').getValue();
                                                    var storeEixo = w.down('#bxElement').getStore().getData().autoSource.items;

                                                    w.close();

                                                    // Na cosulta valores retornarão via Ajax da consulta real
                                                    var cont = 0;
                                                    var newSerie='',x='',y='',z='',xtext='ROL',ytext ='MB',ztext='CC';
                                                    storeEixo.forEach(function(record){


                                                        if(cont == 0){

                                                            for (let index = 0; index < storeEixo.length; index++) {
                                                                const element = storeEixo[index];

                                                                if(element.data.id == yxz[0]){
                                                                    ytext = element.data.name;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        if(cont == 1){

                                                            for (let index = 0; index < storeEixo.length; index++) {
                                                                const element = storeEixo[index];

                                                                if(element.data.id == yxz[1] ){
                                                                    xtext = element.data.name;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        
                                                        if(cont == 2){

                                                            for (let index = 0; index < storeEixo.length; index++) {
                                                                const element = storeEixo[index];

                                                                if(element.data.id == yxz[2] ){
                                                                    ztext = element.data.name;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        cont++;

                                                    });
                                                    
                                                    var y = yxz[0] ? yxz[0].toLowerCase() : 'mb';
                                                    var x = yxz[1] ? yxz[1].toLowerCase() : 'rol';
                                                    var z = yxz[2] ? yxz[2].toLowerCase() : 'cc';

                                                    var idEixos = {
                                                        x: x,
                                                        y: y,
                                                        z: z
                                                    };

                                                    var textEixos = {
                                                        x: xtext,
                                                        y: ytext,
                                                        z: ztext
                                                    };

                                                    me.onConsultar(panelBolha,idEixos,textEixos);

                                                });
                                            }
                                        }
                                    });
                                }

                                window.show();

                            }
                        }
                    ]
                },
                {
                    xtype:'vendasbubblefiltro',
                    region: 'west'
                },
                {
                    xtype: 'panel',
                    region: 'center',
                    layout: 'fit',
                    itemId: 'panelbolha',
                    items:[
                        {
                            xtype: 'vendabubblechart'
                        }
                    ]
                    
                }
            ]
        }
    ],

    onConsultar: function(panelBolha,idEixos,textEixos){
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');

        var filtromarca =  panelBolha.up('container').down('#vendasbubblefiltro');

        var posicionamento  = filtromarca.down('#elposicionamento').getValue();
        var idEmpresas      = filtromarca.down('#elEmp').getValue();
        var regional        = filtromarca.down('#elregional').getValue();
        var datainicio      = filtromarca.down('#datainicio').getRawValue();
        var datafim         = filtromarca.down('#datafim').getRawValue();
        var marcas          = filtromarca.down('#elmarca').getValue();
        var idproduto       = filtromarca.down('#eltagidproduto').getValue();
        var categorias       = filtromarca.down('#elcategoria').getValue();
        
        var params = {
            posicionamento: posicionamento,
            idEmpresas: Ext.encode(idEmpresas),
            regional: Ext.encode(regional),
            datainicio : datainicio,
            datafim: datafim,
            marcas: Ext.encode(marcas),
            idproduto: Ext.encode(idproduto),
            categorias: Ext.encode(categorias),
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

        var charts = panelBolha.down('#vendabubblechart');

        var seriesLength = (charts.chart.series) ? charts.chart.series.length : 0 ;

        for(var i = seriesLength - 1; i > -1; i--)
        {
            charts.chart.series[i].remove();
        }
        charts.setLoading(true);
        charts.chart.update(false,false);

        Ext.Ajax.request({
            url: BASEURL +'/api/bolhasvendas/posicionamento',
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
                    
                    // charts.chart.xAxis[0].setCategories(rsarray.categories);

                    var vSerie = Object();
                    var vData = Array();

                    var x='',y='',z='';
                    var decX = 0,decY = 2,decZ = 0;
                    rsarray.forEach(function(record){

                        x = record[idEixos.x];
                        y = record[idEixos.y];
                        z = record[idEixos.z];
                        decX = record['dec'+idEixos.x];
                        decY = record['dec'+idEixos.y];
                        decZ = record['dec'+idEixos.z];

                        vData.push({
                                x: parseFloat(x),
                                y: parseFloat(y),
                                z: parseFloat(z),
                                ds: record.ds,
                                descricao: record.descricao
                        });

                        cont++;
                    });

                    vSerie = {data: vData};
                    charts.chart.addSeries(vSerie);
                    var nomePosicionamento = filtromarca.down('#elposicionamento').getRawValue();

                    nomePosicionamento = nomePosicionamento? nomePosicionamento: 'Filial';
                    

                    var extraUpdate = {
                        title: {
                            text: 'Posicionamento de '+ nomePosicionamento,
                            style: {
                                fontSize: '14px'
                            }
                        },
                        subtitle:{
                            text: result.referencia.incio + ' até ' + result.referencia.fim
                        },
                        tooltip: {
                            formatter: function () {
        
                                var pointFormat = '<table>';
                                pointFormat += '<tr><th colspan="2">'+this.point.descricao+'</th></tr>';
                                pointFormat += '<tr><th align="left">'+xtext+':</th><td  align="left">'+utilFormat.Value2(this.point.x,parseFloat(decX))+'</td></tr>';
                                pointFormat += '<tr><th align="left">'+ytext+':</th><td  align="left">'+utilFormat.Value2(this.point.y,parseFloat(decY))+'</td></tr>';
                                pointFormat += '<tr><th align="left">'+ztext+':</th><td  align="left">'+utilFormat.Value2(this.point.z,parseFloat(decZ))+'</td></tr>';
                                pointFormat += '</table>';
            
                                return pointFormat;
                            }
                        },
                        xAxis : {
                            title:{
                                text: xtext
                            },
                            
                            labels: {
                               formatter: function () {
                                    return utilFormat.Value2(this.value,parseFloat(decX));
                               }
                            }
                        },
                        yAxis: {
                            title:{
                                text: ytext
                            },
                            labels: {
                               formatter: function () {
                                    return utilFormat.Value2(this.value,parseFloat(decY));
                               }
                            }
                        }

                    };

                    charts.chart.update(extraUpdate);

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
})
