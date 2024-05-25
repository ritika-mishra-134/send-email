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
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/local/blogs/posts.php');
// $page = optional_param('page', 1, PARAM_INT);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_blogs/posts', ['site_url' => $CFG->wwwroot]);
$PAGE->requires->js_call_amd('local_blogs/posts', 'init');
echo $OUTPUT->footer();

