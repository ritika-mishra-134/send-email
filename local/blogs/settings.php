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
*
* @package    local
* @subpackage blogs
* @copyright  2022 Transneuron Techologies Pvt Ltd.
* @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


// Ensure the configurations for this site are set.
if ($hassiteconfig) {
    $settings = new admin_settingpage( 'local_blogs', get_string('pluginname', 'local_blogs'));
    // Create.
	$blog = $DB->get_records('post', ['module' => 'blog']);
    $ADMIN->add('localplugins', $settings );
	foreach($blog as $blogs) {
    $settings->add(
		new admin_setting_configcheckbox(
			'local_blogs/'.$blogs->id.'',
			$blogs->subject,
			'',
			0
			)
		);
	}
}