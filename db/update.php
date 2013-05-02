<?php

function xmldb_block_course_management_upgrade($oldversion) {
    global $DB;

    $result = true;

    $dbman = $DB->get_manager();

    if ($oldversion < 2013042900) {

        // Define field metause to be added to cm_course
        $table = new xmldb_table('cm_course');
        $field = new xmldb_field('metause', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'active');

        // Conditionally launch add field metause
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
	
	// Define index cm_course_ix (not unique) to be added to cm_course
        $table = new xmldb_table('cm_course');
        $index = new xmldb_index('cm_course_ix', XMLDB_INDEX_NOTUNIQUE, array('courseshort'));

        // Conditionally launch add index cm_course_ix
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
	
        // Define index cm_instructor_ix (not unique) to be added to cm_course
        $table = new xmldb_table('cm_course');
        $index = new xmldb_index('cm_instructor_ix', XMLDB_INDEX_NOTUNIQUE, array('instructor'));

        // Conditionally launch add index cm_instructor_ix
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // course_management savepoint reached
        upgrade_block_savepoint(true, 2013042900, 'course_management');
    }

    return $result;
}
