<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/longread/lib.php');
require_once($CFG->libdir . '/filelib.php');

class mod_longread_mod_form extends moodleform_mod
{
    function definition()
    {
        $mform = $this->_form;


        $mform->addElement('text', 'name', get_string('longreadname', 'mod_longread'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');


        $this->standard_intro_elements();

        $mform->addElement(
            'editor',
            'content_editor',
            get_string('content', 'mod_longread'),
            null,
            longread_get_editor_options($this->context)
        );
        $mform->setType('content_editor', PARAM_RAW);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    public function data_preprocessing(&$defaultvalues)
    {
        global $DB;

        if ($this->current->instance) {
            $longread = $DB->get_record('longread', ['id' => $this->current->instance], '*', MUST_EXIST);
            $defaultvalues['content'] = $this->merge_parts(json_decode($longread->content, true));
            $defaultvalues['contentformat'] = $longread->contentformat;

            $draftitemid = file_get_submitted_draft_itemid('content');
            $defaultvalues['content'] = file_prepare_draft_area(
                $draftitemid,
                $this->context->id,
                'mod_longread',
                'content',
                $longread->id,
                longread_get_editor_options($this->context),
                $this->merge_parts(json_decode($longread->content, true))
            );

            $defaultvalues['content_editor'] = [
                'text' => $defaultvalues['content'],
                'format' => $defaultvalues['contentformat'],
                'itemid' => $draftitemid
            ];
        }
    }

    /**
     * Объединяет части контента в один текст с разделителем.
     *
     * @param array $parts Массив частей контента.
     * @return string Объединённый текст.
     */
    private function merge_parts($parts)
    {
        if (empty($parts)) {
            return '';
        }
        return implode("\n\n&lt;!--PART--&gt;\n\n", $parts);
    }
}
