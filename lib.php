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

    public function get_term_list() {
       $t_list = array('Spring 2012 - 201250',
                       'Summer 2012 - 201310',
                       'Fall 2013 - 201340');
       return ($t_list);
    } 

//    public function get_course_list() {
//        $c_list = array('Spring 2012 EDUC 112 001', 
//                        'Spring 2012 EDUC 421 016', 
//                        'Spring 2012 EDUC 421 018', 
//                        'Spring 2012 EDUC 445 002');
//        return ($c_list);
//    }

    static function get_course_list() {
        global $DB;

        // SELECT coursefull FROM mdl_cm_course WHERE instructor=$user AND active=0;
        //get_records_select($table, $select, array $params=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0

        // get_fieldset_select($table, $return, $select, array $params=null)

        $user = $USER->username; 
        $return = 'coursefull';
        $select = 'instructor='.$user.' AND active=0';
        $param = null; 

        $table = 'cm_course';
        
        $c_list = $DB->get_fieldsset_select($table, $return, $select, $param);
        
        return ($c_list);
        
    }
}
?>
