Ext.define('App.view.analisemarca.Toolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'analisemarcatoolbar',
    itemId: 'analisemarcatoolbar',
    region: 'north',
    requires:[
        'App.view.analisemarca.Windowcestaproduto'
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
            hidden: false,
            handler: me.onBtnConsultar
        });

        var btnchart = Ext.create('Ext.button.Button',{

            iconCls: 'fa fa-chart-bar',
            tooltip: 'Gráfico',
            margin: '1 1 1 4',
            enableToggle: true,
            pressed: true,
            toggleHandler: me.onBtnChart
        });

        var btncards = Ext.create('Ext.button.Button',{

            iconCls: 'fa fa-th-list',
            tooltip: 'Listas',
            margin: '1 1 1 4',
            enableToggle: true,
            pressed: false,
            toggleHandler: me.onBtnListas
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
                },
                {
                    xtype: 'checkboxfield',
                    margin: '2 2 2 2',
                    labelWidth: 90,
                    fieldLabel: 'índices',
                    name: 'indices',
                    idItem: 'indices',
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
                            name : 'estoque',
                            value: meWindow.down('checkboxfield[name=estoque]').checked
                        });
                        array.push({
                            name : 'cliente',
                            // value: meWindow.down('checkboxfield[name=cliente]').getValue()
                            value: meWindow.down('checkboxfield[name=cliente]').checked
                        });
                        array.push({
                            name : 'indices',
                            // value: meWindow.down('checkboxfield[name=indices]').getValue()
                            value: meWindow.down('checkboxfield[name=indices]').checked
                        });

                        me.indicadoresAdd = array;

                        meWindow.close();
                    },
                    listeners: {
                        afterrender: function(){
                            var meWindow = this.up('window');

                            if(me.indicadoresAdd){
                                for (let index = 0; index < me.indicadoresAdd.length; index++) {

                                    if(me.indicadoresAdd[index].name == "estoque"){
                                            meWindow.down('checkboxfield[name=estoque]').setValue(me.indicadoresAdd[index].value);
                                            meWindow.down('checkboxfield[name=estoque]').checked = me.indicadoresAdd[index].value;
                                    }

                                    if(me.indicadoresAdd[index].name == "cliente"){
                                            meWindow.down('checkboxfield[name=cliente]').setValue(me.indicadoresAdd[index].value);
                                            meWindow.down('checkboxfield[name=cliente]').checked = me.indicadoresAdd[index].value;
                                    }

                                    if(me.indicadoresAdd[index].name == "indices"){
                                            meWindow.down('checkboxfield[name=indices]').setValue(me.indicadoresAdd[index].value);
                                            meWindow.down('checkboxfield[name=indices]').checked = me.indicadoresAdd[index].value;
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
                btnchart,
                btncards,
                {
                    xtype: 'button',
                    // text: 'Cesta',
                    iconCls: 'fa fa-shopping-basket',
                    tooltip: 'Cesta de Produtos',
                    margin: '1 1 1 4',
                    hidden: false,
                    handler: function(){
                        Ext.create('App.view.analisemarca.Windowcestaproduto').show();
                    }
                },
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
