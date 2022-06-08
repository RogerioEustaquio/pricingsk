Ext.define('App.view.dispersaovenda.DispersaoVendaToolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'dispersaovendatoolbar',
    itemId: 'dispersaovendatoolbar',
    region: 'north',
    requires:[
    ],
    vNiveis: [],
    vData: null,
    vdatainicioa: null,
    vdatafinala: null,
    vdatainiciob: null,
    vdatafinalb: null,
    vEmps: [],
    vMarcas: [],
    vCurvas: [],
    vProdutos: [],

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

        Ext.applyIf(me, {

            items : [
                btnFiltro,
                btnConsultar
            ]
        });

        me.callParent(arguments);

    },

    onBtnGrupo: function(){
        var me = this.up('toolbar');

        objWindow = Ext.create('App.view.analiseperformance.NodeWindowExplore');
        objWindow.show();

        var btnConfirmar = objWindow.down('panel').down('toolbar').down('form').down('button');

        if(me.vNiveis)
            objWindow.down('panel').down('form').down('#bxElement').setValue(me.vNiveis);

        btnConfirmar.on('click',
            function(){

                var myform = objWindow.down('panel').down('form');
                var niveis = myform.down('#bxElement').getValue();
                me.vNiveis = niveis;

                var gridOrder = myform.down('grid').getStore().getData();
                // var pstring  = '';
                var arrayOrder = new Array();
                gridOrder.items.forEach(function(record){

                    if(record.data.ordem){
                        // if(!pstring){
                        //     pstring  = record.data.campo+' '+record.data.ordem
                        // }else{
                        //     pstring += ', '+record.data.campo+' '+record.data.ordem;
                        // }
                        arrayOrder.push(record.data);
                    }
                    
                });
                // console.log(arrayOrder);

                me.vOrdem = arrayOrder;

                objWindow.close();

            }
        );
    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        if(me.up('panel').down('#dispersaovendafiltro').hidden){
            me.up('panel').down('#dispersaovendafiltro').setHidden(false);
        }else{
            me.up('panel').down('#dispersaovendafiltro').setHidden(true);
        }


    },

    onBtnConsultar: function(btn){
        
        var me = this.up('toolbar');

        var idEmpresas  = me.up('panel').down('#dispersaovendafiltro').down('#elfilial').getValue();
        var datainicio  = me.up('panel').down('#dispersaovendafiltro').down('#eldatainicio').getRawValue();
        var datafim     = me.up('panel').down('#dispersaovendafiltro').down('#eldatafim').getRawValue();
        var produto     = me.up('panel').down('#dispersaovendafiltro').down('#elproduto').getValue();
        var marca       = me.up('panel').down('#dispersaovendafiltro').down('#elmarca').getValue();
        var categoria   = me.up('panel').down('#dispersaovendafiltro').down('#elcategoria').getValue();

        
        var charts = me.up('panel').down('#chartsdispersaovenda');

        charts.setLoading({msg: 'Carregando...'});

        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            datainicio: datainicio,
            datafim:    datafim,
            produto: Ext.encode(produto),
            marca: Ext.encode(marca),
            categoria: Ext.encode(categoria)
        };

        var seriesLength = (charts.chart.series) ? charts.chart.series.length : 0 ;

        for(var i = seriesLength - 1; i > -1; i--)
        {
            charts.chart.series[i].remove();
        }

        charts.chart.update(false,false);

        Ext.Ajax.request({
            url: BASEURL +'/api/dispersaovenda/dispersaovenda2',
            method: 'POST',
            params: params,
            async: true,
            timeout: 1080000,
            success: function (response) {
                var result = Ext.decode(response.responseText);

                charts.setLoading(false);
                // charts.chart.hideLoading();
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

                    charts.chart.addSeries(arraySerie[0]);
                    charts.chart.addSeries(arraySerie[1]);
                    charts.chart.addSeries(arraySerie[2]);
                    charts.chart.addSeries(arraySerie[3]);
                    charts.chart.addSeries(arraySerie[4]);
                    charts.chart.addSeries(arraySerie[5]);

                    var textSubtitle = {
                        subtitle:{
                            text: dataInicio + ' até ' + dataFim
                        }
                    };

                    charts.chart.update(textSubtitle);

                }else{

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