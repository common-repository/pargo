<?php

/**
 * Class Wp_Pargo
 * This is the main class used in the Pargo Plugin
 */
class Wp_Pargo
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    2.4.4
     * @access   protected
     * @var      Wp_Pargo_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    2.4.4
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    2.4.4
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    2.4.4
     */

    public function __construct()
    {
        $this->plugin_name = 'pargo-shipping';
        $this->version = '2.4.4';
        // Our fearless loader is called from within this class object.
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-public.php';

        /**
         * The class is responsible for handling Admin processes
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-admin-processes.php';

        /**
         * The class responsible for making Api requests
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-api.php';

        /**
         * The class responsible for making Map requests
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-map.php';

        /**
         * The class responsible for monitoring plugin activity
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-monitor.php';

        /**
         * The class responsible for making post requests to the Api
         */
        require_once PARGOPATH.'includes/controllers/class-wp-pargo-shipping-processes.php';

        $this->loader = new Wp_Pargo_Loader();
    }


    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    2.4.4
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Wp_Pargo_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    2.4.4
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Wp_Pargo_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    2.4.4
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Wp_Pargo_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    2.4.4
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since    2.4.4
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Wp_Pargo_Loader    Orchestrates the hooks of the plugin.
     * @since    2.4.4
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since    2.4.4
     */
    public function get_version()
    {
        return $this->version;
    }
}
