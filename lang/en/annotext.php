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
$string['filetoimport_help'] = 'Browse for and select the HTML file to import.';
$string['html'] = 'HTML content';
$string['import'] = 'Import from HTML';
$string['importfromhtml'] = 'Import from HTML';
$string['modulename'] = 'Annotated text';
$string['modulenameplural'] = 'Annotated texts';
$string['modulename_help'] = 'Use the annotext module for... | The annotext module allows...';
$string['pluginadministration'] = 'Annotext administration';
$string['pluginname'] = 'Annotext';

$string['error:colourmismatch'] = 'The highlight colour "{$a}" used in the text does not match any of the category colours.';
$string['error:invalidid'] = 'Could not find an annotext instance for this course module.';
$string['error:nobody'] = 'Could not find a <body> element in the imported HTML. Check input file against required format.';
$string['error:noannotations'] = 'No annotations found in text. Check input file against required format.';
$string['error:nocategories'] = 'Could not find categories section. Check input file against required format.';
$string['error:updatefail'] = 'Failed to update annotext record id: {$a}.';
$string['error:wrongcategoriesformat'] = 'Categories are in the wrong format. Check input file against required format.';

$string['warning:orphanannotation'] = 'Warning: There is a backreference to an annotation title that does not exist in the annotation "{$a}". This annotation has been dropped (so the text in square brackets will be output instead).';
