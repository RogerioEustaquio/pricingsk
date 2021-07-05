Ext.define('App.view.Toolbar', {
    extend: 'Ext.toolbar.Toolbar',
    xtype: 'apptoolbar',

    initComponent: function() {
        var me = this;

        var user = {usuarioSistema: null};

        if(USUARIO){
            user = JSON.parse(USUARIO);
        }

        
        Ext.applyIf(me, {
            items: [
                {
                    xtype: 'button',
                    text: 'Base Preço',
                    handler: function(){
                        window.document.location= BASEURL +'/#basepreco';
                    }
                },
                {
                    xtype: 'button',
                    text: 'Produto',
                    handler: function(){
                        window.document.location= BASEURL +'/#produto';
                    }
                },
                {
                    xtype: 'button',
                    text: 'Estoque',
                    handler: function(){
                        window.document.location= BASEURL +'/#estoque';
                    }
                },
                {
                    xtype: 'button',
                    text: 'Config. Marca Empresa',
                    handler: function(){
                        window.document.location= BASEURL +'/#configmarcaempresa';
                    }
                },
                {
                    xtype: 'button',
                    text: 'Grupo Desconto',
                    handler: function(){
                        window.document.location= BASEURL +'/#grupodesconto';
                    }
                }
            ]
        });

        me.callParent(arguments);
    }

});
