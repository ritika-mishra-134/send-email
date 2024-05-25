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
 * @category  block
 * @package   block_instructorform
 * @author    iTrack Team <itrackdev@transneuron.com>
 * @copyright 2023 Transneuron Technologies Pvt Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link
 */

namespace block_instructorform\output;
use block_instructorform_external;
use renderable;
use renderer_base;
use templatable;
use moodle_url;
use user_picture;

defined('MOODLE_INTERNAL') || die();

/**
 * This.
 *
 * @category  block
 * @package   block_instructorform
 * @author    iTrack Team <itrackdev@transneuron.com>
 * @copyright 2023 Transneuron Technologies Pvt Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link
 */
class instructorform implements renderable, templatable {
    /**
     * Block config
     *
     * @var object An object containing the configuration information for the current
     * instance of this block.
     */
    protected $config;
    /**
     * Constructor.
     *
     * @param object $config An object containing the configuration information for the
     *                       current instance of this block.
     */
    public function __construct($config) {
        $this->config = $config;
    }


    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     *
     * @return stdClass
     */
    
    public function export_for_template(renderer_base $output) {
        global $USER, $DB, $CFG;
        // if ($this->config == null) {
        //     $hash = ['siteurl' => $CFG->wwwroot, 'heading' => get_string('heading', 'block_instructor'), 'subheading' => get_string('subheading', 'block_instructor'),
        //     'btnhead' => get_string('btnhead', 'block_instructor')];
        // } else {
        //     $fs = get_file_storage();
        //     $itemid = $this->config->instructorimage;
        //     // Assuming $itemid is the ID of the item
        
        //     $files = $fs->get_area_files(1, 'block_instructor', 'instructorimage', $itemid);
        //     $url = "";
        //     foreach ($files as $file) {
        //         $filename = $file->get_filename();
        //         if ($filename != '.') {
        //             $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
        //             $url = $url->out(); 
        //         }
        //     }
            
        //     $hash = ['siteurl' => $CFG->wwwroot, 'heading' => $this->config->heading, 'subheading' => $this->config->subheading,
        //     'btnhead' => $this->config->btnhead, 'url' => $url];
        // }
        // print_object($files);die();
        $hash = ['siteurl' => $CFG->wwwroot];
        return $hash;
        
    }
}
