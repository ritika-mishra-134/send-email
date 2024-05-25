<?php
require_once(__DIR__.'./../../config.php');
require_login();
$id = optional_param('id', 0, PARAM_INT);
if ($id) {
    $userid = $_REQUEST['userid'];
    $applicationid = $id;
} else {
    $userid = $_SESSION['applicationuserid'];
    $applicationid = $_SESSION['applicationid'];
}
$usercontext = context_user::instance($userid);
if (empty($applicationid) & empty($usercontext)) {
    redirect($CFG->wwwroot);
}
$fs = get_file_storage();
if (!empty($_FILES['logo']['name'])) {
    if ($_FILES['logo']) {
        $logo = $_FILES['logo'];
        $fileinfo = array(
            'contextid' => $usercontext->id, // ID of context
            'component' => 'local_assessment',     // usually = table name
            'filearea' => 'logo',     // usually = table name
            'itemid' => $applicationid,               // usually = ID of row in table
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => $logo['name'],
            'userid' => $userid,
        ); // any filename    
        // Get file
        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
        $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename'], $fileinfo['userid']);
        // Delete it if it exists    
        if ($file) {
            $file->delete();
        }
        $file = $fs->create_file_from_pathname($fileinfo, $logo['tmp_name']);    
        $fileid = $file->get_id();
        $updaterecord = new stdClass();
        $updaterecord->id = $applicationid;
        $updaterecord->logo = $fileid;
        try {
            $DB->update_record('local_assessment_partner', $updaterecord);
            if ($id) {
                $_SESSION['showpopup'] = true;
                $_SESSION['icon'] = 'success';
                $_SESSION['text'] = get_string('updatesuccessfull', 'local_assessment');
                redirect($CFG->wwwroot.'/local/assessment/assessmentpartner_list.php');
            } else {
                $_SESSION['showpopup'] = true;
                $_SESSION['icon'] = 'success';
                $_SESSION['text'] = "Created Successfully.Your One Time Username:".$_SESSION['username']." and Password:".$_SESSION['password'];
                redirect($CFG->wwwroot.'/local/assessment/assessmentpartner_creation.php');
            }
        } catch (Exception $e) {
            print_object($e);die();
        }
    }
}

