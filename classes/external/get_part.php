<?php
namespace mod_longread\external;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use context_module;

class get_part extends external_api
{

    public static function get_part_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'longreadid' => new external_value(PARAM_INT, 'ID of longread', VALUE_REQUIRED),
            'part' => new external_value(PARAM_INT, 'Part number', VALUE_REQUIRED),
        ]);
    }

    public static function get_part_returns(): external_single_structure
    {
        return new external_single_structure([
            'content' => new external_value(PARAM_RAW, 'HTML of the requested part'),
            'error' => new external_value(PARAM_BOOL, 'Error flag', VALUE_DEFAULT, false)
        ]);
    }

    public static function get_part($longreadid, $part)
    {
        global $DB, $CFG;

        $longread = $DB->get_record('longread', ['id' => $longreadid], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('longread', $longread->id, $longread->course, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/longread:view', $context);

        $parts = json_decode($longread->content, true);
        if (!isset($parts[$part - 1])) {
            return ['content' => '', 'error' => true];
        }

        $options = array(
            'noclean' => true,
            'overflowdiv' => true,
            'context' => $context
        );

        $formatted = file_rewrite_pluginfile_urls($parts[$part - 1], 'pluginfile.php', $context->id, 'mod_longread', 'content', $longread->id);

        return ['content' => $formatted, 'error' => false];
    }
}
