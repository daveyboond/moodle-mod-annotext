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
 * Outputs annotext data in a printable format
 *
 * @package    mod_annotext
 * @copyright  2014 Steve Bond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->dirroot/course/lib.php");

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
$PAGE->set_url(new moodle_url('/mod/annotext/print.php', array('id' => $cm->id)));
$PAGE->set_pagelayout('base'); // No blocks
$PAGE->navbar->add(get_string('print', 'annotext')); // Why doesn't this work?
$PAGE->set_title(format_string($annotext->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();

// Show the module intro
if ($annotext->intro) {
    echo $OUTPUT->box(format_module_intro('annotext', $annotext, $cm->id),
        'generalbox mod_introbox', 'annotextintro');
}

// Find out what categories exist for this annotext
$categories = $DB->get_records('annotext_categories', array('annotextid' => $annotext->id));

// Create category checkboxes for toggling highlighting, and a local stylesheet
// for category colours. Checkboxes are unchecked at first.
$categoryhtml = "";
$styles = "<style>\n";

foreach ($categories as $cat) {
    $categoryhtml .= '<span class="cat' . $cat->id . 'show">' . $cat->title . '</span><br />' . "\n";
    $styles .= '.cat' . $cat->id . 'show { border: solid 1px; background-color: ' . $cat->colour . ";}\n";
}

$styles .= "</style>\n";

// Get the raw HTML and extract tags
$htmlout = $annotext->html;

// This loop looks for untouched <span> elements, adds highlighting to them,
// and adds popup contents for each. The extra ">" in the initial pattern
// avoids modification of already-modified spans.
preg_match_all('/(id="at_(\d+)")>/', $htmlout, $matches, PREG_SET_ORDER);

for ($a=0; $a<count($matches); $a++) {
    // Get the offset on the first pass
    if ($a == 0) {
        $offset = $matches[$a][2] - 1;
    }
    
    // Get the index of this annotation
    $index = $matches[$a][2] - $offset;
    
    // Look up in the annotations table the id extracted
    $annotation  = $DB->get_record('annotext_annotations', array('id' => $matches[$a][2]), '*');
    
    // Add a div at the end, containing the annotation
    $htmlout = preg_replace('|</body>|is', '<div><h4>' . $index . ': ' . $annotation->title
         . '</h4>'. $annotation->html . "</div>\n</body>", $htmlout, 1);
    
    // Replace the id attribute with a class attribute to add highlighting and add a
    // superscript index at the end of the span
    $htmlout = preg_replace('|'.$matches[$a][0].'([^<]*?</span>)|', $matches[$a][1] . ' class="cat'
        . $annotation->categoryid . 'show">$1<sup>' . $index . '</sup>', $htmlout, 1);
    
}
// Output the processed HTML
echo "<h4>Categories</h4>\n";
echo $styles;
echo $categoryhtml;
echo "<h4>Text</h4>\n";
echo $htmlout;

// Finish the page
echo $OUTPUT->footer();
