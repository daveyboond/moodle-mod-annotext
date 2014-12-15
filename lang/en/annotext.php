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
 * English strings for annotext
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_annotext
 * @copyright  2014 Steve Bond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['annotext:addinstance'] = 'Add a new annotated text';
$string['annotext:manage'] = 'Manage annotated text activity';
$string['annotext:view'] = 'View annotated text activity';
$string['contentsection'] = 'Content';
$string['filetoimport'] = 'File to import';
$string['filetoimport_help'] = 'Browse for and select the HTML file to import. The HTML file must be exported from a Word 2010 file in the correct format, exported using Windows Word 2010, using "Save As Filtered HTML", with text encoding set to UTF-8.';
$string['htmlcontent'] = 'HTML content';
$string['htmlcontent_help'] = 'This field should contain correctly-formatted markup that the Annotext module can understand. If you are creating a new Annotext, just leave this blank and use the HTML import feature to generate the markup. Otherwise you can use this field to make minor edits to the text after import.';
$string['import'] = 'Import from HTML';
$string['importfromhtml'] = 'Import from HTML';
$string['importsuccess'] = 'Import successful.';
$string['modulename'] = 'Annotated text';
$string['modulenameplural'] = 'Annotated texts';
$string['modulename_help'] = 'The Annotext module allows a teacher to upload a text containing annotations (in a specified format), and converts that text into an interactive format whereby a student can turn on and off different categories of annotation. The annotated words and phrases are highlighted in different colours according to category, and the student can click on any highlighted phrase to open a pop-up that displays the annotation for that word/phrase.';
$string['pluginadministration'] = 'Annotext administration';
$string['pluginname'] = 'Annotext';

$string['error:colourmismatch'] = 'The highlight colour "{$a}" used in the text does not match any of the category colours.';
$string['error:invalidid'] = 'Could not find an annotext instance for this course module.';
$string['error:nobody'] = 'Could not find a &lt;body&gt; element in the imported HTML. Check input file against required format.';
$string['error:noannotations'] = 'No annotations found in text. Check input file against required format.';
$string['error:nocategories'] = 'Could not find categories section. Check input file against required format.';
$string['error:updatefail'] = 'Failed to update annotext record id: {$a}.';
$string['error:wrongcategoriesformat'] = 'Categories are in the wrong format. Check input file against required format.';

$string['warning:orphanannotation'] = 'Warning: There is a backreference to an annotation title that does not exist in the annotation "{$a}". This annotation has been dropped (so the text in square brackets will be output instead).';
