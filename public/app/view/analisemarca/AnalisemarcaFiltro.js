Ext.define('App.view.analisemarca.AnalisemarcaFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'analisemarcafiltro',
    itemId: 'analisemarcafiltro',
    title: 'Filtro',
    region: 'west',
    width: 310,
    hidden: true,
    scrollable: true,
    layout: 'vbox',
    requires:[
    ],

    initComponent: function() {
        var me = this;

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
                    url: BASEURL + '/api/analisemarca/listarEmpresas',
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
            valueField: 'emp',
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

        var elTagRegional = Ext.create('Ext.form.field.Tag',{
            name: 'elRegional',
            itemId: 'elRegional',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [
                    { name: 'id', type: 'string' },
                    { name: 'regional', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/analisemarca/listarregional',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            width: 230,
            queryParam: 'regional',
            queryMode: 'local',
            displayField: 'regional',
            valueField: 'id',
            emptyText: 'Regional',
            fieldLabel: 'Regional',
            labelWidth: 60,
            margin: '1 1 1 8',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        elTagRegional.store.load(
            function(){
                elTagRegional.setDisabled(false);
            }
        );

        var fielData = Ext.create('Ext.form.field.Date',{
            name: 'data',
            itemId: 'data',
            labelAlign: 'top',
            fieldLabel: 'Mês de Referência',
            margin: '1 1 1 8',
            padding: 1,
            width: 230,
            labelWidth: 60,
            format: 'm/Y',
            altFormats: 'dmY',
            emptyText: '__/__/____',
            // value: sysdate
        });

        var fielmeses = Ext.create('Ext.form.Panel',{

                    layout: 'hbox',
                    border: false,
                    hidden: false,
                    items:[
                        {
                            xtype: 'radiofield',
                            checked: true,
                            name : 'qtdemeses',
                            inputValue: 12,
                            boxLabel: '12 meses',
                            labelTextAlign: 'right',
                            labelWidth: 24,
                            width: 80,
                            margin: '1 1 1 8'
                        },
                        {
                            xtype: 'radiofield',
                            name : 'qtdemeses',
                            inputValue: 24,
                            boxLabel: '24 meses',
                            labelTextAlign: 'right',
                            labelWidth: 24,
                            width: 80,
                            // margin: '1 1 1 8',
                        },
                        {
                            xtype: 'radiofield',
                            name : 'qtdemeses',
                            inputValue: 36,
                            boxLabel: '36 meses',
                            labelTextAlign: 'right',
                            labelWidth: 24,
                            width: 80,
                            // margin: '1 1 1 8',
                        }
                    ]
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
                    url: BASEURL + '/api/analisemarca/listaridproduto',
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
            fieldLabel: 'Código Produto Sankhya',
            emptyText: 'Código Produto Sankhya',
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
                    url: BASEURL + '/api/analisemarca/listarprodutos',
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
            fieldLabel: 'Código Produto NBS',
            emptyText: 'Código Produto NBS',
            margin: '1 1 1 8',
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
                    url: BASEURL + '/api/analisemarca/listarmarca',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'marca',
            queryMode: 'local',
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

        var elTagMontadora = Ext.create('Ext.form.field.Tag',{
            name: 'elMontadora',
            itemId: 'elMontadora',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    // { name: 'marca', type: 'string' },
                    { name: 'montadora', type: 'string' }
                ],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/analisemarca/listarmontadora',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            queryParam: 'montadora',
            queryMode: 'local',
            displayField: 'montadora',
            valueField: 'montadora',
            emptyText: 'Montadora',
            fieldLabel: 'Montadoras',
            // labelWidth: 60,
            margin: '1 1 1 8',
            // padding: 1,
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled: true
        });
        elTagMontadora.store.load(
            function(){
                elTagMontadora.setDisabled(false);
            }
        );
        
        var elTagCesta = Ext.create('Ext.form.field.Tag',{
            name: 'elcesta',
            itemId: 'elcesta',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            store: Ext.data.Store({
                fields: [{ name: 'codcesta' }, { name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/analisemarca/listarcesta',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'codcesta',
            queryMode: 'remote',
            displayField: 'codcesta',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{codcesta} {descricao}',
                '</tpl>'), 
            valueField: 'codcesta',
            fieldLabel: 'Cesta de Produtos',
            emptyText: 'Mês/Ano',
            margin: '1 1 1 8',
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


        Ext.applyIf(me, {

            items : [
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
                    hidden: false,
                    items:[
                        elTagRegional,
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
                        fielData,
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
                fielmeses,
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
                    items:[
                        elTagMontadora,
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-file',
                            tooltip: 'Limpar',
                            margin: '26 1 1 1',
                            handler: function(form) {
                                form.up('panel').down('tagfield').setValue(null);
                            }
                        },
                        {
                            xtype: 'checkboxfield',
                            margin: '1 1 1 1',
                            fieldLabel: ' ≠ ',
                            labelAlign: 'top',
                            name :'notmontadora',
                            itemId :'notmontadora',
                            autoEl: {
                                tag: 'div',
                                'data-qtip': 'Diferente'
                            }
                            // style: {
                            //     backgroundColor:'#EE0000 !important',
                            //     fontSize: '100px !important'
                            // },
                        }
                    ]
                },
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
                    items:[
                        elTagCesta,
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
                                form.up('toolbar').up('panel').down('tagfield[name=elEmp]').setValue(null);
                                form.up('toolbar').up('panel').down('datefield[name=data]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=eltagidproduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elProduto]').setValue(null);
                                form.up('toolbar').up('panel').down('tagfield[name=elMarca]').setValue(null);
                                
                            }
                        }
                    ]
                }
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        if(me.up('container').down('#analisegraficafiltro').hidden){
            me.up('container').down('#analisegraficafiltro').setHidden(false);
        }else{
            me.up('container').down('#analisegraficafiltro').setHidden(true);
        }
        
    }

});
