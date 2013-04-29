<?php
/**
 * form for user to select term and course 
*/
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

class cm_form_meta extends moodleform {

    public function definition() {
        global $CFG, $USER; 
        $CM_DEBUG = FALSE;

        $available = course_management::_s('courselist');
        $term_list = course_management::get_term_list('short');

        $mform =& $this->_form;

        if (!$term_list) { 

            $no_cmload = course_management::_s('break2')
                .'<div class="warning" style="text-align:center;margin-left:50px;color:red;float:none">'
                .course_management::_s('cm_noload').'</div>';
            $mform->addElement('html' , $no_cmload);

        } else { 

            $mform->addElement('html' , course_management::_s('break2'));

            $previous_heading = '<div style="text-align:left;float:none">'
                .'<b>'.course_management::_s('t_previous').'</b></div>';
            $mform->addElement('html' , $previous_heading);

            $meta_info = course_management::_s('break2') 
                .'<div style="text-align:left;float:none">'
                .course_management::_s('meta_intro').'</div><br>';

            $mform->addElement('html' , $meta_info);

            $mform->addElement('text','metaname',course_management::_s('meta_name'),array('size'=>'48'));

            $mform->addElement('text','breadcrumb',course_management::_s('meta_bread'),array('size'=>'17'));

            $mform->addHelpButton('metaname', 'metanameabout', 'block_course_management');

            $mform->addHelpButton('breadcrumb', 'metabcabout', 'block_course_management');

            foreach ($term_list as $term) {
                $course_menu = course_management::get_course_list_a($term);
                if(count($course_menu) >= 1) {
                    foreach ($course_menu as $id=>$course) {
                        $mform->addElement('advcheckbox', $id, null, $course, null,null);
                    }
                } 
            }
            $buttons2 = array();
            $buttons2[] =& $mform->createElement('submit', 'createmeta', course_management::_s('cm_metareq'));
            $buttons2[] =& $mform->createElement('reset', 'resetb', course_management::_s('cm_revert'));
            $mform->addGroup($buttons2, 'buttons', null, array(' '), false);
            $mform->closeHeaderBefore('buttons');
        }
    }
    
    function validation() {
        return TRUE;
    }

    function definition_after_data() {
    }

    function set_data() {
    }

}
?>
