<?php
function block_products_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    // if ($context->contextlevel != CONTEXT_COURSE) {
    //     return false;
    // }

    // Make sure the filearea is one of those used by the plugin.
    if (!in_array($filearea, ['lmscard1image', 'lmscard2image', 'lmscard3image', 'lmscard4image', 
    'industrycard1image', 'industrycard2image', 'industrycard3image', 'industrycard4image',
    'mktcard1image', 'mktcard2image', 'mktcard3image', 'mktcard4image',
    'virtualcard1', 'virtualcard2', 'virtualcard3', 'virtualcard4'])) {
        return false;
    }
    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    //require_login($course, true, $cm);

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
    $file = $fs->get_file($context->id, 'block_products', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
