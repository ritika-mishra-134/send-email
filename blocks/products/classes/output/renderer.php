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
use plugin_renderer_base;

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
class renderer extends plugin_renderer_base {
    /**
     * Return the main content for the block myprofile.
     *
     * @param myprofile $myprofile The myprofile renderable
     *
     * @return string HTML string
     */
    public function render_products(products $products) {
        global $USER, $DB;
        return $this->render_from_template('block_products/products',
            $products->export_for_template($this));
    }
}
