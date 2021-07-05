Ext.define('App.view.grupodesconto.Toolbar',{
    extend: 'Ext.Toolbar',
    xtype: 'grupodescontotoolbar',
    itemId: 'grupodescontotoolbar',
    region: 'north',
    requires:[
        // 'App.view.fii.ContainerHighCharts'
    ],
    // indicadoresAdd: null,

    initComponent: function() {
        var me = this;

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
                btnFiltro,
                btnConsultar,
                '->',
                {
                    xtype: 'button',
                    text: 'Export',
                    iconCls: 'fa fa-table',
                    handler: function(){

                        var win = open('','forml');
                        var link = BASEURL + '/api/grupodesconto/gerarexcel';
                        var dados = this.dado;

                        var input = "<input type='hidden' name='dados' value='"+dados+"'></input>";
                        input +=  " <input type='hidden' name='nome' value='exportgrupodesconto'></input>";
                        input +=  " <input type='hidden' name='total' value='"+this.total+"'></input>";

                        var html = "<html><body><form id='forml' method='POST' action='"+link+"'> " +input+" </form></body></html>"

                        win.document.write(html);
                        win.document.close();
                        win.document.getElementById('forml').submit();

                    }
                }
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        if(me.up('container').down('#grupodescontofiltro').hidden){
            me.up('container').down('#grupodescontofiltro').setHidden(false);
        }else{
            me.up('container').down('#grupodescontofiltro').setHidden(true);
        }
        
    },

    onBtnConsultar: function(btn){

        var me = this.up('toolbar');

        var idEmpresas      = me.up('container').down('#grupodescontofiltro').down('#elEmp').getValue();
        var tabelaPreco     = me.up('container').down('#grupodescontofiltro').down('#eltagtabpreco').getValue();
        var grupoDesconto   = me.up('container').down('#grupodescontofiltro').down('#eltaggrupodesconto').getValue();
        var descontoMargem  = me.up('container').down('#grupodescontofiltro').down('#eltagdescontomargem').getValue();
        var maximoAlcada    = me.up('container').down('#grupodescontofiltro').down('#eltagmaximoalcada').getValue();

        var grid = me.up('container').down('#panelcenter').down('grid');
        var params = {
            idEmpresas: Ext.encode(idEmpresas),
            tabelaPreco: Ext.encode(tabelaPreco),
            grupoDesconto: Ext.encode(grupoDesconto),
            descontoMargem: Ext.encode(descontoMargem),
            maximoAlcada:  Ext.encode(maximoAlcada)
        };
    
        grid.getStore().getProxy().setExtraParams(params);
        grid.getStore().loadPage(1);

        

    }

});
