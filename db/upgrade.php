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
 * Upgrade library code for the truefalse question type.
 *
 * @package    qtype
 * @subpackage truefalse
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

function mylog($line){
    $myFile = "test.txt";
    $fh = fopen($myFile, 'a') or die("can't open file");
    fwrite($fh, $line);
    fclose($fh);
}

function xmldb_qtype_sc_flashcard_upgrade($oldversion = 0) {
        global $DB;
        $dbman = $DB->get_manager();

        $result = true;
        //mylog("hi from upgrade 1");
        if ($oldversion < 20130510604) {
        //mylog("hi from upgrade 2");

            // Define table question_sc_flashcard to be created
            $table = new xmldb_table('question_sc_flashcard');

            // Adding fields to table question_sc_flashcard
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('answer', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

            // Adding keys to table question_sc_flashcard
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));

            // Conditionally launch create table for question_sc_flashcard
            if (!$dbman->table_exists($table)) {
                $dbman->create_table($table);
            }

            // sc_flashcard savepoint reached
            upgrade_plugin_savepoint(true, 20130510604, 'qtype', 'sc_flashcard');
        }
        //mylog("hi from upgrade 3");
        return $result;
}


/**
 * Class for converting attempt data for truefalse questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/*class qtype_truefalse_qe2_attempt_updater extends question_qtype_attempt_updater {
    public function right_answer() {
        foreach ($this->question->options->answers as $ans) {
            if ($ans->fraction > 0.999) {
                return $ans->answer;
            }
        }
    }

    public function response_summary($state) {
        if (is_numeric($state->answer)) {
            if (array_key_exists($state->answer, $this->question->options->answers)) {
                return $this->question->options->answers[$state->answer]->answer;
            } else {
                $this->logger->log_assumption("Dealing with a place where the
                        student selected a choice that was later deleted for
                        true/false question {$this->question->id}");
                return null;
            }
        } else {
            return null;
        }
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function set_first_step_data_elements($state, &$data) {
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        if (is_numeric($state->answer)) {
            $data['answer'] = (int) ($state->answer == $this->question->options->trueanswer);
        }
    }
}*/
