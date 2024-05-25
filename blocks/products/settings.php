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
 * Adds admin settings for the plugin.
 *
 * @package     block_products
 * @category    admin
 * @copyright   2020 Your Name <email@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();
 if ($hassiteconfig) {
    $productscategory = new admin_category('block_products_topcat', 'Product Settings');
    $ADMIN->add('blocksettings', $productscategory);
    // Heading setings .
    $headingsetting = new admin_settingpage('block_products', get_string('productsetting', 'block_products'));
    $headingsetting->add(new admin_setting_configtext('block_products_home/heading', get_string('heading', 'block_products'), get_string('heading', 'block_products'),
    get_string('products', 'block_products'), PARAM_TEXT, null));
    $headingsetting->add (new admin_setting_configtext('block_products_home/content', get_string('firstcontent', 'block_products'), get_string('firstcontent', 'block_products'),
    get_string('content', 'block_products'), PARAM_TEXT, null));
    $headingsetting->add(new admin_setting_configtext('block_products_home/buttononehed',get_string('buttononehed','block_products'),
    get_string('buttononehed','block_products'), get_string('tryforfree', 'block_products')));
    $headingsetting->add(new admin_setting_configtext('block_products_home/buttononeurl',get_string('buttononeurl','block_products'),
    get_string('buttononeurl','block_products'),'', PARAM_URL));
    $headingsetting->add(new admin_setting_configtext('block_products_home/buttontwohed',get_string('buttontwohed','block_products'),
    get_string('buttontwohed','block_products'), get_string('talk', 'block_products')));
    $headingsetting->add(new admin_setting_configtext('block_products_home/buttonurltwo',get_string('buttonurltwo','block_products'),
    get_string('buttonurltwo','block_products'),'', PARAM_URL));
    $ADMIN->add('block_products_topcat', $headingsetting);

    // LMS setting
    $columnonesettings = new admin_settingpage('block_products_home_rowone', get_string('lms', 'block_products'));
    $columnonesettings->add(new admin_setting_configtext('block_products_one/heading1', get_string('heading1', 'block_products'), get_string('heading1', 'block_products'),
    get_string('lms', 'block_products'), PARAM_TEXT,null));
    $columnonesettings->add(new admin_setting_configtext('block_products_one/Content1', get_string('content1', 'block_products'), get_string('content1', 'block_products'),
    get_string('content', 'block_products'), PARAM_TEXT, null));
    $name = 'block_products/lmscard1image';
    $title = get_string('uploadimage', 'block_products') ;
    $columnonesettings->add(new admin_setting_configstoredfile($name, $title, '', 'lmscard1image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card1lmsheading', get_string('card1heading', 'block_products'), get_string('card1heading', 'block_products'), '', PARAM_TEXT,
    null));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card1lmscontent', get_string('card1content', 'block_products'), get_string('card1content', 'block_products'),
    get_string('cardcontent', 'block_products'), PARAM_TEXT, null));

    $name = 'block_products/lmscard2image';
    $columnonesettings->add(new admin_setting_configstoredfile($name, $title, '', 'lmscard2image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card2lmsheading', get_string('card2heading', 'block_products'), get_string('card2heading', 'block_products'), '', PARAM_TEXT,
    null));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card2lmscontent', get_string('card2content', 'block_products'), get_string('card2content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/lmscard3image';
    $columnonesettings->add(new admin_setting_configstoredfile($name, $title, '', 'lmscard3image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card3lmsheading', get_string('card3heading', 'block_products'), get_string('card3heading', 'block_products'), '', PARAM_TEXT,
    null));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card3lmscontent', get_string('card3content', 'block_products'), get_string('card3content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/lmscard4image';
    $columnonesettings->add(new admin_setting_configstoredfile($name, $title, '', 'lmscard4image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card4lmsheading', get_string('card4heading', 'block_products'), get_string('card4heading', 'block_products'), '', PARAM_TEXT,
    null));
    $columnonesettings->add (new admin_setting_configtext('block_products_one/card4lmscontent', get_string('card4content', 'block_products'), get_string('card4content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));
    $ADMIN->add('block_products_topcat', $columnonesettings);

    // Industry Connect
    $rowtwo = new admin_settingpage('block_products_home_two', get_string('industry', 'block_products'));
    $rowtwo->add(new admin_setting_configtext('block_products_two/heading1', get_string('heading1', 'block_products'),
    get_string('heading1', 'block_products'), get_string('industry', 'block_products'), PARAM_TEXT,null));
    $rowtwo->add(new admin_setting_configtext('block_products_two/Content1', get_string('content1', 'block_products'),get_string('content1', 'block_products'),
    get_string('content', 'block_products'), PARAM_TEXT, null));
    $name = 'block_products/industrycard1image';
    $title = 'Upload image' ;
    $rowtwo->add(new admin_setting_configstoredfile($name, $title, '', 'industrycard1image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card1lmsheading', get_string('card1heading', 'block_products'), get_string('card1heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card1lmscontent', get_string('card1content', 'block_products'), get_string('card1content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/industrycard2image';
    $rowtwo->add(new admin_setting_configstoredfile($name, $title, '', 'industrycard2image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card2lmsheading', get_string('card2heading', 'block_products'), get_string('card2heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card2lmscontent', get_string('card2content', 'block_products'), get_string('card2content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/industrycard3image';
    $rowtwo->add(new admin_setting_configstoredfile($name, $title, '', 'industrycard3image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card3lmsheading', get_string('card3heading', 'block_products'), get_string('card3heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card3lmscontent', get_string('card3content', 'block_products'), get_string('card3content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/industrycard4image';
    $rowtwo->add(new admin_setting_configstoredfile($name, $title, '', 'industrycard4image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card4lmsheading', get_string('card4heading', 'block_products'), get_string('card4heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowtwo->add (new admin_setting_configtext('block_products_two/card4lmscontent', get_string('card4content', 'block_products'), get_string('card4content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));
    $ADMIN->add('block_products_topcat', $rowtwo);

    // Marketplace
    $rowthree = new admin_settingpage('block_products_home_three', 'Marketplace');
    $rowthree->add(new admin_setting_configtext('block_products_three/heading1', get_string('heading1', 'block_products'),
    get_string('heading1', 'block_products'), get_string('marketplace', 'block_products'), PARAM_TEXT,null));
    $rowthree->add(new admin_setting_configtext('block_products_three/Content1', get_string('content1', 'block_products'),
    get_string('content1', 'block_products'), get_string('content', 'block_products'), PARAM_TEXT, null));
    $name = 'block_products/mktcard1image';
    $title = 'Upload image' ;
    $rowthree->add(new admin_setting_configstoredfile($name, $title, '', 'mktcard1image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowthree->add (new admin_setting_configtext('block_products_three/card1lmsheading', get_string('card1heading', 'block_products'),
    get_string('card1heading', 'block_products'), '', PARAM_TEXT, null));
    $rowthree->add (new admin_setting_configtext('block_products_three/card1lmscontent', get_string('card1content', 'block_products'), get_string('card1content', 'block_products'),
    get_string('cardcontent', 'block_products'), PARAM_TEXT, null));

    $name = 'block_products/mktcard2image';
    $rowthree->add(new admin_setting_configstoredfile($name, $title, '', 'mktcard2image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowthree->add (new admin_setting_configtext('block_products_three/card2lmsheading', get_string('card2heading', 'block_products'), get_string('card2heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowthree->add (new admin_setting_configtext('block_products_three/card2lmscontent', get_string('card2content', 'block_products'),
    get_string('card2content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT, null));

    $name = 'block_products/mktcard3image';
    $rowthree->add(new admin_setting_configstoredfile($name, $title, '', 'mktcard3image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowthree->add (new admin_setting_configtext('block_products_three/card3lmsheading', get_string('card3heading', 'block_products'),
    get_string('card3heading', 'block_products'), '', PARAM_TEXT, null));
    $rowthree->add (new admin_setting_configtext('block_products_three/card3lmscontent', get_string('card3content', 'block_products'),
    get_string('card3content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT, null));

    $name = 'block_products/mktcard4image';
    $rowthree->add(new admin_setting_configstoredfile($name, $title, '', 'mktcard4image', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowthree->add (new admin_setting_configtext('block_products_three/card4lmsheading', get_string('card4heading', 'block_products'), get_string('card4heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowthree->add (new admin_setting_configtext('block_products_three/card4lmscontent',
    get_string('card4content', 'block_products'), get_string('card4content', 'block_products'),
    get_string('cardcontent', 'block_products'), PARAM_TEXT, null));
    $ADMIN->add('block_products_topcat', $rowthree);

    // Virtual labs
    $rowfour = new admin_settingpage('block_products_home_four', 'Virtual Labs');
    $rowfour->add(new admin_setting_configtext('block_products_four/heading1',get_string('heading1', 'block_products'),
    get_string('heading1', 'block_products'), get_string('virtualabs', 'block_products'), PARAM_TEXT,null));
    $rowfour->add(new admin_setting_configtext('block_products_four/Content1',get_string('content1', 'block_products'),
    get_string('content1', 'block_products'),get_string('content', 'block_products'), PARAM_TEXT, null));
    $name = 'block_products/virtualcard1';
    $title = 'Upload image' ;
    $rowfour->add(new admin_setting_configstoredfile($name, $title, '', 'virtualcard1', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowfour->add (new admin_setting_configtext('block_products_four/card1lmsheading',
    get_string('card1heading', 'block_products'), get_string('card1heading', 'block_products'), '', PARAM_TEXT, null));
    $rowfour->add (new admin_setting_configtext('block_products_four/card1lmscontent', get_string('card1content', 'block_products'),
    get_string('card1content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/virtualcard2';
    $rowfour->add(new admin_setting_configstoredfile($name, $title, '', 'virtualcard2', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowfour->add (new admin_setting_configtext('block_products_four/card2lmsheading', get_string('card2heading', 'block_products'), get_string('card2heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowfour->add (new admin_setting_configtext('block_products_four/card2lmscontent', get_string('card2content', 'block_products'),
    get_string('card2content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/virtualcard3';
    $rowfour->add(new admin_setting_configstoredfile($name, $title, '', 'virtualcard3', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowfour->add (new admin_setting_configtext('block_products_four/card3lmsheading', get_string('card3heading', 'block_products'),
    get_string('card3heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowfour->add (new admin_setting_configtext('block_products_four/card3lmscontent', 'Card3content', 'Card3content', get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));

    $name = 'block_products/virtualcard4';
    $rowfour->add(new admin_setting_configstoredfile($name, $title, '', 'virtualcard4', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image'))));
    $rowfour->add (new admin_setting_configtext('block_products_four/card4lmsheading', get_string('card4heading', 'block_products'), get_string('card4heading', 'block_products'), '', PARAM_TEXT,
    null));
    $rowfour->add (new admin_setting_configtext('block_products_four/card4lmscontent', get_string('card4content', 'block_products'), get_string('card4content', 'block_products'), get_string('cardcontent', 'block_products'), PARAM_TEXT,
    null));
    $ADMIN->add('block_products_topcat', $rowfour);
 }

    // if ($ADMIN->fulltree) {
    //     $settings->add (new admin_setting_configtext('block_products/heading', 'Heading', 'Heading', get_string('products', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/content', 'Content', 'Content', get_string('content', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/heading1', 'Heading1', 'Heading1', get_string('lms', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/content1', 'Content1', 'Content1', get_string('content', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/heading2', 'Heading2', 'Heading2', get_string('industry', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/content2', 'Content2', 'Content2', get_string('content', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/heading3', 'Heading3', 'Heading3', get_string('marketplace', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/content3', 'Content3', 'Content3', get_string('content', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/heading4', 'Heading4', 'Heading4', get_string('virtualabs', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/content4', 'Content4', 'Content4', get_string('content', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card1heading', 'Card1heading', 'Card1heading', get_string('business', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card1content', 'Card1content', 'Card1content', get_string('cardcontent', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card2heading', 'Card2heading', 'Card2heading', get_string('business', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card2content', 'Card2content', 'Card2content', get_string('cardcontent', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card3heading', 'Card3heading', 'Card3heading', get_string('business', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card3content', 'Card3content', 'Card3content', get_string('cardcontent', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card4heading', 'Card4heading', 'Card4heading', get_string('business', 'block_products'), PARAM_TEXT,
    //     null));
    //     $settings->add (new admin_setting_configtext('block_products/card4content', 'Card4content', 'Card4content', get_string('cardcontent', 'block_products'), PARAM_TEXT,
    //     null));
       
    // }