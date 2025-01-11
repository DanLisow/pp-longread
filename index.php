<?php
require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$course = get_course($id);
require_login($course);

$PAGE->set_url('/mod/longread/index.php', array('id' => $id));
$PAGE->set_title(get_string('modulenameplural', 'mod_longread'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_longread'));

$longreads = $DB->get_records('longread', ['course' => $course->id]);

if (!$longreads) {
    echo $OUTPUT->notification(get_string('nothingtodisplay'));
} else {
    foreach ($longreads as $lr) {
        $link = new moodle_url('/mod/longread/view.php', ['id' => $lr->id]);
        echo html_writer::link($link, format_string($lr->name)) . html_writer::empty_tag('br');
    }
}

echo $OUTPUT->footer();
