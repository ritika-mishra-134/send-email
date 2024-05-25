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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This.
 *
 * @category  local
 * @package   local_blogs
 * @author    iTrack Team <itrackdev@transneuron.com>
 * @copyright 2023 Transneuron Technologies Pvt Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link
 */
require_once(__DIR__ . '/../../config.php');
$systemcontext = context_system::instance();
$id = optional_param('id', null, PARAM_INT);
$sql = "SELECT * FROM {post} WHERE module = 'blog' AND id=$id";
$blog = $DB->get_record_sql($sql);
$fs = get_file_storage();
$files = $fs->get_area_files(1, 'blog', 'attachment', $blog->id, 'id', false);
foreach ($files as $file) {
    $filename = $file->get_filename();
    if (!$filename <> '.') {
        $image = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $blog->id, $file->get_filepath(), $filename);
        $blog->blogimage = $image->out();
    }
}
$blog->timecreated = date('M j Y', $detail->created);
$featuredblog = get_config('local_blogs');
$filteredkeys = array();
$fblogs = new stdClass();
foreach ($featuredblog as $key => $value) {
    // Check if the value is 1.
    if ($value == 1) {
        $data = $DB->get_record('post', ['module' => 'blog', 'id' => $key]);
        $filteredkeys[] = ['id' => $data->id, 'title' => $data->subject, 'date' => date('M j Y', $data->created)];   
    }  
}
$blog->featuredblog = $filteredkeys;
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('standard');
$url = new moodle_url($CFG->wwwroot.'/local/blogs/blog_detail.php',['id' =>$id]);
$string = get_string('blogdetails', 'local_blogs');
$PAGE->set_url($url);
$PAGE->set_title($string);
echo $OUTPUT->header();
$hash = ['siteurl' => $CFG->wwwroot, 'blog' => $blog];
echo $OUTPUT->render_from_template('local_blogs/blog_detail', $hash);
echo $OUTPUT->footer();
