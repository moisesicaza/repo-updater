<?php
namespace RUpdater;

class SettingsPage
{
    private $options;
    private $available_themes;
    private $available_repositories;

    /**
     * SettingsPage constructor.
     */
    private function __construct() {
        // Initialize values
        $this->available_repositories = Store::REPOSITORIES;
        $this->available_themes = Store::get_available_themes();

        // Add hooks
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
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
            'themes',
            __( 'Available themes', R_UPDATER_CONTEXT ),
            array( $this, 'themes_callback' ),
            'repo-updater',
            'setting_section'
        );

        add_settings_field(
            'repositories',
            __( 'Available repositories', R_UPDATER_CONTEXT ),
            array( $this, 'repositories_callback' ),
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

        if( isset( $input['repositories'] ) )
            $new_input['repositories'] = sanitize_text_field( $input['repositories'] );

        if( isset( $input['themes'] ) )
            $new_input['themes'] = sanitize_text_field( $input['themes'] );

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
        Input::text( 'username', '_r_updater[username]', $this->options['username'] );
    }

    /**
     * Renders the input field for the repository password
     */
    public function password_callback() {
        Input::password( 'password', '_r_updater[password]', $this->options['password'] );
    }

    /**
     * Renders the input field for the repository access token
     */
    public function token_callback() {
        Input::text( 'token', '_r_updater[token]', $this->options['token'] );
    }

    /**
     * Renders the list of available repositories in a selection field
     */
    public function repositories_callback() {
        Input::select( 'repositories', '_r_updater[repositories]', $this->available_repositories, $this->options['repositories'] );
    }

    /**
     * Renders the list of available themes in a selection field
     */
    public function themes_callback() {
        $themes = array_column( $this->available_themes, 'name', 'stylesheet' );
        Input::select( 'themes', '_r_updater[themes]', $themes, $this->options['themes'] );
        Input::description( __( 'Be sure to select a theme that corresponds to the released in the repository', R_UPDATER_CONTEXT ) );
    }
}