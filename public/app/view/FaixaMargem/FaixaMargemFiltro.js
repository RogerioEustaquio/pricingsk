Ext.define('App.view.faixamargem.FaixaMargemFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'faixamargemfiltro',
    itemId: 'faixamargemfiltro',
    title: 'Filtro',
    region: 'west',
    width: 290,
    hidden: false,
    scrollable: true,
    layout: 'vbox',
    requires:[
    ],

    initComponent: function() {
        var me = this;

        var dataXy = [
            {"id":0, "name":"Rede"},
            {"id":1, "name":"Filial"},
            {"id":2, "name":"Margem Item"},
            {"id":3, "name":"Margem Marca"},
            {"id":4, "name":"Margem Cliente"},
            {"id":5, "name":"Faixa MB Item / Nota 0-36"},
            {"id":6, "name":"Faixa MB  Item / Nota 0-30"},
            {"id":7, "name":"Faixa MB Item / Nota 0..5-100"},
            {"id":8, "name":"Faixa MB Item / Nota 0..10-100"},
            {"id":9, "name":"Pareto ROL Marca / Filial"},
            {"id":10, "name":"Pareto ROL Cliente / Filial"},
            {"id":11, "name":"Faixa MB Marca 0-36"},
            {"id":12, "name":"Faixa MB Marca 0-30"},
            {"id":13, "name":"Faixa MB Cliente 0-36"},
            {"id":14, "name":"Faixa MB Cliente 0-30"}
        ];

       var dataValor = [
            {"id":0,"valor":"ROL"},
            {"id":1,"valor":"MB"},
            {"id":2,"valor":"QTD"},
            {"id":3,"valor":"LB"},
            {"id":4,"valor":"CC"},
            {"id":5,"valor":"NF"}
        ];

        var valorY = Ext.create('Ext.form.field.ComboBox',{
            multiSelect: false,
            width: 100,
            name: 'y',
            itemId: 'y',
            labelAlign: 'top',
            emptyText: 'Y',
            fieldLabel: 'Y',
            store: Ext.data.Store({
                fields: [
                    { name: 'name', type: 'string' }
                ],
                data: dataXy
            }),
            queryParam: 'name',
            queryMode: 'local',
            displayField: 'name',
            // displayTpl: Ext.create('Ext.XTemplate','<tpl for=".">','<b>{tipo}</b>','</tpl>'),
            valueField: 'id',
            width: 230,
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            disabled: false,
            // value: me.showType[recordSeries.index] == 'line'? null : me.showType[recordSeries.index],
            listeners : {
                select : function(record,index){

                    var array = [];
                    var bolleanEntra = true;
                    for (let i = 0; i < dataXy.length; i++) {
                        const element = dataXy[i];

                        if(bolleanEntra && index.data.name != dataXy[i].name)
                            array.push(element);

                        bolleanEntra = true;
                        
                    }
                    this.up('panel').up('panel').down('#x').store.setData(array);
                }
            }
        });
        
        var valorX = Ext.create('Ext.form.field.ComboBox',{
            multiSelect: false,
            width: 100,
            name: 'x',
            itemId: 'x',
            labelAlign: 'top',
            emptyText: 'X',
            fieldLabel: 'X',
            store: Ext.data.Store({
                fields: [
                    { name: 'name', type: 'string' }
                ],
                data: dataXy
            }),
            queryParam: 'name',
            queryMode: 'local',
            displayField: 'name',
            // displayTpl: Ext.create('Ext.XTemplate','<tpl for=".">','<b>{tipo}</b>','</tpl>'),
            valueField: 'id',
            width: 230,
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            disabled: false,
            // value: me.showType[recordSeries.index] == 'line'? null : me.showType[recordSeries.index],
            listeners : {
                select : function(record,index){

                    var array = [];
                    var bolleanEntra = true;
                    for (let i = 0; i < dataXy.length; i++) {
                        const element = dataXy[i];

                        if(bolleanEntra && index.data.name != dataXy[i].name)
                            array.push(element);
                        
                        bolleanEntra = true;
                        
                    }
                    this.up('panel').up('panel').down('#y').store.setData(array);
                }
            }
        });
        
        var valorPrincipal = Ext.create('Ext.form.field.ComboBox',{
            multiSelect: false,
            width: 100,
            name: 'valorprincipal',
            itemId: 'valorprincipal',
            labelAlign: 'top',
            emptyText: 'Valor',
            fieldLabel: 'Valor',
            store: Ext.data.Store({
                fields: [
                    { name: 'valor', type: 'string' }
                ],
                data: dataValor
            }),
            queryParam: 'valor',
            queryMode: 'local',
            displayField: 'valor',
            // displayTpl: Ext.create('Ext.XTemplate','<tpl for=".">','<b>{tipo}</b>','</tpl>'),
            valueField: 'id',
            width: 230,
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            disabled: false,
            // value: me.showType[recordSeries.index] == 'line'? null : me.showType[recordSeries.index],
            listeners : {
            }
        });

        var elTagEmpresa = Ext.create('Ext.form.field.Tag',{
            name: 'elEmp',
            itemId: 'elEmp',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [
                    { name: 'emp', type: 'string' },
                    { name: 'idEmpresa', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/faixamargem/listarEmpresas',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            width: 230,
            queryParam: 'emp',
            queryMode: 'local',
            displayField: 'emp',
            valueField: 'idEmpresa',
            emptyText: 'Empresa',
            fieldLabel: 'Empresas',
            labelWidth: 60,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        elTagEmpresa.store.load(
            function(){
                elTagEmpresa.setDisabled(false);
            }
        );
        
        var dataIni = new Date().getFullYear();
        dataIni = '01/01/'+dataIni;
        var fielDataInicio = Ext.create('Ext.form.field.Date',{
            name: 'datainicio',
            itemId: 'datainicio',
            labelAlign: 'top',
            fieldLabel: 'Data Início',
            margin: '1 1 1 8',
            padding: 1,
            width: 230,
            labelWidth: 60,
            format: 'd/m/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            value: dataIni
        });
        
        var fielDataFinal = Ext.create('Ext.form.field.Date',{
            name: 'datafinal',
            itemId: 'datafinal',
            labelAlign: 'top',
            fieldLabel: 'Data Final',
            margin: '1 1 1 8',
            padding: 1,
            width: 230,
            labelWidth: 60,
            format: 'd/m/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            // value: sysdate
        });

        var btnNotMarca = Ext.create('Ext.Button', {
            name: 'notmarca',
            itemId: 'notmarca',
            hidden: true
        });
        
        var elTagMarca = Ext.create('Ext.form.field.Tag',{
            name: 'elMarca',
            itemId: 'elMarca',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'marca', type: 'string' },
                    { name: 'idMarca', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/faixamargem/listarmarca',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'marca',
            queryMode: 'local',/////
            displayField: 'marca',
            valueField: 'idMarca',
            emptyText: 'Marca',
            fieldLabel: 'Marcas',
            // labelWidth: 60,
            margin: '1 1 1 8',
            // padding: 1,
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagMarca.store.load(
            function(){
                elTagMarca.setDisabled(false);
            }
        );

        var elTagProduto = Ext.create('Ext.form.field.Tag',{
            name: 'elProduto',
            itemId: 'elProduto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [{ name: 'coditem' }, { name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/faixamargem/listarprodutos',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'codItem',
            queryMode: 'remote',
            displayField: 'codItem',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{codItem} {descricao} {marca}',
                '</tpl>'), 
            valueField: 'codItem',
            // emptyText: 'Produto',
            fieldLabel: 'Produtos',
            emptyText: 'Código Item NBS',
            // matchFieldWidth: false,
            // padding: 1,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',

            listeners: {
                
            },
            
            // allowBlank: false,
            listConfig: {
                loadingText: 'Carregando...',
                emptyText: '<div class="notificacao-red">Nenhuma produto encontrado!</div>',
                getInnerTpl: function() {
                    return '{[ values.codItem]} {[ values.descricao]} {[ values.marca]}';
                }
            }
        });

        var elTagIdProduto = Ext.create('Ext.form.field.Tag',{
            name: 'eltagidproduto',
            itemId: 'eltagidproduto',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            minChars: 2,
            store: Ext.data.Store({
                fields: [{ name: 'idProduto' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/faixamargem/listaridproduto',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'idProduto',
            queryMode: 'remote',
            displayField: 'idProduto',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{idProduto}',
                '</tpl>'),
            valueField: 'idProduto',
            // emptyText: 'Produto',
            fieldLabel: 'Código Produto',
            emptyText: 'Código Produto',
            // matchFieldWidth: false,
            // padding: 1,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',

            listeners: {
                
            },
            
            // allowBlank: false,
            listConfig: {
                loadingText: 'Carregando...',
                emptyText: '<div class="notificacao-red">Nenhuma produto encontrado!</div>',
                getInnerTpl: function() {
                    return '{[ values.idProduto]} {[ values.descricao]} {[ values.marca]}';
                }
            }
        });
        
        var elTagCategoria = Ext.create('Ext.form.field.Tag',{
            name: 'elcategoria',
            itemId: 'elcategoria',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [
                    { name: 'categoria', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/analisemarca/listarcategoria',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'categoria',
            queryMode: 'local',
            displayField: 'categoria',
            valueField: 'categoria',
            emptyText: 'Categoria',
            fieldLabel: 'Categorias',
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagCategoria.store.load(
            function(){
                elTagCategoria.setDisabled(false);
            }
        );

        Ext.applyIf(me, {

            items : [
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        valorY
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        valorX
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        valorPrincipal
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        elTagEmpresa,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        fielDataInicio,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('datefield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        fielDataFinal,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('datefield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        btnNotMarca,
                        elTagMarca,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    hidden: true,
                    items:[
                        elTagProduto,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        elTagIdProduto,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        elTagCategoria,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        }
                    ]
                },
                {
                    xtype: 'toolbar',
                    width: '100%',
                    border: false,
                    items:[
                        '->',
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            text: 'Limpar Filtros',
                            tooltip: 'Limpar Filtros',
                            handler: function(form) {
                               
                                form.up('toolbar').up('panel').down('button[name=notmarca]').value = 0;
                                form.up('toolbar').up('panel').down('tagfield[name=elMarca]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elProduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagidproduto]').setValue(null);
                            }
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);

    }

});
