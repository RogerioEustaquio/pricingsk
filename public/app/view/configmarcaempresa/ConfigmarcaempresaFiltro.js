Ext.define('App.view.configmarcaempresa.ConfigmarcaempresaFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'configmarcaempresafiltro',
    itemId: 'configmarcaempresafiltro',
    title: 'Filtro',
    region: 'west',
    width: 290,
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
                    url: BASEURL + '/api/configmarcaempresa/listarEmpresas',
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
                    url: BASEURL + '/api/configmarcaempresa/listarmarca',
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
                    url: BASEURL + '/api/configmarcaempresa/listaridproduto',
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

        var elEstoque = Ext.create('Ext.form.field.ComboBox',{
            name: 'elestoque',
            itemId: 'elestoque',
            multiSelect: false,
            // labelAlign: 'top',
            width: 230,
            store: Ext.data.Store({
                fields: [
                    { name: 'elestoque', type: 'string' },
                    { name: 'name', type: 'string' }
                ],
                
                data : [
                    {"elestoque":"", "name":""},
                    {"elestoque":"Com", "name":"Com"},
                    {"elestoque":"Sem", "name":"Sem"}
                ]
            }),
            queryMode: 'local',
            displayField: 'name',
            valueField: 'elestoque',
            emptyText: '',
            fieldLabel: 'Estoque',
            labelWidth: 110,
            margin: '10 1 10 0',
            filterPickList: true,
            disabled: false
        });

        Ext.applyIf(me, {

            items : [
                {
                    xtype: 'panel',
                    layout: 'hbox',
                    border: false,
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
                xtype:'fieldset',
                title: 'Com/Sem Parâmetro', // title or checkboxToggle creates fieldset header
                // columnWidth: 0.5,
                checkboxToggle: true,
                collapsed: true, // fieldset initially collapsed
                layout:'anchor',
                hidden: true,
                items :[
                        {
                            xtype: 'panel',
                            layout: 'hbox',
                            border: false,
                            items:[
                                elEstoque,
                                {
                                    xtype: 'button',
                                    iconCls: 'fa fa-file',
                                    tooltip: 'Limpar',
                                    margin: '10 1 1 1',
                                    handler: function(form) {
                                        form.up('panel').down('combobox').setValue(null);
                                    }
                                }
                            ]
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
                                form.up('toolbar').up('panel').down('tagfield[name=eltagidproduto]').setValue(null);
                                form.up('toolbar').up('panel').down('button[name=notmarca]').value = 0;
                                form.up('toolbar').up('panel').down('tagfield[name=elMarca]').setValue(null);
                                form.up('toolbar').up('panel').down('fieldset').setCollapsed(true);
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

        if(me.up('container').down('#bprecofiltro').hidden){
            me.up('container').down('#bprecofiltro').setHidden(false);
        }else{
            me.up('container').down('#bprecofiltro').setHidden(true);
        }

    },

    onBtnConsultar: function(btn){
        var me = this.up('toolbar');

    }

});
