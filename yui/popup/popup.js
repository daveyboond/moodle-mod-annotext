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

            /* Get all elements with class "catbox" */
            var catboxes = Y.all('.catbox');

            /* Loop through the elements, adding click event handler to each */
            catboxes.each(function (catbox) {
                catbox.on('change', function(e) {
                    /* Event handler adds/removes cat#show classes */
                    var catanns = Y.all('.cat' + e.target.get('id'));
                    
                    if (e.target.get('checked')) {
                        catanns.each(function (catann) {
                            catann.addClass('annotation');
                            catann.addClass('cat' + e.target.get('id') + 'show');
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

            /* Get all elements with class "annotation". This may seem clumsy - why not
             * add the event handlers in the loop above? It's because this script also
             * has to deal with the initial state, before anything's been clicked */
            var annotations = Y.all('.annotation');

            /* Loop through the elements, adding click event handler to each */
            annotations.each(function (annot) {
                annot.detach();
                annot.on('click', function(e) {
                    /* Event handler finds corresponding hidden content and shows it in popup */
                    var content = Y.one('#'+ e.target.get('id') + '_content');
                    Y.one('#par').setHTML(content.getHTML());
                    panel.show();
                });
            });
        }
        
    };
}, '@VERSION@', {
    requires: ['moodle-core-notification-dialogue', 'node', 'io', 'model-list', 'datatable', 'datatype-date-format', 'datatype-date-parse']
});
