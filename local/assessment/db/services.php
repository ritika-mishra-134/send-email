<?php
$functions = array(
    // 'local_assessment_swayam_assessment_status' => array(
    //     'classname' => 'local_assessment_external',
    //     'methodname' => 'swayam_assessment_status',
    //     'classpath' => 'local/assessment/externallib.php',
    //     'description' => 'This function is used recieve the status of user status against the
    //     swayam assessment',
    //     'type' => 'write',
    //     // 'ajax' => true, // Disabling ajax since this will be no ajax call.
    //     // 'loginrequired' => true,
    // ),
    // 'local_assessment_swayam_result_user_mapping' => array(
    //     'classname' => 'local_assessment_external',
    //     'methodname' => 'swayam_result_user_mapping',
    //     'classpath' => 'local/assessment/externallib.php',
    //     'description' => 'This function is used recieve the status of user status against the
    //     swayam assessment',
    //     'type' => 'write',
    //     // 'ajax' => true, // Disabling ajax since this will be no ajax call.
    //     // 'loginrequired' => true,
    // ),
    // 'local_assessment_carrerprep' => array(
    //     'classname' => 'local_assessment_external',
    //     'methodname' => 'carrerprep',
    //     'classpath' => 'local/assessment/externallib.php',
    //     'description' => 'This function is used to insert careerprep aptitude data',
    //     'type' => 'write',
    //     // 'ajax' => true, // Disabling ajax since this will be no ajax call.
    //     // 'loginrequired' => true,
    // ),
    'local_assessment_assessment_status' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'assessment_status',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is used recieve the status of user status against the
        swayam assessment',
        'type' => 'write',
        // 'ajax' => true, // Disabling ajax since this will be no ajax call.
        // 'loginrequired' => true,
    ),
    'local_assessment_result_user_mapping' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'result_user_mapping',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is used recieve the status of user status against the
        swayam assessment',
        'type' => 'write',
        // 'ajax' => true, // Disabling ajax since this will be no ajax call.
        // 'loginrequired' => true,
    ),
    'local_assessment_results_data' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'results_data',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is used to insert careerprep aptitude data',
        'type' => 'write',
        // 'ajax' => true, // Disabling ajax since this will be no ajax call.
        // 'loginrequired' => true,
    ),
    'local_assessment_assessment_creation' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'assessment_creation',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is used to create assessment partner',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),
    // 'local_assessment_assessment_partner_delete' => array(
    //     'classname' => 'local_assessment_external',
    //     'methodname' => 'assessment_partner_delete',
    //     'classpath' => 'local/assessment/externallib.php',
    //     'description' => 'This function is used to delete assessment partner',
    //     'type' => 'write',
    //     'ajax' => true, // Disabling ajax since this will be no ajax call.
    // ),
    'local_assessment_assessmentpartner_edit' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'assessmentpartner_edit',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is used to edit assessment partner',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),
    'local_assessment_createassessment' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'createassessment',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is to create assessment',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),
    // 'local_assessment_assessment_delete' => array(
    //     'classname' => 'local_assessment_external',
    //     'methodname' => 'assessment_delete',
    //     'classpath' => 'local/assessment/externallib.php',
    //     'description' => 'This function is to delete assessment',
    //     'type' => 'write',
    //     'ajax' => true, // Disabling ajax since this will be no ajax call.
    // ),
    'local_assessment_assessmentedit' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'assessmentedit',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is to edit assessment',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),
    'local_assessment_ssovalues' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'ssovalues',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is to get sso values',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),
    'local_assessment_assessment_partnerlist' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'assessment_partnerlist',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is to get list of assessment partner',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),
    'local_assessment_assessmentlist' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'assessmentlist',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is to get list of assessment',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),   
    'local_assessment_paymentdetails' => array(
        'classname' => 'local_assessment_external',
        'methodname' => 'paymentdetails',
        'classpath' => 'local/assessment/externallib.php',
        'description' => 'This function is to get payment detail',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
    ),   
);
$services = array(
    // 'Swayam Assessment APIs' => array(
    //     'functions' => array(
    //         'local_assessment_swayam_assessment_status',
    //         'local_assessment_swayam_result_user_mapping'
    //     ),
    //     'restrictedusers' => 0,
    //     'enabled' => 1,
    //     'shortname' => 'swayamapi',
    //     'uploadfiles' => true,
    // ),
    // 'Carrerprep APIs' => array(
    //     'functions' => array(
    //         'local_assessment_carrerprep'
    //     ),
    //     'restrictedusers' => 0,
    //     'enabled' => 1,
    //     'shortname' => 'careerprep',
    //     'uploadfiles' => true,
    // ),
    'Assessment APIs' => array(
        'functions' => array_keys($functions),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'assesmentapi',
        'uploadfiles' => true,
    )
);