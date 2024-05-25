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

// We defined the web service functions to install.
defined('MOODLE_INTERNAL') || die();
 $functions = array(
    'block_instructorform_register_instructor' => array(
        'classname' => 'block_instructorform_external',
        'methodname' => 'instructor_details',
        'classpath' => 'blocks/instructorform/externallib.php',
        'description' => 'Instructor Details',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => false,
    ),
);
$services = array(
    'Instructor Web Services' => array(
        'functions' => array_keys($functions),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);