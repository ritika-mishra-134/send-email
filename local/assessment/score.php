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
 * Display course(s) to CollegeAdmin(CA) created by its parent CollegeSuperAdmin(CSA)
 *
 * It contain list of course(s) which is created by the CollegeSuperAdmin(CSA)
 * The CSA will be the user which contain different college(s) control by CA.
 * The CA can use the course(s) created by its CSA for their usage.
 *
 * @package    local_course
 * @category   local
 * @copyright  2020 Transneuron Techologies Pvt Ltd.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'./../../config.php');
require_login();
$assessmentid = required_param('id', PARAM_INT);
$systemcontext = context_system::instance();
$string = get_string('score', 'local_assessment');
$url = new moodle_url('/local/assessment/score.php');
$PAGE->set_url($url);
$PAGE->navbar->add($string, $url);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('custom');
$PAGE->set_heading($string);
$PAGE->set_title($string);
function capitalizeFirstLetter($str) {
    return ucfirst($str);
}
$quizrecords = $DB->get_record(
    'careerprep_result',
    array('userid' => $USER->id, 'quizid' => $assessmentid)
);
$sectiondata = array_values($DB->get_records(
    'careerprep_section_result',
    array('userid' => $USER->id, 'assessmentid' => $assessmentid)
));
$quizdata = [$quizrecords->correct, $quizrecords->wrong, $quizrecords->noattempt ];
$section = array_column($sectiondata, 'name');
$section = array_map("capitalizeFirstLetter", $section);
$score = array_column($sectiondata, 'score');
echo $OUTPUT->header();
$hash = [
    'siteurl' => $CFG->wwwroot,
    'sectiondata' => $sectiondata
];
echo $OUTPUT->render_from_template('local_assessment/score', $hash);
echo $PAGE->requires->js_call_amd('local_assessment/score', 'init', ['quizdata' => $quizdata,'sectionname' => $section,'score' => $score,'sec'=> $sectiondata]);
echo $OUTPUT->footer();