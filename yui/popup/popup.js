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

            /* Get all category checkboxes: elements with class "catbox" */
            var catboxes = Y.all('.catbox');

            /* Loop through the checkboxes, adding click event handler to each */
            catboxes.each(function (catbox) {
                catbox.on('change', function(e) {
                    /* Event handler adds/removes highlighting classes and click handlers.
                     * The 'annotation' class is only for showing the pointer cursor */
                    var catanns = Y.all('.cat' + e.target.get('id'));
                    
                    if (e.target.get('checked')) {
                        catanns.each(function (catann) {
                            catann.addClass('annotation');
                            catann.addClass('cat' + e.target.get('id') + 'show');
                            catann.on('click', function(evt) {
                                /* Event handler finds corresponding hidden content and shows it in popup */
                                var content = Y.one('#'+ evt.target.get('id') + '_content');
                                Y.one('#par').setHTML(content.getHTML());
                                panel.show();
                            });
                        });
                    } else {
                        catanns.each(function (catann) {
                            catann.removeClass('annotation');
                            catann.removeClass('cat' + e.target.get('id') + 'show');
                            catann.detach();
                        });
                    }
                });
            });

        }
        
    };
}, '@VERSION@', {
    requires: ['moodle-core-notification-dialogue', 'node', 'io', 'model-list', 'datatable', 'datatype-date-format', 'datatype-date-parse']
});
