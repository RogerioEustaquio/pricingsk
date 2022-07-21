Ext.define('App.view.analiseperformance.TreeGridExplore',{
    extend: 'Ext.tree.Panel',
    xtype: 'treegridexplore',
    itemId: 'treegridexplore',
    rootVisible: false,
    width: '100%',
    height: '40%',

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
                                        {name: 'grupo', type: 'string'},
                                        {name:'rolDiaM0', type: 'number'},
                                        {name:'lbDiaM0', type: 'number'},
                                        {name:'mbM0', type: 'number'},
                                        {name:'rolDiaM1', type: 'number'},
                                        {name:'lbDiaM1', type: 'number'},
                                        {name:'mbM1', type: 'number'},

                                        {name:'rolDiaAcAt', type: 'number'},
                                        {name:'lbDiaAcAt', type: 'number'},
                                        {name:'rolDiaAcAn', type: 'number'},
                                        {name:'lbDiaAcAn', type: 'number'},
                                        {name:'mbAcAn', type: 'number'},
                                        {name:'varRdM0M1', type: 'number'},
                                        {name:'varLbM0M1', type: 'number'},
                                        {name:'varMbM0M1', type: 'number'},
                                        {name:'varRdAcAtAn', type: 'number'},
                                        {name:'varLdAcAtAn', type: 'number'}
                                    ]
                        });

        var mystore = Ext.create('Ext.data.TreeStore', {
            model: myModel,
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: BASEURL + '/api/explore/listartreepvd',
                encode: true,
                timeout: 480000,
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
                    text: 'ROL/DIA Mês',
                    dataIndex: 'rolDiaM0',
                    width: 120,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB/DIA Mês',
                    dataIndex: 'lbDiaM0',
                    width: 120,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'MB Mês',
                    dataIndex: 'mbM0',
                    width: 80,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.Value2(v,2);
                    }
                },
                {
                    text: 'ROL/DIA Mês Anterior',
                    dataIndex: 'rolDiaM1',
                    width: 150,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA Mês Anterior',
                    dataIndex: 'lbDiaM1',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'MB Mês Anterior',
                    dataIndex: 'mbM1',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        return utilFormat.Value2(v,2);
                    }
                },
                {
                    text: 'ROL/DIA Ano Atual',
                    dataIndex: 'rolDiaAcAt',
                    width: 160,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA Ano Atual',
                    dataIndex: 'lbDiaAcAt',
                    width: 160,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA Ano Anterior',
                    dataIndex: 'rolDiaAcAn',
                    width: 160,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'LB/DIA Ano Anterior',
                    dataIndex: 'lbDiaAcAn',
                    width: 160,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'MB Ano Anterior',
                    dataIndex: 'mbAcAn',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        return utilFormat.Value2(v,2);
                    }
                },
                {
                    text: 'ROL/DIA Mês Atual x Anterior',
                    dataIndex: 'varRdM0M1',
                    width: 190,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor ;
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor;
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'LB/DIA Mês Atual x Anterior',
                    dataIndex: 'varLbM0M1',
                    width: 180,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor ;
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor;
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'MB Mês Atual x Anterior',
                    dataIndex: 'varMbM0M1',
                    width: 160,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor ;
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor;
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return utilFormat.Value2(valor,2);
                    }
                },
                {
                    text: 'ROL/DIA Ano Atual x Anterior',
                    dataIndex: 'varRdAcAtAn',
                    width: 190,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor ;
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor;
                            metaData.style = 'color: #FF5B5B;';
                        }

                        return valor;
                    }
                },
                {
                    text: 'LB/DIA Ano Atual x Anterior',
                    dataIndex: 'varLdAcAtAn',
                    width: 180,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        if (v > 0){
                            valor = pathMaior +' '+ valor ;
                            metaData.style = 'color: #26C953;';
                        }
                        if (v < 0){
                            valor = pathMenor +' '+valor;
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