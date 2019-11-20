<?php

use local_activitychooser\minimize_form;
defined('MOODLE_INTERNAL') || die;

function local_activitychooser_before_footer() {
    global $PAGE;

    $PAGE->requires->js_call_amd('local_activitychooser/main', 'init');
}

/**
 * Load the minimal mform
 *
 * @param $args
 *
 * @return string
 * @throws dml_exception
 * @throws moodle_exception
 */
function local_activitychooser_output_fragment_minimal_form($args){

    $form = new minimize_form($args['name'], $args['courseid'], $args['section']);

    return $form->render();

}