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

// Show the module intro
if ($annotext->intro) {
    echo $OUTPUT->box(format_module_intro('annotext', $annotext, $cm->id),
        'generalbox mod_introbox', 'annotextintro');
}

// Include JS stuff needed to display popups
$PAGE->requires->yui_module('moodle-mod_annotext-popup', 'M.mod_annotext.popup.init');
$PAGE->requires->string_for_js('modulename', 'mod_annotext');

// Find out what categories exist for this annotext
$categories = $DB->get_records('annotext_categories', array('annotextid' => $annotext->id));

// Create category checkboxes for toggling highlighting
$categoryhtml = "";
foreach ($categories as $cat) {
    $categoryhtml .= '<div class="colourtab" style="border-color: #' . $cat->colour
        . '" /><input id="' . $cat->id
        . '" type="checkbox">' . $cat->title . '</input></div><br />' . "\n";
}

// Get the raw HTML and extract tags
$htmlout = $annotext->html;

// This loop looks for untouched <span> elements, adds highlighting to them,
// and adds popup contents for each. The extra ">" in the initial pattern
// avoids modification of already-modified spans.
preg_match_all('/(id="at_(\d+)")>/', $htmlout, $matches, PREG_SET_ORDER);

for ($a=0; $a<count($matches); $a++) {
    // Look up in the annotations table the id extracted
    $annotation  = $DB->get_record('annotext_annotations', array('id' => $matches[$a][2]), '*');
    // Add a hidden div at the end, containing the popup text for this target word
    $htmlout = preg_replace('|</body>|is',
        '<div id="at_'.$matches[$a][2].'_content" style="display:none"><h3>'
            .$annotation->title.'</h3>'.$annotation->html."</div>\n</body>", $htmlout, 1);
    
    // Look up the category to get the highlighting colour
    $category = $DB->get_record('annotext_categories', array('id' => $annotation->categoryid), '*');
    $colourrgb = '#' . $category->colour;
    // Replace the id tag with a style tag to highlight the text
    $htmlout = preg_replace('/'.$matches[$a][0].'/', $matches[$a][1] . ' class="annotation cat'
        . $annotation->categoryid . '" style="background-color:' . $colourrgb.';">', $htmlout, 1);
}
// Output the processed HTML
echo $categoryhtml;
echo $htmlout;

// Finish the page
echo $OUTPUT->footer();
