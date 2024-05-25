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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/user/editlib.php');
class uploaduser_form extends moodleform {
    public static function definition () {
        $mform = $this->_form;
        $mform->addElement('header', 'settingsheader', get_string('upload'));
        $url = new moodle_url('example.csv');
        $link = html_writer::link($url, 'example.csv');
        $mform->addElement('static', 'examplecsv', get_string('examplecsv', 'tool_uploaduser'), $link);
        $mform->addHelpButton('examplecsv', 'examplecsv', 'tool_uploaduser');
        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');
        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $mform->setDefault('encoding', 'UTF-8');
        $choices = ['10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '100000' => 100000];
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'tool_uploaduser'), $choices);
        $mform->setType('previewrows', PARAM_INT);
        $this->add_action_buttons(false, get_string('uploadusers', 'tool_uploaduser'));
    }
}

class uploaduser_form2 extends moodleform {
    public static function definition () {
        global $CFG, $USER;
        $mform = $this->_form;
        $data = $this->_customdata['data'];
        $columns = $this->_customdata['columns'];
        $mform->addElement('hidden', 'iid');
        $mform->setType('iid', PARAM_INT);
        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_INT);
        $this->add_action_buttons(true, get_string('mailschedule', 'local_emailsender'));
        $this->set_data($data);
    }
    public static function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $columns = $this->_customdata['columns'];
        if (!in_array('email', $columns) && empty($data['email'])) {
            $errors['email'] = get_string('requiredtemplate', 'local_emailsender');
        }
        if (!in_array('firstname', $columns) && empty($data['firstname'])) {
            $errors['firstname'] = get_string('requiredtemplate', 'local_emailsender');
        }
        if (!in_array('lastname', $columns) && empty($data['lastname'])) {
            $errors['lastname'] = get_string('requiredtemplate', 'local_emailsender');
        }
        return $errors;
    }
}

class random_userform extends moodleform {
    public static function definition () {
        global $DB;
        $mform = $this->_form;
        $users = $DB->get_records('user');
        $userarray = [];
        foreach ($users as $user) {
            $userarray[$user->id] = $user->firstname." ".$user->lastname;
        }
        $options = ['multiple' => true];
        $mform->addElement('autocomplete', 'users', get_string('users'), $userarray, $options);
        $mform->addElement('textarea', 'mailbody',
        get_string("mailtext", "local_emailsender"), 'wrap="virtual" rows="20" cols="50"');
        $mform->setDefault('mailbody', get_string('sampletext', 'local_emailsender'));
        $this->add_action_buttons(true, get_string('mailnow', 'local_emailsender'));
    }
}
