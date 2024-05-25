<?php
defined('MOODLE_INTERNAL') || die;
// require_once($CFG->dirroot . '/vendor/autoload.php');

function send_otp_mail_to_user_api($uVal, $mailtype, $args = [])
{
    global $CFG, $DB;
    
    // $mail = new \stdClass();
    $mail = new PHPMailer(true);
    if ($uVal->email) {
        $mail->isSMTP();
        $mail->Host = $CFG->smtphosts;
        $mail->SMTPAuth = true;
        $mail->Username = $CFG->smtpuser;
        $mail->Password = $CFG->smtppass;
        $mail->SMTPSecure = $CFG->smtpsecure;
        $mail->Mailer = "smtp";
        $mail->Port = 465;

        $mail->setFrom($CFG->noreplyaddress, 'iTrack', 0);
        //Set an alternative reply-to address
        $mail->addReplyTo($CFG->noreplyaddress, 'iTrack');
        //$uemail = "withravis@gmail.com";
        $mail->addAddress($uVal->email, $uVal->fname . ' ' . $uVal->lname);
        if ($uVal->spocemail) {
            if ($uVal->spocemail != $uVal->email) {
                $mail->addBCC($uVal->spocemail, $uVal->fname.' '.$uVal->lname);
            }
        }

        $mail->isHTML(true);

        switch ($mailtype) {
            case 'contactus_details':
                $ext = PHPMailer::mb_pathinfo($_FILES['resume-instructor']['name'], PATHINFO_EXTENSION);
                
                //Define a safe location to move the uploaded file to, preserving the extension
                $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['resume-instructor']['name'])) . '.' . $ext;
                // print_object($uploadfile);die();
                
                $mail->Subject = 'Contact Details for Instrcutor';
                $mail->Body = ' <div dir="ltr">
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt"><b>Details of a Instructor</b></p>
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt"><br></p>
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt">Name: <b>' . $uVal->fname . ' ' . $uVal->lname . '</b>,</p>
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt">Email: <b>' . $uVal->cemail . '</b>,</p>
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt">Phone: <b>' . $uVal->phone . '</b>,</p>
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt">Field of expertise: <b>' . $uVal->expertise .'</b>,</p>
    
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt"><br></p>
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt">Thanks and Regards</p>
        <p dir="ltr" style="line-height:1.655;margin-top:0pt;margin-bottom:0pt">iTrack</p>
        </div>';
        move_uploaded_file($_FILES['resume-instructor']['tmp_name'], $uploadfile);
        $mail->addAttachment($uploadfile, 'Uploaded resume', 'base64');
        break;
            default:
                break;
        }
        $log = new stdClass();
        $log->userid = null;
        $log->toemail = $uVal->email;
        $log->touserfullname = $uVal->fname . ' ' . $uVal->lname;
        $log->setfromemail = $CFG->noreplyaddress;
        $log->setfromname = 'iTrack';
        $log->replytoname = 'iTrack';
        $log->replytoemail = $CFG->noreplyaddress;
        $log->subject = $mail->Subject;
        $log->body = $mail->Body;
        $log->timecreated = time();
        $log->createdby = 1;
        $log->timemodified = time();
        $log->processed = 1;
        $log->processedat = time();
        $log->id = $DB->insert_record('scheduled_mail', $log);


        // $schedulemail = new \local_mail_notification_settings\task\adhoc_scheduled_mail();
        // $futuretime = 0;
        // $schedulemail->set_next_run_time(time() + $futuretime);
        // $schedulemail->set_userid(2);
        // \core\task\manager::queue_adhoc_task($schedulemail, true);


        try {
            $response = $mail->send();
            if ($response) {
                $log->response = 'success';
                $response = true;
            } else {
                $log->response = "Mailer Error: " . $mail->ErrorInfo;
                $response = false;
            }
        } catch (Exception $e) {
            $response = false;
            $log->response = "Mailer Error: " . $mail->ErrorInfo;
        }
        $DB->update_record('scheduled_mail', $log);
        return $response;
        // return true;
    }
}