<?php
/**
 * form for user to select term and course 
*/
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

class cm_form extends moodleform {

    function definition() {
        global $CFG, $USER; 

        $available = course_management::_s('courselist');

        $term_list = course_management::get_term_list('short');
        $course_menu = course_management::get_course_list_f('201250');
        $course_list = course_management::get_course_list('201250');

        $mform =& $this->_form;
        
        foreach ($course_menu as $id=>$course) {
            $mform->addElement('advcheckbox', $id, null, $course, array('group'=>(int)'201250'));
        }
        $this->add_checkbox_controller((int)'201250');

        $mform->addElement('select', 'course', $available, $course_list);

        foreach ($term_list as $t) {
            echo "$t ";
        }
        echo "<br>";
        foreach ($course_menu as $id=>$val) {
            echo "$id $val";
        }
        echo "<br>";
        foreach ($course_list as $course) {
            echo $course;
        }

        $buttons = array();
        $buttons[] =& $mform->createElement('submit', 'createb', course_management::_s('cmcreate'));
        $buttons[] =& $mform->createElement('reset', 'resetb', course_management::_s('cmrevert'));
        $mform->addGroup($buttons, 'buttons', course_management::_s('action'), array(' '), false);
        //$mform->closeHeaderBefore('button');
    }
}
?>
