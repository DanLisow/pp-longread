<?php
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");

/**
 * Возвращает параметры для редактора в модуле Longread.
 *
 * @param context $context Контекст.
 * @return array Параметры редактора.
 */
function longread_get_editor_options($context)
{
    global $CFG;
    return [
        'subdirs' => 1,
        'maxbytes' => $CFG->maxbytes,
        'maxfiles' => -1,
        'changeformat' => 1,
        'context' => $context,
        'noclean' => 1,
        'trusttext' => 0
    ];
}

/**
 * Добавление нового экземпляра модуля
 */
function longread_add_instance($data, $mform = null)
{
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    $content = $data->content_editor;
    $data->content = json_encode(explode_parts($content['text']));
    $data->contentformat = $content['format'];
    $data->id = $DB->insert_record('longread', $data);

    $context = context_module::instance($data->coursemodule);
    file_save_draft_area_files(
        $content['itemid'],
        $context->id,
        'mod_longread',
        'content',
        $data->id,
        longread_get_editor_options($context)
    );

    return $data->id;
}

function longread_update_instance($data, $mform = null)
{
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    $content = $data->content_editor;
    $data->content = json_encode(explode_parts($content['text']));
    $data->contentformat = $content['format'];

    $DB->update_record('longread', $data);

    $context = context_module::instance($data->coursemodule);
    file_save_draft_area_files(
        $content['itemid'],
        $context->id,
        'mod_longread',
        'content',
        0,
        longread_get_editor_options($context),
        $data->content
    );

    return true;
}

/**
 * Разделяет контент на части по разделителю <!--PART-->.
 *
 * @param string $content Полный текст с разделителями.
 * @return array Массив частей контента.
 */
function explode_parts($content)
{
    return array_filter(array_map('trim', explode('&lt;!--PART--&gt;', $content)));
}

function longread_delete_instance($id)
{
    global $DB;

    if (!$longread = $DB->get_record('longread', array('id' => $id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('longread', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'longread', $id, null);

    $DB->delete_records('longread', array('id' => $longread->id));

    return true;
}

/**
 * Serve the files from mod_longread file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool|void
 */
function mod_longread_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = [])
{
    global $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    // if (!has_capability('mod/longread:view', $context)) {
    //     return false;
    // }

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_longread', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}