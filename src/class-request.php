<?php
namespace RUpdater;

use Exception;

class Request {
    private function __construct() {}

    /**
     * It checks the authentication type to assign this header to the request.
     * At the moment it only allows basic or token authentication.
     *
     * @param string $authentication_type Authentication type name (basic, token).
     * @param array $credentials Application credentials.
     * @example
     *
     *  $credentials = [
     *    'username'=> get_option('ru_github_username'),
     *    'password' => get_option('ru_github_password'),
     *    'access_token'=> get_option('ru_github_access_token'),
     * ];
     *
     * @return array
     */
    private static function getHeaders(string $authentication_type, array $credentials) {
        $headers      = [ 'Accept' => 'application/json' ];
        $username     = $credentials['username'] ?? false;
        $password     = $credentials['password'] ?? false;
        $access_token = $credentials['access_token'] ?? false;

        switch ($authentication_type) {
            case 'basic':
                $headers['Authorization'] = $username && $password
                    ? "Basic ". base64_encode( "$username:$password" )
                    : '';
            break;
            case 'token':
                $headers['Authorization'] = $access_token
                    ? "Bearer $access_token"
                    : '';
            break;
        }

        return $headers;
    }

    /**
     * It makes the request to a given resource from the application and returns the results.
     * In case of a failure it raises an exception.
     *
     * @param string $resource Application endpoint to be consulted.
     * @param array $headers Application headers.
     * @return array
     * @throws Exception
     */
    protected static function requestHandler(string $resource, array $headers) {
        $response = wp_remote_get( esc_url_raw( $resource ), [
            'headers' => $headers,
        ]);

        if ( is_wp_error( $response ) ) {
            throw new Exception( $response->get_error_message() );
        } else {
            return json_decode( wp_remote_retrieve_body( $response ), true );
        }
    }

    /**
     * Public method that allows to make a request of a url established as a parameter.
     *
     * @param array $credentials Application credentials.
     * @param string $resource Application endpoint to be consulted.
     * @param string $authentication_type Authentication type name (basic, token).
     * @return array
     */
    public static function sendRequest(array $credentials, string $resource, string $authentication_type) {
        try {
            $headers = Request::getHeaders($authentication_type, $credentials);
            return Request::requestHandler($resource, $headers);

        } catch (Exception $e) {
            printf('Error: %s', $e->getMessage());
        }
    }
}