<?php
require_once(__DIR__.'./../../config.php');
require_login();
if(!is_siteadmin()) {
    redirect($CFG->wwwroot.'/dashboard');
} else {
    $systemcontext = context_system::instance();
    $id = optional_param('id', null, PARAM_INT);
    $sql = "SELECT la.id,la.samlssoid,la.name,la.assessmentpartner,la.paymentpriceid,la.description,
                la.link,la.partnerassessmentid,la.logo,lap.name AS assessmentpartnername,pcp.amount,pcp.splitjsondata,
                ss.name AS sso 
            FROM {local_assessment} la 
        LEFT JOIN {local_assessment_partner} lap ON lap.id=la.assessmentpartner
        LEFT JOIN {samlidp_sp} ss ON ss.id=la.samlssoid
        LEFT JOIN {payment_ccavenue_price} pcp ON pcp.id=la.paymentpriceid 
            WHERE la.id=$id";
    $record = $DB->get_record_sql($sql); 
    $data = json_decode($record->splitjsondata, true);
    $mercomm = $data['merComm'];
    $splitamount = $data['split_data_list'][0]['splitAmount'];
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
    $apdatasql = "SELECT id,
                        (CASE
                            WHEN id = $record->assessmentpartner 
                            THEN 1 ELSE 0
                        END) AS selected,name
                    FROM {local_assessment_partner}";
    $assessmentpartner = array_values($DB->get_records_sql($apdatasql));
    $ssosql = "SELECT id,
                    (CASE
                        WHEN id = $record->samlssoid
                        THEN 1 ELSE 0
                    END) AS SELECTED,name
                FROM {samlidp_sp}";
    $sso = array_values($DB->get_records_sql($ssosql));
    $string = get_string('editassessment', 'local_assessment');
    $url = new moodle_url($CFG->wwwroot.'/local/assessment/assessment_edit.php',['id' =>$id]);
    $record->apdata = $assessmentpartner;
    $record->ssodata = $sso;
    $record->mercomm = $mercomm;
    $record->subacc = $splitamount;
    $hash = [
        'siteurl' => $CFG->wwwroot,
        'record' => $record,
        'sesskey' => $USER->sesskey,
        'userid' => $USER->id
    ];
    $PAGE->set_context($systemcontext);
    $PAGE->set_pagelayout('custom');
    $PAGE->navbar->add($string, $url);
    $PAGE->set_title($string);
    $PAGE->set_url($url);
    $PAGE->set_heading($string);
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_assessment/create_assessment', $hash);
    $PAGE->requires->js_call_amd('local_assessment/assessment_edit', 'init', [$id, $record->paymentpriceid]);
    echo $OUTPUT->footer();
}
