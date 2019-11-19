<?php

$functions = [
    'local_activitychooser_toggle_starred' => [
        'classname' => 'local_activitychooser\\ajax\\toggle_starred',
        'methodname' => 'service',
        'description' => 'Add or remove a favorite activity',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_activitychooser_get_activites' => [
        'classname' => 'local_activitychooser\\ajax\\get_activities',
        'methodname' => 'service',
        'description' => 'Returns all activities and resources in respect to favorited items',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_activitychooser_change_starred_sortorder' => [
        'classname' => 'local_activitychooser\\ajax\\change_starred_sortorder',
        'methodname' => 'service',
        'description' => 'Reorder an activity in favourites list',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_activitychooser_render_form' => [
        'classname' => 'local_activitychooser\\ajax\\get_rendered_form',
        'methodname' => 'service',
        'description' => 'Render a form ',
        'type' => 'read',
        'ajax' => true,
    ],
];

// During the plugin installation/upgrade, Moodle installs these services as pre-build services.
// A pre-build service is not editable by administrator.
$services = [];