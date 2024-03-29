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

    /* Create a course using CM backing
     * 
     * @param $id  cm_course.id of course to create 
     * @return bool
     */
    public static function cm_create_course($id) {
        global $DB;
        $retval = false;
        $table = 'cm_course';

        if (course_management::do_make_cshell($id)) {
            try {

                $sql = 'SELECT id FROM {'.$table.'} WHERE id = ? OR courseshort = (SELECT courseshort FROM {'.$table.'} WHERE id = ?)';
                $cmcourse = $DB->get_records_sql($sql,array($id,$id));

                foreach($cmcourse as $cmrec) {
                    course_management::do_set_active($cmrec->id);
                }
                $retval = true; 

            } catch (Exception $e) {
                throw new Exception('Error doing post setup of course cmid:'.$id, 0, $e);
            } 

        }
        return ($retval);
    }

    /* Create a Meta course using CM backing
     * 
     */
    public static function cm_create_metacourse($metareq) {
        global $DB, $USER;
        $retval = false;
        $table = 'cm_course';

        $ccarray = $metareq->childarray;

        $sql = 'SELECT * FROM {'.$table.'} WHERE id  = ?';
        $c1_rec = $DB->get_record_sql($sql,array($ccarray[0]));

        $termid = $c1_rec->termcode;

        try {
            $metacourse = course_management::do_make_metacourse($metareq,$termid);
            course_management::do_meta_enrol($metacourse,$ccarray);
            course_management::do_set_metause($ccarray);
            $retval = true;
        } catch (Exception $e) {
            throw new Exception('Error: ['.$termid.']['.$USER->username.'] '.$e, 0, $e);
        }
        return($retval);
    }

    /* Add the known enrolment to the course
     * 
     * @param $id cm_course.id of course
     * @return ?bool?
     */
    public static function cm_add_enrolment($id) {
    	global $DB, $USER;
       
        $courseid = $id;
        $cmcourse = $DB->get_record('cm_course',array('id'=>$id));

        try { 

            $pop = course_management::get_enrolment($cmcourse->courseshort);
            course_management::do_enrol_users($pop, $courseid);

        } catch (Exception $e) {
            throw new Exception('Error Enrollment Failed:'.$cmcourse->courseshort, 0, $e);
        }
    }

    /* Validate the string data that comes in from form
     * 
     * @param stdClass  // form values
     * @return array[]  // containing error messages
     */
    public function cm_valid_metareq($input) {
        
        // explode the input obj
        $intitle = $input->metaname;
        $incrumb = $input->breadcrumb;
        
        if (!course_management::is_breadcrumb($incrumb)) {
            $errs[] = course_management::_s('er_bcrumb');
        }
   
        if (!course_management::is_title($intitle)) {
            $errs[] = course_management::_s('er_mname');
        }

        return ($errs);
    } 

    /* Get array of current terms
     *
     * @param $type  string  [full || short]
     * @return $list object
     */
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
    public static function get_course_list($t) {
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
    public static function get_course_list_f($t) {
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
    public static function get_course_list_a($t) {
        global $DB, $USER;

        $termcode = $t; 
        $table = 'cm_course';
        $user = $USER->username; 

        $sql = 'SELECT id,coursefull FROM {'.$table.'} WHERE active = ? AND termcode = ? AND instructor = ?';
        $array = array(1,$termcode,$user);
        
        $c_list = $DB->get_records_sql_menu($sql,$array);
        
        return ($c_list);
    }

    // get key pair array of active = 1 and metause = 0 courses 
    public static function get_course_list_meta_a($t) {
        global $DB, $USER;

        $termcode = $t; 
        $table = 'cm_course';
        $user = $USER->username; 

        $sql = 'SELECT id,coursefull FROM {'.$table.'} WHERE active = ? AND metause = ? AND termcode = ? AND instructor = ?';
        $array = array(1,0,$termcode,$user);
        
        $c_list = $DB->get_records_sql_menu($sql,$array);
        
        return ($c_list);
    }

    public static function get_enrolment($courseshort) {
        global $DB;
        $courseshort = $courseshort;
        $table = 'cm_enrollment';
        $sql = 'SELECT id,username FROM {'.$table.'} WHERE courseshort = ?'; 
        $result = $DB->get_records_sql($sql,array($courseshort));
        return ($result);
    }
 
    /* Verify breadcrumb input 
     * 
     * @param String   // breadcrumb string 
     * @return bool
     */
    private static function is_breadcrumb($str) {
        $retval = false;

        $length = 15;
        $regex  = '/^[A-Za-z0-9]+$/';

        if (strlen($str) <= $length) {
            if (preg_match($regex, $str)) {
                $retval = true;
            }
        }
        return ($retval);
    }

    /* Verify title input 
     * 
     * @param String   // title string 
     * @return bool
     */
    private static function is_title($str) {
        $retval = false;

        $length = 45;
        $regex  = '/^[A-Za-z0-9\s]+$/';

        if (strlen($str) <= $length) {
            if (preg_match($regex, $str)) {
                $retval = true;
            }
        }
        return ($retval);
    }
    
    private static function do_enrol_users($pop, $id) {
        global $DB;

        try { 
            $sql = 'SELECT * FROM {course} WHERE idnumber = (SELECT courseshort FROM {cm_course} WHERE id = ?)';
            $course = $DB->get_record_sql($sql,array($id));
            $role = $DB->get_record('role',array('shortname'=>'student')); 
            $plugin = enrol_get_plugin('manual');
            $plugin->add_instance($course);
            $instance = $DB->get_record('enrol',array('courseid'=>$course->id,'enrol'=>'manual'));
        
            foreach ($pop as $enrole) {
                $user = $DB->get_record('user',array('username'=>$enrole->username));
                $plugin->enrol_user($instance, $user->id, $role->id);
            } 

        } catch (Exception $e) {
            throw new Exception('Error adding enrollment set:'.$course->shortname, 0, $e);
        }

    }

    /* Add child courses to the meta parent
     *
     * @param $parent Parent course object
     * @param $ccarray Array of cm_course id to add as children
     * @return 
     */
    private static function do_meta_enrol($parent,$ccarray) {
        global $DB;

        try {
            $enrol = enrol_get_plugin('meta');
            foreach ($ccarray as $cmchild) {
                $sql = 'SELECT * FROM {course} WHERE idnumber = (SELECT courseshort FROM {cm_course} WHERE id = ?)';
                $childcourse = $DB->get_record_sql($sql,array($cmchild));
                $eid = $enrol->add_instance($parent, array('customint1'=>$childcourse->id));
            }
            enrol_meta_sync($parent->id);
        } catch (Exception $e) {
            throw new Exception ('[[ERROR]] faild to add child course'.$e,0,$e);
        }
    }

    /* Set the active flag on cm record
     *
     */
    private static function do_set_active($id) {
        global $DB;
        $table = 'cm_course';
        
        $data_record = new stdClass;
        $data_record->id = (int)$id;
        $data_record->active = 1;
        
        $retval = $DB->update_record($table,$data_record, $bulk=false);
        
        return ($retval);
    }

    /* Set the metause flag on the cm records
     *
     * @param array of cm_course.id
     * @return bool
     */
    private static function do_set_metause($array) {
        global $DB;
        $table = 'cm_course';
        
        foreach ($array as $id) { 
            $data_record = new stdClass;
            $data_record->id = (int)$id;
            $data_record->metause = 1;
        
            $retval = $DB->update_record($table,$data_record, $bulk=false);
        }
        
        return ($retval);
    }
 
    static function do_make_cshell($id) {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot .'/course/lib.php');

        $retval = FALSE;
        $table = 'cm_course';

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
    
 
    static function do_make_metacourse($metaobj,$termid) {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot .'/course/lib.php');
 
        $retval = false;
        $table = 'cm_course';

        $in_title    = $metaobj->titlestring;
        $in_bcrumb   = $metaobj->breadcrumb;
        $in_children = $metaobj->childarray;
        
        $termstring = course_management::get_term_name($termid);

        $course_full  = $termstring . " " . $USER->username . " Meta " . $in_title;
        $course_short = $termid . '-' . $USER->username . "-meta-" . $in_bcrumb;
        $course_short = strtoupper($course_short);
        $course_id    = $course_short;
        $course_id    = str_replace('-', '', $course_id);

        // Check for a previous instance of this course
        $sql = 'SELECT * FROM {course} WHERE shortname = ?';
        $array = array($course_short);
        $course = $DB->get_record_sql($sql,$array);
        
        if (!$course) {
            
            $term_category = $DB->get_record('course_categories',array('idnumber'=>"$termid"));
        
            $new_meta = new stdClass;

            $new_meta->category   = $term_category->id;
            $new_meta->fullname   = $course_full;
            $new_meta->shortname  = $course_short;
            $new_meta->idnumber   = $course_id;
            $new_meta->format     = "weeks";
            $new_meta->startdate  = time();
            $new_meta->maxbytes   = "52428800";
            $new_meta->visible    = 0;
            $new_meta->visibleold = 0;
            
            try {
                $new_course = create_course($new_meta);
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
                $retval = $new_course;
            } catch (Exception $e) {
                throw new Exception ('[Error] creating course: '.
                  ' '.$course_full.' '.$course_short.' '.$term_category->id.' ',0,
                $e);
            }
            
        } 
        
        return($retval);
    }

}
?>
