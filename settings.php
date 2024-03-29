<?php
/**
 * @package    block
 * @subpackage course_management 
 * @author kpfoote[at]iup.edu
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

        $settings->add(
          new admin_setting_heading(
                "cm/plugindescheader",
                get_string("plugindescheader", "block_course_management"),
                get_string("plugindescription", "block_course_management")
          )
        );

        if (!isset($plugin->version)
          || !isset($plugin->cm_release)
          || !isset($plugin->requires)) {
                $version_file = dirname(__FILE__) . "/version.php";
                if (is_readable($version_file)) {
                        include($version_file);
                }
        }

        if (isset($plugin->version)) {
                $settings->add(
                  new admin_setting_heading(
                        "cm/pluginversionheader",
                        get_string("pluginversionheader", "block_course_management"),
                        "$plugin->version ($plugin->cm_release)"
                  )
                );
        }

//        if (isset($plugin->cm_latest) && $plugin->cm_latest < $CFG->version) {
//                        $warning = get_string("upgradewarning", "block_course_management");
//                        $warning .= $plugin->cm_latest;
//                        $settings->add(
//                          new admin_setting_heading(
//                                "cm/upgradewarningheader",
//                                get_string("upgradewarningheader", "block_course_management"),
//                                $warning
//                          )
//                        );
//        }

        $settings->add(
          new admin_setting_heading(
                "cm/adminsettingsheader",
                get_string("adminsettingsheader", "block_course_management"),
                get_string("noadminsettings", "block_course_management")
          )
        );

}

?>
