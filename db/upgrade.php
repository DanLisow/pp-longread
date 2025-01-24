<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_longread_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024120705) {
        // $table = new xmldb_table('longread');

        // // Новое поле content
        // $field = new xmldb_field('content', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'introformat');
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Новое поле contentformat
        // $field = new xmldb_field('contentformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'content');
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        upgrade_mod_savepoint(true, 2024120705, 'longread');
    }

    return true;
}
