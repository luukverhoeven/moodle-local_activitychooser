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

namespace local_activitychooser\ajax;

use local_activitychooser\minimize_form;

/**
 * Class get_rendered_form
 */
class get_rendered_form extends \external_api {

    /**
     * @return \external_function_parameters
     */
    public static function service_parameters() {

        return new \external_function_parameters(
            [
                'courseid' => new \external_value(PARAM_INT, 'Course id', VALUE_REQUIRED),
                'section' => new \external_value(PARAM_INT, 'Section', VALUE_OPTIONAL),
                'name' => new \external_value(PARAM_TEXT, 'Name', VALUE_OPTIONAL),
            ]
        );
    }

    /**
     * @return \external_single_structure
     */
    public static function service_returns() {
        return
            new \external_single_structure(
                [
                    'mod_form' => new \external_value(PARAM_RAW, 'id of activity in modules table', VALUE_REQUIRED),
                ]
            );
    }

    public static function service($courseid, $section, $name) {

        $form = new minimize_form($name, $courseid, $section);

        return [
            'mod_form' => $form->get_form()
        ];

    }
}