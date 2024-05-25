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

/**
 *
 * @package    block_products
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
class block_products extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_products');
    }
    function has_config() {
        return true;
    }
    function get_content() {
        global $CFG, $DB, $OUTPUT;
        if($this->content !== NULL) {
            return $this->content;
        }
        $renderable = new \block_products\output\products($this->config);
        $renderer = $this->page->get_renderer('block_products');
        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';
        return $this->content;
    }
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => false,
            'mod' => false,
            'my' => false
        ];
    }
    function hide_header() {
        return true;
    }
    public function instance_allow_multiple() {
        return true;
    }
}