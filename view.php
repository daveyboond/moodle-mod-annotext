<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of annotext
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_annotext
 * @copyright  2014 Steve Bond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace annotext with the name of your module and remove this line)

require_once("../../config.php");
require_once("lib.php");

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // annotext instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('annotext', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $annotext  = $DB->get_record('annotext', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $annotext  = $DB->get_record('annotext', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $annotext->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('annotext', $annotext->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

add_to_log($course->id, 'annotext', 'view', "view.php?id={$cm->id}", $annotext->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/annotext/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($annotext->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('annotext-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($annotext->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('annotext', $annotext, $cm->id), 'generalbox mod_introbox', 'annotextintro');
}

/* Render the HTML from the module record, after substituting for custom markup */

// Get the raw HTML and extract tags
$htmlout = $annotext->html;

while (preg_match('/id="at_(\d+)"/', $htmlout, $matches)) {
    // Look up in the annotations table the id extracted
    $annotation  = $DB->get_record('annotext_annotations', array('id' => $matches[1]), '*');
    // Look up the category to get the highlighting colour
    $category = $DB->get_record('annotext_categories', array('id' => $annotation->categoryid), '*');
    $colourrgb = '#' . $category->colour;
    // Replace the id tag with a style tag to highlight the text
    $htmlout = preg_replace('/id="at_(\d+)"/', 'style="background-color:'.$colourrgb.';"', $htmlout, 1);
    // (Later) add JS to the span element to create popup - use <span class="helptooltip">?
}

$PAGE->requires->yui_module('moodle-mod_annotext-popup', 'M.mod_annotext.popup.init');
$htmlout .= '<div id="almastatus"><span class="alma_active">Popup</span></div>';

// Output the processed HTML (THIS MAY NEED AN $OUTPUT CALL INSTEAD OF echo)
echo $htmlout;

// Finish the page
echo $OUTPUT->footer();
