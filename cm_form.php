<?php
/**
 * form for user to select term and course 
*/
require_once("$CFG->libdir/formslib.php");
require_once('lib.php');

class cm_form extends moodleform {

    function definition() {
        global $CFG, $USER; 

        $available = course_management::_s('courselist');
        // get course listing for user 
        // mock
        $course_list = course_management::get_course_list($USER->username);

        $mform =& $this->_form;
        
        $mform->addElement('select', 'course', $available, $course_list);

        $buttons = array();
        $buttons[] =& $mform->createElement('submit', 'createb', course_management::_s('cmcreate'));
        $buttons[] = &$mform->createElement('reset', 'resetb', course_management::_s('cmrevert'));
        $mform->addGroup($buttons, 'buttons', course_management::_s('action'), array(' '), false);
        //$mform->closeHeaderBefore('button');
    }
}
?>
