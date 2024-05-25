<?php
/**
 * Assessment Settings file
 *
 * @package     local
 * @subpackage  local_assessment
 * @author   	Thomas Threadgold
 * @copyright   2015 LearningWorks Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_assessment', 'Split');
    $ADMIN->add('localplugins', $settings );
    $settings->add(
        new admin_setting_heading(
            'local_assessment',
            'Split',
            'Settings for Split API'
        )
    );
    $settings->add(new admin_setting_configcheckbox(
        'local_assessment/payment_split_enable',
        'Enable', 'Enable split Payment Gateway, Only 1 gateway can be enabled at a time', 0));
    $settings->add(new admin_setting_configcheckbox('local_assessment/payment_split_sandbox', 'Enable Sandbox', 'Enable Split Payment Gateway for sandbox purpose only [https://apitest.ccavenue.com]', 1));


    $settings->add(new admin_setting_configtext(
        'local_assessment/payment_split_merchant_data',
        'Merchant Data',
        'Merchant Data',
        '',
        PARAM_TEXT,
        null
    ));
    $settings->add(new admin_setting_configtext(
        'local_assessment/payment_split_production_url',
        'Production URL',
        'Production Split URL',
        'https://api.ccavenue.com',
        PARAM_URL
    ));
    $settings->add(new admin_setting_configtext(
        'local_assessment/payment_split_sandbox_url',
        'Production URL',
        'Production Split URL',
        'https://apitest.ccavenue.com',
        PARAM_URL
    ));
    $settings->add(new admin_setting_configpasswordunmask(
        'local_assessment/payment_split_working_key',
        'Working Key [Production]',
        'Working Key [Production] [https://api.ccavenue.com]',
        ''
    ));
    $settings->add(new admin_setting_configpasswordunmask(
        'local_assessment/payment_split_access_key',
        'Access Key [Production]',
        'Access Key [Production] [https://api.ccavenue.com]',
        ''
    ));
    $settings->add(new admin_setting_configpasswordunmask(
        'local_assessment/payment_split_sandbox_working_key',
        'Working Key [Sandbox]',
        'Working Key [Sandbox] [https://apitest.ccavenue.com]',
        ''
    ));
    $settings->add(new admin_setting_configpasswordunmask(
        'local_assessment/payment_split_sandbox_access_key',
        'Access Key [Sandbox]',
        'Access Key [Sandbox] [https://apitest.ccavenue.com]',
        ''
    ));
    $settings->add(new admin_setting_configpasswordunmask(
        'local_assessment/jwt_key',
        'Jwt Key',
        'Key to encode and decode jwt token',
        ''
    ));
    $settings->add(new admin_setting_configtext(
        'local_assessment/careerprep_url',
        'CareerPrep URL',
        'CareerPrep URL',
        'https://ksdc.careerprepindia.com',
        PARAM_URL
    ));
    
    


}