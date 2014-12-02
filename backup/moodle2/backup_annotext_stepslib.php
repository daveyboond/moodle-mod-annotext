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
 * Define all the backup steps that will be used by the backup_annotext_activity_task
 */

class backup_annotext_activity_structure_step extends backup_activity_structure_step {
 
    protected function define_structure() {
 
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
 
        // Define each element separated
        $annotext = new backup_nested_element('annotext', array('id'), array(
            'name', 'intro', 'introformat', 'html', 'timecreated', 'timemodified'));
 
        $categories = new backup_nested_element('categories');
 
        $category = new backup_nested_element('category', array('id'), array(
            'title', 'colour'));
 
        $annotations = new backup_nested_element('annotations');
 
        $annotation = new backup_nested_element('annotation', array('id'), array(
            'title', 'html'));
 
        // Build the tree
        $annotext->add_child($categories);
        $categories->add_child($category);
        $category->add_child($annotations);
        $annotations->add_child($annotation);
 
        // Define sources
        $annotext->set_source_table('annotext', array('id' => backup::VAR_ACTIVITYID));
        $category->set_source_table('annotext_categories', array('annotextid' => backup::VAR_PARENTID));
        $annotation->set_source_table('annotext_annotations', array('categoryid' => backup::VAR_PARENTID));
        
        // No id nor file annotations needed (i.e. ids that reference other data objects, like user id)
 
        // Return the root element (annotext), wrapped into standard activity structure
        return $this->prepare_activity_structure($annotext);
 
    }
}