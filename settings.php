<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'longread/someconfig',
        get_string('someconfig', 'mod_longread'),
        get_string('someconfig_desc', 'mod_longread'),
        '',
        PARAM_TEXT
    ));
}
