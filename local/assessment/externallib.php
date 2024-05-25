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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
* This file contain webservices required to interact with assessment
* module.
*
* @package    local_assessment
* @category   local
* @copyright  2023 Transneuron Technologies
* @license
*/

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/lib/externallib.php');
require_once($CFG->dirroot.'/local/assessment/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot.'/lib/moodlelib.php');
/**
 *
 */
class local_assessment_external extends external_api {
    /**
    * This function is used to check if user is valid assessment partner
    *
    * @return object
    */
    public static function basicassessmentauthentication() {
        global $DB, $USER;

        return $response;
    }
    /**
    * @return external_function_parameters
    */
    public static function returndatatable() {
        return array(
            'columns' => new external_multiple_structure(
                    new external_single_structure(array(
                        'data' => new external_value(PARAM_TEXT, 'data'),
                        'name' => new external_value(PARAM_ALPHA, 'name'),
                        'orderable' => new external_value(PARAM_BOOL, 'orderable'),
                        'search' => new external_single_structure(array(
                                'regex' => new external_value(PARAM_ALPHANUMEXT, 'regex'),
                                'value' => new external_value(PARAM_ALPHANUM, 'value'),
                            )
                        ),
                        'searchable' => new external_value(PARAM_BOOL, 'searchable')
                    )
                )
            ),
            'order' => new external_multiple_structure(
                new external_single_structure(array(
                    'column' => new external_value(PARAM_INT, ' '),
                    'dir' => new external_value(PARAM_ALPHA, '')
                )
            )),
            'draw' => new external_value(PARAM_INT, ''),
            'length' => new external_value(PARAM_INT, ''),
            'search' => new external_single_structure(array(
                    'regex' => new external_value(PARAM_ALPHANUMEXT, ''),
                    'value' => new external_value(PARAM_TEXT, '')
                )
            ),
            'start' => new external_value(PARAM_INT, '')
        );
    }
    public static function assessment_status_parameters() {
        return new external_function_parameters(array(
            'emailaddress' => new external_value(PARAM_EMAIL, ''),
            'status' => new external_value(PARAM_TEXT, "Registered', 'PartiallyCompleted', 'Completed'"),
            'assessmentid' => new external_value(PARAM_TEXT, 'Internal Assessment ID of provider')
        ));
    }
    /**
    * This webservice method is used to update the status of assessment against the user.
    * This doesnt represent the score albet the progress against the assessment by a user.
    * This is used on local/assessment/my.php
    *
    * @param string $email Email address of user
    */
    public static function assessment_status($email, $status, $internalassessmentid) {
        global $DB, $USER;
        $response = new stdClass();
        $assessmentpartner = $DB->get_record(
            'local_assessment_partner',
            array('userid' => $USER->id)
        );
        if (!$assessmentpartner) {
            $response->status = false;
            $response->message = 'You are not valid Assessment Partner.';
            return $response;
        }
        $validstatus = ['Registered', 'PartiallyCompleted', 'Completed'];
        $assessmentuser = $DB->get_record('user', array('email' => $email, 'deleted' => 0, 'confirmed' => 1), 'id');
        $assessment = $DB->get_record('local_assessment', array(
            'partnerassessmentid' => $internalassessmentid,
            'assessmentpartner' => $assessmentpartner->id)
        );
        if (!$assessment) {
            $response->status = false;
            $response->message = 'Invalid Assessment Id/ Assessment Partner.';
        } else if (!$assessmentuser) {
            $response->status = false;
            $response->message = 'User Doesnt exists.';
        } else if (!in_array($status, $validstatus)) {
            $response->status = false;
            $response->message = 'Assessment status is not valid.';
        } else {
            $assessmentstatus = $DB->get_record('local_assessment_status', array(
                'userid' => $assessmentuser->id,
                'assessementid' => $assessment->id
            ));
            try {
                $status = array_search($status, $validstatus);
                if ($assessmentstatus) {
                    $assessmentstatus->status = $status;
                    $assessmentstatus->timemodified = time();
                    $DB->update_record('local_assessment_status', $assessmentstatus);
                } else {
                    $assessmentstatus = new stdClass();
                    $assessmentstatus->userid = $assessmentuser->id;
                    $assessmentstatus->assessementid = $assessment->id;
                    $assessmentstatus->status = $status;
                    $assessmentstatus->timecreated = time();
                    $assessmentstatus->timemodified = time();
                    $id = $DB->insert_record('local_assessment_status', $assessmentstatus);
                }
                $response->status = true;
                $response->message = 'Status updated successfully.';
            } catch (Exception $e) {
                $response->status = false;
                $response->message = 'Status update failed. Please try later.';
            }
        }
        return $response;
    }
    /**
    * @return external_single_structure
    */
    public static function assessment_status_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, ''),
            'message' => new external_value(PARAM_TEXT, '')
        ));
    }
    /**
    * @return external_function_parameters
    */
    public static function result_user_mapping_parameters() {
        return new external_function_parameters(array(
            'itemid' => new external_value(PARAM_INT, 'itemid against uploaded users'),
            'emailaddress' => new external_value(PARAM_EMAIL, 'Email address of new users'),
            'assessmentid' => new external_value(PARAM_TEXT, 'Internal Assessment ID of provider')
        ));
    }
    public static function result_user_mapping($itemid, $email, $internalassessmentid) {
        global $USER, $DB;
        $response = new stdClass();
        $assessmentpartner = $DB->get_record('local_assessment_partner', array('userid' => $USER->id));
        if (!$assessmentpartner) {
            $response->status = false;
            $response->message = 'You are not valid Assessment Partner.';
            return $response;
        }
        $assessmentuser = $DB->get_record('user', array('email' => $email, 'deleted' => 0, 'confirmed' => 1), 'id');
        $assessment = $DB->get_record('local_assessment', array(
            'partnerassessmentid' => $internalassessmentid,
            'assessmentpartner' => $assessmentpartner->id)
        );
        if (!$assessment) {
            $response->status = false;
            $response->message = 'Invalid Assessment Id/ Assessment Partner.';
        } else if (!$assessmentuser) {
            $response->status = false;
            $response->message = 'User Doesnt exists.';
        } else {
            $fs = get_file_storage();
            $context = context_user::instance($USER->id);
            if ($fs->is_area_empty($context->id, 'user', 'draft', $itemid)) {
                $response->status = false;
                $response->message = "Invalid Result Item Id.";
            } else {
                $files = $fs->get_area_files($context->id, 'user', 'draft', $itemid);
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    if ($filename != '.') {
                        $mimetype = $file->get_mimetype();
                        $validextensions = ['pdf'];
                        $mimetypes = ['application/pdf'];
                        $extension = array_reverse(explode(".", $filename))[0];
                        if (!in_array($mimetype, $mimetypes) || !in_array($extension, $validextensions)) {
                            $response->status = false;
                            $response->message = "Invalid Uploaded File. File should be pdf";
                            $file->delete();
                            break;
                        }
                        $assessmentresult = $DB->get_record('careerprep_result', array(
                            'userid' => $assessmentuser->id,
                            'quizid' => $assessment->id
                        ));
                        if (!$assessmentresult) {
                            $assessmentresult = new stdClass();
                            $assessmentresult->userid = $assessmentuser->id;
                            $assessmentresult->quizid = $assessment->id;
                            $assessmentresult->timecreated = time();
                            $assessmentresult->quiztitle = $assessment->name;
                            $assessmentresult->id = $DB->insert_record('careerprep_result', $assessmentresult);
                        } else {
                            // Delete existing file if available.
                            if ($assessmentresult->resultfile) {
                                $existingfile = $fs->get_file_by_id($assessmentresult->resultfile);
                                if ($existingfile) {
                                    $existingfile->delete();
                                }
                            }
                        }
                        // Create new assessment result file for assessmentuser.
                        $assessmentusercontext = context_user::instance($assessmentuser->id);
                        $fileinfo = array(
                            'contextid' => $assessmentusercontext->id, // ID of context.
                            'component' => 'local_assessment',     // Usually = table name.
                            'filearea' => 'careerprep_result',     // Usually = table name.
                            'itemid' => $assessmentresult->id, // Usually = ID of row in table.
                            'filepath' => '/',           // Any path beginning and ending in.
                            'filename' => $filename
                        ); // Any filename.
                        $assessmentresultfile = $fs->create_file_from_storedfile($fileinfo, $file->get_id());
                        $fileid = $assessmentresultfile->get_id();
                        if ($fileid) {
                            $assessmentresult->resultfile = $fileid;
                        } else {
                            $assessmentresult->resultfile = null;
                        }
                        $DB->update_record('careerprep_result', $assessmentresult);
                        // Now delete this draft file area.
                        $removedraftfiles = $fs->get_area_files($context->id, 'user', 'draft', $itemid);
                        foreach ($removedraftfiles as $removedraftfile) {
                            $removedraftfilename = $removedraftfile->get_filename();
                            if ($removedraftfilename != '.') {
                                $removedraftfile->delete();
                            }
                        }
                        // Update assessment_status.
                        $assessmentstatus = $DB->get_record('local_assessment_status', array(
                            'userid' => $assessmentuser->id,
                            'assessementid' => $assessment->id
                        ));
                        if ($assessmentstatus) {
                            $assessmentstatus->status = 2;
                            $assessmentstatus->timemodified = time();
                            $DB->update_record('local_assessment_status', $assessmentstatus);
                        } else {
                            $assessmentstatus = new stdClass();
                            $assessmentstatus->userid = $assessmentuser->id;
                            $assessmentstatus->assessementid = $assessment->id;
                            $assessmentstatus->status = 2;
                            $assessmentstatus->timecreated = time();
                            $DB->insert_record('local_assessment_status', $assessmentstatus);
                        }
                        $response->status = true;
                        $response->message = "Result uploaded successfully";
                        break;
                    }
                }
            }
        }
        return $response;
    }
    public static function result_user_mapping_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, ''),
            'message' => new external_value(PARAM_TEXT, '')
        ));
    }
    public static function results_data_parameters() {
        return new external_function_parameters(array(
            'hash' => new external_value(PARAM_RAW, 'Hash', VALUE_DEFAULT, ''),
            'email' => new external_value(PARAM_EMAIL, 'Email address of the user'),
            'score' => new external_value(PARAM_FLOAT, 'Score'),
            'totalquestions' => new external_value(PARAM_INT, 'Total questions'),
            'correct' => new external_value(PARAM_INT, 'Correct'),
            'wrong' => new external_value(PARAM_INT, 'Wrong'),
            'noattempt' => new external_value(PARAM_INT, 'Noattempt'),
            'quizduration' => new external_value(PARAM_INT, 'Quiz duration'),
            'timespent' => new external_value(PARAM_INT, 'Time spent'),
            'quiztitle' => new external_value(PARAM_TEXT, 'Quiz Title'),
            'sections' => new external_multiple_structure(new external_single_structure(array(
                'id' => new external_value(PARAM_TEXT, 'Section Id'),
                'name' => new external_value(PARAM_TEXT, 'Section Name'),
                'score' => new external_value(PARAM_FLOAT, 'Score'),
                'totalquestions' => new external_value(PARAM_INT, 'Questions'),
                'correct' => new external_value(PARAM_INT, 'Correct'),
                'wrong' => new external_value(PARAM_INT, 'Wrong'),
                'noattempt' => new external_value(PARAM_INT, 'No attempt')
            ))),
            'assessmentid' => new external_value(PARAM_TEXT, 'assessmentid'),
            'id' => new external_value(PARAM_TEXT, 'assessmentid')
        ));
    }
    public static function results_data() {
        global $DB, $USER;

        $response = new stdClass();
        $arguments = func_get_args();
        $hash = $arguments[0];
        $email = $arguments[1];
        $totalscore = $arguments[2];
        $totalquestions = $arguments[3];
        $correct = $arguments[4];
        $wrong = $arguments[5];
        $noattempt = $arguments[6];
        $quizduration = $arguments[7];
        $timespent = $arguments[8];
        $quiztitle = $arguments[9];
        $sections = $arguments[10];
        $internalassessmentid = $arguments[11];

        $assessmentpartner = $DB->get_record('local_assessment_partner', array('userid' => $USER->id));
        if (!$assessmentpartner) {
            $response->status = false;
            $response->message = 'You are not valid Assessment Partner.';
            return $response;
        }
        if (!$email && $hash != '') {
            try {
                $decodevalue = decode($hash);
            } catch (Exception $e) {
                $response->status = false;
                $response->message = 'Invalid hash';
            }
            if (isset($decodevalue->email)) {
                $email = $decodevalue->email;
            } else {
                $response->status = false;
                $response->message = 'User Doesnt exists.';
                return $response;
            }
        }
        $assessmentuser = $DB->get_record('user', array('email' => $email, 'deleted' => 0, 'confirmed' => 1), 'id');
        $assessment = $DB->get_record('local_assessment', array(
            'partnerassessmentid' => $internalassessmentid,
            'assessmentpartner' => $assessmentpartner->id)
        );
        if (!$assessment) {
            $response->status = false;
            $response->message = 'Invalid Assessment Id/ Assessment Partner.';
        } else if (!$assessmentuser) {
            $response->status = false;
            $response->message = 'User Doesnt exists.';
        } else {
            // Now check if careerprep_result record already exists.
            $assessmentresult = $DB->get_record('careerprep_result', array('userid' => $assessmentuser->id, 'quizid' => $assessment->id));
            $assessmentresult->userid = $assessmentuser->id;
            $assessmentresult->quizid = $assessment->id;
            $assessmentresult->totalscore = $totalscore;
            $assessmentresult->totalquestions = $totalquestions;
            $assessmentresult->correct = $correct;
            $assessmentresult->wrong = $wrong;
            $assessmentresult->noattempt = $noattempt;
            $assessmentresult->quizduration = $quizduration;
            $assessmentresult->timespent = $timespent;
            $assessmentresult->quiztitle = $quiztitle;
            if (isset($assessmentresult->id)) {
                $assessmentresult->timemodified = time();
                $DB->update_record('careerprep_result', $assessmentresult);
            } else {
                $assessmentresult->timecreated = time();
                $DB->insert_record('careerprep_result', $assessmentresult);
            }
            foreach ($sections as $section) {
                $sectionresult = $DB->get_record('careerprep_section_result', array(
                    'userid' => $assessmentuser->id,
                    'assessmentid' => $assessment->id,
                    'sectioned' => $section['id']
                ));
                $sectionresult->userid = $assessmentuser->id;
                $sectionresult->assessmentid = $assessment->id;
                $sectionresult->sectioned = $section['id'];
                $sectionresult->score = $section['score'];
                $sectionresult->questions = $section['totalquestions'];
                $sectionresult->correct = $section['correct'];
                $sectionresult->wrong = $section['wrong'];
                $sectionresult->noattempt = $section['noattempt'];
                $sectionresult->name = $section['name'];
                if (isset($sectionresult->id)) {
                    $DB->update_record('careerprep_section_result', $sectionresult);
                } else {
                    $DB->insert_record('careerprep_section_result', $sectionresult);
                }
            }
            // Update assessment_status.
            $assessmentstatus = $DB->get_record('local_assessment_status', array(
                'userid' => $assessmentuser->id,
                'assessementid' => $assessment->id
            ));
            if ($assessmentstatus) {
                $assessmentstatus->status = 2;
                $assessmentstatus->timemodified = time();
                $DB->update_record('local_assessment_status', $assessmentstatus);
            } else {
                $assessmentstatus = new stdClass();
                $assessmentstatus->userid = $assessmentuser->id;
                $assessmentstatus->assessementid = $assessment->id;
                $assessmentstatus->status = 2;
                $assessmentstatus->timecreated = time();
                $DB->insert_record('local_assessment_status', $assessmentstatus);
            }
            $response->status = true;
            $response->message = 'Assessment Updated Successfully';
        }
        return $response;
    }
    public static function results_data_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'status of api'),
            'message' => new external_value(PARAM_TEXT, 'text message'),
        ));
    }
    public static function assessment_creation_parameters() {
        return new external_function_parameters(
            array(
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'description' => new external_value(PARAM_TEXT, 'Description'),
                'sso' => new external_value(PARAM_TEXT, 'SSO Configuration', VALUE_OPTIONAL),
                'payment' => new external_value(PARAM_TEXT, 'Payment', VALUE_OPTIONAL),
                'subpayment' => new external_value(PARAM_TEXT, 'Sub payment', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_EMAIL, 'Email'), 
                'apiendpoint' => new external_value(PARAM_URL, 'API Endpoint', VALUE_OPTIONAL), 
                'apitoken' => new external_value(PARAM_TEXT, 'Api token', VALUE_OPTIONAL),
        ));

    }
    public static function assessment_creation() {
        $arguments = func_get_args();        
        global $DB, $USER;    
        $assessment = new stdClass();
        $response = new stdClass();
        $user = new stdClass();  
        $plainpassword = generate_password(12);
        $user->firstname = $arguments[0];   
        $user->lastname = get_string('lastname', 'local_assessment');
        $user->email = $arguments[5];
        $user->username = strtolower($arguments[5]);  
        $user->password = hash_internal_user_password($plainpassword);
        try {
            $userid = user_create_user($user, false, true);
        } catch (Exception $e) {    
            if ($DB->record_exists('user', array('email' => $arguments[5], 'deleted' => 0))) {
                $response->status = false;
                $response->message = get_string('emailexist', 'local_assessment'); 
                return $response;
            }    
        }    
        $assessment->name = $arguments[0];
        $assessment->description = $arguments[1];
        $assessment->samlssoid = $arguments[2] ? $arguments[2] : "";
        $assessment->parentpayment = $arguments[3] ? $arguments[3] : "";
        $assessment->subaccpayment = $arguments[4] ? $arguments[4] : "";
        $assessment->apiendpoint = $arguments[6] ? $arguments[6] : "";
        $assessment->apitoken = $arguments[7] ? $arguments[7] : "";    
        $assessment->userid = $userid;
        $assessment->createdby = $USER->id;
        $assessment->timecreated = time();     
        try {   
            $assessmentid = $DB->insert_record('local_assessment_partner', $assessment);    
            $response->status = true;
            $response->message = get_string('createdsuccessfully', 'local_assessment'); 
        } catch(Exception $e){
            $response->status = false;
            $response->message = get_string('notinsert', 'local_assessment'); 
        }
        session_start();    
        $_SESSION['applicationid'] = $assessmentid;
        $_SESSION['applicationuserid'] = $userid;
        $_SESSION['username'] = $arguments[5];
        $_SESSION['password'] = $plainpassword;        
        return $response;

    }
    public static function assessment_creation_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'status of api'),
            'message' => new external_value(PARAM_TEXT, 'text message'),
        ));
    }
    public static function assessment_partner_delete_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Id'),
            )
        );
    }
    public static function assessment_partner_delete($id) {    
        global $DB;
        $response = new stdClass();
        $userid = $DB->get_record('local_assessment_partner', ['id' => $id], 'userid');    
        $user = new stdClass();
        $assessment = new stdClass();        
        $user->id = $userid->userid;
        $user->deleted = 1;
        $user->timemodified = time();    
        $assessment->id = $id;
        $assessment->deleted = 1;
        $assessment->timemodified = time();
        try {
            $DB->update_record('user', $user);    
            $DB->update_record('local_assessment_partner', $assessment);
            $response->status = true;
            $response->message = get_string('updatesuccessfull', 'local_assessment');    
        } catch(Exception $e) {    
            $response->status = false;
            $response->message = get_string('notupdate', 'local_assessment');
        }
        return $response;
    }
    public static function assessment_partner_delete_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'status of api'),
            'message' => new external_value(PARAM_TEXT, 'text message'),
        ));
    }  
    public static function assessmentpartner_edit_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Id'),    
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'description' => new external_value(PARAM_TEXT, 'Description'),
                'sso' => new external_value(PARAM_TEXT, 'SSO Configuration', VALUE_OPTIONAL),
                'payment' => new external_value(PARAM_TEXT, 'Payment', VALUE_OPTIONAL),
                'subpayment' => new external_value(PARAM_TEXT, 'Sub payment', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_EMAIL, 'Email'), 
                'apiendpoint' => new external_value(PARAM_TEXT, 'API Endpoint', VALUE_OPTIONAL), 
                'apitoken' => new external_value(PARAM_TEXT, 'Api token', VALUE_OPTIONAL),
                'userid' => new external_value(PARAM_INT, 'Userid')
        ));
    } 
    public static function assessmentpartner_edit() {
        global $DB,$USER;
        $args = func_get_args();
        $user = new stdClass();
        $record = new stdClass();
        $response = new stdClass();
        $user->id = $args[0];
        $user->name = $args[1];
        $user->description = $args[2];
        $user->samlssoid = $args[3];
        $user->parentpayment = $args[4];
        $user->subaccpayment = $args[5];
        $user->apiendpoint = $args[7];
        $user->apitoken = $args[8];
        $record->id = $args[9];
        $record->email = $args[6];
        $record->timemodified = time();
        try {            
            $DB->update_record('local_assessment_partner', $user);
            $DB->update_record('user', $record);
            $response->status = true;
            $response->message = get_string('updatesuccessfull', 'local_assessment'); 
        } catch(Exception $e) {
            $response->status = false;
            $response->message = get_string('notupdate', 'local_assessment');
        }
        return $response;   
    }
    public static function assessmentpartner_edit_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'status of api'),
            'message' => new external_value(PARAM_TEXT, 'text message'),
        ));
    }
    public static function createassessment_parameters() {
        return new external_function_parameters(
            array(
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'description' => new external_value(PARAM_TEXT, 'Description'),
                'assessmentpartner' => new external_value(PARAM_INT, 'Assessment Partner'),
                'assessmentid' => new external_value(PARAM_TEXT, 'Assessment Id'),
                'sso' => new external_value(PARAM_TEXT, 'SSO Configuration', VALUE_OPTIONAL),         
                'link' => new external_value(PARAM_TEXT, 'Link', VALUE_OPTIONAL), 
                'merchantprice' => new external_value(PARAM_TEXT, 'Assessment Price', VALUE_OPTIONAL), 
                'assessmentpaid' => new external_value(PARAM_TEXT, 'Assessment Paid status', VALUE_OPTIONAL),
                'vendorprice' => new external_value(PARAM_TEXT, 'Assessment Price', VALUE_OPTIONAL), 


        ));
    }
    public static function createassessment() {
        global $DB, $USER;
        $args = func_get_args();
        $assessment = new stdClass();
        $response = new stdClass();
        $values = new stdClass();
        $assessment->name = $args[0];
        $assessment->description = $args[1];
        $assessment->partnerassessmentid = $args[3];
        $assessment->assessmentpartner = $args[2];
        $assessment->samlssoid = $args[4];
        $assessment->link = $args[5];
        $assessment->createdby = $USER->id;
        $assessment->timecreated = time(); 
        $payment = $DB->get_record('local_assessment_partner', array('id' => $args[2]));
        $values->type = get_string('asst', 'local_assessment'); 
        $values->usermodified = $USER->id; 
        $values->timecreated = time();
        if ($args[7] == "1") {
            if ($payment->parentpayment) {
                $splitapi = $DB->get_record('payment_ccavenue', ['paymentid' => $payment->parentpayment], 'splitapienabled');
                if ($splitapi->splitapienabled == 1) {
                    if ($payment->subaccpayment) {
                        $subacc = $DB->get_record('payment_ccavenue_subaccount', ['id ' => $payment->subaccpayment], 'subaccountid');
                        $values->amount = $args[6]+$args[8];
                        $assessmentprice = number_format($assessmentprice, 2, '.', '');
                        $vendorprice = number_format($vendorprice, 2, '.', '');
                        $splitData = [
                            'split_tdr_charge_type' => 'M',
                            'merComm' => $args[6],
                            'split_data_list' => [
                                [
                                    'splitAmount' => $args[8],
                                    'subAccId' => $subacc->subaccountid,
                                ]
                            ],
                        ];
                        $values->splitjsondata = json_encode($splitData);
                        try {
                            $paymentid = $DB->insert_record('payment_ccavenue_price', $values);
                        } catch(Exception $e) {    
                            print_object($e);die();
                        }
                    } else {
                        $values->amount = $args[6];
                        $values->splitjsondata = '{}';
                        try {
                            $paymentid = $DB->insert_record('payment_ccavenue_price', $values);
                        } catch(Exception $e) {    
                            print_object($e);die();
                        }
                    }
                } else {
                    $values->amount = $args[6];
                    $values->splitjsondata = '';
                    try {
                        $paymentid = $DB->insert_record('payment_ccavenue_price', $values);
                    } catch(Exception $e) {    
                        print_object($e);die();
                    }
                }
            } 
        } else {
            $paymentid = "";
        }
        $assessment->paymentpriceid = $paymentid;
        $nameexist = $DB->get_record('local_assessment', array('assessmentpartner' => $args[2], 'name' => $args[0]));
        $recordexist = $DB->get_record('local_assessment', array('assessmentpartner' => $args[2], 'partnerassessmentid' => $args[3])); 
        if ($nameexist) {
            $response->status = false;
            $response->message = get_string('namexist', 'local_assessment') ;  
            return $response;
        }
        if ($recordexist) {
            $assessment->id = $recordexist->id;
            $assessment->timemodified = time();
            $DB->update_record('local_assessment', $assessment);
            session_start();    
            $_SESSION['applicationid'] = $recordexist->id;
            $_SESSION['applicationuserid'] = $USER->id;
            $response->status = true;
            $response->message = get_string('updatesuccessfull', 'local_assessment'); ;   
        } else {
            try {
                $assessmentid = $DB->insert_record('local_assessment', $assessment);
                session_start();    
                $_SESSION['applicationid'] = $assessmentid;
                $_SESSION['applicationuserid'] = $USER->id;
                $response->status = true;
                $response->message = get_string('createdsuccessfully', 'local_assessment'); 
            } catch(Exception $e) {
                $response->status = false;
                $response->message = get_string('notinsert', 'local_assessment');
            }
        }    
        return $response;
    } 
    public static function createassessment_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'status of api'),
            'message' => new external_value(PARAM_TEXT, 'text message'),
        ));
    }
    public function assessment_delete_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Id'),
            )
        );
    }
    public static function assessment_delete($id) {
        global $DB;
        $response = new stdClass();
        $assessment =  new stdClass();
        $assessment->id = $id;
        $assessment->deleted = 1;
        $assessment->timemodified = time();
        $paymentid = $DB->get_record('local_assessment', array('id' => $id), 'paymentpriceid');
        $DB->delete_records('payment_ccavenue_price', array('id' => $paymentid->paymentpriceid ));
        if ($DB->update_record('local_assessment', $assessment)) {
            $response->status = true;
            $response->message = get_string('deletedsuccessfully', 'local_assessment');
        }
        return $response;
    }
    public static function assessment_delete_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'status of api'),
            'message' => new external_value(PARAM_RAW, 'text message'),
        ));
    }
    public static function assessmentedit_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Id'),    
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'description' => new external_value(PARAM_RAW, 'Description'),
                'sso' => new external_value(PARAM_TEXT, 'SSO Configuration', VALUE_OPTIONAL),
                'assessmentpartner' => new external_value(PARAM_TEXT, 'Assessment Partner'),
                'assessmentid' => new external_value(PARAM_TEXT, 'Assessment Id'),
                'link' => new external_value(PARAM_TEXT, 'Link', VALUE_OPTIONAL), 
                'merchantprice' => new external_value(PARAM_TEXT, 'Assessment Price', VALUE_OPTIONAL), 
                'paymentid' => new external_value(PARAM_TEXT, 'Payment id', VALUE_OPTIONAL),
                'amountpaid' => new external_value(PARAM_TEXT, 'Assessment Paid status', VALUE_OPTIONAL),
                'vendorprice' => new external_value(PARAM_TEXT, 'Assessment Price', VALUE_OPTIONAL),
        ));

    }
    public static function assessmentedit() {
        $args = func_get_args();
        global $DB,$USER;    
        $assessment = new stdClass();
        $response = new stdClass();
        $paymentvalues = new stdClass();
        $assessmentprice = $args[7];
        $vendorprice = $args[10];
        $paymentid = $args[8];
        $paymentstatus = $args[9];
        $assessment->id = $args[0];
        $assessment->name = $args[1];
        $assessment->description = $args[2];
        $assessment->samlssoid = $args[3];
        $assessment->assessmentpartner = $args[4];
        $assessment->partnerassessmentid = $args[5];
        $assessment->link = $args[6];
        $payment = $DB->get_record('local_assessment_partner', array('id' => $args[4]), 'parentpayment,subaccpayment');  
        $paymentvalues->type = get_string('asst', 'local_assessment');;
        $paymentvalues->usermodified = $USER->id;        
        if ($paymentid) {
            $paymentvalues->id = $paymentid;
            if ($paymentstatus == 2) {
                $DB->delete_records('payment_ccavenue_price', ['id' => $paymentid]);
                $assessment->paymentpriceid = $payment_id;
            } else {
                if ($payment->parentpayment) {
                    $splitapi = $DB->get_record('payment_ccavenue', ['paymentid' => $payment->parentpayment], 'splitapienabled');
                    if ($splitapi->splitapienabled == 1) {
                        if ($payment->subaccpayment) {
                            $subacc = $DB->get_record('payment_ccavenue_subaccount', ['id ' => $payment->subaccpayment], 'subaccountid');
                            $paymentvalues->amount = $assessmentprice + $vendorprice;
                            $splitData = [
                                'split_tdr_charge_type' => 'M',
                                'merComm' => $assessmentprice,
                                'split_data_list' => [
                                    [
                                        'splitAmount' => $vendorprice,
                                        'subAccId' => $subacc->subaccountid,
                                    ]
                                ],
                            ];
                            $paymentvalues->splitjsondata = json_encode($splitData);
                            $paymentvalues->timemodified = time();
                            $DB->update_record('payment_ccavenue_price', $paymentvalues);
                        } else {
                            $paymentvalues->amount = $assessmentprice;
                            $paymentvalues->splitjsondata = '{}';
                            try {
                                $paymentid = $DB->update_record('payment_ccavenue_price', $paymentvalues);
                            } catch(Exception $e) {    
                                print_object($e);die();
                            }
                        }
                    } else {
                        $paymentvalues->amount = $assessmentprice;
                        $paymentvalues->splitjsondata = '';
                        try {
                            $paymentid = $DB->update_record('payment_ccavenue_price', $paymentvalues);
                        } catch(Exception $e) {    
                            print_object($e);die();
                        }
                    }
                } 
            }
        } else {
            if ($payment->parentpayment) {
                $splitapi = $DB->get_record('payment_ccavenue', ['paymentid' => $payment->parentpayment], 'splitapienabled');
                if ($splitapi->splitapienabled == 1) {
                    if ($payment->subaccpayment) {
                        $subacc = $DB->get_record('payment_ccavenue_subaccount', ['id ' => $payment->subaccpayment], 'subaccountid');
                        $paymentvalues->amount = $assessmentprice+$vendorprice;
                        $splitData = [
                            'split_tdr_charge_type' => 'M',
                            'merComm' => $assessmentprice,
                            'split_data_list' => [
                                [
                                    'splitAmount' => $vendorprice,
                                    'subAccId' => $subacc->subaccountid,
                                ]
                            ],
                        ];
                        $paymentvalues->splitjsondata = json_encode($splitData);
                        try {
                            $payment_id = $DB->insert_record('payment_ccavenue_price', $paymentvalues);
                        } catch(Exception $e) {    
                            print_object($e);die();
                        }
                    } else {
                        $paymentvalues->amount = $assessmentprice;
                        $paymentvalues->splitjsondata = '{}';
                        try {
                            $payment_id = $DB->insert_record('payment_ccavenue_price', $paymentvalues);
                        } catch(Exception $e) {    
                            print_object($e);die();
                        }
                    }
                } else {
                    $paymentvalues->amount = $assessmentprice;
                    $paymentvalues->splitjsondata = '';
                    try {
                        $payment_id = $DB->insert_record('payment_ccavenue_price', $paymentvalues);
                    } catch(Exception $e) {    
                        print_object($e);die();
                    }
                }
            } 
            $assessment->paymentpriceid = $payment_id;
        }    
        try {
            $DB->update_record('local_assessment', $assessment);
            $response->status = true;
            $response->message = get_string('updatesuccessfull', 'local_assessment');
        } catch (Excepion $e) {
            $response->status = false;
            $response->message = get_string('notupdate', 'local_assessment');;
        }
        return $response;
    }
    public static function assessmentedit_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'status of api'),
            'message' => new external_value(PARAM_TEXT, 'text message'),
        ));
    }
    public function ssovalues_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Id'),
            )
        );
    }
    public static function ssovalues($id) {
        global $DB;
        $response = new stdClass();
        $sql = "SELECT lap.samlssoid,lap.id,ss.name 
                  FROM {local_assessment_partner} lap
             LEFT JOIN {samlidp_sp} ss ON ss.id=lap.samlssoid 
                 WHERE lap.id=$id";
        $sso = $DB->get_record_sql($sql);
        return $sso;
    }
    public static function ssovalues_returns() {
        return new external_single_structure(array(
            'samlssoid' => new external_value(PARAM_TEXT, ''),
            'name' => new external_value(PARAM_TEXT, ''),
        ));
    }
    public static function datatableparameters($parameters) {
        $datatable = new stdClass();
        $datatable->sortcolumn = $parameters[1][0]['column'];
        $datatable->sortdir = $parameters[1][0]['dir'];
        $datatable->draw = (int) $parameters[2];
        $datatable->length = $parameters[3];
        $datatable->search = filter_var(trim($parameters[4]['value']), FILTER_SANITIZE_STRING);
        $datatable->start = $parameters[5];
        return $datatable;
    }
   
    public static function assessment_partnerlist_parameters() {
        $array = self::returndatatable();
        $array['default'] = new external_value(PARAM_RAW, '', VALUE_DEFAULT, 0);
        return new external_function_parameters($array);
    }
    public static function assessment_partnerlist() {
        $parameters = func_get_args();    
        $filtering = false;
        $datatable = self::datatableparameters($parameters);
        global $DB, $USER;
        $sql = "SELECT ap.id,ap.name,ap.userid,SUBSTRING_INDEX(ap.description, ' ', 10) AS description,ss.name AS sso,
                       IF(ISNULL(ap.logo) OR ap.logo='', 'not available', 'available') AS logo_availability,
                       p.name AS payment,pcs.name AS subpayment 
                  FROM {local_assessment_partner} ap 
             LEFT JOIN {samlidp_sp} ss ON ap.samlssoid=ss.id 
             LEFT JOIN {payment} p ON ap.parentpayment=p.id
             LEFT JOIN {payment_ccavenue_subaccount} pcs ON pcs.id=ap.subaccpayment
                  JOIN {user} u ON u.id=ap.userid 
                 WHERE u.deleted=0 AND ap.deleted=0";
        $totalsql = $sql;
        if ($datatable->search) {
            $filtering = true;
            $term = $datatable->search;
            $sql .= " AND (ap.name LIKE '%" . $term . "%'
                        OR p.name LIKE '%" . $term . "%'
                        OR pcs.name LIKE '%" . $term . "%'
                        OR ss.name LIKE '%" . $term . "%'
                        OR p.name LIKE '%" . $term . "%')";            
            $filtersql = $sql;
        }
        $sql .=  " LIMIT $datatable->start,$datatable->length"; 
        try {
            $record = new stdClass(); 
            $record->data = array_values($DB->get_records_sql($sql));;
            $record->recordsTotal = count(array_values($DB->get_records_sql($totalsql)));
            if ($filtering) {   
                $record->recordsFiltered = count(array_values($DB->get_records_sql($filtersql)));
            } else {
                $record->recordsFiltered = $record->recordsTotal;
            }
            $record->draw = $datatable->draw;
            return $record;
        } catch (Exception $e) {
            $record = new stdClass();
            $record->draw = (int) $datatable->draw;
            $record->error = get_string('wentwrong', 'local_assessment');
            return $record;
        }
    }
    public static function assessment_partnerlist_returns() {
        return new external_single_structure(array(
            'draw' => new external_value(PARAM_RAW, ''),
            'recordsTotal' => new external_value(PARAM_RAW, '', VALUE_OPTIONAL),
            'recordsFiltered' =>  new external_value(PARAM_RAW, '', VALUE_OPTIONAL),
            'data' => new external_multiple_structure(new external_single_structure(array(    
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'description' => new external_value(PARAM_TEXT, 'Description'),
                'sso' => new external_value(PARAM_TEXT, 'SSO'),
                'payment' => new external_value(PARAM_TEXT, 'Payment'),
                'subpayment' => new external_value(PARAM_TEXT, 'Subpayment'),
                'logo_availability' => new external_value(PARAM_TEXT, 'logo'),
                'id' => new external_value(PARAM_INT, 'ID'),
        )),  ''),
        )
        );
    }
    public static function assessmentlist_parameters() {
        $array = self::returndatatable();
        $array['default'] = new external_value(PARAM_RAW, '', VALUE_DEFAULT, 0);
        return new external_function_parameters($array);
    }
    public static function assessmentlist() {
        $parameters = func_get_args();    
        $filtering = false;
        $datatable = self::datatableparameters($parameters);
        global $DB, $USER;
        $sql = "SELECT la.id,la.name,SUBSTRING_INDEX(la.description, ' ', 10) AS description,
                       la.partnerassessmentid,la.assessmentpartner,la.samlssoid,la.link,la.paymentpriceid,la.deleted,
                       IF(ISNULL(la.logo) OR la.logo = '', 'not available', 'available') AS logo_availability,
                       lap.name AS assessmentpartner,pcp.amount,ss.name  AS sso 
                  FROM {local_assessment} la 
             LEFT JOIN {local_assessment_partner} lap ON lap.id=la.assessmentpartner
             LEFT JOIN {samlidp_sp} ss ON ss.id=la.samlssoid
             LEFT JOIN {payment_ccavenue_price} pcp ON pcp.id=la.paymentpriceid 
                 WHERE la.deleted=0" ;
        $totalsql = $sql;
        if ($datatable->search) {
            $filtering = true;
            $term = $datatable->search;
            $sql .= " AND (la.name LIKE '%" . $term . "%'
                       OR lap.name LIKE '%" . $term . "%'
                       OR ss.name LIKE '%" . $term . "%')";            
            $filtersql = $sql;
        }
        $sql .=  " LIMIT $datatable->start,$datatable->length"; 
        try {
            $record = new stdClass();
            $record->data = array_values($DB->get_records_sql($sql));
            $record->recordsTotal = count(array_values($DB->get_records_sql($totalsql)));
            if ($filtering) {   
                $record->recordsFiltered = count(array_values($DB->get_records_sql($filtersql)));
            } else {
                $record->recordsFiltered = $record->recordsTotal;
            }
            $record->draw = $datatable->draw;    
            return $record;
        } catch (Exception $e) {
            $record = new stdClass();
            $record->draw = (int) $datatable->draw;
            $record->error = get_string('wentwrong', 'local_assessment');
            return $record;
        }    
    }
    public static function assessmentlist_returns() {
        return new external_single_structure(array(
            'draw' => new external_value(PARAM_RAW, ''),
            'recordsTotal' => new external_value(PARAM_RAW, '', VALUE_OPTIONAL),
            'recordsFiltered' =>  new external_value(PARAM_RAW, '', VALUE_OPTIONAL),
            'data' => new external_multiple_structure(new external_single_structure(array(    
                'name' => new external_value(PARAM_TEXT, 'Name'),
                'description' => new external_value(PARAM_RAW, 'Description'),
                'assessmentpartner' => new external_value(PARAM_TEXT, 'Assessment Partner'),
                'sso' => new external_value(PARAM_TEXT, 'SSO', VALUE_OPTIONAL),
                'amount' => new external_value(PARAM_TEXT, 'Price', VALUE_OPTIONAL),
                'logo_availability' => new external_value(PARAM_TEXT, 'logo'),
                'id' => new external_value(PARAM_INT, 'ID'),
        )),  ''),
        )
        );
    }
    public static function paymentdetails_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'Assessment Partner Id'),
            )
        );
    }
    public static function paymentdetails($id) {
        global $DB;
        $response =  new stdClass();
        $payment = $DB->get_record('local_assessment_partner', ['id' => $id], 'parentpayment,subaccpayment');
        if ($payment->parentpayment) {
            $response->paid = true;
            $splitapi = $DB->get_record('payment_ccavenue', ['paymentid' => $payment->parentpayment], 'splitapienabled');
            if ($splitapi->splitapienabled == 1) {
                if ($payment->subaccpayment) {
                    $response->vendor = true;
                } else {
                    $response->vendor = false;
                }
            }
        } else {
            $response->vendor = false;
            $response->paid = false;
        }
        return $response;
    }
    public static function paymentdetails_returns() {
        return new external_single_structure(array(
            'vendor' => new external_value(PARAM_BOOL, ''),   
            'paid' =>  new external_value(PARAM_BOOL, ''),
        ));

    }
}
