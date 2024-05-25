<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/user/lib.php');


class block_instructorform_external extends external_api {
    public static function instructor_details_parameters() {
        return new external_function_parameters(
            array(
                'firstname' => new external_value(PARAM_TEXT, 'First Name'),
                'lastname' => new external_value(PARAM_TEXT, 'Last Name'),
                'email' => new external_value(PARAM_EMAIL, 'Email'),
                'phone' => new external_value(PARAM_INT, 'COntect Number'),
                'expertise' => new external_value(PARAM_TEXT, 'Field of Expertise'),
            )
        );
    }
    public static function instructor_details() {
        global $DB, $CFG, $USER;
        $arguments = func_get_args();
        // echo "hello";die();
        
        $instructordetails = new stdClass();
        $response = new stdClass();
        $instructordetails->firstname = $arguments[0];
        $instructordetails->lastname = $arguments[1];
        $instructordetails->email = $arguments[2];
        $instructordetails->phone = $arguments[3];
        $instructordetails->fieldofexpertise = $arguments[4];
        $user = new stdClass();  
        $plainpassword = generate_password(12);
        $user->firstname = $arguments[0];   
        $user->lastname = $arguments[1];
        $user->email = $arguments[2];
        $user->username = strtolower($arguments[2]);  
        $user->password = hash_internal_user_password($plainpassword);
        try {
            $userid = user_create_user($user, false, true);
        } catch (Exception $e) {    
            if ($DB->record_exists('user', array('email' => $arguments[2], 'deleted' => 0))) {
                $response->status = false;
                $response->message = get_string('emailexist', 'block_instructorform'); 
                return $response;
            }    
        }
        try {
            $instructor = $DB->insert_record('intructor_details', $instructordetails);
            session_start();
            $_SESSION['applicationuserid'] = $userid;
            $_SESSION['applicationid'] = $instructor;
            $_SESSION['firstname'] = $arguments[0];
            $_SESSION['lastname'] = $arguments[1];
            $_SESSION['email'] = $arguments[2];
            $_SESSION['phone'] = $arguments[3];
            $_SESSION['expertise'] = $arguments[4];

            $response->status = true;
            $response->message = 'Data inserted successfully';

        } catch(Exception $e) {
            // var_dump($e);die();
            print_object($e);die();
        }
        
        
        return $response;
    }
    public static function instructor_details_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'Status'),
            'message' => new external_value(PARAM_TEXT, 'Message')
        ));
    }

}