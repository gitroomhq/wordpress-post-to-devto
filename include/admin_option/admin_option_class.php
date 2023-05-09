<?php 
/*
* Plugin Admin Option
*/
class WordPressToDev_SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_menu_page(
        __( 'WPToDev API', 'textdomain' ),
        'GitHub20k DEV.to settings',
        'manage_options',
        'WordPressToDevAPI_setting.php',
        array( $this, 'create_admin_page' ),
        'dashicons-buddicons-community',
        6
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'WordPressToDevAPI_Setting' );
        ?>
        <div class="wrap">
            <h1>Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'WordPressToDevAPI_group' );
                do_settings_sections( 'WordPressToDevAPI-setting' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'WordPressToDevAPI_group', // Option group
            'WordPressToDevAPI_Setting', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'WordPressToDevAPI-setting' // Page
        );  

        add_settings_field(
            'WordPressToDevAPI_API_key', // ID
            'Dev.to API Key', // Title 
            array( $this, 'WordPressToDevAPI_API_key_final_callback' ), // Callback
            'WordPressToDevAPI-setting', // Page
            'setting_section_id' // Section           
        );                             
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['WordPressToDevAPI_API_key'] ) )
            $new_input['WordPressToDevAPI_API_key'] = $input['WordPressToDevAPI_API_key'];
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function WordPressToDevAPI_API_key_final_callback()
    {
        printf(
            '<input type="text" id="WordPressToDevAPI_API_key" name="WordPressToDevAPI_Setting[WordPressToDevAPI_API_key]" value="%s" />',
            isset( $this->options['WordPressToDevAPI_API_key'] ) ? esc_attr( $this->options['WordPressToDevAPI_API_key']) : ''
        );
    }
  
}