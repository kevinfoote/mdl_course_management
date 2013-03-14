<?php
/**
 *
 * @package    block
 * @subpackage course_management 
 * @author iup.edu
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('block_course_management.php');
include_once("lib.php");

global $CFG, $USER;

//Check user have logged in to the system or not
require_login(0, false);
require_once('cm_form.php');

// create and get our select form
$cm = new cm_form();
$site = get_system_context();

if ($cm->is_cancelled()) {
    // cancelled forms redirect back to /my/
    redirect("$CFG->wwwroot/my/");
} else if ($data = $cm->get_data()) { 
    // DO data processing etc..
    $blockname = course_management::_s('blockname');
    $header = course_management::_s('cmcreate');

    // setup page components 
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
    $PAGE->navbar->add(get_string('myhome'), new moodle_url('/my/'));
    $PAGE->navbar->add($blockname);
    $PAGE->set_title($blockname . ': '. $header);
    $PAGE->set_heading($blockname . ': '.$header);
    $PAGE->set_url('/blocks/course_management/cm.php');
    $PAGE->set_pagelayout('standard');


    // draw page to screen
    echo $OUTPUT->header();
    echo $OUTPUT->heading($blockname);
    // CONTENT
    echo '<p>Hello '.$USER->firstname.' '.$USER->lastname.' ('.$USER->username.').</p>';
    echo '<p>Use the below form to select a term and course to create.</p>';

//    foreach ($warnings as $type => $warning) {
//        $class = ($type == 'success') ? 'notifysuccess' : 'notifyproblem';
//        echo $OUTPUT->notification($warning, $class);
//    }

    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    $cm->display();

    echo '<p>Here is what you selected previously.</p>';
    foreach ($data as $id=>$val) {
        echo "$id is $val";
        echo '<br>';
    } 

    echo html_writer::end_tag('div');
    echo $OUTPUT->footer();

} else {
    
    $blockname = course_management::_s('blockname');
    $header = course_management::_s('cmcreate');

    // setup page components 
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
    $PAGE->navbar->add(get_string('myhome'), new moodle_url('/my/'));
    $PAGE->navbar->add($blockname);
    $PAGE->set_title($blockname . ': '. $header);
    $PAGE->set_heading($blockname . ': '.$header);
    $PAGE->set_url('/blocks/course_management/cm.php');
    $PAGE->set_pagelayout('standard');


    // draw page to screen
    echo $OUTPUT->header();
    echo $OUTPUT->heading($blockname);
    // CONTENT
    echo '<p>Hello '.$USER->firstname.' '.$USER->lastname.' ('.$USER->username.').</p>';
    echo '<p>Use the below form to select a term and course to create.</p>';

//    foreach ($warnings as $type => $warning) {
//        $class = ($type == 'success') ? 'notifysuccess' : 'notifyproblem';
//        echo $OUTPUT->notification($warning, $class);
//    }

    echo html_writer::start_tag('div', array('class' => 'no-overflow'));
    $cm->display();
    echo html_writer::end_tag('div');

    echo $OUTPUT->footer();
}

?>
