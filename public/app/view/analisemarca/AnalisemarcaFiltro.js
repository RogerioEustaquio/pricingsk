Ext.define('App.view.analisemarca.AnalisemarcaFiltro',{
    extend: 'Ext.panel.Panel',
    xtype: 'analisemarcafiltro',
    itemId: 'analisemarcafiltro',
    title: 'Filtro',
    region: 'west',
    width: 160,
    hidden: true,
    scrollable: true,
    layout: 'vbox',
    requires:[
    ],

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {

            items : [
                {
                    xtype: 'checkboxfield',
                    name: 'grafico',
                    itemId: 'grafico',
                    checked: true,
                    boxLabel: 'Gráfico',
                    labelWidth: '80%',
                    labelAlign: 'right',
                    // margin: '2 2 2 2',
                    handler: function(){

                        if(!this.value){
                            me.up('panel').down('#analisemarcachart').setHidden(true);

                        }else{
                            
                            me.up('panel').down('#analisemarcachart').setHidden(false);
                        }
                    }
                }
            ]
        });

        me.callParent(arguments);

    },

    onBtnFiltros: function(btn){
        var me = this.up('toolbar');

        console.log(me.up('container').down('#analisemarcafiltro'));

        if(me.up('container').down('#analisemarcafiltro').hidden){
            me.up('container').down('#analisemarcafiltro').setHidden(false);
        }else{
            me.up('container').down('#analisemarcafiltro').setHidden(true);
        }
        
    }

});
