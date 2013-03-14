<?php
/**
 * form for user to select term and course 
*/
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

class cm_form extends moodleform {


    function definition() {
        global $CFG, $USER; 
        $CM_DEBUG=TRUE;

        $available = course_management::_s('courselist');
        $term_list = course_management::get_term_list('short');

        $mform =& $this->_form;

        $html_table = '<table align="center" border="1" cellspacing="0" cellpadding="0">';
        $mform->addElement('html',$html_table);
        
        foreach ($term_list as $term) {
            $termname = course_management::get_term_name($term);
            $course_menu = course_management::get_course_list_f($term);
            $table_row = '<tr><th align="left">'.$termname.'</th></tr>'
                 .'<tr><td align="center">';
            $mform->addElement('html', $table_row);
            foreach ($course_menu as $id=>$course) {
                $mform->addElement('advcheckbox', $id, null, $course, array('group'=>(int)$term));
            }
            $this->add_checkbox_controller((int)$term);
            $mform->addElement('html','</td></tr>');
        }

        $mform->addElement('html','</table>');

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
        $mform->addGroup($buttons, 'buttons', course_management::_s('action'), array(' '), false);
        //$mform->closeHeaderBefore('button');
    }
}
?>
