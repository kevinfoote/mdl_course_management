<?php
/**
 *
 * @package    block
 * @subpackage course_management 
 * @author iup.edu
 */

class block_course_management extends block_base {
    public function init() {
        $this->title = get_string('cm', 'block_course_management');
    }

     /**
     * Limits where the block can be added.
     **/
    function applicable_formats() {
        return array('site' => false, 'my' => true, 'course' => false);
    }
    
    /**
    * Allow only one instance 
    */
    public function instance_allow_multiple() {
        return false;
    }

    public function get_content() {
        global $USER, $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->items = array();
        $this->content->icons = array();

        if (empty($this->instance)) {
            return $this->content;
        }

        $this->content->text = '<div class="cmblock"><a href="'.$CFG->wwwroot.'/blocks/course_management/cm.php">'.get_string('cmcreate','block_course_management').'</a></div>';
        return $this->content;
    }
}
?>

