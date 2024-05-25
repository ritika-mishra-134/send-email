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

require_once(__DIR__ . './../../config.php');
require_login();
// Check if assessmentid is valid.
$assessmentid = required_param('id', PARAM_INT);
$assessment = $DB->get_record('local_assessment', array('id' => $assessmentid, 'visible' => 1, 'deleted' => 0));
if (!$assessment) {
    $hash = [
        'siteurl' => $CFG->wwwroot,
        'notavailable' => true
    ];
} else {
    $assessmentuserenrol = $DB->get_record('local_assessment_user_enrol', array('userid' => $USER->id, 'assessmentid' => $assessmentid));
    if ($assessment->paymentpriceid && !$assessmentuserenrol) {
        // This is paid asssessment & user is not enrolled
        // ... thus we have to perform payment.
        // Find payment info based on payment provider since payment is required.
        $assessmentpartner = $DB->get_record(
            'local_assessment_partner',
            array('id' => $assessment->assessmentpartner)
        );
        // The above will give you info about parent payment account.
        // and also it will give id of subaccount if payment support that.
        $payment = $DB->get_record('payment', array('id' => $assessmentpartner->parentpayment));

        // We have to create entry in local_assessment_transaction table
        // ... about this assessment purchase by this user.

        $assessmenttransaction = $DB->get_record('local_assessment_transaction', array(
            'userid' => $USER->id,
            'assessmentid' => $assessment->id,
            'processed' => 0
            )
        );
        if ($assessmenttransaction) {
            $assessmenttransaction->timecreated = time();
            $DB->update_record(
                'local_assessment_transaction',
                $assessmenttransaction
            );
        } else {
            $assessmenttransaction = new stdClass();
            $assessmenttransaction->userid = $USER->id;
            $assessmenttransaction->assessmentid = $assessment->id;
            $assessmenttransaction->orderid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $assessmenttransaction->timecreated = time();
            $assessmenttransaction->id = $DB->insert_record(
                'local_assessment_transaction',
                $assessmenttransaction
            );
        }
        // The above will provide us info about table in which we have to search subaccount info.
        switch ($payment->paymentprovider) {
            case 1:
                // By default all will be cc avenue.
                // Thus we will now check price of product (Assessment/course) based on this table.
                $paymentccavenue = $DB->get_record(
                    'payment_ccavenue',
                    array('id' => $payment->paymentpartnerconfigid)
                );
                $ccavenuepaymentprice = $DB->get_record('payment_ccavenue_price', array('id' => $assessment->paymentpriceid));
                $hash = [
                    'name' => $assessment->name,
                    'assessmentinfo' => $assessment->description,
                    'partnername' => $assessmentpartner->name,
                    'partnerinfo' => $assessmentpartner->description,
                    'action' => $CFG->wwwroot . '/local/payment/plugins/ccavenue/initiate.php',
                    'paymentid' => $paymentccavenue->id,
                    'orderid' => $assessmenttransaction->orderid,
                    'amount' => $ccavenuepaymentprice->amount,
                    'redirecturl' => (new moodle_url('/local/payment/plugins/ccavenue/response.php'))->out(),
                    'cancelurl' => (new moodle_url('/local/payment/plugins/ccavenue/response.php'))->out(),
                    'splitjsondata' => $ccavenuepaymentprice->splitjsondata,
                    'merchantparam1' => '',
                    'merchantparam2' => '',
                    'merchantparam3' => '',
                    'merchantparam4' => '',
                    'merchantparam5' => '',
                    'producttype' => 'assessment'
                ];
                $hash['paymentformhtml'] = $OUTPUT->render_from_template(
                    'payment_ccavenue/form',
                    $hash
                );
                $hash['netamount'] = $ccavenuepaymentprice->unitprice;
                $hash['platformfees'] = $ccavenuepaymentprice->platformfees;
                $hash['totalnetamount'] = $ccavenuepaymentprice->unitprice + $ccavenuepaymentprice->platformfees;
                $hash['shippingcharges'] = 0.00;
                $hash['igstpercent'] = '0 %';
                $hash['cgstpercent'] = '@ 9.0%';
                $hash['sgstpercent'] = '@ 9.0%';
                $hash['igst'] = $ccavenuepaymentprice->igst;
                $hash['cgst'] = $ccavenuepaymentprice->cgst;
                $hash['sgst'] = $ccavenuepaymentprice->sgst;
                $hash['totalgst'] = $ccavenuepaymentprice->igst + $ccavenuepaymentprice->cgst + $ccavenuepaymentprice->sgst;
                $hash['grandtotal'] = ($hash['totalgst'] + $hash['totalnetamount'] == 0) ? $ccavenuepaymentprice->amount : $hash['totalgst'] + $hash['totalnetamount'];
                $hash['netpayable'] = $ccavenuepaymentprice->amount;
            break;
        }
    } else {
        $assessmentpartner = $DB->get_record(
            'local_assessment_partner',
            array('id' => $assessment->assessmentpartner)
        );
        $hash = [
            'name' => $assessment->name,
            'assessmentinfo' => $assessment->description,
            'partnername' => $assessmentpartner->name,
            'partnerinfo' => $assessmentpartner->description
        ];
    }
}

$systemcontext = context_system::instance();
$string = get_string('introductionx', 'local_assessment', $assessment->name);
$url = new moodle_url('/local/assessment/introduction.php', array('id' => $assessmentid));
$PAGE->set_url($url);
$PAGE->navbar->add($string, $url);
$PAGE->set_context($systemcontext);
$PAGE->requires->css(new moodle_url('/local/assessment/styles/introduction.css'));
$PAGE->set_pagelayout('custom');
// $PAGE->set_heading($string);
$PAGE->set_title($string);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_assessment/introduction', $hash);
echo $OUTPUT->footer();
