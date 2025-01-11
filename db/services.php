<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_longread_get_part' => [
        'classname' => 'mod_longread\external\get_part',
        'methodname' => 'get_part',
        'description' => 'Creates new groups.',
        'classpath' => 'mod/longread/classes/external/get_part.php',
        'type' => 'read',
        'ajax' => true,
        'services' => [
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ],
    ],
];

$services = [
    'Longread service' => [
        'functions' => ['mod_longread_get_part'],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'longreadservice'
    ]
];