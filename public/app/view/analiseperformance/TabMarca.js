Ext.define('App.view.analiseperformance.TabMarca', {
    extend: 'Ext.panel.Panel',
    xtype: 'tabmarca',
    itemId: 'tabmarca',
    closable: false,
    requires: [
        'App.view.analiseperformance.GridMarcaOverview'
    ],
    title: 'Marca',
    layout: 'card',
    border: false,
    tbar: {
        border: false,
        items:[
            {
                xtype: 'button',    
                text: 'Overview',
                handler: function(){
                
                    this.up('panel').setActiveItem(0);
                }
            }
        ]
    },
    items:[
        {
            xtype: 'container',
            layout:'border',
            itemId: 'containerbolha',
            items:[
                {
                    xtype:'filtrofilial',
                    region: 'west'
                },
                {
                    xtype: 'panel',
                    region: 'center',
                    layout: 'fit',
                    itemId: 'panelbolha',
                    tbar:[
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-filter',
                            handler: function() {
                                var filtro =  this.up('panel').up('container').down('#filtrofilial');
                                var hidden = (filtro.hidden) ? false : true;
                                filtro.setHidden(hidden);
                            }
                        },
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-search',
                            margin: '0 0 0 2',
                            tooltip: 'Consultar',
                            handler: function() {

                                var filtro =  this.up('panel').up('container').down('#gridmarcaoverview');
                                // var empresas = filtro.down('#elEmpresa').getValue();
                                // var data = filtro.down('#data').getRawValue();
                                // var marcas = filtro.down('#elmarca').getValue();
                                // var grupomarcas = filtro.down('#elgrupomarca').getValue();

                                // if(grupomarcas.length > 0){
                                //     marcas = marcas.concat(grupomarcas);
                                // }
                                
                                var params = {
                                    // idEmpresas: Ext.encode(empresas),
                                    // data : data,
                                    // idMarcas: Ext.encode(marcas)
                                };
                
                                var gridStore = this.up('panel').down('grid').getStore();
                
                                gridStore.getProxy().setExtraParams(params);
                                gridStore.load();
                
                            }
                        },
                        '->',
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-cog',
                            handler: function() {

                              
                            }
                        }
                    ],
                    items:[
                        {
                            xtype: 'gridmarcaoverview'
                        }
                    ]
                    
                }
            ]
        },
        {
            xtype: 'container',
            layout:'border',
            itemId: 'containerbolha',
            items:[
                {
                    xtype:'filtrofilial',
                    region: 'west'
                },
                {
                    xtype: 'panel',
                    region: 'center',
                    layout: 'fit',
                    itemId: 'panelbolha',
                    tbar:[
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-filter',
                            handler: function() {
                                var filtro =  this.up('panel').up('container').down('#filtrofilial');
                                var hidden = (filtro.hidden) ? false : true;
                                filtro.setHidden(hidden);
                            }
                        },
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-search',
                            margin: '0 0 0 2',
                            tooltip: 'Consultar',
                            handler: function() {

                                var filtro =  this.up('panel').up('container').down('#gridfilialoverview');
                                // var empresas = filtro.down('#elEmpresa').getValue();
                                // var data = filtro.down('#data').getRawValue();
                                // var marcas = filtro.down('#elmarca').getValue();
                                // var grupomarcas = filtro.down('#elgrupomarca').getValue();

                                // if(grupomarcas.length > 0){
                                //     marcas = marcas.concat(grupomarcas);
                                // }
                                
                                var params = {
                                    // idEmpresas: Ext.encode(empresas),
                                    // data : data,
                                    // idMarcas: Ext.encode(marcas)
                                };
                
                                var gridStore = this.up('panel').down('grid').getStore();
                
                                gridStore.getProxy().setExtraParams(params);
                                gridStore.load();
                
                            }
                        },
                        '->',
                        {
                            xtype: 'button',
                            iconCls: 'fa fa-cog',
                            handler: function() {

                              
                            }
                        }
                    ],
                    items:[
                        {
                            xtype: 'gridmarcaoverview'
                        }
                    ]
                    
                }
            ]
        }
    ]
})
