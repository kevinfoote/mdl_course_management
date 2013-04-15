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
require_once("lib.php");
require_once('cm_form.php');

global $CFG, $USER;

//Check user have logged in to the system or not
require_login();

$blockname = course_management::_s('blockname');
$header = course_management::_s('cmcreate');

// setup page components 
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/course_management/cm.php');
$PAGE->set_title($blockname . ': '. $header);
$PAGE->set_heading($blockname . ': '.$header);
$PAGE->set_pagelayout('standard');
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my/'));
$PAGE->navbar->add($blockname);

// draw page to screen
echo $OUTPUT->header();
echo $OUTPUT->heading($blockname);

// CONTENT
echo $OUTPUT->container(course_management::_s('break1'));
echo $OUTPUT->container_start(null,null);
echo course_management::_s('cm_greet').' '.$USER->firstname.' '.$USER->lastname;
echo $OUTPUT->container_end();
echo $OUTPUT->container(course_management::_s('break1'));
echo $OUTPUT->container(course_management::_s('cm_intro'));

$cm = new cm_form();

if ($cm->is_cancelled()) {

    redirect("$CFG->wwwroot/my/");

} else if ($data = $cm->get_data()) { 


//    foreach ($warnings as $type => $warning) {
//        $class = ($type == 'success') ? 'notifysuccess' : 'notifyproblem';
//        echo $OUTPUT->notification($warning, $class);
//    }

    echo $OUTPUT->container_start();
    foreach ($data as $id=>$val) {
        // ex: 85 is 1 
        if ($val == 1) {
            course_management::cm_create_course($id);
            echo "create course $id <br>";
        }
    } 
    echo $OUTPUT->container_end();

    echo $OUTPUT->container($cm->display());
    echo $OUTPUT->footer();

} else {

    echo $OUTPUT->container($cm->display());

//    foreach ($warnings as $type => $warning) {
//        $class = ($type == 'success') ? 'notifysuccess' : 'notifyproblem';
//        echo $OUTPUT->notification($warning, $class);
//    }

    echo $OUTPUT->footer();
}

?>
