<?php
namespace RUpdater;

class SettingsPage
{
    private $options;
    private $available_themes;
    private $authentication_types;
    private $available_repositories;

    /**
     * SettingsPage constructor.
     */
    private function __construct() {
        // Initialize values
        $this->available_themes = Store::get_available_themes();
        $this->authentication_types = Store::AUTH_TYPES;
        $this->available_repositories = Store::REPOSITORIES;

        // Add hooks
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_js_scripts' ) );
        add_action( 'pre_update_option__r_updater', [ $this, 'on_pre_update' ], 10, 1 );
    }

    /**
     * Run method
     */
    public static function show() {
        if ( is_admin() ) {
            new SettingsPage();
        }
    }

    /**
     * Loads custom JavaScript scripts into admin
     */
    public function add_js_scripts() {
        wp_enqueue_script( 'ru-settings-form', R_UPDATER_ASSETS_PATH . '/js/settings-form.js', array(), null );
    }

    /**
     * Add the page to the admin menu
     */
    public function add_plugin_page() {
        add_menu_page(
            __( 'Repo Updater Settings', R_UPDATER_CONTEXT ),
            __( 'Repo Updater', R_UPDATER_CONTEXT ),
            'manage_options',
            'repo-updater',
            array( $this, 'create_admin_page' ),
            'dashicons-cloud'
        );
    }

    /**
     * Handles the setting form
     */
    public function create_admin_page() {
        $this->options = get_option( '_r_updater' );
        ?>
        <div class="wrap">
            <h1>Repo Updater Settings</h1>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields( '_r_updater' );
                do_settings_sections( 'repo-updater' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Sets up the input fields
     */
    public function page_init() {
        register_setting(
            '_r_updater',
            '_r_updater',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'setting_section',
            __( 'Sync new repository', R_UPDATER_CONTEXT ),
            null,
            'repo-updater'
        );

        add_settings_field(
            'theme',
            __( 'Available themes', R_UPDATER_CONTEXT ),
            array( $this, 'themes_callback' ),
            'repo-updater',
            'setting_section'
        );

        add_settings_field(
            'repository',
            __( 'Available repositories', R_UPDATER_CONTEXT ),
            array( $this, 'repositories_callback' ),
            'repo-updater',
            'setting_section'
        );

        add_settings_field(
            'repository_name',
            __( 'Repository name', R_UPDATER_CONTEXT ),
            array( $this, 'repository_name_callback' ),
            'repo-updater',
            'setting_section'
        );

        add_settings_field(
            'authentication_type',
            __( 'Type of credentials', R_UPDATER_CONTEXT ),
            array( $this, 'authentication_type_callback' ),
            'repo-updater',
            'setting_section'
        );

        add_settings_field(
            'username',
            __( 'Username', R_UPDATER_CONTEXT ),
            array( $this, 'username_callback' ),
            'repo-updater',
            'setting_section'
        );

        add_settings_field(
            'password',
            __( 'Password', R_UPDATER_CONTEXT ),
            array( $this, 'password_callback' ),
            'repo-updater',
            'setting_section'
        );

        add_settings_field(
            'token',
            __( 'Token', R_UPDATER_CONTEXT ),
            array( $this, 'token_callback' ),
            'repo-updater',
            'setting_section'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize( $input ) {
        $new_input = [];

        if( isset( $input['repository'] ) )
            $new_input['repository'] = sanitize_text_field( $input['repository'] );

        if( isset( $input['repository_name'] ) )
            $new_input['repository_name'] = sanitize_text_field( $input['repository_name'] );

        if( isset( $input['theme'] ) )
            $new_input['theme'] = sanitize_text_field( $input['theme'] );

        if( isset( $input['authentication_type'] ) )
            $new_input['authentication_type'] = sanitize_text_field( $input['authentication_type'] );

        if( isset( $input['username'] ) )
            $new_input['username'] = sanitize_text_field( $input['username'] );

        if( isset( $input['password'] ) )
            $new_input['password'] = sanitize_text_field( $input['password'] );

        if( isset( $input['token'] ) )
            $new_input['token'] = sanitize_text_field( $input['token'] );

        return $new_input;
    }

    /**
     * Renders the input field for the repository username
     */
    public function username_callback() {
        Input::text( 'r_updater_username', '_r_updater[username]', $this->options['username'] );
    }

    /**
     * Renders the input field for the repository password
     */
    public function password_callback() {
        Input::password( 'r_updater_password', '_r_updater[password]', $this->options['password'] );
    }

    /**
     * Renders the input field for the repository access token
     */
    public function token_callback() {
        Input::text( 'r_updater_token', '_r_updater[token]', $this->options['token'] );
    }

    /**
     * Renders the input field for the repository name
     */
    public function repository_name_callback() {
        Input::text( 'r_updater_repository_name', '_r_updater[repository_name]', $this->options['repository_name'] );
        Input::description( __( 'Repository names usually are the following format <my username>/<my project> e.g. user01/best-project', R_UPDATER_CONTEXT ) );
    }

    /**
     * Renders the list of available repositories in a selection field
     */
    public function repositories_callback() {
        Input::select( 'r_updater_repositories', '_r_updater[repository]', $this->available_repositories, $this->options['repository'] );
    }

    /**
     * Renders the list of available themes in a selection field
     */
    public function themes_callback() {
        $themes = array_column( $this->available_themes, 'name', 'stylesheet' );
        Input::select( 'r_updater_themes', '_r_updater[theme]', $themes, $this->options['theme'] );
        Input::description( __( 'Be sure to select a theme that corresponds to the released in the repository', R_UPDATER_CONTEXT ) );
    }

    /**
     * Renders the list of available authentication types
     */
    public function authentication_type_callback() {
        Input::select( 'r_updater_auth_types', '_r_updater[authentication_type]', $this->authentication_types, $this->options['authentication_type'] );;
    }

    /**
     * Method to run before saving a WordPress option in the database
     *
     * @param array $value Set of options and their values
     * @return array
     */
    public function on_pre_update( $value ) {
        // Clear credential fields according to authentication type
        switch ( $value['authentication_type'] ) {
            case 'basic':
                $value['token'] = '';
                $value['password'] = base64_encode( $value['password'] ); // Encrypt the password before save
            break;
            case 'token':
                $value['username'] = '';
                $value['password'] = '';
                // TODO Encrypt the token before save
            break;
        }

        return $value;
    }
}