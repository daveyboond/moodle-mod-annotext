YUI.add('moodle-mod_annotext-popup', function(Y) {

    M.mod_annotext = M.mod_annotext || {};

    M.mod_annotext.popup = {

        /* Create an object to contain the popup */
        panel : new M.core.dialogue({
            draggable    : true,
            headerContent: M.util.get_string('modulename','mod_annotext'),
            bodyContent  : '<p id="par">'+'Undefined'+'</p>',
            centered     : true,
            width        : '640px',
            modal        : true,
            visible      : false,
        }),

        init: function(param1) {

            var panel = this.panel;

            /* Get all elements with class "annotation" */
            var annotations = Y.all('.annotation');

            /* Loop through the elements, adding click event handler to each */
            annotations.each(function (annot) {
                annot.on('click', function(e) {
                    /* Event handler finds corresponding hidden content and shows it in popup */
                    var content = Y.one('#'+ e.target.get('id') + '_content');
                    Y.one('#par').setHTML(content.getHTML());
                    panel.show();
                })
            })
        }
        
    };
}, '@VERSION@', {
    requires: ['moodle-core-notification-dialogue', 'node', 'io', 'model-list', 'datatable', 'datatype-date-format', 'datatype-date-parse']
});
