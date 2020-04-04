<?php
namespace RUpdater;

class Store {
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
}