Ext.define('App.view.analiseperformance.TreeGridExplore',{
    extend: 'Ext.tree.Panel',
    xtype: 'treegridexplore',
    itemId: 'treegridexplore',
    rootVisible: false,

    constructor: function() {
        var me = this;
        var utilFormat = Ext.create('Ext.ux.util.Format');

        var stylesvg = 'vertical-align: middle;';
        stylesvg += 'width: 12px;';
        stylesvg += 'margin-right: 4px;';
        stylesvg += 'height: 12px;';
        stylesvg += 'border-radius: 50%;';
        stylesvg += 'display: -webkit-inline-flex;';
        stylesvg += 'display: inline-flex;';
        stylesvg += '-webkit-align-items: center;';
        stylesvg += 'align-items: center;';
        stylesvg += '-webkit-justify-content: center;';
        stylesvg += 'justify-content: center;';
        var pathMaior = '<div class="ValueChange_triangle__3YfpU" style="background: #dbfddd;'+stylesvg+'"><svg width="6" height="4" viewBox="0 0 6 4" xmlns="http://www.w3.org/2000/svg"><path fill="#26C953" d="M2.66175 0.159705C2.83527 -0.0532454 3.16516 -0.0532329 3.33868 0.15973L5.90421 3.30859C6.13124 3.58724 5.92918 4 5.56574 4H0.434261C0.0708052 4 -0.131254 3.58721 0.0958059 3.30857L2.66175 0.159705Z"></path></svg></div>';
        var pathMenor = '<div class="ValueChange_triangle__3YfpU" style="background: #ffe6e6;'+stylesvg+'"><svg width="6" height="4" viewBox="0 0 6 4" xmlns="http://www.w3.org/2000/svg"><path fill="#FF5B5B" d="M3.33825 3.8403C3.16473 4.05325 2.83484 4.05323 2.66132 3.84027L0.0957854 0.691405C-0.131243 0.412756 0.0708202 -5.18345e-07 0.434261 -4.86572e-07L5.56574 -3.79643e-08C5.9292 -6.18999e-09 6.13125 0.412786 5.90419 0.69143L3.33825 3.8403Z"></path></svg></div>';

        var myModel = Ext.create('Ext.data.TreeModel', {
                            fields: [
                                        { name: 'grupo', type: 'string'},
                                        {name:'filial', type: 'string'},
                                        {name:'diasUteisM0', type: 'number'},
                                        {name:'diasUteisM1', type: 'number'},
                                        {name:'rolM0', type: 'number'},
                                        {name:'rolDiaM0', type: 'number'},
                                        {name:'rolDiaM01', type: 'number'},
                                        {name:'rolDiaM0X_1m', type: 'number'},
                                        {name:'lbM0', type: 'number'},
                                        {name:'lbDiaM0', type: 'number'},
                                        {name:'lbDiaM0X_1m', type: 'number'},
                                        {name:'mbM0', type: 'number'},
                                        {name:'mbM0X_1m', type: 'number'},
                                        {name:'qtdeM0', type: 'number'},
                                        {name:'qtdeDiaM0', type: 'number'},
                                        {name:'qtdeDiaM0X_1m', type: 'number'},
                                        {name:'ccM0', type: 'number'},
                                        {name:'ccDiaM0', type: 'number'},
                                        {name:'ccDiaM0X_1m', type: 'number'},
                                        {name:'nfM0', type: 'number'},
                                        {name:'nfDiaM0', type: 'number'},
                                        {name:'nfDiaM0X_1m', type: 'number'},
                                        {name:'skuM0', type: 'number'},
                                        {name:'estoqueQtde', type: 'number'},
                                        {name:'estoqueValor', type: 'number'},
                                        {name:'estoqueSkuDisp', type: 'number'}
                                    ]
                        });

        var mystore = Ext.create('Ext.data.TreeStore', {
            model: myModel,
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: BASEURL + '/api/explore/listartreepvd',
                encode: true,
                timeout: 240000,
                reader: {
                    type: 'json',
                    successProperty: 'success',
                    messageProperty: 'message',
                    root: 'data'
                }
            },
            root: {
                expanded: true,
                text: "",
                // children: [],
                "data": []
            }
        });

        Ext.applyIf(me, {

            store: mystore,
            columns: [
                {
                    xtype: 'treecolumn', // this is so we know which column will show the tree
                    text: '', //Descrição
                    dataIndex: 'grupo',
                    flex: 1,
                    minWidth: 228,
                    sortable: true
                },
                {
                    text: 'ROL/DIA',
                    dataIndex: 'rolDia',
                    width: 90,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'Ac. Ano Anterior',
                    dataIndex: 'acanoanterior',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Ano Anterior',
                    dataIndex: 'anoanterior',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 12 Meses',
                    dataIndex: 'med_12Meses',
                    width: 110,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 6 Meses',
                    dataIndex: 'med_6Meses',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 3 Meses',
                    dataIndex: 'med_3Meses',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Mês Ano Anterior',
                    dataIndex: 'mesAnoAnterior',
                    width: 130,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Mês Anterior',
                    dataIndex: 'mesAnterior',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'LB/DIA',
                    dataIndex: 'lbDia',
                    width: 80,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'Ac. Ano Anterior',
                    dataIndex: 'lbAcanoanterior',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Ano Anterior',
                    dataIndex: 'LbAnoanterior',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 12 Meses',
                    dataIndex: 'LbMed_12Meses',
                    width: 110,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 6 Meses',
                    dataIndex: 'LbMed_6Meses',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 3 Meses',
                    dataIndex: 'LbMed_3Meses',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Mês Ano Anterior',
                    dataIndex: 'LbMesAnoAnterior',
                    width: 130,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Mês Anterior',
                    dataIndex: 'LbMesAnterior',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'MB/DIA',
                    dataIndex: 'mbDia',
                    width: 80,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'Ac. Ano Anterior',
                    dataIndex: 'mbAcanoanterior',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Ano Anterior',
                    dataIndex: 'mbAnoanterior',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 12 Meses',
                    dataIndex: 'mbMed_12Meses',
                    width: 110,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 6 Meses',
                    dataIndex: 'mbMed_6Meses',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Méd. 3 Meses',
                    dataIndex: 'mbMed_3Meses',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Mês Ano Anterior',
                    dataIndex: 'mbMesAnoAnterior',
                    width: 130,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'Mês Anterior',
                    dataIndex: 'mbMesAnterior',
                    width: 100,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor +'%';
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor +'%';
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                }
               
            ],
            listeners: {
                // click: {
                //     element: 'el', //bind to the underlying el property on the panel
                //     fn: function(e){ console.log( this.getLoader()); }
                // },
                // dblclick: {
                //     element: 'body', //bind to the underlying body property on the panel
                //     fn: function(){ console.log('dblclick body'); }
                // }
            }

        });

        me.callParent(arguments);

    }
    
});