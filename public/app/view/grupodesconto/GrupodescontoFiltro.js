Ext.define('App.view.grupodesconto.GrupodescontoFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'grupodescontofiltro',
    itemId: 'grupodescontofiltro',
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
                    url: BASEURL + '/api/grupodesconto/listarEmpresas',
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

        var elTagTabPreco = Ext.create('Ext.form.field.Tag',{
            name: 'eltagtabpreco',
            itemId: 'eltagtabpreco',
            multiSelect: true,
            labelAlign: 'top',
            width: 230,
            labelWidth: 60,
            minChars: 2,
            store: Ext.data.Store({
                fields: [{ name: 'codTabPreco' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/grupodesconto/listartabelapreco',
                    reader: { type: 'json', root: 'data' },
                    extraParams: { tipoSql: 0}
                }
            }),
            queryParam: 'codTabPreco',
            queryMode: 'remote',
            displayField: 'codTabPreco',
            displayTpl: Ext.create('Ext.XTemplate',
                '<tpl for=".">',		                            
                '{codTabPreco}',
                '</tpl>'), 
            valueField: 'codTabPreco',
            // emptyText: 'Produto',
            fieldLabel: 'Código Table Preço',
            emptyText: 'Código Table Preço',
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
                emptyText: '<div class="notificacao-red">Nenhuma encontrado!</div>',
                getInnerTpl: function() {
                    return '{[ values.codTabPreco]} {[ values.descricao]} {[ values.marca]}';
                }
            }
        });

        var elTagGrupoDesconto = Ext.create('Ext.form.field.Tag',{
            name: 'eltaggrupodesconto',
            itemId: 'eltaggrupodesconto',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [{ name: 'codGrupoDesconto' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/grupodesconto/listargrupodescontos',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            width: 230,
            queryParam: 'codGrupoDesconto',
            queryMode: 'local',
            displayField: 'codGrupoDesconto',
            valueField: 'codGrupoDesconto',
            emptyText: 'Grupo Desconto',
            fieldLabel: 'Grupo Desconto',
            labelWidth: 60,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        elTagGrupoDesconto.store.load(
            function(){
                elTagGrupoDesconto.setDisabled(false);
            }
        );

        var elTagDescontoMargem = Ext.create('Ext.form.field.Tag',{
            name: 'eltagdescontomargem',
            itemId: 'eltagdescontomargem',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [{ name: 'descontoMargem' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/grupodesconto/listardescontomargem',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            width: 230,
            queryParam: 'descontoMargem',
            queryMode: 'local',
            displayField: 'descontoMargem',
            valueField: 'descontoMargem',
            emptyText: 'Desconto + Margem',
            fieldLabel: 'Desconto + Margem',
            labelWidth: 60,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        elTagDescontoMargem.store.load(
            function(){
                elTagDescontoMargem.setDisabled(false);
            }
        );

        var elTagMaximoAlcada = Ext.create('Ext.form.field.Tag',{
            name: 'eltagmaximoalcada',
            itemId: 'eltagmaximoalcada',
            labelAlign: 'top',
            multiSelect: true,
            store: Ext.data.Store({
                fields: [{ name: 'maximoAlcada' },{ name: 'descricao' }],
                proxy: {
                    type: 'ajax',
                    url: BASEURL + '/api/grupodesconto/listarmaximoalcada',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                }
            }),
            width: 230,
            queryParam: 'maximoAlcada',
            queryMode: 'local',
            displayField: 'maximoAlcada',
            valueField: 'maximoAlcada',
            emptyText: 'Máximo Alcada',
            fieldLabel: 'Máximo Alcada',
            labelWidth: 60,
            margin: '1 1 1 8',
            // plugins:'dragdroptag',
            filterPickList: true,
            publishes: 'value',
            disabled:true
        });
        elTagMaximoAlcada.store.load(
            function(){
                elTagMaximoAlcada.setDisabled(false);
            }
        );

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
                        elTagTabPreco,
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
                        elTagGrupoDesconto,
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
                        elTagDescontoMargem,
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
                        elTagMaximoAlcada,
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
