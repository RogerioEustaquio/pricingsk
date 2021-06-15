Ext.define('App.view.basepreco.PluginToggle', {
    extend: 'Ext.Slider',
    xtype : 'togglefield',
    alternateClassName: 'Ext.form.Toggle',
    requires: ['Ext.slider.Widget'],

    config: {
        /**
         * @cfg
         * @inheritdoc
         */
        cls: 'x-toggle-field'
    },

    proxyConfig: {
        /**
         * @cfg {String} minValueCls See {@link Ext.slider.Toggle#minValueCls}
         * @accessor
         */
        minValueCls: 'x-toggle-off',

        /**
         * @cfg {String} maxValueCls  See {@link Ext.slider.Toggle#maxValueCls}
         * @accessor
         */
        maxValueCls: 'x-toggle-on'
    },

    // @private
    applyComponent: function(config) {
        return Ext.factory(config, Ext.slider.Toggle);
    },

    /**
     * Sets the value of the toggle.
     * @param {Number} value **1** for toggled, **0** for untoggled.
     */
    setValue: function(newValue) {
        if (newValue === true) {
            newValue = 2;
        }

        this.getComponent().setValue(newValue);

        return this;
    },

   getValue: function() {
       return (this.getComponent().getValue() == 1) ? 1 : 0;
   },

   /**
    * Toggles the value of this toggle field.
    * @return this
    */
   toggle: function() {
       this.getComponent().toggle();
       return this;
   }
});