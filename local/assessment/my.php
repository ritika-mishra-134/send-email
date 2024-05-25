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
* @package    local_assessment
* @category   local
* @copyright  2020 Transneuron Techologies Pvt Ltd.
* @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once(__DIR__.'./../../config.php');
require_login();
require_once($CFG->dirroot.'/local/assessment/lib.php');
$sql = "SELECT la.id, la.name, lap.name AS partnername, la.paymentpriceid, la.samlssoid, la.link,
la.visible AS avisible,la.logo, lap.visible AS apvisible
FROM {local_assessment} la
JOIN {local_assessment_partner} lap ON la.assessmentpartner = lap.id
WHERE la.deleted = 0 ANd lap.deleted = 0 AND lap.visible = 1 ANd la.visible = 1";

$assessments = array_values($DB->get_records_sql($sql));
foreach ($assessments as $key => $assessment) {
    if ($assessment->avisible == 0 || $assessment->apvisible == 0) {
        // Assessment | Assessment Partner is not visible.
        $assessment->visible = 0;
    }
    if (!$assessment->logo) {
        $assessment->logo = (new moodle_url('/assets/src/en/stock_assessment.png'))->out();
    } else {
        // Load logo using moodle file_browser,
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($assessment->logo);
        if ($file) {
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
            $assessment->logo = $url->out();
        } else {
            $assessment->logo = (new moodle_url('/assets/src/en/stock_assessment.png'))->out();
        }
    }
    $assessment->introlink = (new moodle_url('/local/assessment/introduction.php', array('id' => $assessment->id)))->out();
    if ($assessment->paymentpriceid) {
        // Check payment status against this assessment.
        $assessmentuserenrol = $DB->get_record(
            'local_assessment_user_enrol',
            array('userid' => $USER->id, 'assessmentid' => $assessment->id)
        );
        if ($assessmentuserenrol) {
            // Payment is done . Thats why user is enrolled.
            $assessment->enrolled = true;
            // Check output of transaction API.
            $transactionapi = $DB->get_record('local_asssessment_paid_api', array('processed' => 1, 'status' => 1, 'userid' => $USER->id, 'assessmentid' => $assessment->id));
            if (!$transactionapi) {
                // User is enrolled by transaction api didnt work.
                $assessment->enrolled = true;
                $assessment->buttonlink = '#';
                $assessment->buttonname = get_string('enrolmentinprogress', 'local_assessment');
            } else {
                // Now user is enrolled in assessment. Check whether to view test or view result.
                if ($assessment->samlssoid) {
                    $array = array('id' => $assessment->samlssoid);
                    if ($assessment->link) {
                        $array['relaystate'] = $assessment->link;
                    }
                    $assessment->buttonlink = (new moodle_url('/local/samlidp/generatesamlresponse.php', $array))->out();
                } else {
                    $assessment->buttonlink = $assessment->link;
                }
                $assessment->link = str_replace("{{hash}}", encode($USER->email), $assessment->link);
                $assessmentstatus = $DB->get_record(
                    'local_assessment_status',
                    array('userid' => $USER->id, 'assessementid' => $assessment->id)
                );
                if (!$assessmentstatus) {
                    $assessment->buttonname = get_string('proceedtotest', 'local_assessment');
                } else {
                    if ($assessmentstatus->status == 0) {
                        $assessment->buttonname = get_string('proceedtotest', 'local_assessment');
                    } else if ($assessmentstatus->status == 1) {
                        $assessment->buttonname = get_string('resumetest', 'local_assessment');
                    } else {
                        $assessment->buttonname = get_string('viewresult', 'local_assessment');
                        $assessmentresult = $DB->get_record(
                            'careerprep_result',
                            array('userid' => $USER->id, 'quizid' => $assessment->id)
                        );
                        if ($assessmentresult->resultfile) {
                            // Moodle file upload.
                            $fs = get_file_storage();
                            $file = $fs->get_file_by_id($assessmentresult->resultfile);
                            if ($file) {
                                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
                                $link = $url->out();
                            } else {
                                // Since no valid pdf.
                                $link = "#";
                                $assessment->buttonname = get_string('waitingforresult', 'local_assessment');
                            }
                        } else if ($assessmentresult->totalscore != null) {
                            $link = (new moodle_url('/local/assessment/score.php', array('id' => $assessment->id)))->out();
                        } else {
                            $link = '#';
                            $assessment->buttonname = get_string('waitingforresult', 'local_assessment');
                        }
                        // Assessment is completed. Give link to result.
                        $assessment->buttonlink = $link;
                    }
                }
            }


        } else {
            // Payment is not done.
            // Find the price info.
            $ccavenuepaymentprice = $DB->get_record('payment_ccavenue_price', array('id' => $assessment->paymentpriceid));


            $assessment->enrolled = false;
            $assessment->amount = $ccavenuepaymentprice->amount;
            $assessment->link = (new moodle_url('/local/assessment/introduction.php', array('id' => $assessment->id)))->out();
        }
    } else {
        // Since no payment user can directly view the stuff.
        if ($assessment->samlssoid) {
            $array = array('id' => $assessment->samlssoid);
            if ($assessment->link) {
                $array['relaystate'] = $assessment->link;
            }
            $assessment->link = (new moodle_url('/local/samlidp/generatesamlresponse.php', $array))->out();
        } else {
            $assessment->link = $assessment->link;
        }
        $assessment->link = str_replace("{{hash}}", encode($USER->email), $assessment->link);
        $assessmentstatus = $DB->get_record(
            'local_assessment_status',
            array('userid' => $USER->id, 'assessementid' => $assessment->id)
        );
        if (!$assessmentstatus) {
            $assessment->buttonname = get_string('proceedtotest', 'local_assessment');
        } else {
            if ($assessmentstatus->status == 0) {
                $assessment->buttonname = get_string('proceedtotest', 'local_assessment');
            } else if ($assessmentstatus->status == 1) {
                $assessment->buttonname = get_string('resumetest', 'local_assessment');
            } else {
                $assessment->buttonname = get_string('viewresult', 'local_assessment');
                $assessmentresult = $DB->get_record(
                    'careerprep_result',
                    array('userid' => $USER->id, 'quizid' => $assessment->id)
                );
                if ($assessmentresult->resultfile) {
                    // Moodle file upload.
                    $fs = get_file_storage();
                    $file = $fs->get_file_by_id($assessmentresult->resultfile);
                    if ($file) {
                        $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
                        $link = $url->out();
                    } else {
                        // Since no valid pdf.
                        $link = "#";
                        $assessment->buttonname = get_string('waitingforresult', 'local_assessment');
                    }
                } else if ($assessmentresult->totalscore != null) {
                    $link = (new moodle_url('/local/assessment/score.php', array('id' => $assessment->id)))->out();
                } else {
                    $link = '#';
                    $assessment->buttonname = get_string('waitingforresult', 'local_assessment');
                }
                // Assessment is completed. Give link to result.
                $assessment->link = $link;
            }
        }
    }
    $assessments[$key] = $assessment;
}
$systemcontext = context_system::instance();
$string = get_string('assessments', 'local_assessment');
$url = new moodle_url('/local/assessment/my.php');
$PAGE->set_url($url);
$PAGE->navbar->add($string, $url);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('custom');
$PAGE->set_heading($string);
$PAGE->set_title($string);
$PAGE->requires->css(new moodle_url('/local/assessment/styles/my.css'));
$hash = [
    'siteurl' => $CFG->wwwroot,
    'assessments' => $assessments,
];
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_assessment/my', $hash);
// echo $PAGE->requires->js_call_amd('local_assessment/my', 'init', ['quizdata' => $quizdata,'sectionname' => $section,'score' => $score,'sec'=> $sectiondata]);
echo $OUTPUT->footer();