<?php
namespace RUpdater;

class SettingsPage
{
    const PAGE_NAME = 'repo-updater';

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
            SettingsPage::PAGE_NAME,
            array( $this, 'create_admin_page' ),
            'dashicons-cloud'
        );
    }

    /**
     * Handles the setting form
     */
    public function create_admin_page() {
        $this->options = get_option( 'settings_group' );
        ?>
        <div class="wrap">
            <h1>Repo Updater Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'settings_group' );
                do_settings_sections( SettingsPage::PAGE_NAME );
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
            'settings_group',
            'settings_group',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'setting_section',
            __( 'Sync new repository', R_UPDATER_CONTEXT ),
            null,
            SettingsPage::PAGE_NAME
        );

        add_settings_field(
            'repositories',
            __( 'Available repositories', R_UPDATER_CONTEXT ),
            array( $this, 'repositories_callback' ),
            SettingsPage::PAGE_NAME,
            'setting_section'
        );

        add_settings_field(
            'themes',
            __( 'Available themes', R_UPDATER_CONTEXT ),
            array( $this, 'themes_callback' ),
            SettingsPage::PAGE_NAME,
            'setting_section'
        );

        add_settings_field(
            'username',
            __( 'Username', R_UPDATER_CONTEXT ),
            array( $this, 'username_callback' ),
            SettingsPage::PAGE_NAME,
            'setting_section'
        );

        add_settings_field(
            'password',
            __( 'Password', R_UPDATER_CONTEXT ),
            array( $this, 'password_callback' ),
            SettingsPage::PAGE_NAME,
            'setting_section'
        );

        add_settings_field(
            'token',
            __( 'Token', R_UPDATER_CONTEXT ),
            array( $this, 'token_callback' ),
            SettingsPage::PAGE_NAME,
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
        Input::text( 'username', 'settings_group[username]', esc_attr( $this->options['username'] ) );
    }

    /**
     * Renders the input field for the repository password
     */
    public function password_callback() {
        Input::password( 'password', 'settings_group[password]', esc_attr( $this->options['password'] ) );
    }

    /**
     * Renders the input field for the repository access token
     */
    public function token_callback() {
        Input::text( 'token', 'settings_group[token]', esc_attr( $this->options['token'] ) );
    }

    /**
     * Renders the list of available repositories in a selection field
     */
    public function repositories_callback() {
        Input::select( 'repositories', 'settings_group[repositories]', $this->available_repositories, esc_attr( $this->options['repositories'] ) );
    }

    /**
     * Renders the list of available themes in a selection field
     */
    public function themes_callback() {
        $themes = array_column( $this->available_themes, 'name', 'stylesheet' );
        Input::select( 'themes', 'settings_group[themes]', $themes, esc_attr( $this->options['themes'] ) );
    }
}