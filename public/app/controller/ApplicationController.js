Ext.define('App.controller.ApplicationController', {
    extend: 'Ext.app.Controller',

    requires: [
        'App.view.Toolbar',
        'App.view.home.HomePanel',
        'App.view.basepreco.BaseprecoPanel',
        'App.view.produto.ProdutoPanel',
        'App.view.estoque.EstoquePanel',
        'App.view.configmarcaempresa.ConfigmarcaempresaPanel',
        'App.view.grupodesconto.GrupodescontoPanel',
        'App.view.analisegrafica.AnalisegraficaPanel',
    ],

    control: {

    },

    routes: {
        'home': { action: 'homeAction' },
        'basepreco': { action: 'baseprecoAction' },
        'produto': { action: 'produtoAction' },
        'estoque': { action: 'estoqueAction' },
        'configmarcaempresa': { action: 'configmarcaempresaAction' },
        'grupodesconto': { action: 'grupodescontoAction' },
        'analisegrafica': { action: 'analisegraficaAction' }
    },

    controllerEvent: function(){
        var me = this;

        return {
            'apptoolbar #home': {
                click: function(btn) {
                    me.redirectTo('home')
                }
            },

            // 'apptoolbar button[action=recarregar]': {
            'apptoolbar #cotacaodolarpomercial': {
                click: function(btn) {
                    me.redirectTo('cotacaodolarpomercial')
                }
            }
        }
    },

    init: function() {
        var me = this;

        me.control(me.controllerEvent());
        me.configViewport();
    },

    goActionMasterTab: function(route, closable){
        this.addMasterTab(route, route + 'panel', closable);
    },

    homeAction: function(){
        // this.goActionMasterTab('home', true)
        this.goActionMasterTab('home', true)
    },

    baseprecoAction: function(){
        this.goActionMasterTab('basepreco', true)
    },

    produtoAction: function(){
        // this.goActionMasterTab('home', true)
        this.goActionMasterTab('produto', true)
    },

    estoqueAction: function(){
        // this.goActionMasterTab('home', true)
        this.goActionMasterTab('estoque', true)
    },

    configmarcaempresaAction: function(){
        // this.goActionMasterTab('home', true)
        this.goActionMasterTab('configmarcaempresa', true)
    },

    grupodescontoAction: function(){
        // this.goActionMasterTab('home', true)
        this.goActionMasterTab('grupodesconto', true)
    },

    analisegraficaAction: function(){
        // this.goActionMasterTab('home', true)
        this.goActionMasterTab('analisegrafica', true)
    },

    configViewport: function(){
        var me = this,
            viewport = me.getViewport();
        
        if(viewport){
            viewport.add({
                itemId: 'applicationtabs',
                region: 'center',
                xtype: 'tabpanel',
                layout: 'fit'
            });
        }
    },
    
    addMasterTab: function(route, xtype, closable){
        var me = this,
            viewport = me.getViewport(),
            viewportTabs = viewport.down('#applicationtabs'),
            tab = viewportTabs.down(xtype);

        if(!tab){
            tab = viewportTabs.add({
                closable: closable,
                xtype: xtype,
                route: route, 
                listeners: {
                    show: function(aTab){
                        me.redirectTo(aTab.route)
                    },
                    destroy: function(aTab){
                        if(aTab.xtype === 'homepanel')
                        me.redirectTo('#')
                    }
                }
            });
        };
        
        viewportTabs.setActiveItem(tab);
    },

    getViewport: function(){
        return App.getApplication().getMainView();
    },

    getCurrentRoute: function(){
        return App.getApplication().getRouter().getRoute(this.getCurrentRouteName());
    }
    
});
