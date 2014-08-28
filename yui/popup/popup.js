YUI.add('moodle-mod_annotext-popup', function(Y) {

    M.mod_annotext = M.mod_annotext || {}

    M.mod_annotext.popup = {

        panel : new M.core.dialogue({
            draggable    : true,
            headerContent: M.util.get_string('pluginname','mod_annotext'),
            bodyContent  : '<p>Hello world</p>',
            centered     : true,
            width        : '640px',
            modal        : true,
            visible      : false,
        }),

        init: function() {

            var panel = this.panel;
            Y.one('#almastatus').on('click', panel.show, panel);

        }
    };
}, '@VERSION@', {
    requires: ['moodle-core-notification-dialogue', 'node', 'io', 'model-list', 'datatable', 'datatype-date-format', 'datatype-date-parse']
});
