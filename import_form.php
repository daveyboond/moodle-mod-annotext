<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class mod_annotext_import_form extends moodleform {

    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $cmid = $this->_customdata['id'];

        $mform->addElement('filepicker', 'file', get_string('filetoimport', 'annotext'));
        $mform->addHelpButton('file', 'filetoimport', 'annotext');
        $submit_string = get_string('submit');
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons(false, $submit_string);
    }
}
