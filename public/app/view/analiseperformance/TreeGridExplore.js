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
                                        {name:'cmvAcan', type: 'number'},
                                        {name:'cmvAcat', type: 'number'},
                                        {name:'cmvM0', type: 'number'},
                                        {name:'cmvM1', type: 'number'},
                                        {name:'cmvM2', type: 'number'},
                                        {name:'cmvM3', type: 'number'},
                                        {name:'cmv_3m', type: 'number'},
                                        {name:'cmv_6m', type: 'number'},
                                        {name:'cmv_12m', type: 'number'},
                                        {name:'cmvdiaAcan', type: 'number'},
                                        {name:'cmvdiaAcat', type: 'number'},
                                        {name:'cmvdiaM0', type: 'number'},
                                        {name:'cmvdiaM1', type: 'number'},
                                        {name:'cmvdiaM2', type: 'number'},
                                        {name:'cmvdiaM3', type: 'number'},
                                        {name:'cmvdia_3m', type: 'number'},
                                        {name:'cmvdia_6m', type: 'number'},
                                        {name:'cmvdia_12m', type: 'number'},

                                        {name:'lbAcan', type: 'number'},
                                        {name:'lbAcat', type: 'number'},
                                        {name:'lbM0', type: 'number'},
                                        {name:'lbM1', type: 'number'},
                                        {name:'lbM2', type: 'number'},
                                        {name:'lbM3', type: 'number'},
                                        {name:'lb_3m', type: 'number'},
                                        {name:'lb_6m', type: 'number'},
                                        {name:'lb_12m', type: 'number'},
                                        {name:'lbdiaAcan', type: 'number'},
                                        {name:'lbdiaAcat', type: 'number'},
                                        {name:'lbdiaM0', type: 'number'},
                                        {name:'lbdiaM1', type: 'number'},
                                        {name:'lbdiaM2', type: 'number'},
                                        {name:'lbdiaM3', type: 'number'},
                                        {name:'lbdia_3m', type: 'number'},
                                        {name:'lbdia_6m', type: 'number'},
                                        {name:'lbdia_12m', type: 'number'},

                                        {name:'qtdAcan', type: 'number'},
                                        {name:'qtdAcat', type: 'number'},
                                        {name:'qtdM0', type: 'number'},
                                        {name:'qtdM1', type: 'number'},
                                        {name:'qtdM2', type: 'number'},
                                        {name:'qtdM3', type: 'number'},
                                        {name:'qtd_3m', type: 'number'},
                                        {name:'qtd_6m', type: 'number'},
                                        {name:'qtd_12m', type: 'number'},
                                        {name:'qtddiaAcan', type: 'number'},
                                        {name:'qtddiaAcat', type: 'number'},
                                        {name:'qtddiaM0', type: 'number'},
                                        {name:'qtddiaM1', type: 'number'},
                                        {name:'qtddiaM2', type: 'number'},
                                        {name:'qtddiaM3', type: 'number'},
                                        {name:'qtddia_3m', type: 'number'},
                                        {name:'qtddia_6m', type: 'number'},
                                        {name:'qtddia_12m', type: 'number'},

                                        {name:'rolAcan', type: 'number'},
                                        {name:'rolAcat', type: 'number'},
                                        {name:'rolM0', type: 'number'},
                                        {name:'rolM1', type: 'number'},
                                        {name:'rolM2', type: 'number'},
                                        {name:'rolM3', type: 'number'},
                                        {name:'rol_3m', type: 'number'},
                                        {name:'rol_6m', type: 'number'},
                                        {name:'rol_12m', type: 'number'},
                                        {name:'roldiaAcan', type: 'number'},
                                        {name:'roldiaAcat', type: 'number'},
                                        {name:'roldiaM0', type: 'number'},
                                        {name:'roldiaM1', type: 'number'},
                                        {name:'roldiaM2', type: 'number'},
                                        {name:'roldiaM3', type: 'number'},
                                        {name:'roldia_3m', type: 'number'},
                                        {name:'roldia_6m', type: 'number'},
                                        {name:'roldia_12m', type: 'number'},
                                        
                                        {name:'varLbdAcatAcan', type: 'number'},
                                        {name:'varLbdM0M1', type: 'number'},
                                        {name:'varMbdM0M1', type: 'number'},
                                        {name:'varRoldAcatAcan', type: 'number'},
                                        {name:'varRoldM0M1', type: 'number'},
                                    ]
                        });

        var mystore = Ext.create('Ext.data.TreeStore', {
            model: myModel,
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: BASEURL + '/api/explore/listartreepvd',
                encode: true,
                timeout: 1200000,
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
                //----------------ROL-------------------------
                {
                    text: 'ROL Mês',
                    dataIndex: 'rolM0',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL Ant.',
                    dataIndex: 'rolM1',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL M2',
                    dataIndex: 'rolM2',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL M3',
                    dataIndex: 'rolM3',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL 3M',
                    dataIndex: 'rol_3m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL 6M',
                    dataIndex: 'rol_6m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL 12M',
                    dataIndex: 'rol_12m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL Ac Ant.',
                    dataIndex: 'rolAcan',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL Ac Atual',
                    dataIndex: 'rolAcat',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                //------------------------------------------
                //----------------CMV-------------------------
                {
                    text: 'CMV Mês',
                    dataIndex: 'cmvM0',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV Ant.',
                    dataIndex: 'cmvM1',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV M2',
                    dataIndex: 'cmvM2',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV M3',
                    dataIndex: 'cmvM3',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV 3M',
                    dataIndex: 'cmv_3m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV 6M',
                    dataIndex: 'cmv_6m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV 12M',
                    dataIndex: 'cmv_12m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV Ac Ant.',
                    dataIndex: 'cmvAcan',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV Ac Atual',
                    dataIndex: 'cmvAcat',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                //------------------------------------------
                //----------------LB-------------------------
                {
                    text: 'LB Mês',
                    dataIndex: 'lbM0',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB Ant.',
                    dataIndex: 'lbM1',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB M2',
                    dataIndex: 'lbM2',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB M3',
                    dataIndex: 'lbM3',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB 3M',
                    dataIndex: 'lb_3m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB 6M',
                    dataIndex: 'lb_6m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB 12M',
                    dataIndex: 'lb_12m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB Ac Ant.',
                    dataIndex: 'lbAcan',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB Ac Atual',
                    dataIndex: 'lbAcat',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                //------------------------------------------
                //----------------QTD-------------------------
                {
                    text: 'QTD Mês',
                    dataIndex: 'qtdM0',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD Ant.',
                    dataIndex: 'qtdM1',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD M2',
                    dataIndex: 'qtdM2',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD M3',
                    dataIndex: 'qtdM3',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD 3M',
                    dataIndex: 'qtd_3m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD 6M',
                    dataIndex: 'qtd_6m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD 12M',
                    dataIndex: 'qtd_12m',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD Ac Ant.',
                    dataIndex: 'qtdAcan',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD Ac Atual',
                    dataIndex: 'qtdAcat',
                    width: 90,
                    align: 'right',
                    hidden: true,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                //------------------------------------------
                //------------------ROL DIA ------------------------
                {
                    text: 'ROL/DIA Mês',
                    dataIndex: 'roldiaM0',
                    width: 120,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'ROL/DIA Mês Ant.',
                    dataIndex: 'roldiaM1',
                    width: 130,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA M2',
                    dataIndex: 'roldiaM2',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA M3',
                    dataIndex: 'roldiaM3',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA 3M',
                    dataIndex: 'roldia_3m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA 6M',
                    dataIndex: 'roldia_6m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA 12M',
                    dataIndex: 'roldia_12m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA Ano Ant.',
                    dataIndex: 'roldiaAcan',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'ROL/DIA Ano Atual',
                    dataIndex: 'roldiaAcat',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                //--------------------------------------------------------
                //------------------CMV DIA ------------------------------
                {
                    text: 'CMV/DIA Mês',
                    dataIndex: 'cmvdiaM0',
                    width: 120,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'CMV/DIA Mês Ant.',
                    dataIndex: 'cmvdiaM1',
                    width: 130,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'CMV/DIA M2',
                    dataIndex: 'cmvdiaM2',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'CMV/DIA M3',
                    dataIndex: 'cmvdiaM3',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'CMV/DIA 3M',
                    dataIndex: 'cmvdia_3m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'CMV/DIA 6M',
                    dataIndex: 'cmvdia_6m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'CMV/DIA 12M',
                    dataIndex: 'cmvdia_12m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'CMV/DIA Ano Ant.',
                    dataIndex: 'cmvdiaAcan',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'CMV/DIA Ano Atual',
                    dataIndex: 'cmvdiaAcat',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                //--------------------------------------------------------
                //------------------LB DIA ------------------------------
                {
                    text: 'LB/DIA Mês',
                    dataIndex: 'lbdiaM0',
                    width: 120,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'LB/DIA Mês Ant.',
                    dataIndex: 'lbdiaM1',
                    width: 130,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA M2',
                    dataIndex: 'lbdiaM2',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA M3',
                    dataIndex: 'lbdiaM3',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA 3M',
                    dataIndex: 'lbdia_3m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA 6M',
                    dataIndex: 'lbdia_6m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA 12M',
                    dataIndex: 'lbdia_12m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'LB/DIA Ano Ant.',
                    dataIndex: 'lbdiaAcan',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'LB/DIA Ano Atual',
                    dataIndex: 'lbdiaAcat',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                //--------------------------------------------------------
                //------------------QTD DIA ------------------------------
                {
                    text: 'QTD/DIA Mês',
                    dataIndex: 'qtddiaM0',
                    width: 120,
                    align: 'right',
                    hidden: false,
                    renderer: function (v) {
                        return utilFormat.ValueZero(v);
                    }
                },
                {
                    text: 'QTD/DIA Mês Ant.',
                    dataIndex: 'qtddiaM1',
                    width: 130,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'QTD/DIA M2',
                    dataIndex: 'qtddiaM2',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'QTD/DIA M3',
                    dataIndex: 'qtddiaM3',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'QTD/DIA 3M',
                    dataIndex: 'qtddia_3m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'QTD/DIA 6M',
                    dataIndex: 'qtddia_6m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'QTD/DIA 12M',
                    dataIndex: 'qtddia_12m',
                    width: 120,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                {
                    text: 'QTD/DIA Ano Ant.',
                    dataIndex: 'qtddiaAcan',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);

                        return valor;
                    }
                },
                {
                    text: 'QTD/DIA Ano Atual',
                    dataIndex: 'qtddiaAcat',
                    width: 140,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value(v);
                        return valor;
                    }
                },
                //--------------------------------------------------------
                //----------------- Variáveis ----------------------------//
                
                {
                    text: 'ROL/DIA Ano Atual x Anterior',
                    dataIndex: 'varRoldAcatAcan',
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
                    text: 'ROL/DIA Mês Atual x Anterior',
                    dataIndex: 'varRoldM0M1',
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
                    dataIndex: 'varLbdAcatAcan',
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
                    text: 'LB/DIA Mês Atual x Anterior',
                    dataIndex: 'varLbdM0M1',
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
                    dataIndex: 'varMbdM0M1',
                    width: 160,
                    align: 'left',
                    renderer: function (v, metaData, record) {

                        var valor = utilFormat.Value2(v,2);
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