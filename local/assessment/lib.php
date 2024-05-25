<?php
require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/../../vendor/autoload.php');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
/**
 *
 */
function encode($email) {
    global $USER, $CFG;
    $key = get_config('local_assessment', 'jwt_key');
    $careerprepurl = get_config('local_assessment', 'careerprep_url');
    $payload = array(
        'name'  => $USER->firstname. ' '. $USER->lastname,
        'iat' => 1516239022,
        'nbf' => 1516239022,
        'aud' => $careerprepurl,
        'iss' => $CFG->wwwroot,
        'email' => $email
    );
    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;

}
/**
 *
 */
function decode($jwt) {
    $key = get_config('local_assessment', 'jwt_key');
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    // Pass a stdClass in as the third parameter to get the decoded header values
    // $decoded = JWT::decode($jwt, new Key($key, 'HS256'), $headers = new stdClass());
    return $decoded;
}

/**
 * Serve the files from the MYPLUGIN file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function local_assessment_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.

    // Make sure the filearea is one of those used by the plugin.
    if (!in_array($filearea, array('careerprep_result', 'assessmentresult', 'assessment', 'assessmentpartner', 'logo', 'assessment_logo'))) {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true);

    // Check the relevant capabilities - these may vary depending on the filearea being accessed.

    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.

    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_assessment', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
