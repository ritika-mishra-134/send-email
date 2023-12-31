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
 * Version information
 *
 * @package    local_emailsender
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.lmsofindia.com 2017 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->component = 'local_emailsender'; // Full name of the plugin (used for diagnostics).
$plugin->version  = 2023122600;    // The current module version (Date: YYYYMMDDXX).
$plugin->requires  = 2012062500; // Requires this Moodle version.
$plugin->cron      = 0;          // Period for cron to check this module (secs).
