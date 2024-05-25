<?php
require_once(__DIR__.'./../../config.php');
// require_once('lib.php');
$userid = $_SESSION['applicationuserid'];
$applicationid = $_SESSION['applicationid'];
$usercontext = context_user::instance($userid);
if (empty($applicationid) & empty($usercontext)) {
    redirect($CFG->wwwroot);
}
// // Require login not required in this file
// // Upload related docs
$fs = get_file_storage();
if (!empty($_FILES['resume-instructor']['name'])) {
    if ($_FILES['resume-instructor']) {
        $logofile = $_FILES['resume-instructor'];
        $fileinfo = array(
            'contextid' => $usercontext->id, // ID of context
            'component' => 'block_instructorform',     // usually = table name
            'filearea' => 'resume',     // usually = table name
            'itemid' => $applicationid,               // usually = ID of row in table
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => $logofile['name'],
            'userid' => $userid,
        ); // any filename

        // Get file
        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename'], $fileinfo['userid']);
        // Delete it if it exists
        if ($file) {
            $data = $file->delete();
        }
        $file = $fs->create_file_from_pathname($fileinfo, $logofile['tmp_name']);
        $fileid = $file->get_id();
        $updaterecord = new stdClass();
        $updaterecord->id = $applicationid;
        $updaterecord->resume = $fileid;
        try {
            $DB->update_record('intructor_details', $updaterecord);
            // $contactus = new stdClass(); 
            // $contactus->fname = $_SESSION['firstname'];
            // $contactus->lname = $_SESSION['lastname'];
            // $contactus->cemail = $_SESSION['email'];
            // $contactus->phone = $_SESSION['phone'];
            // $contactus->expertise = $_SESSION['expertise'];
            // $contactus->email = 'info@transneuron.com';
            // $contactus->spocemail = 'info@transneuron.com';
            // send_otp_mail_to_user_api($contactus, 'contactus_details', $args = []);
            redirect($CFG->wwwroot.'/local/pages/become_instructor.php');    
                
            
        } catch(Exception $e) {
            print_object($e);die();
        }
    }
}