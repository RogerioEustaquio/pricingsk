Ext.define('App.view.analisemarca.ContainerHighCharts', {
    extend: 'Ext.Container',
    xtype: 'analisemarcachart',
    itemId: 'analisemarcachart',
    width: '100%',
    height: '40%',
    // margin: '10 2 2 2',
    style: {
        background: '#ffffff'
    },
    requires: [ 
    ],
    showLegend: [],
    showType: [],
    showOrder: '',
    // controller: 'chart',
    layout: 'border',
    border: true,

    chart: null,

    constructor: function(config) {
        var me = this;
        // var utilFormat = Ext.create('Ext.ux.util.Format');
        me.showLegend   = Array();
        me.showType     = Array();
        me.showOrder    = '';

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
                            var yaxis = me.buildChartYaix();
                            
                            Ext.Ajax.request({
                                url: BASEURL + '/api/analisemarca/listarfichaitemgrafico',
                                method: 'POST',
                                params: me.params,
                                async: true,
                                timeout: 480000,
                                success: function (response) {
                                    
                                    me.setLoading(false);
                                    var result = Ext.decode(response.responseText);
                                    if(result.success){

                                        rsarray = result.data;

                                        if(rsarray.series){

                                            var contOrder = 0;
                                            var cont =0;
                                            rsarray.series.forEach(function(record){
                                                if(me.showLegend){
                                                    me.showLegend.push(record.showInLegend);
                                                }

                                                if(record.visible){
                                                    me.showOrder += ','+ record.name;
                                                    record.zIndex = contOrder ;
                                                    record.color = Highcharts.getOptions().colors[contOrder];
                                                    yaxis[cont].title.style.color = Highcharts.getOptions().colors[contOrder];
                                                    yaxis[cont].labels.style.color= Highcharts.getOptions().colors[contOrder];

                                                    contOrder++;
                                                }

                                                cont++;
                                            });

                                            me.showOrder = me.showOrder.substr(1, parseInt(me.showOrder.length));
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

                                    me.buildChartContainer(el,rsarray.categories,rsarray.series,yaxis);
                                },
                                error: function() {
                                    
                                    me.setLoading(false);
                                    rsarray = [];
                                    yaxis = [];

                                    me.buildChartContainer(el,rsarray.categories,rsarray.series,yaxis);

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

    buildChartYaix: function(){

        var utilFormat = Ext.create('Ext.ux.util.Format');

        colors = ["#63b598","#ce7d78","#ea9e70","#a48a9e","#c6e1e8","#648177","#0d5ac1","#f205e6","#1c0365","#14a9ad","#4ca2f9"
        ,"#a4e43f","#d298e2","#6119d0","#d2737d","#c0a43c","#f2510e","#651be6","#79806e","#61da5e","#cd2f00","#9348af"
        ,"#01ac53","#c5a4fb","#996635","#b11573","#2f3f94","#2f7b99","#da967d","#34891f","#b0d87b","#4bb473","#75d89e"];

        var arrayYaxis= [
            {
                title: {
                    text: 'ROB',
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
                visible: false
            },
            {
                title: {
                    text: 'ROL',
                    style: {
                        color: colors[1], //Highcharts.getOptions().colors[0],
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
                            color: colors[1],
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
                        color: colors[2],
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
                    color: colors[2],
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
                        color: colors[3],
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
                        color: colors[3],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: true
            },
            {
                title: {
                    text: 'PREÇO MÉDIO ROB',
                    style: {
                        color: colors[4],
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
                        color: colors[4],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'PREÇO MÉDIO ROL',
                    style: {
                        color: colors[5],
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
                        color: colors[5],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'CUSTO MÉDIO',
                    style: {
                        color: colors[6],
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
                        color: colors[6],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'Dias',
                    style: {
                        color: colors[7],
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
                        color: colors[7],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: true
            },
            {
                title: {
                    text: 'QTD',
                    style: {
                        color: colors[8],
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
                        color: colors[8],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'CMV',
                    style: {
                        color: colors[9],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[9],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'Impostos',
                    style: {
                        color: colors[10],
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
                        color: colors[10],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'ROB Dia',
                    style: {
                        color: colors[11],
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
                        color: colors[11],
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
                        color: colors[12],
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
                        color: colors[12],
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
                        color: colors[13],
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
                        color: colors[13],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'QTD Dia',
                    style: {
                        color: colors[14],
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
                        color: colors[14],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'CMV Dia',
                    style: {
                        color: colors[15],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[15],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'ES. QTD',
                    style: {
                        color: colors[16],
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
                        color: colors[16],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'ES. Custo Médio',
                    style: {
                        color: colors[17],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[17],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'ES. Valor',
                    style: {
                        color: colors[18],
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
                        color: colors[18],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'ES. Fator',
                    style: {
                        color: colors[19],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[19],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'ES. GIRO',
                    style: {
                        color: colors[20],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[20],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'ES. DIAS',
                    style: {
                        color: colors[21],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[21],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'SKUD',
                    style: {
                        color: colors[22],
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
                        color: colors[22],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'CC',
                    style: {
                        color: colors[23],
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
                        color: colors[23],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'NF',
                    style: {
                        color: colors[24],
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
                        color: colors[24],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'TKM',
                    style: {
                        color: colors[25],
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
                        color: colors[25],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'CC Dia',
                    style: {
                        color: colors[26],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[26],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'Inflação de Estoque',
                    style: {
                        color: colors[27],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[27],
                        fontSize: '10px'
                    }
                },
                opposite: true,
                visible: false
            },
            {
                title: {
                    text: 'Inflação de Compra',
                    style: {
                        color: colors[28],
                        fontSize: '10px'
                    }
                },
                labels: {
                    formatter: function () {
                        return utilFormat.Value2(this.value,2);
                    },
                    x: 0,
                    y: 0,
                    padding: 0,
                    style: {
                        color: colors[28],
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
            //             color: colors[11],
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
            //             color: colors[11],
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
            //             color: colors[12],
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
            //             color: colors[12],
            //             fontSize: '10px'
            //         }
            //     },
            //     opposite: true,
            //     visible: false
            // }
        ];
        return arrayYaxis;
    },

    buildChartContainer: function(el,meses,series,aYaxis){
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');


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
                            var elementCombo = '';
                            var arryElements = {
                                xtype: 'panel',
                                layout: 'hbox',
                                width: 336,
                                border: false,
                                items: [
                                    {
                                        xtype:'label',
                                        html: 'Indicador',
                                        margin: '2 2 2 2',
                                        labelWidth: 180,
                                        width: 200,
                                    },
                                    {
                                        xtype:'label',
                                        html: 'Tipo',
                                        width: 100,
                                        margin: '2 2 2 2',
                                    }
                                ]
                            };

                            lista.push(arryElements);

                            meChart.series.forEach(function(record){

                                var recordSeries = record;

                                var indicadoresAdd = me.up('panel').up('container').down('#analisemarcatoolbar').indicadoresAdd;

                                elementCombo = Ext.create('Ext.form.field.Tag',{
                                    multiSelect: false,
                                    width: 100,
                                    name: 'cb'+record.name,
                                    itemId: 'cb'+record.name,
                                    store: Ext.data.Store({
                                        fields: [
                                            { name: 'tipo', type: 'string' },
                                            { name: 'name', type: 'string' }
                                        ],
                                        data: [
                                            {"tipo":"line", "name":"line"},
                                            {"tipo":"column", "name":"column"},
                                            {"tipo":"spline", "name":"spline"},
                                            {"tipo":"scatter", "name":"scatter"}
                                        ]
                                    }),
                                    queryParam: 'tipo',
                                    queryMode: 'local',
                                    displayField: 'name',
                                    displayTpl: Ext.create('Ext.XTemplate','<tpl for=".">','<b>{tipo}</b>','</tpl>'),
                                    valueField: 'tipo',
                                    fieldLabel: '',
                                    margin: '2 2 2 2',
                                    filterPickList: true,
                                    publishes: 'value',
                                    disabled: false,
                                    value: me.showType[recordSeries.index] == 'line'? null : me.showType[recordSeries.index],
                                    listeners : {
                                        select : function(record,index){

                                            if(!record.value)
                                                record.value = 'line';

                                            me.showType[recordSeries.index] = record.value ;

                                            recordSeries.update({type: record.value},false);
                                            meChart.redraw();
                                        }
                                    }
                                });

                                element = {
                                    xtype: 'checkboxfield',
                                    margin: '2 2 2 2',
                                    labelWidth: 180,
                                    width: 200,
                                    fieldLabel: record.name,
                                    name: record.name,
                                    checked: recordSeries.options.showInLegend,
                                    handler: function(record,index){

                                        var cont = 0;
                                        if(index){

                                            var listaCheck = record.up('window').items;

                                            for (let i = 0; i < listaCheck.length; i++) {
                                                const itemsPanel = listaCheck.items[i];

                                                if(i == 0){
                                                     continue;
                                                }
                                                const element  = itemsPanel.items.items[0]; // checket
                                                // var element2 = itemsPanel.items.items[1]; // tipo
                                                
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

                                            var arrayOrder = me.showOrder ? me.showOrder.split(',') : Array();

                                            if(record.checked){
                                                
                                                arrayOrder.push(record.name);
                                                
                                                if(me.showOrder.length > 0){
                                                    me.showOrder += ','+ record.name;
                                                }else{
                                                    me.showOrder = record.name;
                                                }
                                                
                                                var contOrder = 0;
                                                arrayOrder.forEach(function(){

                                                    meChart.yAxis[recordSeries.index].update(
                                                        {
                                                            title:{
                                                                style:{
                                                                    color: Highcharts.getOptions().colors[contOrder]
                                                
                                                                }
                                                            },
                                                            labels:{
                                                                style:{
                                                                    color: Highcharts.getOptions().colors[contOrder]
                                                
                                                                }
                                                            }
                                                        }
                                                    ,false);
                                                    
                                                    recordSeries.update(
                                                        {
                                                            zIndex: contOrder,
                                                            color : Highcharts.getOptions().colors[contOrder]
                                                        }
                                                    ,false);

                                                    contOrder++
                                                    
                                                });

                                            }else{  // Remover da Ordem //

                                                var removido = '';
                                                me.showOrder = '';
                                                var contOrder = 0;
                                                arrayOrder.forEach(function(v){

                                                    if(!record.checked && v == record.name){
                                                    removido = v;
                                                    recordSeries.update({zIndex: recordSeries.index},false);
        
                                                    }else if(v){
                                                        me.showOrder += ','+ v;

                                                        for (let conty = 0; conty < aYaxis.length; conty++) {

                                                            if(v == meChart.series[conty].name){
                                                                
                                                                meChart.yAxis[conty].update(
                                                                    {
                                                                        title:{
                                                                            style:{
                                                                                color: Highcharts.getOptions().colors[contOrder]
                                                            
                                                                            }
                                                                        },
                                                                        labels:{
                                                                            style:{
                                                                                color: Highcharts.getOptions().colors[contOrder]
                                                            
                                                                            }
                                                                        }
                                                                    }
                                                                ,false);

                                                                meChart.series[conty].update(
                                                                    {
                                                                        zIndex: contOrder,
                                                                        color : Highcharts.getOptions().colors[contOrder]
                                                                    }
                                                                ,false);

                                                                break;
                                                            }

                                                        };

                                                        contOrder++
                                                    }
                                                });

                                                me.showOrder = me.showOrder.substr(1, parseInt(me.showOrder.length));

                                            }

                                            me.showLegend[recordSeries.index] = index ;
                                            recordSeries.update({showInLegend: index, visible: index},false);
                                            meChart.yAxis[recordSeries.index].update({visible: index},false);

                                        }

                                        record.up('window').down('displayfield[name=contCheck]').setValue(cont);

                                        meChart.redraw(true);
                                        // meChart.reflow(true);
                                        
                                    }
                                };
                                
                                if(indicadoresAdd){
                                    for (let e = 0; e < indicadoresAdd.length; e++) {
                                        if(indicadoresAdd[e].name == record.name){
                                            element     = (!indicadoresAdd[e].value) ? null: element;
                                            elementCombo= (!indicadoresAdd[e].value) ? null: elementCombo;
                                        }
                                    }
                                }

                                var serieExtras = ['ES. QTD','ES. CUSTO MÉDIO','ES. VALOR','ES. FATOR','ES. GIRO','ES. DIAS','SKUD','CC','NF','TKM','CC Dia','Inflação de Estoque','Inflação de Compra'];

                                for (let e = 0; e < serieExtras.length; e++) {
                                    if(serieExtras[e] == record.name){

                                        if(record.checked)
                                            me.showOrder += ','+ record.name;

                                        element     = (recordSeries.yData.length > 0) ? element : null;
                                        elementCombo= (recordSeries.yData.length > 0) ? elementCombo : null;
                                    }
                                }

                                if(element){
                                    arryElements = {
                                        xtype: 'panel',
                                        layout: 'hbox',
                                        width: 336,
                                        border: false,
                                        items: [
                                            element,
                                            elementCombo
                                        ]
                                         
                                    };

                                    lista.push(arryElements);
                                }

                                // contIndex++;
                                
                            });

                            Ext.create('Ext.window.Window', {
                                title: 'Habilitar/Desabilitar Indicadores',
                                // renderTo: me,
                                scrollable: true,
                                height: 300,
                                width: 366,
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
                                                    cont++;
                                                }
                                            });
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
                                                    const itemsPanel = listaCheck.items[i];

                                                   if(itemsPanel.items.items[1].config.html == 'Tipo'){
                                                        continue;
                                                   }

                                                   const element  = itemsPanel.items.items[0]; // checket
                                                   var element2 = itemsPanel.items.items[1]; // tipo

                                                    if(element2.value && element2.value != 'line'){
                                                        meChart.series[i-1].update({type: 'line'},false);
                                                    }

                                                    element2.setValue(null);
                                                    me.showType[i-1] = 'line' ;
                                                    element.setValue(false);
                                                    me.showLegend[i-1] = false ;
                                                    meChart.series[i-1].setVisible(false, false);
                                                    meChart.yAxis[i-1].update({visible: false},false);

                                                }

                                                // meChart.redraw();

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
                            // this.chart.redraw();
                        },
                        show: function(){
                            this.chart.yAxis[this.index].update({visible: true},false);
                            // this.chart.redraw();
                        }
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
                text: '',
                // style: {
                //     fontSize: '14px'
                // }
            },
            xAxis: {
                categories: meses,
                crosshair: true
            },
            yAxis: aYaxis,
            tooltip: {
                // shared: true,
                // outside: true
            },
            series: series
        });

    }
});
