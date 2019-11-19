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
 *
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-local_activitychooser
 * @copyright 19/11/2019 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace local_activitychooser;

use block_voucher\pdf\element_factory;
use moodle_url;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

defined('MOODLE_INTERNAL') || die;

/**
 * Class minimize_form
 *
 * @package local_activitychooser
 */
class minimize_form {

    /**
     * @var string
     */
    private $modname;
    /**
     * @var int
     */
    private $courseid;
    /**
     * @var int
     */
    private $section;

    public function __construct(string $modname, int $courseid, int $section) {
        $this->modname = $modname;
        $this->courseid = $courseid;
        $this->section = $section;
    }

    /**
     * @return string
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function get_form() {
        global $CFG, $DB, $PAGE;

        $course = $DB->get_record('course', ['id' => $this->courseid], '*', MUST_EXIST);
        require_login($course);

        require_once($CFG->dirroot . '/course/modlib.php');
        list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $this->modname, $this->section);

        $modmoodleform = "$CFG->dirroot/mod/$module->name/mod_form.php";
        if (!file_exists($modmoodleform)) {
            // Show error.
            print_error('error:missing_form' , 'local_activitychooser');
        }

        require_once($modmoodleform);

        $url = new moodle_url('/course/modedit.php');
        $PAGE->set_url($url);
        $PAGE->set_context($context);

        $data->return = 0;
        $data->sr = $this->section;
        $data->add = $this->modname;

        /** @var \moodleform_mod $mformclassname */
        $mformclassname = 'mod_'.$module->name.'_mod_form';
        $mform = new $mformclassname($data, $cw->section, $cm, $course);
        $mform->set_data($data);

        // TODO render_minimal()
        return $mform->render();
    }

}