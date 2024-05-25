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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
* This file contain webservices required to interact with assessment
* module.
*
* @package    local_assessment
* @category   local
* @copyright  2023 Transneuron Technologies
* @license
*/

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/lib/externallib.php');

class local_blogs_external extends external_api {

    public static function blogdetails_parameters() {
        return new external_function_parameters(array(
            'blogtype' => new external_value(PARAM_TEXT, ''),
            'currentpage' => new external_value(PARAM_INT, ''),
            
        ));
    }
    public static function blogdetails($blogtype, $currentpage) {
        global $DB, $CFG;
        $perpage = 12; // Number of records per page.
        $page = $currentpage;
        // Calculate the offset for the SQL query.
        $offset = ($page - 1) * $perpage;

        // Get the total number of records.
        // $totalrecords = $DB->count_records_select('post', "module = 'blog'");

        // Get the records for the current page.
        if ($blogtype == 'all') {
            $totalrecords = $DB->count_records_select('post', "module = 'blog'");
            $blocksql = "SELECT * FROM {post} WHERE module = 'blog' ORDER BY created LIMIT $offset, $perpage";
        } else {
            $totalrecords = COUNT($DB->get_records_sql("Select * from {tag} t join {tag_instance} ti on t.id=ti.tagid  where t.name='$blogtype'"));
            $blocksql = "SELECT p.*,t.name FROM mdl_post p join mdl_tag_instance ti on ti.itemid=p.id join mdl_tag t 
                        on t.id=ti.tagid where p.module='blog' and t.name='$blogtype' ORDER BY p.created LIMIT $offset, $perpage";
        }
        $getdetails = $DB->get_records_sql($blocksql);
        $tempresult = array();
        $tempresults = array();
        foreach ($getdetails as $detail) {
            $data = new stdClass();
            if ($detail->id) {
                $fs = get_file_storage();
                $files = $fs->get_area_files(1, 'blog', 'attachment', $detail->id, 'id', false);
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    if ($filename != '.') {
                        $image = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $detail->id, $file->get_filepath(), $filename);
                        $data->blogimage = $image->out();
                    }
                }
            }
            $tempresult = [
                'id' => $detail->id,
                'subject' => $detail->subject,
                'timecreated' => date('j M Y', $detail->created),
                'summary' => $detail->summary,
                'siteurl' => $CFG->wwwroot,
                'image' => $data->blogimage ?? null, // Use null if no image found
            ];
            $tempresults[] = $tempresult;
        }

        // Calculate the total number of pages.
        $totalpages = ceil($totalrecords / $perpage);
        // Create pagination array.
        $pagination = [];
        if ($totalpages > 1) {
            for ($i = 1; $i <= $totalpages; $i++) {
                $pagination[] = [
                    'page' => $i,
                    'current' => $i == $page,
                ];
            }
        } 
        $hash['one'] = array_values($tempresults);
        $hash['site_url'] = $CFG->wwwroot;
        $hash['pagination'] = $pagination;
        $hash['has_previous'] = $page > 1;
        $hash['has_next'] = $page < $totalpages;
        return $hash;
    }
    public static function blogdetails_returns() {
        return new external_single_structure(
            array(
                'one' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Blog post ID'),
                            'subject' => new external_value(PARAM_TEXT, 'Subject of the blog post'),
                            'timecreated' => new external_value(PARAM_TEXT, 'Creation time of the blog post'),
                            'summary' => new external_value(PARAM_RAW, 'Summary of the blog post'),
                            'siteurl' => new external_value(PARAM_URL, 'Site URL'),
                            'image' => new external_value(PARAM_RAW, 'Blog image URL')
                        )
                    )
                ),
                'site_url' => new external_value(PARAM_URL, 'Site URL'),
                'pagination' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'page' => new external_value(PARAM_INT, 'Page number', VALUE_OPTIONAL),
                            'current' => new external_value(PARAM_TEXT, 'Is current page', VALUE_OPTIONAL),
                        )
                    )
                ),
                'has_previous' => new external_value(PARAM_TEXT, 'Has previous page', VALUE_OPTIONAL),
                'has_next' => new external_value(PARAM_TEXT, 'Has next page', VALUE_OPTIONAL),
                // 'previous_page_url' => new external_value(PARAM_RAW, 'Previous page URL', VALUE_OPTIONAL),
                // 'next_page_url' => new external_value(PARAM_RAW, 'Next page URL', VALUE_OPTIONAL),
            )
        );
    }
    
}