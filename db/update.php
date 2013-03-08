<?php
 
function xmldb_cm_upgrade($oldversion) {
    global $CFG;
    
    $result = TRUE;
 
// Insert PHP code from XMLDB Editor here
if ($oldversion < 2013020100) {

        // Define table cm_course to be created
        $table = new xmldb_table('cm_course');

        // Adding fields to table cm_course
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseshort', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('coursefull', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('instructor', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('term', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table cm_course
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cm_course
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table cm_enrollment to be created
        $table = new xmldb_table('cm_enrollment');

        // Adding fields to table cm_enrollment
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('username', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseshort', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table cm_enrollment
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cm_enrollment
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // course_management savepoint reached
        upgrade_block_savepoint(true, $version, 'course_management');
    }

 if ($oldversion < 2013020100.01) {

        // Define table cm_term to be created
        $table = new xmldb_table('cm_term');

        // Adding fields to table cm_term
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('termcode', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('termname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table cm_term
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for cm_term
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // course_management savepoint reached
        upgrade_block_savepoint(true, XXXXXXXXXX, 'course_management');
    }
 
    return $result;
}
?>
