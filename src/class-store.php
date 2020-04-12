<?php
namespace RUpdater;

class Store {
    const REPOSITORIES = [
        'github' => 'Github',
        'gitlab' => 'Gitlab'
    ];

    const AUTH_TYPES = [
        'basic' => 'User and password',
        'token' => 'By access token'
    ];

    /**
     * Returns the plugin settings stored in the database.
     *
     * @return array|bool
     */
    public static function get_settings() {
        return get_option( '_r_updater' );
    }

    /**
     * Returns stored repository credentials.
     *
     * @return bool|array
     */
    public static function get_credentials() {
        $option = Store::get_settings();

        if ( !$option ) return false;

        return [
            'token' => $option['token'] ?? '',
            'username' => $option['username'] ?? '',
            'password' => $option['password'] ?? '',
        ];
    }

    /**
     * Returns the name of the repository.
     *
     * @return bool|string
     */
    public static function get_repository_name() {
        $option = Store::get_settings();

        if ( !$option ) return false;

        return $option['repository_name'];
    }

    /**
     * Returns the selected repository.
     *
     * @return bool|string
     */
    public static function get_selected_repository() {
        $option = Store::get_settings();

        if ( !$option ) return false;

        return $option['repository'];
    }

    /**
     * Gets the list of available themes in the WordPress installation.
     *
     * @example
     * array (
     *  'fake-theme' => array (
     *      'name' => 'Fake Theme',
     *      'update' => false,
     *      'version' => '1.0.0',
     *      'stylesheet' => 'fake-theme',
     *      'path' => '/var/www/wp-test/wp-content/themes/fake-theme',
     *   ),
     * )
     *
     * @return array|bool
     */
    public static function get_available_themes() {
        $themes = wp_get_themes();

        if( !is_array( $themes ) ) return [];

        return array_map( function( $theme ) {
            return [
                'name' => $theme->get('Name'),
                'update' => $theme->get('updated'),
                'version' => $theme->get('Version'),
                'stylesheet' => $theme->get_stylesheet(),
                'path' => $theme->get_stylesheet_directory(),
            ];
        }, $themes );
    }
}