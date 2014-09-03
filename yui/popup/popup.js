YUI.add('moodle-mod_annotext-popup', function(Y) {

    M.mod_annotext = M.mod_annotext || {}

    M.mod_annotext.popup = {

        panel : new M.core.dialogue({
            draggable    : true,
            headerContent: M.util.get_string('pluginname','mod_annotext'),
            bodyContent  : '<p id="par">'+'Undefined'+'</p>',
            centered     : true,
            width        : '640px',
            modal        : true,
            visible      : false,
        }),

        init: function(param1) {

            var panel = this.panel;
            
            Y.one('.annotation').on('click', function(e) {
                var content = Y.one('#'+ e.target.get('id') + '_content');
                Y.one('#par').setHTML(content.getHTML());
                panel.show();
           });
        }
        
    };
}, '@VERSION@', {
    requires: ['moodle-core-notification-dialogue', 'node', 'io', 'model-list', 'datatable', 'datatype-date-format', 'datatype-date-parse']
});
