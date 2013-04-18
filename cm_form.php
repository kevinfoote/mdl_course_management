<?php
/**
 * form for user to select term and course 
*/
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

class cm_form extends moodleform {

    function definition_after_data() {
    }

    function set_data() {
    }

    public function definition() {
        global $CFG, $USER; 
        $CM_DEBUG=FALSE;

        $available = course_management::_s('courselist');
        $term_list = course_management::get_term_list('short');

        $mform =& $this->_form;

        $available_heading = '<div style="text-align:left;float:none">'
            .'<b>'.course_management::_s('t_available').'</b></div>'
            .course_management::_s('break1');
        $mform->addElement('html' , $available_heading);

        foreach ($term_list as $term) {
            $termname = course_management::get_term_name($term);
            $course_menu = course_management::get_course_list_f($term);
            $term_heading = '<div style="text-align:left;margin-left:20px;float:none">'
                           .'&nbsp;&nbsp;&nbsp;&nbsp;'
                           .'<b>'.course_management::_s('termtitle').' :: '.$termname.'</b></div>';
            $mform->addElement('html', $term_heading);

            if (count($course_menu) < 1) {
                $term_nocourse = course_management::_s('break2')
                    .'<div style="text-align:center;margin-left:50px;color:red;float:none">'
                    .course_management::_s('nocourse').'</div>';
                $mform->addElement('html' , $term_nocourse);
            } else {
                foreach ($course_menu as $id=>$course) {
                    $mform->addElement('advcheckbox', $id, null, $course, array('group'=>(int)$term));
                }
                $this->add_checkbox_controller((int)$term);
            }
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
        $buttons[] =& $mform->createElement('submit', 'createb', course_management::_s('c_create'));
        $buttons[] =& $mform->createElement('reset', 'resetb', course_management::_s('cmrevert'));
        $mform->addGroup($buttons, 'buttons', null, array(' '), false);

        $mform->addElement('html' , course_management::_s('break2'));

        $previous_heading = '<div style="text-align:left;float:none">'
            .'<b>'.course_management::_s('t_previous').'</b></div>';
        $mform->addElement('html' , $previous_heading);

        $meta_info = course_management::_s('break2') 
            .'<div style="text-align:left;float:none">'
            .course_management::_s('meta_intro').'</div><br>';

        $mform->addElement('html' , $meta_info);

        //
        // TODO: add textbox here for name of meta to create 
        //
        foreach ($term_list as $term) {
            $course_menu = course_management::get_course_list_a($term);
            if(count($course_menu) >= 1) {
                foreach ($course_menu as $id=>$course) {
                    $mform->addElement('advcheckbox', $id, null, $course, null,null);
                }
            } 
        }
        $buttons2 = array();
        $buttons2[] =& $mform->createElement('submit', 'createb', course_management::_s('m_create'));
        $buttons2[] =& $mform->createElement('reset', 'resetb', course_management::_s('cmrevert'));
        $mform->addGroup($buttons2, 'buttons', null, array(' '), false);
        $mform->closeHeaderBefore('buttons');
    }
    
}
?>
