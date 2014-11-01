<?php

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->dirroot/course/lib.php");
require_once('import_form.php');

$id = required_param('id', PARAM_INT);    // Course Module ID

$url = new moodle_url('/mod/annotext/import.php', array('id'=>$id));
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('annotext', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $annotext = $DB->get_record("annotext", array("id"=>$cm->instance))) {
    print_error('invalidid', 'annotext');
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/annotext:import', $context);

$strimport = get_string('importfromhtml', 'annotext');

$PAGE->navbar->add($strimport);
$PAGE->set_title($annotext->name);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($strimport);

$form = new mod_annotext_import_form();

if ( !$data = $form->get_data() ) {
    echo $OUTPUT->box_start('generalbox');
    // display upload form
    $data = new stdClass();
    $data->id = $id;
    $form->set_data($data);
    $form->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// Get content of uploaded file (CHARACTER ENCODING?)
$result = $form->get_file_content('file');

if (empty($result)) {
    echo $OUTPUT->box_start('generalbox');
    echo $OUTPUT->continue_button('import.php?id='.$id);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
}

// Strip out unwanted tags from the content
if (preg_match('|<body.*?>(.*?)</body>|is', $result, $matches)) {
    $bodyhtml = $matches[1];
} else {
    echo $OUTPUT->box_start('generalbox');
    echo "<p>Could not find a body.</p>";
    echo $OUTPUT->box_end();
    die();
}

$bodyhtml = preg_replace('|<p.*?>|is', '<p>', $bodyhtml);

// Separate the content, annotations and categories sections. Abort if any section
// missing.
if (preg_match('|(^.*)<p>.*?Annotations.*?</p>(.*)<p>.*?Categories.*?</p>(.*$)|is',
    $bodyhtml, $matches)) {
    
    $contenthtml = $matches[1];
    $annothtml = $matches[2];
    $cathtml = $matches[3];
} else {
    echo $OUTPUT->box_start('generalbox');
    echo "<p>Could not find annotation and/or categories section.</p>";
    echo $OUTPUT->box_end();
    die();
}

echo "<p>Content:</p>$contenthtml<p>Annotations:</p>$annothtml<p>Categories:</p>$cathtml";

// Convert lists of annotations and categories to arrays
if (!preg_match_all('|(\d+):\s*(.*?)<|', $annothtml, $annotations, PREG_SET_ORDER)) {
    echo $OUTPUT->box_start('generalbox');
    echo "<p>Annotations are in the wrong format.</p>";
    echo $OUTPUT->box_end();
    die();    
}

foreach($annotations as $a){echo "<p>".$a[1]." - ".$a[2]."</p>";}

if (!preg_match_all('|<p><span.*?style=["\']background:(.*?)["\'].*?>(.*?)</span></p>|',
    $cathtml, $categories, PREG_SET_ORDER)) {
    
    echo $OUTPUT->box_start('generalbox');
    echo "<p>Categories are in the wrong format.</p>";
    echo $OUTPUT->box_end();
    die();    
}

foreach($categories as $c){echo "<p>".$c[1]." - ".$c[2]."</p>";}

// Verify that annotations and categories match content.

// Add annotations and categories to database. Put id numbers of the added records
// back into the arrays.

// Step through content, picking up highlight span elements. Match the index number
// to the annotation list to get annotation id. Match the highlighting colour to
// the categories list to get category id.

// Convert the span element into the ‘at_#’ format. Add the category id into the
// corresponding field in the annotations table.

// Update the annotext table with the converted markup.



// Finish the page
echo $OUTPUT->footer();
