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
    print_error('error:invalidid', 'annotext');
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

// Get content of uploaded file (which must be UTF-8)
$result = $form->get_file_content('file');

if (empty($result)) {
    echo $OUTPUT->box_start('generalbox');
    echo $OUTPUT->continue_button('import.php?id='.$id);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// Isolate the HTML body
if (preg_match('|<body.*?>(.*?)</body>|is', $result, $matches)) {
    $bodyhtml = $matches[1];
} else {
    echo $OUTPUT->box_start('generalbox');
    echo "<p>" . get_string('error:nobody', 'annotext') . "</p>";
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button('view.php?id=' . $cm->id);
    echo $OUTPUT->footer();
    exit;
}

// Convert highlighting tags into a more convenient form
$bodyhtml = preg_replace('|<span[^>]*?style=["\']background:\s*(.*?)["\'].*?>(.*?)</span>|is',
    "<tag $1>$2</tag>", $bodyhtml);

// Tidy up p tags
$bodyhtml = preg_replace('|<p.*?>|is', '<p>', $bodyhtml);

// Remove all other span tags
$bodyhtml = preg_replace('|<span.*?>|is', '', $bodyhtml);
$bodyhtml = preg_replace('|</span>|is', '', $bodyhtml);

// Separate the content and categories sections. Abort if categories section
// missing.
if (preg_match('|(^.*)<p>.*?Categories.*?</p>(.*$)|is',
    $bodyhtml, $matches)) {
    
    $contenthtml = $matches[1];
    $cathtml = $matches[2];
} else {
    echo $OUTPUT->box_start('generalbox');
    echo "<p>" . get_string('error:nocategories', 'annotext') . "</p>";
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button('view.php?id=' . $cm->id);
    echo $OUTPUT->footer();
    exit;
}

// Convert list of categories to array
if (!preg_match_all('|<p><tag (.*?)>(.*?)</tag></p>|',
    $cathtml, $categories, PREG_SET_ORDER)) {
    
    echo $OUTPUT->box_start('generalbox');
    echo "<p>" . get_string('error:wrongcategoriesformat', 'annotext') . "</p>";
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button('view.php?id=' . $cm->id);
    echo $OUTPUT->footer();
    exit;
}

// Extract the annotations from the content
if (!preg_match_all('|<tag (.*?)>(.*?)</tag>\s*?\[(.*?)\]|is',
    $contenthtml, $annotations, PREG_SET_ORDER)) {
    
    echo $OUTPUT->box_start('generalbox');
    echo "<p>" . get_string('error:noannotations', 'annotext') . "</p>";
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button('view.php?id=' . $cm->id);
    echo $OUTPUT->footer();
    exit;
}

// Match highlight colours to category indicies, abort if no match
foreach ($annotations as $a) {
    $gotcat = false;
    foreach ($categories as $c) {
        if ($a[1] == $c[1]) {
            $gotcat = true;
            break;
        }
    }
    if (!$gotcat) {
        echo $OUTPUT->box_start('generalbox');
        echo "<p>" . get_string('error:colourmismatch', 'annotext', $a[1]) . "</p>";
        echo $OUTPUT->box_end();
        echo $OUTPUT->continue_button('view.php?id=' . $cm->id);
        echo $OUTPUT->footer();
        exit;
    }
}

// Delete existing annotations and categories linked to the current annotext
$oldcats = $DB->get_records("annotext_categories", array("annotextid"=>$annotext->id));

foreach ($oldcats as $oldcat) {
    $DB->delete_records("annotext_annotations", array("categoryid"=>$oldcat->id));
    $DB->delete_records("annotext_categories", array("id"=>$oldcat->id));
}

// Add new categories to database. Put id numbers of the added records
// back into the category array.
foreach ($categories as &$cat) {
    $newcat = new stdClass();
    $newcat->annotextid = $annotext->id;
    $newcat->title = $cat[2];
    $newcat->colour = $cat[1];
    $cat[3] = $DB->insert_record("annotext_categories", $newcat, true);
}

// Step through annotations, deconstructing the annotation, matching the
// highlighting colour to the categories list to get category id, and adding
// records to annotations table.

foreach ($annotations as $key => &$anno) {
    // Check if there's a pipe in the annotation, and split on it (ignoring tags)
    // if there is. If not, it's a backreference and nothing needs to be added
    // to the database
    if (preg_match('/([^>]*)\s*?\|\s*?([^<]*)/', $anno[3], $annobits)) {
        // If the title is left blank, use the highlighted word as title
        if (trim($annobits[1]) == false) {
            $title = $anno[2];        
        } else {
            $title = $annobits[1];
        }
        $html = $annobits[2];
        
        // Find the category ID for this colour
        foreach ($categories as $c) {
            if ($anno[1] == $c[1]) {
                $catid = $c[3];
                break;
            }
        }
        
        // Create the data object to be added to database
        $newanno = new stdClass();
        $newanno->categoryid = $catid;
        $newanno->title = trim($title);
        $newanno->html = trim($html);
        
        // Add the record and collect the ID
        $anno['id'] = $DB->insert_record("annotext_annotations", $newanno, true);

    } else {
        // Nothing to be added to database, just refer back to the
        // corresponding entry
        if ($backanno = $DB->get_record_select("annotext_annotations",
            'LOWER(title) = "' . strtolower(trim($anno[3])) . '"')) {
            
            $anno['id'] = $backanno->id;
        } else {
            echo $OUTPUT->box_start('generalbox');
            echo "<p>" . get_string('warning:orphanannotation', 'annotext', $anno[0]) . "</p>";
            echo $OUTPUT->box_end();
            unset($annotations[$key]);
        }
    }
}

// Find the highlighting elements in the content, and convert them to
// ‘at_#’ format. NB Trying to reuse $anno here caused problems, presumably
// because it was passed by reference before, hence using $ann
foreach ($annotations as $ann) {
    $pattern = preg_quote($ann[0],'|');
    $replace = '<span id="at_'.$ann['id'].'">'.$ann[2].'</span>';
    $contenthtml = preg_replace("|$pattern|is", $replace, $contenthtml);
}

// Replace the body tags because view.php needs them
$contenthtml = "<body>\n" . $contenthtml . "\n</body>";

$newannotext = new stdClass();
$newannotext->id = $annotext->id;
$newannotext->html = $contenthtml;

if (!$DB->update_record("annotext", $newannotext)) {
    echo $OUTPUT->box_start('generalbox');
    echo "<p>" . get_string('error:updatefail', 'annotext', $newannotext->id) . "</p>";
    echo $OUTPUT->box_end();
    echo $OUTPUT->continue_button('view.php?id=' . $cm->id);
    echo $OUTPUT->footer();
    exit;
}

// Print continue button
echo "<p>Import successful.</p>";
echo $OUTPUT->continue_button('view.php?id=' . $cm->id);

// Finish the page
echo $OUTPUT->footer();
