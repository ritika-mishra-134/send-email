<?php
require_once(__DIR__.'./../../config.php');
require_login();
if(!is_siteadmin()) {
    redirect($CFG->wwwroot.'/dashboard');
} else {
    $systemcontext = context_system::instance();
    $id = optional_param('id', null, PARAM_INT);
    $sql = "SELECT ap.*,ss.name AS ssoname,p.name AS payment,pcs.name AS subpayment,u.email 
            FROM {local_assessment_partner} ap 
        LEFT JOIN {samlidp_sp} ss ON ap.samlssoid=ss.id 
        LEFT JOIN {payment} p ON ap.parentpayment = p.id
        LEFT JOIN {payment_ccavenue_subaccount} pcs ON pcs.id=ap.subaccpayment
            JOIN {user} u ON u.id=ap.userid 
            WHERE ap.id=$id";
    $record = $DB->get_record_sql($sql);
    $fs = get_file_storage();
    $files = $fs->get_file_by_id($record->logo);
    if ($files) {
        $filename = $files->get_filename();
        if ($filename != '.') {
            $url = moodle_url::make_pluginfile_url($files->get_contextid(), $files->get_component(), 
                $files->get_filearea(), $files->get_itemid(), $files->get_filepath(),
                $files->get_filename(), false);
            $url = $url->out();
        }       
    }
    $record->filename = $filename;
    $record->url = $url;
    $ssosql = "SELECT id,
                    (CASE
                        WHEN id = $record->samlssoid THEN 1
                        ELSE 0
                    END) AS SELECTED,name
                FROM {samlidp_sp}";
    $data = array_values($DB->get_records_sql($ssosql));
    $paymentsql = "SELECT id,
                        (CASE
                            WHEN id = $record->parentpayment 
                            THEN 1 ELSE 0
                        END) AS SELECTED,name
                    FROM {payment}";           
    $payment = array_values($DB->get_records_sql($paymentsql));
    $subaccsql = "SELECT id,
                        (CASE
                            WHEN id = $record->subaccpayment 
                            THEN 1 ELSE 0
                        END) AS SELECTED,name
                    FROM {payment_ccavenue_subaccount}"; 
    $subaccount = array_values($DB->get_records_sql($subaccsql));
    $string = get_string('editpartner', 'local_assessment');
    $url = new moodle_url($CFG->wwwroot.'/local/assessment/assessmentpartner_edit.php', ['id' => $id]);
    $record->ssodata = $data;
    $record->paymentdata = $payment;
    $record->subaccdata = $subaccount;
    $hash = [
        'siteurl' => $CFG->wwwroot,
        'record' => $record,
        'sesskey' => $USER->sesskey
    ];
    $PAGE->set_context($systemcontext);
    $PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/assessment/styles/createbutton.min.css'));
    $PAGE->set_pagelayout('custom');
    $PAGE->navbar->add($string, $url);
    $PAGE->set_title($string);
    $PAGE->set_heading($string);
    $PAGE->set_url($url);
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_assessment/assessmentpartner_creation', $hash);
    $PAGE->requires->js_call_amd('local_assessment/assessmentpartner_edit', 'init', [$id, $record->userid]);
    echo $OUTPUT->footer();
}