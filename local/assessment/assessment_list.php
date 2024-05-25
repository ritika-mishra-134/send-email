<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 *
 * @package    local
 * @subpackage assessment
 * @copyright  2022 Transneuron Techologies Pvt Ltd.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'./../../config.php');
require_login();
if(!is_siteadmin()) {
    redirect($CFG->wwwroot.'/dashboard');
} else {
    $hash = [
        'siteurl' => $CFG->wwwroot,
    ];
    $systemcontext = context_system::instance();
    $PAGE->set_context($systemcontext);
    $PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/assessment/styles/createbutton.min.css'));
    $PAGE->set_pagelayout('custom');
    $string = get_string('assessmentlist', 'local_assessment');
    $url = new moodle_url($CFG->wwwroot.'/local/assessment/assessment_list.php');
    $PAGE->navbar->add($string, $url);
    $PAGE->set_title($string);
    $PAGE->set_url($url);
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_assessment/assessment_list', $hash);
    $PAGE->requires->js_call_amd('local_assessment/assessment_list', 'init');
    echo $OUTPUT->footer();
}
