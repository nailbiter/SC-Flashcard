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
 * Question type class for the sc_flashcard question type.
 *
 * @package    qtype
 * @subpackage sc_flashcard
 * @copyright  Alex Leontiev (alozz1991@gmail.com)
 * @author alozz1991@gmail.com

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/sc_flashcard/question.php');


/**
 * The SC-Flashcard question type.
 *
 * @copyright  Alex Leontiev (alozz1991@gmail.com)
 * @author alozz1991@gmail.com

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sc_flashcard extends question_type {

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    /** We get here when we save question after editing. It should save
     * everything to DB.
     */
    public function save_question_options($question) {
        global $DB;
        $context = $question->context;

        // Fetch old answer ids so that we can reuse them
        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // Save the true answer - update an existing answer if possible.
        $answer = array_shift($oldanswers);
        if (!$answer) {
            $answer = new stdClass();
            $answer->question = $question->id;
            $answer->answer = '';
            $answer->feedback = '';
            $answer->id = $DB->insert_record('question_answers', $answer);
        }

        $answer->answer   = $question->answerForm['text'];
        $answer->fraction = 1;//FIXME: is that wrong?
        //I don't know what these commands do, so I'll comment them and see
        //what will happen :)
        /*$answer->feedback = $this->import_or_save_files($question->feedbacktrue,
                $context, 'question', 'answerfeedback', $answer->id);
        $answer->feedbackformat = $question->feedbacktrue['format'];*/
        if(!false){
            debugging("answer: ".print_r($answer,true));
            $this->save_hints($question);
        }
        $id=$DB->update_record('question_answers', $answer);

        // Delete any left over old answer records.
        /*$fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }*/

        if(!true){
            return true;
        }

        // Save question options in question_truefalse table
        if ($options = $DB->get_record('question_sc_flashcard', array('question' => $question->id))) {
            // No need to do anything, since the answer IDs won't have changed
            // But we'll do it anyway, just for robustness
            $options->answer  = $id;
            $DB->update_record('question_sc_flashcard', $options);
        } else {
            $options = new stdClass();
            $options->question    = $question->id;
            $options->answer  = $id;
            $DB->insert_record('question_sc_flashcard', $options);
        }

        $this->save_hints($question);

        return true;
    }

    public function get_question_options($question) {
        global $DB, $OUTPUT;
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = $DB->get_record('question_sc_flashcard',
                array('question' => $question->id))) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }
        // Load the answers
        if (!$question->options->answers = $DB->get_records('question_answers',
                array('question' =>  $question->id), 'id ASC')) {
            echo $OUTPUT->notification('Error: Missing question answers for sc_flashcard question ' .
                    $question->id . '!');
            return false;
        }

        return true;
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        debugging("hell from initialise");
        parent::initialise_question_instance($question, $questiondata);
        $answer = array_shift($questiondata->options->answers);
        $question->answertext=$answer->answer;
    }

    public function get_random_guess_score($questiondata) {
        // TODOs.
        return 0;
    }

    public function get_possible_responses($questiondata) {
        return array(
            $questiondata->id => array(
                0 => new question_possible_response($questiondata->options[
                $questiondata->options->answer]->answer,1),
                null => question_possible_response::no_response()
            )
        );
    }
}
