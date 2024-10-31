<?php


class Wp_Pargo_Admin
{
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param  string  $plugin_name  The name of this plugin.
     * @param  string  $version      The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Plugin_Name_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Plugin_Name_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        $current_screen = get_current_screen();
        if (strpos($current_screen->base, 'pargo-shipping') === false) {
            echo "";
        } else {
            wp_enqueue_style(
                'pargo-bootstrap-style',
                PARGOPLUGINURL.'assets/bootstrap/css/bootstrap.css',
                array(),
                '3.4.1',
                'all'
            );
        }
        if (strpos($current_screen->base, 'pargo-help') === false) {
            //			return;
            echo "";
        } else {
            wp_enqueue_style(
                'pargo-bootstrap-style',
                PARGOPLUGINURL.'assets/bootstrap/css/bootstrap.css',
                array(),
                '3.4.1',
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Plugin_Name_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Plugin_Name_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('pargo_main_script', PARGOPLUGINURL.'assets/js/main.js', array('jquery'), $this->version, false);

        wp_localize_script('pargo_main_script', 'ajax_object', [
            'adminurl' =>  admin_url('admin-ajax.php'),
        ]);
    }
}
