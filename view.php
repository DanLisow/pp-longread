<?php
require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('longread', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$longread = $DB->get_record('longread', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/longread/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($longread->name));
$PAGE->set_heading($course->fullname);

$parts = json_decode($longread->content, true);
if (empty($parts)) {
    print_error('nolongreadcontent', 'mod_longread');
}
$partcount = count($parts);
$firstpart = $parts[0];

$firstpart = file_rewrite_pluginfile_urls(
    $firstpart,
    'pluginfile.php',
    $context->id,
    'mod_longread',
    'content',
    $longread->id
);

$options = array(
    'noclean' => true,
    'overflowdiv' => true,
    'context' => $context
);
$firstpart = format_text($firstpart, $longread->contentformat, $options);

$PAGE->requires->js_call_amd('mod_longread/loader', 'init', [$longread->id, $partcount]);
$PAGE->requires->js_call_amd('mod_longread/animation', 'init');
$PAGE->requires->js_call_amd('mod_longread/progress', 'init', [$partcount, 1]);
$PAGE->requires->css(new moodle_url('/mod/longread/css/custom.css'));

echo $OUTPUT->header();
// echo $OUTPUT->heading(format_string($longread->name));


echo html_writer::start_div('longread-block');
echo html_writer::start_div('longread-progress');
echo html_writer::tag('p', "Прогресс");
echo html_writer::tag('div', html_writer::div('', '', ['id' => 'longread-progress-bar']), ['class' => 'longread-progress-container']);
echo html_writer::end_div();

echo html_writer::start_div('longread-container');
echo html_writer::tag('h2', format_string($longread->name));
echo html_writer::div($firstpart, 'longread-part visible', ['id' => 'longread-part-1']);
echo html_writer::div('', 'longread-content', ['id' => 'longread-content']);

// Добавление контента через AJAX
echo html_writer::end_div();
echo html_writer::end_div();

echo $OUTPUT->footer();