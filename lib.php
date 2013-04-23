<?php
/**
 * @package    block
 * @subpackage course_management 
 * @author kpfoote[at]iup.edu
 */

abstract class cm_b {
    abstract static function pluginname();

    static function is_iup() {
        global $CFG;
        return isset($CFG->is_iup) and $CFG->is_iup;
    }

    public static function _s($key, $a = null) {
        $class = get_called_class();

        return get_string($key, $class::pluginname(), $a);
    }

    /**
     * Shorten locally called string even more
     */
    public static function gen_str() {
        $class = get_called_class();

        return function ($key, $a = null) use ($class) {
            return get_string($key, $class::pluginname(), $a);
        };
    }
}

abstract class course_management extends cm_b {
    static function pluginname() {
        return 'block_course_management';
    }

    // get array of current terms 
    //   incomming: string full OR short 
    public function get_term_list($type) {
        global $DB;

        $table = 'cm_term';
        $return = 'termcode';

        if ($type == 'full') {
            $return = 'termname';
        } 
        if ($type == 'short') {
            $return = 'termcode';
        } 

        $sql = 'SELECT '.$return.' FROM {'.$table.'} WHERE active = ?';
        $array = array(1);
        $t_list = $DB->get_fieldset_sql($sql,$array);
        return ($t_list);
    } 

    // get the full name of the term from cm_term
    //   in: termcode
    //   return: termname
    public function get_term_name($t) {
        global $DB;
        
        $table = 'cm_term';
        $sql = 'SELECT termname FROM {'.$table.'} WHERE termcode = ?';
        $array = array($t);

        $tname = $DB->get_field_sql($sql,$array);
        return ($tname);
    }

    // get array of courses 
    static function get_course_list($t) {
        global $DB, $USER;

        $termcode = $t; 
        $table = 'cm_course';
        $user = $USER->username; 

        $sql = 'SELECT coursefull FROM {'.$table.'} WHERE active = ? AND termcode = ? AND instructor = ?';
        $array = array(0,$termcode,$user);
        
        $c_list = $DB->get_fieldset_sql($sql,$array);
        
        return ($c_list);
    }
    
    // get key pair array of courses for setting up a checkbox UI
    static function get_course_list_f($t) {
        global $DB, $USER;

        $termcode = $t; 
        $table = 'cm_course';
        $user = $USER->username; 

        $sql = 'SELECT id,coursefull FROM {'.$table.'} WHERE active = ? AND termcode = ? AND instructor = ?';
        $array = array(0,$termcode,$user);
        
        $c_list = $DB->get_records_sql_menu($sql,$array);
        
        return ($c_list);
    }

    // get key pair array of active courses for setting up a checkbox UI
    static function get_course_list_a($t) {
        global $DB, $USER;

        $termcode = $t; 
        $table = 'cm_course';
        $user = $USER->username; 

        $sql = 'SELECT id,coursefull FROM {'.$table.'} WHERE active = ? AND termcode = ? AND instructor = ?';
        $array = array(1,$termcode,$user);
        
        $c_list = $DB->get_records_sql_menu($sql,$array);
        
        return ($c_list);
    }

    static function get_enrollment($courseshort) {
        global $DB;
        $retval = false;
        return ($retval);
    }
    
    static function do_set_enrollment($id) {
        global $DB;
        $retval = false;
        return ($retval);
    }

    static function do_set_active($id) {
        global $DB;
        $table = 'cm_course';
        
        $data_record = new stdClass;
        $data_record->id = (int)$id;
        $data_record->active = 1;
        
        $retval = $DB->update_record($table,$data_record, $bulk=false);
        
        return ($retval);
    }
 
    static function do_make_cshell($id) {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot .'/course/lib.php');

        $retval = FALSE;
        $table = 'cm_course';
        $meta = 0;

        $sql = 'SELECT * FROM {'.$table.'} WHERE id = ?';
        $cm_data = $DB->get_record_sql($sql,array($id)); 

        $course_full  = $cm_data->coursefull;
        $course_short = $cm_data->courseshort;
        $course_inst  = $cm_data->instructor;
        $course_term  = $cm_data->termcode;
        $course_actv  = $cm_data->active;

        // Check for a previous instance of this course
        $sql = 'SELECT * FROM {course} WHERE shortname = ?';
        $array = array($course_short);
        $course = $DB->get_record_sql($sql,$array);
        
        if (!$course && $course_actv == 0) {
            
            $sql = 'SELECT id FROM {course_categories} WHERE idnumber = (SELECT termcode FROM {cm_course} WHERE id =?)';
            $cterm = $DB->get_record_sql($sql,array($id));

            // DO create the course
            $new_cshell->category   = $cterm->id;       
            $new_cshell->fullname   = "$course_full";   
            $new_cshell->shortname  = "$course_short"; 
            $new_cshell->idnumber   = "$course_short";  
            $new_cshell->format     = "weeks";
            $new_cshell->startdate  = time();     // need this so weekly outline will display correctly
            $new_cshell->maxbytes   = "52428800"; // This should be a plugin:settings var
            $new_cshell->visible    = 0;
            $new_cshell->visibleold = 0;

	    try { 
                $new_course = create_course($new_cshell);
                $role = $DB->get_record_sql('SELECT * FROM {role} WHERE shortname = ?',array('editingteacher'));
                $coursecontext = context_course::instance($new_course->id);
                role_assign($role->id,$USER->id,$coursecontext->id);
                $enrolplugin = enrol_get_plugin('manual');
                $enrolplugin->add_instance($new_course);
                $enrolinstances = enrol_get_instances($new_course->id, false);
                foreach ($enrolinstances as $enrolinstance) {
                    if ($enrolinstance->enrol === 'manual') {
                        break;
                    }
                }
                $enrolplugin->enrol_user($enrolinstance, $USER->id);
                $retval = true;
            } catch (Exception $e) {
                throw new Exception ('Error creating course:'.$course_short, 0, $e);
            }
        }

        return ($retval);
    }
    
    static function cm_create_course($id) {
        global $DB;
        $retval = false;

        if (course_management::do_make_cshell($id)) {
            try {
                course_management::do_set_active($id);
                course_management::do_set_enrollment($id);
                $retval = true; 
            } catch (Exception $e) {
                throw new Exception('Error doing post setup of course cmid:'.$id, 0, $e);
            } 
        }
        return ($retval);
    }
 
    static function do_make_metashell($courseshort) {
        $meta = 1;
        return;
    }
}
?>
