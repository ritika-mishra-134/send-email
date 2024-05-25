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
 * @package   block_products
 * @author    iTrack Team <itrackdev@transneuron.com>
 * @copyright 2023 Transneuron Technologies Pvt Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link
 */

namespace block_products\output;
use block_products_external;
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
 * @package   block_products
 * @author    iTrack Team <itrackdev@transneuron.com>
 * @copyright 2023 Transneuron Technologies Pvt Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link
 */
class products implements renderable, templatable {
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
        $headingcontent = get_config('block_products_home');
        $heading = explode(" ", $headingcontent->heading);
        $headingcontent->firstelement = !empty($heading) ? array_shift($heading) : null;
        $headingcontent->otherelements = !empty($heading) ? implode(" ", $heading) : null;
 
        $rowone = get_config('block_products_one');
        $fs = get_file_storage();
        $files = $fs->get_area_files(1, 'block_products', 'lmscard1image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowone->img1 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'lmscard2image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowone->img2 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'lmscard3image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowone->img3 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'lmscard4image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowone->img4 = $url->out();  
            }
        }

        $rowtwo = get_config('block_products_two');
        $files = $fs->get_area_files(1, 'block_products', 'industrycard1image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowtwo->img1 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'industrycard2image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowtwo->img2 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'industrycard3image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowtwo->img3 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'industrycard4image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowtwo->img4 = $url->out();  
            }
        }
        
        $rowthree = get_config('block_products_three');
        $files = $fs->get_area_files(1, 'block_products', 'mktcard1image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowthree->img1 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'mktcard2image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowthree->img2 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'mktcard3image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowthree->img3 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'mktcard4image', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowthree->img4 = $url->out();  
            }
        }
        
        $rowfour = get_config('block_products_four');
        $files = $fs->get_area_files(1, 'block_products', 'virtualcard1', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowfour->img1 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'virtualcard2', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowfour->img2 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'virtualcard3', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowfour->img3 = $url->out();  
            }
        }
        $files = $fs->get_area_files(1, 'block_products', 'virtualcard4', 0, "filename", false);
        foreach($files as $file){
            $filename = $file->get_filename();
            $itemid = $file->get_itemid();
            if (!$filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $itemid, $file->get_filepath(), $filename);
                $rowfour->img4 = $url->out();  
            }
        }
        return ['siteurl' => $CFG->wwwroot, 'headingcontent' => $headingcontent, 'rowone' => $rowone, 'rowtwo' => $rowtwo, 'rowthree' => $rowthree, 'rowfour' => $rowfour];
        
    }
}
