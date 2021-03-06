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
        'App.view.analisemarca.AnalisemarcaPanel',
        'App.view.acompanhamentovenda.AcompanhaVendaPanel',
        'App.view.faixamargem.FaixaMargemPanel',
        'App.view.analiseperformance.AnalisePerformancePanel',
        'App.view.dispersaovenda.DispersaoVendaPanel',
        'App.view.bolhasvendas.BolhasvendasPanel'
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
        'analisegrafica': { action: 'analisegraficaAction' },
        'analisemarca': { action: 'analisemarcaAction' },
        'acompanhavenda': { action: 'acompanhavendaAction' },
        'faixamargem': { action: 'faixamargemAction' },
        'analiseperformance': { action: 'analiseperformanceAction' },
        'dispersaovenda': { action: 'dispersaovendaAction' },
        'bolhasvendas': { action: 'bolhasvendasAction' }
    },

    controllerEvent: function(){
        var me = this;

        return {
            'apptoolbar #home': {
                click: function(btn) {
                    me.redirectTo('home')
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
        this.goActionMasterTab('home', true)
    },

    baseprecoAction: function(){
        this.goActionMasterTab('basepreco', true)
    },

    produtoAction: function(){
        this.goActionMasterTab('produto', true)
    },

    estoqueAction: function(){
        this.goActionMasterTab('estoque', true)
    },

    configmarcaempresaAction: function(){
        this.goActionMasterTab('configmarcaempresa', true)
    },

    grupodescontoAction: function(){
        this.goActionMasterTab('grupodesconto', true)
    },

    analisegraficaAction: function(){
        this.goActionMasterTab('analisegrafica', true)
    },

    analisemarcaAction: function(){
        this.goActionMasterTab('analisemarca', true)
    },

    acompanhavendaAction: function(){
        this.goActionMasterTab('acompanhavenda', true)
    },

    faixamargemAction: function(){
        this.goActionMasterTab('faixamargem', true)
    },

    analiseperformanceAction: function(){
        this.goActionMasterTab('analiseperformance', true)
    },

    dispersaovendaAction: function(){
        this.goActionMasterTab('dispersaovenda', true)
    },

    bolhasvendasAction: function(){
        this.goActionMasterTab('bolhasvendas', true)
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

        var acessos = ['EVERTON','ROGERIOADM','RENATO','PEDROOPE','MAYKONRS','WELISONOPE','EVERTONOPE','JOSECARLOS','ESTEFANIA','DEBORA','LUIZSANTOS','RAIMUNDOMA','NEYMA','FABIOSP'];

        if(acessos.indexOf(USUARIO.usuarioSistema) === -1){

            if(USUARIO !== '')

                alert(`Acesso negado para o usu??rio ${USUARIO.usuarioSistema}`)
    
                me.redirectTo('home')

                return;

        }

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
