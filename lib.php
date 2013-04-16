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

        // SELECT coursefull FROM mdl_cm_course WHERE instructor=$user AND active=0;
        // get_fieldset_select($table, $return, $select, array $params=null)

        //$sql = 'SELECT coursefull FROM {cm_course} WHERE active = ? AND term = ? AND instructor = ?';
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

        // SELECT id,coursefull FROM mdl_cm_course WHERE active=0 AND term='201310' AND instructor='kpfoote';
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

        // SELECT id,coursefull FROM mdl_cm_course WHERE active=0 AND term='201310' AND instructor='kpfoote';
        $sql = 'SELECT id,coursefull FROM {'.$table.'} WHERE active = ? AND termcode = ? AND instructor = ?';
        $array = array(1,$termcode,$user);
        
        $c_list = $DB->get_records_sql_menu($sql,$array);
        
        return ($c_list);
    }

    static function get_enrollment($courseshort) {
        return;
    }
    
    static function do_set_active($courseshort) {
        return;
    }
 
    static function do_make_cshell($id) {
        global $DB, $CFG;
        require_once($CFG->dirroot .'/course/lib.php');

        $retval = FALSE;
        $table = 'cm_course';

        $sql = 'SELECT * FROM {'.$table.'} WHERE id = ?';
        $in_data = $DB->get_record_sql($sql,array($id)); 

        $enrollment = $enrollment;
        $meta = 0;

        foreach ($item as $id=>$values) {
            $course_full  = $values[0];
            $course_short = $values[1];
            $course_inst  = $values[2];
            $course_term  = $values[3];
            $course_actv  = $values[4];
        }

        $sql = 'SELECT * FROM {course} WHERE shortname = ?';
        $array = array($course_short);
        $course = $DB->get_record_sql($sql,$array);
        
        if( !$course ) {

            // DO create the course
            $new_cshell->category = 'Courses';        //NEED TO PULL this from mdl_course_categories.name match of mdl_term.termname
            $new_cshell->fullname = "$course_full";   //NEED TO GET 
            $new_cshell->shortname = "$course_short"; //NEED TO GET
            $new_cshell->idnumber = "$course_short";  //NEED TO GET
            $new_cshell->format = "weeks";
            $new_cshell->startdate = time();  //need this so weekly outline will display correctly
            $new_cshell->maxbytes = "52428800"; //50mb uploads IRT new default
            $new_cshell->visible = 0;
            $new_cshell->visibleold = 0;
            //$new_cshell->numsections = 1;
            //$new_cshell->enrollable = 0;
            //$new_cshell->numsections = "15"; //15 weeks
            //$new_cshell->metacourse = $meta;
 
            if (!$course = create_course($new_cshell)) {
                echo "ERROR CREATING COURSE";
            }
        }
       
        // DO add the initial enrollment 
        $sql = 'SELECT * FROM {course} WHERE shortname = ?';
        $array = array($course_short);
        $course = $DB->get_record_sql($sql,$array);

        if ($DB->get_record_sql($sql,$array)) {
            $retval = TRUE;
        }

        return ($retval);
    }
    
    static function cm_create_course($id) {
        global $DB;

        // if (do_make_cshell(parm,parm)) 
        //     mark cm_course as created
        if (do_make_cshell($id)) {
            // now add the enrollment data
            if (do_add_enrollment($id)) {
                $retval = TRUE; 
            }
        }
        return ($retval)
    }
 
    static function do_make_metashell($courseshort) {
        $meta = 1;
        return;
    }
}
?>
