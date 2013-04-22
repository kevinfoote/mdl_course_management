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
$CM_DEBUG = FALSE;

//Check user have logged in to the system or not
require_login();

$fulltitle  = course_management::_s('cm_title_f');
$shorttitle = course_management::_s('cm_title_s');
$shortdescr = course_management::_s('cm_shortdes');

// setup page components 
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/course_management/cm.php');
$PAGE->set_title($shorttitle . ': '. $shortdescr);
$PAGE->set_heading($shorttitle . ': '.$shortdescr);
$PAGE->set_pagelayout('standard');
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my/'));
$PAGE->navbar->add($shorttitle);

// draw page to screen
echo $OUTPUT->header();
echo $OUTPUT->heading($fulltitle);

// CONTENT
echo $OUTPUT->container(course_management::_s('break1'));
echo $OUTPUT->container(course_management::_s('cm_intro'));
echo $OUTPUT->container(course_management::_s('break1'));

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
        if ($CM_DEBUG) {
            echo "[dbg] $id is $val<br>";
        }

        if ($val == 1 && is_int($id)) {
            if (!$CM_DEBUG) {
                course_management::cm_create_course($id);
            } else {
                echo "[dbg] operating on $id<br>";
            }
        }
    }

    echo $OUTPUT->container_end();
    
    $cm = new cm_form();

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
