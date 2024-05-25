<?php

function xmldb_local_assessment_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2023062608) {

        // Define table local_assessment_status to be created.
        $table = new xmldb_table('local_assessment_status');

        // Adding fields to table local_assessment_status.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('assessementid', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('assessmentpartner', XMLDB_TYPE_CHAR, '110', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table local_assessment_status.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_assessment_status.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062608, 'local', 'assessment');
    }
    if ($oldversion < 2023062609) {

        // Define table local_assessment to be created.
        $table = new xmldb_table('local_assessment');

        // Adding fields to table local_assessment.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('partnerassessmentid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('assessmentpartner', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('samlssoid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('link', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('deleted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, null, null, '1');

        // Adding keys to table local_assessment.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_assessment.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_assessment_partner to be created.
        $table = new xmldb_table('local_assessment_partner');

        // Adding fields to table local_assessment_partner.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('samlssoid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('delted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, null, null, '1');
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '15', null, null, null, null);

        // Adding keys to table local_assessment_partner.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_assessment_partner.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Define field userid to be added to local_assessment_status.
        $table = new xmldb_table('local_assessment_status');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null, 'assessementid');

        // Conditionally launch add field userid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $table = new xmldb_table('local_assessment_status');
        $field = new xmldb_field('assessmentpartner');

        // Conditionally launch drop field assessmentpartner.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062609, 'local', 'assessment');
    }
    if ($oldversion < 2023062610) {

        // Rename field deleted on table local_assessment_partner to NEWNAMEGOESHERE.
        $table = new xmldb_table('local_assessment_partner');
        $field = new xmldb_field('delted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'samlssoid');

        // Launch rename field deleted.
        $dbman->rename_field($table, $field, 'deleted');

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062610, 'local', 'assessment');
    }
    if ($oldversion < 2023062611) {

        // Define table careerprep_result to be created.
        $table = new xmldb_table('careerprep_result');

        // Adding fields to table careerprep_result.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('quizid', XMLDB_TYPE_CHAR, '110', null, null, null, null);
        $table->add_field('totalscore', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('correct', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('wrong', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('noattempt', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('quizduration', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('timespent', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('sections', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table careerprep_result.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for careerprep_result.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062611, 'local', 'assessment');
    }
    if ($oldversion < 2023062613) {

        // Define table careerprep_section_result to be created.
        $table = new xmldb_table('careerprep_section_result');

        // Adding fields to table careerprep_section_result.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('sectioned', XMLDB_TYPE_CHAR, '110', null, null, null, null);
        $table->add_field('score', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('questions', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('correct', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('wrong', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('noattempt', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table careerprep_section_result.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for careerprep_section_result.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062613, 'local', 'assessment');
    }
    if ($oldversion < 2023062614) {

        // Define key sectioned (foreign) to be added to careerprep_section_result.
        $table = new xmldb_table('careerprep_section_result');
        $key = new xmldb_key('sectioned', XMLDB_KEY_FOREIGN, ['sectioned'], 'careerprep_result', ['quizid']);

        // Launch add key sectioned.
        $dbman->add_key($table, $key);

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062614, 'local', 'assessment');
    }
    if ($oldversion < 2023062615) {

        // Define field totalquestions to be added to careerprep_result.
        $table = new xmldb_table('careerprep_result');
        $field = new xmldb_field('totalquestions', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'timemodified');

        // Conditionally launch add field totalquestions.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062615, 'local', 'assessment');
    }
    if ($oldversion < 2023062619) {

        // Define field quiztitle to be added to careerprep_result.
        $table = new xmldb_table('careerprep_result');
        $field = new xmldb_field('quiztitle', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'totalquestions');

        // Conditionally launch add field quiztitle.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062619, 'local', 'assessment');
    }
    if ($oldversion < 2023062620) {

        // Define field name to be added to careerprep_section_result.
        $table = new xmldb_table('careerprep_section_result');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'noattempt');

        // Conditionally launch add field name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062620, 'local', 'assessment');
    }
    if ($oldversion < 2023062621) {

        // Changing type of field totalscore on table careerprep_result to number.
        $table = new xmldb_table('careerprep_result');
        $field = new xmldb_field('totalscore', XMLDB_TYPE_NUMBER, '20, 2', null, null, null, null, 'quizid');

        // Launch change of type for field totalscore.
        $dbman->change_field_type($table, $field);

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062621, 'local', 'assessment');
    }
    if ($oldversion < 2023062622) {

        // Changing type of field score on table careerprep_section_result to number.
        $table = new xmldb_table('careerprep_section_result');
        $field = new xmldb_field('score', XMLDB_TYPE_NUMBER, '20, 2', null, null, null, null, 'sectioned');

        // Launch change of type for field score.
        $dbman->change_field_type($table, $field);

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062622, 'local', 'assessment');
    }

    if ($oldversion < 2023062623) {

        // Changing type of field score on table careerprep_section_result to number.
        $table = new xmldb_table('local_assessment_partner');
        $field1 = new xmldb_field('logo', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'visible');
        $field2 = new xmldb_field('parentpayment', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'logo');
        $field3 = new xmldb_field('subaccpayment', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'parentpayment');
        $field4 = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'subaccpayment');

        // Conditionally launch add field quiztitle.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        if (!$dbman->field_exists($table, $field3)) {
            $dbman->add_field($table, $field3);
        }

        if (!$dbman->field_exists($table, $field4)) {
            $dbman->add_field($table, $field4);
        }

        $table1 = new xmldb_table('local_assessment');
        $field5 = new xmldb_field('logo', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null. 'visible');
        $field6 = new xmldb_field('ksdcprice', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'logo');
        $field7 = new xmldb_field('vendorprice', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'ksdcprice');

        // Conditionally launch add field quiztitle.
        if (!$dbman->field_exists($table1, $field5)) {
            $dbman->add_field($table1, $field5);
        }

        if (!$dbman->field_exists($table1, $field6)) {
            $dbman->add_field($table1, $field6);
        }

        if (!$dbman->field_exists($table1, $field7)) {
            $dbman->add_field($table1, $field7);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062623, 'local', 'assessment');
    }
    if ($oldversion < 2023062626) {

        // Define field ksdcprice to be dropped from local_assessment.
        $table = new xmldb_table('local_assessment');
        $field = new xmldb_field('ksdcprice');

        // Conditionally launch drop field ksdcprice.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field vendorprice to be dropped from local_assessment.
        $field = new xmldb_field('vendorprice');

        // Conditionally launch drop field vendorprice.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field paymentpriceid to be added to local_assessment.
        $field = new xmldb_field('paymentpriceid', XMLDB_TYPE_INTEGER, '15', null, null, null, '0', 'logo');

        // Conditionally launch add field paymentpriceid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062626, 'local', 'assessment');
    }
    if ($oldversion < 2023062627) {

        // Define field attempt to be added to careerprep_result.
        $table = new xmldb_table('careerprep_result');
        $field = new xmldb_field('attempt', XMLDB_TYPE_INTEGER, '15', null, null, null, '1', 'correct');

        // Conditionally launch add field attempt.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Define field resultfile to be added to careerprep_result.
        $table = new xmldb_table('careerprep_result');
        $field = new xmldb_field('resultfile', XMLDB_TYPE_INTEGER, '15', null, null, null, '0', 'sections');

        // Conditionally launch add field resultfile.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table local_assessment_user_enrol to be created.
        $table = new xmldb_table('local_assessment_user_enrol');

        // Adding fields to table local_assessment_user_enrol.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('assessmentid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('assessmenttransactionid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_assessment_user_enrol.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_assessment_user_enrol.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_assessment_transaction to be created.
        $table = new xmldb_table('local_assessment_transaction');

        // Adding fields to table local_assessment_transaction.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('assessmentid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('orderid', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('processed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

        // Adding keys to table local_assessment_transaction.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_assessment_transaction.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062627, 'local', 'assessment');
    }
    if ($oldversion < 2023062628) {

        // Define field assessmentid to be added to careerprep_section_result.
        $table = new xmldb_table('careerprep_section_result');
        $field = new xmldb_field('assessmentid', XMLDB_TYPE_INTEGER, '15', null, null, null, null, 'userid');

        // Conditionally launch add field assessmentid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062628, 'local', 'assessment');
    }
    if ($oldversion < 2023062629) {

        // Define field apiendpoint to be added to local_assessment_partner.
        $table = new xmldb_table('local_assessment_partner');
        $field = new xmldb_field('apiendpoint', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timemodified');

        // Conditionally launch add field apiendpoint.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field apitoken to be added to local_assessment_partner.
        $field = new xmldb_field('apitoken', XMLDB_TYPE_CHAR, '200', null, null, null, null, 'apiendpoint');

        // Conditionally launch add field apitoken.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062629, 'local', 'assessment');
    }
    if ($oldversion < 2023062630) {

        // Define table local_asssessment_paid_api to be created.
        $table = new xmldb_table('local_asssessment_paid_api');

        // Adding fields to table local_asssessment_paid_api.
        // Adding fields to table local_asssessment_paid_api.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('orderid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('assessmentid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('assessmentpartnerid', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('payload', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('processed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('message', XMLDB_TYPE_CHAR, '100', null, null, null, null);

        // Adding keys to table local_asssessment_paid_api.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_asssessment_paid_api.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062630, 'local', 'assessment');
    }
    if ($oldversion < 2023062641) {
        // Changing type of field logo on table local_assessment to char.
        $table = new xmldb_table('local_assessment');
        $field = new xmldb_field('logo', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'visible');

        // Launch change of type for field logo.
        $dbman->change_field_type($table, $field);

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062641, 'local', 'assessment');
    }
    if ($oldversion < 2023062642) {

        // Changing type of field logo on table local_assessment_partner to char.
        $table = new xmldb_table('local_assessment_partner');
        $field = new xmldb_field('logo', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'visible');

        // Launch change of type for field logo.
        $dbman->change_field_type($table, $field);

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062642, 'local', 'assessment');
    }
    if ($oldversion < 2023062645) {

        // Define field businessid to be added to local_assessment_partner.
        $table = new xmldb_table('local_assessment_partner');
        $field = new xmldb_field('businessid', XMLDB_TYPE_INTEGER, '15', null, null, null, null, 'apitoken');

        // Conditionally launch add field businessid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Assessment savepoint reached.
        upgrade_plugin_savepoint(true, 2023062645, 'local', 'assessment');
    }

    return true;
}