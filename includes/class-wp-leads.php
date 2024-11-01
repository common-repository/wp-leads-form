<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://profiles.wordpress.org/itpixelz/
 * @since      1.0.0
 * @package    Wp_Leads
 * @subpackage Wp_Leads/includes
 * @author     Umar Draz <umar.draz001@gmail.com>
 */
class Wp_Leads
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wp_Leads_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
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
     * @since    1.0.0
     */
    public function __construct()
    {
        if ( defined( 'WP_LEADS_VERSION' ) ) {
            $this->version = WP_LEADS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'wp-leads';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_common_hooks();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wp_Leads_Loader. Orchestrates the hooks of the plugin.
     * - Wp_Leads_i18n. Defines internationalization functionality.
     * - Wp_Leads_Admin. Defines all hooks for the admin area.
     * - Wp_Leads_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-leads-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-leads-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-leads-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-leads-public.php';

        $this->loader = new Wp_Leads_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wp_Leads_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Wp_Leads_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of common hooks of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_common_hooks()
    {

        $this->loader->add_action( 'init', $this, 'register_custom_post_type', 0 );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Wp_Leads_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

        // add post meta box for customer leads
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_customer_leads_meta_box' );

        // save post data | limited to only post_type = customer
        $this->loader->add_action( 'save_post_customer', $plugin_admin, 'save_postdata' );

        // change title to customer in add/edit customer post
        $this->loader->add_action( 'enter_title_here', $plugin_admin, 'change_title_text' );

        // change title to customer in table list
        $this->loader->add_action( 'manage_customer_posts_columns', $plugin_admin, 'customer_title_table' );

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Wp_Leads_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        // register wp lead form submission ajax
        $this->loader->add_action( 'wp_ajax_wp_lead_save', $plugin_public, 'wp_lead_save_handler' );
        $this->loader->add_action( 'wp_ajax_nopriv_wp_lead_save', $plugin_public, 'wp_lead_save_handler' );

        // register shortcode hook
        add_shortcode( 'wp-leads-form', array( $plugin_public, 'leads_form_shortcode' ) );


    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
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
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Wp_Leads_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }


    /**
     * Register custom post type for customer leads, its categories and tags
     *
     * @since     1.0.0
     */
    public function register_custom_post_type()
    {

        // custom post type
        $labels = array(
            'name' => _x( 'Customers', 'Post Type General Name', 'wp-leads' ),
            'singular_name' => _x( 'Customer', 'Post Type Singular Name', 'wp-leads' ),
            'menu_name' => __( 'Customers', 'wp-leads' ),
            'name_admin_bar' => __( 'Customers', 'wp-leads' ),
            'archives' => __( 'Customer Archives', 'wp-leads' ),
            'attributes' => __( 'Customer Attributes', 'wp-leads' ),
            'parent_item_colon' => __( 'Parent Customer:', 'wp-leads' ),
            'all_items' => __( 'All Customers', 'wp-leads' ),
            'add_new_item' => __( 'Add New Customer', 'wp-leads' ),
            'add_new' => __( 'Add New', 'wp-leads' ),
            'new_item' => __( 'New Customer', 'wp-leads' ),
            'edit_item' => __( 'Edit Customer', 'wp-leads' ),
            'update_item' => __( 'Update Customer', 'wp-leads' ),
            'view_item' => __( 'View Customer', 'wp-leads' ),
            'view_items' => __( 'View Customers', 'wp-leads' ),
            'search_items' => __( 'Search Customer', 'wp-leads' ),
            'not_found' => __( 'Not found', 'wp-leads' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'wp-leads' ),
            'featured_image' => __( 'Featured Image', 'wp-leads' ),
            'set_featured_image' => __( 'Set featured image', 'wp-leads' ),
            'remove_featured_image' => __( 'Remove featured image', 'wp-leads' ),
            'use_featured_image' => __( 'Use as featured image', 'wp-leads' ),
            'insert_into_item' => __( 'Insert into customer', 'wp-leads' ),
            'uploaded_to_this_item' => __( 'Uploaded to this customer', 'wp-leads' ),
            'items_list' => __( 'Customers list', 'wp-leads' ),
            'items_list_navigation' => __( 'Customers list navigation', 'wp-leads' ),
            'filter_items_list' => __( 'Filter customers list', 'wp-leads' ),
        );
        $args = array(
            'label' => __( 'Customer', 'wp-leads' ),
            'description' => __( 'Customer leads', 'wp-leads' ),
            'labels' => $labels,
            'supports' => array( 'title' ),
            'taxonomies' => array( 'customer_category', 'customer_tag' ),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-groups',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'can_export' => false,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'rewrite' => false,
            'capability_type' => 'page',
            'show_in_rest' => false,
        );
        register_post_type( 'customer', $args );


        // register taxonomy category for customer
        $labels = array(
            'name' => _x( 'Categories', 'Taxonomy General Name', 'wp-leads' ),
            'singular_name' => _x( 'Category', 'Taxonomy Singular Name', 'wp-leads' ),
            'menu_name' => __( 'Categories', 'wp-leads' ),
            'all_items' => __( 'All Categories', 'wp-leads' ),
            'parent_item' => __( 'Parent Category', 'wp-leads' ),
            'parent_item_colon' => __( 'Parent Category:', 'wp-leads' ),
            'new_item_name' => __( 'New Category Name', 'wp-leads' ),
            'add_new_item' => __( 'Add New Category', 'wp-leads' ),
            'edit_item' => __( 'Edit Category', 'wp-leads' ),
            'update_item' => __( 'Update Category', 'wp-leads' ),
            'view_item' => __( 'View Category', 'wp-leads' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'wp-leads' ),
            'add_or_remove_items' => __( 'Add or remove categories', 'wp-leads' ),
            'choose_from_most_used' => __( 'Choose from the most used', 'wp-leads' ),
            'popular_items' => __( 'Popular Categories', 'wp-leads' ),
            'search_items' => __( 'Search Categories', 'wp-leads' ),
            'not_found' => __( 'Not Found', 'wp-leads' ),
            'no_terms' => __( 'No categories', 'wp-leads' ),
            'items_list' => __( 'Categories list', 'wp-leads' ),
            'items_list_navigation' => __( 'Categories list navigation', 'wp-leads' ),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'rewrite' => false,
            'show_in_rest' => false,
        );
        register_taxonomy( 'customer_category', array( 'customer' ), $args );


        //  register taxonomy tag for customer
        $labels = array(
            'name' => _x( 'Tags', 'Taxonomy General Name', 'wp-leads' ),
            'singular_name' => _x( 'Tag', 'Taxonomy Singular Name', 'wp-leads' ),
            'menu_name' => __( 'Tags', 'wp-leads' ),
            'all_items' => __( 'All Tags', 'wp-leads' ),
            'parent_item' => __( 'Parent Tag', 'wp-leads' ),
            'parent_item_colon' => __( 'Parent Tag:', 'wp-leads' ),
            'new_item_name' => __( 'New Tag Name', 'wp-leads' ),
            'add_new_item' => __( 'Add New Tag', 'wp-leads' ),
            'edit_item' => __( 'Edit Tag', 'wp-leads' ),
            'update_item' => __( 'Update Tag', 'wp-leads' ),
            'view_item' => __( 'View Tag', 'wp-leads' ),
            'separate_items_with_commas' => __( 'Separate tags with commas', 'wp-leads' ),
            'add_or_remove_items' => __( 'Add or remove Tags', 'wp-leads' ),
            'choose_from_most_used' => __( 'Choose from the most used', 'wp-leads' ),
            'popular_items' => __( 'Popular Tags', 'wp-leads' ),
            'search_items' => __( 'Search Tags', 'wp-leads' ),
            'not_found' => __( 'Not Found', 'wp-leads' ),
            'no_terms' => __( 'No tags', 'wp-leads' ),
            'items_list' => __( 'Tags list', 'wp-leads' ),
            'items_list_navigation' => __( 'Tags list navigation', 'wp-leads' ),
        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'rewrite' => false,
            'show_in_rest' => false,
        );
        register_taxonomy( 'customer_tag', array( 'customer' ), $args );

    }


}
