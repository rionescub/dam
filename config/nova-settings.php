<?php

return [

    /**
     * Set a name for the settings table
     */
    'table' => 'nova_settings',

    /**
     * URL path of settings page
     */
    'base_path' => 'nova-settings',

    /**
     * Reload the entire page on save. Useful when updating any Nova UI related settings.
     */
    'reload_page_on_save' => false,

    /**
     * We need to know which eloquent model should be used to retrieve your permissions.
     *
     * Our settings are team scoped, so the package model is replaced by one that
     * always constrains queries to the current team.
     */
    'models' => [
        'settings' => \App\Models\TeamSetting::class,
    ],

    /**
     * Show the sidebar menu
     */
    'show_in_sidebar' => true,
];
