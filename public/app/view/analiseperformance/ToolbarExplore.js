Ext.define('App.view.analiseperformance.ToolbarExplore',{
    extend: 'Ext.Toolbar',
    xtype: 'toolbarexplore',
    itemId: 'toolbarexplore',
    region: 'north',
    requires:[
        'App.view.analiseperformance.NodeWindowExplore',
        'App.view.analiseperformance.FiltrosWindowExplore'
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

        // var today = new Date();
        // var sysdate = today.getMonth() +'/'+ today.getFullYear();

        var btnGrupo = Ext.create('Ext.button.Button',{
            iconCls: 'fa fa-list',
            margin: '1 1 1 4',
            handler: me.onBtnGrupo
        });

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
                btnGrupo,
                btnFiltro,
                btnConsultar,
                '->',
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Referência',
                    name: 'dtreferencia',
                    itemId: 'dtreferencia',
                    labelWidth: 64,
                    margin: '1 20 1 1',
                    // value: sysdate
                }
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

        var filtro = me.up('panel').down('panel').down('#explorefiltro');

        if(filtro.hidden){
            filtro.setHidden(false);
        }else{
            filtro.setHidden(true);
        }

    },

    onBtnConsultar: function(btn){
        var me = this.up('toolbar');

        var grid = me.up('container').down('panel').down('treepanel');
        
        var filtro = me.up('panel').down('panel').down('#explorefiltro');

        var idEmpresa       = filtro.down('#elfilial').getValue();
        var regional        = filtro.down('#elregional').getValue();
        var dataReferencia  = filtro.down('#datareferencia').getRawValue();
        var produtos        = filtro.down('#elproduto').getValue();
        var marcas          = filtro.down('#elmarca').getValue();
        var categorias      = filtro.down('#elcategoria').getValue();

        var params = {
            idempresa : Ext.encode(idEmpresa),
            regional: Ext.encode(regional),
            datareferencia: dataReferencia,
            produtos: Ext.encode(produtos),
            marcas: Ext.encode(marcas),
            categorias: Ext.encode(categorias),
            niveis: Ext.encode(me.vNiveis),
            ordem : Ext.encode(me.vOrdem)
        };
        me.down('#dtreferencia').setValue(dataReferencia);

        grid.getStore().getProxy().setExtraParams(params);
        grid.getStore().load();

    }
    
});