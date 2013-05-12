<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the editing form for the sc_flashcard question type.
 *
 * @package    qtype
 * @subpackage sc_flashcard
 * @copyright  Alex Leontiev (alozz1991@gmail.com)
 * @author alozz1991@gmail.com

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/question/type/edit_question_form.php');


/**
 * sc_flashcard question editing form definition - it defines what teacher sees when he or she
 * creates/edits existing question.
 *
 * @copyright  2013 Alex Leontiev (alozz1991@gmail.com)
 * @author alozz1991@gmail.com

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sc_flashcard_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
	debugging("definition_inner");
        //$this->add_interactive_settings();
        $mform->addElement('editor', 'answerForm',
                get_string('answer', 'qtype_sc_flashcard'), array('rows' => 10), $this->editoroptions);
        $mform->setType('answerForm', PARAM_RAW);
    }

    /** This function is also called when editing. It sets the default values
     */
    protected function data_preprocessing($question) {
	    debugging("data_preprocessing with options ".print_r($question->options,true));
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function qtype() {
	debugging('qtype');
        return 'sc_flashcard';
    }
}
