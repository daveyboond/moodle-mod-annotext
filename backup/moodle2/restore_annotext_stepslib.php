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
 * @package    mod_annotext
 * @subpackage backup-moodle2
 * @copyright  2014 Steve Bond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one annotext activity
 */
class restore_annotext_activity_structure_step extends restore_activity_structure_step {
 
    protected function define_structure() {
 
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');
 
        $paths[] = new restore_path_element('annotext', '/activity/annotext');
        $paths[] = new restore_path_element('annotext_category', '/activity/annotext/categories/category');
        $paths[] = new restore_path_element('annotext_annotation', '/activity/annotext/categories/category/annotations/annotation');
 
        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }
 
    protected function process_annotext($data) {
        global $DB;
 
        $data = (object)$data;
        $data->course = $this->get_courseid();
 
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
 
        // insert the annotext record
        $newitemid = $DB->insert_record('annotext', $data);
        $this->id = $newitemid;
        
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
 
    protected function process_annotext_category($data) {
        global $DB;
 
        $data = (object)$data;
        $oldid = $data->id;
 
        $data->annotextid = $this->get_new_parentid('annotext');
 
        $newitemid = $DB->insert_record('annotext_categories', $data);
        $this->set_mapping('annotext_category', $oldid, $newitemid);
    }
 
    protected function process_annotext_annotation($data) {
        global $DB;
 
        $data = (object)$data;
        $oldid = $data->id;
 
        $data->categoryid = $this->get_new_parentid('annotext_category');
 
        $newitemid = $DB->insert_record('annotext_annotations', $data);
        $this->set_mapping('annotext_annotation', $oldid, $newitemid);
    }
 
    protected function after_execute() {
        // Need to update all the annotation references in the HTML field
        // Get HTML from database
        global $DB;
        $annotext = $DB->get_record('annotext', array('id' => $this->id));
        
        // Find each instance of old annotation reference id="at_###"
        while (preg_match('/id=\"at_([0-9]+)/', $annotext->html, $matches)) {
            // Swap the reference for a placeholder plus new id
            $newid = $this->get_mappingid('annotext_annotation', $matches[1]);
            $pattern = '/id="at_' . $matches[1] . '/';
            $replacement = 'id="xxxt_' . $newid;
            $annotext->html = preg_replace($pattern, $replacement, $annotext->html);
        }
        
        // Switch all the placeholders back to original format
        $annotext->html = preg_replace('/xxxt_/', 'at_', $annotext->html);
        
        // Restore HTML to database
        $DB->update_record("annotext", $annotext);
        
    }
}

