<?php
namespace RUpdater;

class Store {
    const REPOSITORIES = [
        'github' => 'Github',
        'gitlab' => 'Gitlab'
    ];

    /**
     * Returns stored credentials from the Github repository.
     *
     * @return array
     */
    public static function get_github_credential() {
        return [
            'username' => get_option( 'ru_github_username' ),
            'password' => get_option( 'ru_github_password' ),
            'access_token' => get_option( 'ru_github_access_token' ),
        ];
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