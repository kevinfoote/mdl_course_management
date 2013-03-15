<?php
/**
 * form for user to select term and course 
*/
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

class cm_form extends moodleform {


    function definition() {
        global $CFG, $USER; 
        $CM_DEBUG=FALSE;

        $available = course_management::_s('courselist');
        $term_list = course_management::get_term_list('short');

        $mform =& $this->_form;

        $form_div = '<div style="text-align:center;width: 40%">';

        $mform->addElement('html' , $form_div);

        foreach ($term_list as $term) {
            $termname = course_management::get_term_name($term);
            $course_menu = course_management::get_course_list_f($term);
            $term_heading = '<div style="text-align:left">'
                           .'&nbsp;&nbsp;&nbsp;&nbsp;'
                           .'<b>Term :: '.$termname.'</b>';
            $mform->addElement('html', $term_heading);

            if (count($course_menu) < 1) {
                $term_nocourse = '<br><br>'
                    .'<div style="color:red;text-align:center">'
                    .course_management::_s('nocourse').'</div>';
                $mform->addElement('html' , $term_nocourse);
            } else {
                foreach ($course_menu as $id=>$course) {
                    $mform->addElement('advcheckbox', $id, null, $course, array('group'=>(int)$term));
                }
                $this->add_checkbox_controller((int)$term);
            }
            $mform->addElement('html' , '</div>');
        }


        if($CM_DEBUG) {
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
        }

        $buttons = array();
        $buttons[] =& $mform->createElement('submit', 'createb', course_management::_s('cmcreate'));
        $buttons[] =& $mform->createElement('reset', 'resetb', course_management::_s('cmrevert'));
        
        $mform->addGroup($buttons, 'buttons', null, array(' '), false);
        // end the form_div
        $mform->addElement('html' , '</div>');
        // TODO: meta-course form bits should go here
        $mform->closeHeaderBefore('buttons');
    }
}
?>
