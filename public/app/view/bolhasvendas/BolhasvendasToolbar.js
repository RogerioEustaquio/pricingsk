Ext.define('App.view.bolhasvendas.BolhasvendasToolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'bolhasvendastoolbar',
    itemId: 'bolhasvendastoolbar',
    region: 'north',
    requires:[
        // 'App.view.analisemarca.Windowcestaproduto'
    ],
    // indicadoresAdd: null,

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {

            items : [
                {
                    xtype: 'button',
                    iconCls: 'fa fa-filter',
                    handler: function() {
                        // var filtromarca =  this.up('panel').up('container').down('#filtrofilialposicionamento');
                        // var hidden = (filtromarca.hidden) ? false : true;
                        // filtromarca.setHidden(hidden);
                    }
                },
                {
                    xtype: 'button',
                    iconCls: 'fa fa-search',
                    margin: '0 0 0 2',
                    tooltip: 'Consultar',
                    handler: function() {

                        // var me = this.up('panel').up('container').up('panel');
                        // var panelBolha =  this.up('panel');

                        // var idEixos = null;
                        // var textEixos = null

                        // var window = Ext.getCmp('eixofilialwindow');
                        // if(window){
                        //     idEixos = window.idEixos;
                        //     textEixos = window.textEixos;
                        // }

                        // me.onConsultar(panelBolha,idEixos,textEixos);
        
                    }
                },
                '->',
                {
                    xtype: 'button',
                    iconCls: 'fa fa-cog',
                    handler: function() {

                        // var me = this.up('panel').up('container').up('panel');
                        // var panelBolha =  this.up('panel');

                        // var window = Ext.getCmp('eixofilialwindow');
                        // if(!window){
                        //     window = Ext.create('App.view.rpe.EixoFilialWindow', {
                        //         listeners: {
                        //             render: function(w){

                        //                 w.down('#btnconfirmar').on('click',function(btn){

                        //                     var xyz = w.down('#bxElement').getValue();
                        //                     var storeEixo = w.down('#bxElement').getStore().getData().autoSource.items;

                        //                     w.close();

                        //                     // Na cosulta valores retornarão via Ajax da consulta real
                        //                     var cont = 0;
                        //                     var newSerie='',x='',y='',z='',xtext='ROL',ytext ='MB',ztext='CC';
                        //                     storeEixo.forEach(function(record){

                        //                         if(cont == 0){

                        //                             for (let index = 0; index < storeEixo.length; index++) {
                        //                                 const element = storeEixo[index];

                        //                                 if(element.data.id == xyz[0] ){
                        //                                     xtext = element.data.name;
                        //                                     break;
                        //                                 }
                        //                             }
                        //                         }

                        //                         if(cont == 1){

                        //                             for (let index = 0; index < storeEixo.length; index++) {
                        //                                 const element = storeEixo[index];

                        //                                 if(element.data.id == xyz[1]){
                        //                                     ytext = element.data.name;
                        //                                     break;
                        //                                 }
                        //                             }
                        //                         }
                                                
                        //                         if(cont == 2){

                        //                             for (let index = 0; index < storeEixo.length; index++) {
                        //                                 const element = storeEixo[index];

                        //                                 if(element.data.id == xyz[2] ){
                        //                                     ztext = element.data.name;
                        //                                     break;
                        //                                 }
                        //                             }
                        //                         }

                        //                         cont++;

                        //                     });
                                            
                        //                     var x = xyz[0] ? xyz[0].toLowerCase() : 'rol';
                        //                     var y = xyz[1] ? xyz[1].toLowerCase() : 'mb';
                        //                     var z = xyz[2] ? xyz[2].toLowerCase() : 'cc';

                        //                     var idEixos = {
                        //                         x: x,
                        //                         y: y,
                        //                         z: z
                        //                     };

                        //                     var textEixos = {
                        //                         x: xtext,
                        //                         y: ytext,
                        //                         z: ztext
                        //                     };

                        //                     me.onConsultar(panelBolha,idEixos,textEixos);

                        //                 });
                        //             }
                        //         }
                        //     });
                        // }

                        // window.show();

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

        var idEmpresas      = me.up('container').down('#analisemarcafiltro').down('#elEmp').getValue();
        var regional        = me.up('container').down('#analisemarcafiltro').down('#elRegional').getValue();
        var data            = me.up('container').down('#analisemarcafiltro').down('#data').getRawValue();
        var meses24         = me.up('container').down('#analisemarcafiltro').down('radiofield[inputValue=24]');
        var meses36         = me.up('container').down('#analisemarcafiltro').down('radiofield[inputValue=36]');
        var curva           = me.up('container').down('#analisemarcafiltro').down('#elCurva').getValue();
        var idproduto       = me.up('container').down('#analisemarcafiltro').down('#eltagidproduto').getValue();
        var produto         = me.up('container').down('#analisemarcafiltro').down('#elProduto').getValue();
        var marca           = me.up('container').down('#analisemarcafiltro').down('#elMarca').getValue();
        var notmarca        = me.up('container').down('#analisemarcafiltro').down('#notmarca').checked;
        var montadora       = me.up('container').down('#analisemarcafiltro').down('#elMontadora').getValue();
        var notmontadora    = me.up('container').down('#analisemarcafiltro').down('#notmontadora').checked;
        var cesta           = me.up('container').down('#analisemarcafiltro').down('#elcesta').getValue();
        var especialproduto = me.up('container').down('#analisemarcafiltro').down('#elespecialproduto').getValue();
        var categoria       = me.up('container').down('#analisemarcafiltro').down('#elcategoria').getValue();

        if(especialproduto.length > 0 && cesta.length > 0){
            alert('Informar no filtro somente Cesta de produto ou Seleção especial de produto!');
            return '';
        }
        
        var charts = me.up('container').down('#panelcenter').down('#analisemarcachart');

        charts.setLoading({msg: 'Carregando...'});

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
            curva:  Ext.encode(curva),
            idProduto:  Ext.encode(idproduto),
            produto:  Ext.encode(produto),
            marca: Ext.encode(marca),
            notmarca : notmarca,
            montadora : Ext.encode(montadora),
            notmontadora : notmontadora,
            cesta : Ext.encode(cesta),
            especialproduto: Ext.encode(especialproduto),
            categoria: Ext.encode(categoria),
            indicadoresAdd: Ext.encode(me.indicadoresAdd)
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

        if(seriesLength>0){
            for(var i = seriesLength - 1; i > -1; i--)
            {
                charts.chart.series[i].remove();
            }
        }

        charts.chart.update(false,false);

        Ext.Ajax.request({
            url: BASEURL +'/api/analisemarca/listarfichaitemgrafico',
            method: 'POST',
            params: params,
            async: true,
            timeout: 1080000,
            success: function (response) {
                var result = Ext.decode(response.responseText);

                charts.setLoading(false);
                // charts.chart.hideLoading();
                if(result.success){

                    rsarray = result.data;
                    var cont = 0;
                    charts.chart.xAxis[0].setCategories(rsarray.categories);

                    var arrayOrder = charts.showOrder ? charts.showOrder.split(',') : Array();

                    rsarray.series.forEach(function(record){

                        record.visible      = seriesOrig[cont].visible;
                        record.color        = seriesCores[cont];
                        record.showInLegend = charts.showLegend[cont];
                        if(charts.showType[cont] != 'line')
                            record.type = charts.showType[cont];


                        for (let contOrder = 0; contOrder < arrayOrder.length; contOrder++) {
                          
                            if(arrayOrder[contOrder] == record.name){
                                record.zIndex = contOrder ;
                                record.color = Highcharts.getOptions().colors[contOrder];
                                break;
                            }
                        }
                        
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

        var grid = me.up('container').down('#panelcenter').down('#listaspanel').down('#produtotpanel').down('#rankgrid');
        grid.getStore().getProxy().setExtraParams(params);
        grid.getStore().loadPage(1);

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
    },

    onBtnListas: function(btn, pressed){

        var me = this.up('toolbar');

        var panelChart = me.up('container').down('#listaspanel');

        var charts = me.up('container').down('#panelcenter').down('#analisemarcachart');

        if(pressed){

            panelChart.setHidden(false);
            charts.up('panel').setLayout('border');
            
        }else{
            
            panelChart.setHidden(true);
            charts.up('panel').setLayout('fit');
            
        }
        
        // setTimeout(function(){
            charts.chart.redraw();
        // },250);

    }

});
