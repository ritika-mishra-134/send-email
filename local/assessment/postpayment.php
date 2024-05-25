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

require_once(__DIR__.'/../../config.php');
// To process the payment even when user loggout out just based on request.
// .. require_login();
$data = required_param('data', PARAM_RAW);
$data = json_decode($data);

// Right now we will get data from some payment portal.
// we are not sure about which payment portal we are going to use.
// that why we will use order id to find everyhting
// that is from order id will will find assessmentid, assessment partner, payment gateway,
// etc etc.


// Now using assessment transaction.
// Check if valid asssessment transaction.
$assessmenttransaction = $DB->get_record('local_assessment_transaction', array('orderid' => $data->order_id));

// Userid who initiated the transaction.
$userid = $assessmenttransaction->userid;
// Find assessment info using same.
$assessment = $DB->get_record('local_assessment', array('id' => $assessmenttransaction->assessmentid));
// Now find assessment partner and payment info for same.
$assessmentpartner = $DB->get_record('local_assessment_partner', array('id' => $assessment->assessmentpartner));
// Now find payment details using same.
$payment = $DB->get_record('payment', array('id' => $assessmentpartner->parentpayment));

// Using above record you will find payment related details to get below info.


switch ($payment->paymentprovider) {
    case 1:
        // Payment provider is ccavenue.

        $paymenttransaction = $DB->get_record(
            'payment_ccavenue_transaction',
            array('orderid' => $data->order_id, 'producttype' => 'assessment'
        ));

        if (!$paymenttransaction) {
            redirect(new moodle_url('/local/assessment/my.php'));
        }

        // Since Valid assessment payment transaction.

        // Now updating assessment transaction status.
        $assessmenttransaction->processed = 1;
        $DB->update_record('local_assessment_transaction', $assessmenttransaction);

        $assessmentuser = $DB->get_record('user', array('id' => $assessmenttransaction->userid), 'id,email');

        // Now check payment status.

        if ($paymenttransaction->orderstatus == 'Success') {
            // Payment Passed. Enrol User.
            $assessmentuserenrol = new stdClass();
            $assessmentuserenrol->userid = $assessmenttransaction->userid;
            $assessmentuserenrol->assessmentid = $assessmenttransaction->assessmentid;
            $assessmentuserenrol->assessmenttransactionid = $assessmenttransaction->id;
            $assessmentuserenrol->timecreated = time();
            $assessmentuserenrol->id = $DB->insert_record(
                'local_assessment_user_enrol',
                $assessmentuserenrol
            );
            // Call API to enrol this user in this particular test.

            $sql = "SELECT lap.*
            FROM {local_assessment_partner} lap
            JOIN {local_assessment} la ON la.assessmentpartner = lap.id
            WHERE la.id = :id";

            $assessmentpartner = $DB->get_record_sql($sql, array('id' => $assessmenttransaction->assessmentid));
            $curl = curl_init();

            $splitdata = json_decode($paymenttransaction->splitjsondata);
            if (isset($splitdata->merComm)) {
                $merchantcommission = $splitdata->merComm;
                $vendorcommision = $splitdata->split_data_list[0]->splitAmount;
                $vendorsubaccount = $splitdata->split_data_list[0]->subAccId;
            }
            // Creating curl call for Transaction API for paid assessment.

            $array = array(
                CURLOPT_URL => trim($assessmentpartner->apiendpoint),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'order_id=' . $paymenttransaction->orderid
                .'&email_address=' . $assessmentuser->email
                .'&reference_id=' . $paymenttransaction->trackingid
                .'&assessment_id=' . $assessment->partnerassessmentid
                .'&transaction_at=' . $paymenttransaction->transactionfinishedat
                .'&total_price=' . $paymenttransaction->amount
                .'&merchant_commision=' . $merchantcommission
                .'&vendor_comission=' . $vendorcommision
                .'&currency='.$paymenttransaction->currency
                .'&vendor_subaccount=' . $vendorsubaccount
                .'&payment_gateway=CCAvenue'
                .'&payment_mode='.$paymenttransaction->paymentmode,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                )
            );
            if ($assessmentpartner->apitoken) {
                $array[CURLOPT_HTTPHEADER][] = 'token: '.trim($assessmentpartner->apitoken);
            }
            curl_setopt_array($curl, $array);

            $response = curl_exec($curl);
            if (!$response) {
                $status = 0;
                $message = 'response=;httpcode='. curl_getinfo($curl, CURLINFO_HTTP_CODE);
            } else {
                $response = json_decode(trim($response));
                if (!isset($response->status)) {
                    $status = 0;
                    $message = 'No response recieved';
                } else {
                    $status = $response->status;
                    $message = $response->message;
                }
                $message = 'response=' . $message . ';httpcode=' . curl_getinfo($curl, CURLINFO_HTTP_CODE);
            }
            if (curl_errno($curl)) {
                $status = 0;
                $message = curl_error($curl);
            }
            curl_close($curl);
            $assessmenttransactionapi = new stdClass();
            $assessmenttransactionapi->orderid = $paymenttransaction->orderid;
            $assessmenttransactionapi->userid = $assessmenttransaction->userid;
            $assessmenttransactionapi->assessmentid = $assessment->id;
            $assessmenttransactionapi->assessmentpartnerid = $assessmentpartner->id;
            $assessmenttransactionapi->timecreated = time();
            $assessmenttransactionapi->processed = 1;
            $assessmenttransactionapi->status = $status;
            $assessmenttransactionapi->message = $message;
            $assessmenttransactionapi->payload = json_encode(explode('&', 'order_id=' . $paymenttransaction->orderid
                .'&email_address=' . $assessmentuser->email
                .'&reference_id=' . $paymenttransaction->trackingid
                .'&assessment_id=' . $assessment->partnerassessmentid
                .'&transaction_at=' . $paymenttransaction->transactionfinishedat
                .'&total_price=' . $paymenttransaction->amount
                .'&merchant_commision=' . $merchantcommission
                .'&vendor_comission=' . $vendorcommision
                .'&currency='.$paymenttransaction->currency
                .'&vendor_subaccount=' . $vendorsubaccount
                .'&payment_gateway=CCAvenue'
                .'&payment_mode='.$paymenttransaction->paymentmode));

            $DB->insert_record('local_asssessment_paid_api', $assessmenttransactionapi);
            require_once($CFG->dirroot.'/local/invoice/lib.php');
            // Generate invoice number.
            $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(timecreated), '%Y-%m-%d') AS dateoforder, COUNT(id) AS totalorderindate,
                                assessmentid
                        FROM {local_assessment_transaction}
                        WHERE processed = 1 AND assessmentid = :id
                        GROUP BY dateoforder,assessmentid
                        HAVING  dateoforder = DATE_FORMAT(NOW(), '%Y-%m-%d')
                        ORDER BY dateoforder";
            $totalordersinaday = $DB->get_record_sql($sql, array('id' => $assessment->id), 'totalorderindate');
            $business = $DB->get_record('local_invoice_business', array('id' => $assessmentpartner->businessid));
            $invoicenumber = generateinvoiceid($business->invoiceidentifier, $totalordersinaday->totalorderindate);
            $paymenttransaction->invoicenumber = $invoicenumber;
            $DB->update_record('payment_ccavenue_transaction', $paymenttransaction);
            // Now sending invoice.
            require_once($CFG->dirroot.'/local/invoice/classes/invoicepdf.php');
            require_once($CFG->dirroot.'/local/user_auth/lib.php');
            $data = createassessmentinvoice($paymenttransaction->orderid, $assessmenttransaction->userid);
            $invoicetemplate = $DB->get_record('local_invoice_template', array('businessid' => $assessmentpartner->businessid));
            if ($data !== false) {
                // Sending invoice email.
                $attachment = new stdClass();
                $attachment->content = $data;
                $attachment->filename = 'Invoice-'.$paymenttransaction->orderid.'.pdf';
                send_mail_to_user_api($assessmenttransaction->userid, 'assessmentinvoice', [
                    'bcc' => explode(',', $invoicetemplate->bcc),
                    'attachments' => [$attachment]
                ]);
            }
        } else {
            // Payment Failed. Unenrol users.
            $DB->delete_records('local_assessment_user_enrol', array(
                'userid' => $USER->id,
                'assessmentid' => $assessmentid,
                'assessmenttransactionid' => $assessmenttransaction->id
            ));
        }
        break;
}
redirect(new moodle_url('/local/assessment/my.php'));
