<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://endif.media
 * @since      1.0.0
 *
 * @package    Userdocs
 * @subpackage Userdocs/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Userdocs
 * @subpackage Userdocs/admin
 * @author     Ethan Allen <yourfriendethan@gmail.com>
 */
class Userdocs_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function setup_post_types() {
		$labels = array(
			'name'                  => _x('UserDocs', $this->plugin_name),// shows up edit post type screen
			'singular_name'         => _x('UserDoc', $this->plugin_name),
			'menu_name'             => _x('UserDocs', $this->plugin_name),//left hand nav menu title
			'all_items'             => _x('All UserDocs', $this->plugin_name),
			'add_new_item'          => _x('Add new UserDoc', $this->plugin_name),
			'edit_item'             => _x('Edit UserDoc', $this->plugin_name),
			'not_found'             => _x('No UserDocs found', $this->plugin_name),
		);
		$args = array(
			'label'                 => _x('UserDoc', $this->plugin_name),
			'labels'                => $labels,
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'menu_icon'			    => 'dashicons-info',
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => array('slug' => 'userdocs', 'with_front' => false)
		);
		register_post_type( 'userdocs', $args );

		$labels = array(
			'name'                       => _x('Topics', $this->plugin_name),// shows up edit post type screen
			'singular_name'              => _x('Topic', $this->plugin_name),
			'menu_name'                  => _x('Topics', $this->plugin_name),
			'new_item_name'              => _x('New Topic', $this->plugin_name),
			'add_new_item'               => _x('Add New Topic', $this->plugin_name),
			'edit_item'                  => _x('Edit Topic', $this->plugin_name),
			'not_found'                  => _x('Topic not found', $this->plugin_name),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_quick_edit'         => false,
			'meta_box_cb'                => false,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'rewrite'                    => array('slug' => 'topics', 'with_front' => false)
		);
		register_taxonomy( 'userdocs_taxonomy', array( 'userdocs' ), $args );
	}

	/**
     * Flush rewrites if flag is set, then delete.
     *
     * @since    1.0.1
     */
	function maybe_flush_rewrites(){
        if (get_option('userdocs_flush_rewrites_flag')) {
            flush_rewrite_rules();
            delete_option('userdocs_flush_rewrites_flag');
        }
    }

	/**
	 * Set up metaboxes.
	 */
	public function setup_userdocs_metaboxes(){
		add_meta_box('UserDocs', "This document belongs with&hellip;", array(&$this, 'taxonomy_details'), 'userdocs', 'side');
	}

	/**
	 * maybe use wp_category_checklist().
	 */
	public function taxonomy_details(){
		$selected = (!empty($_GET['post'])) ? wp_get_post_terms(intval($_GET['post']), 'userdocs_taxonomy')[0]->term_id : null;
		wp_dropdown_categories(array(
			'taxonomy'        =>  'userdocs_taxonomy',
			'orderby'         =>  'name',
			'selected'        =>  $selected,
			'hierarchical'    =>  true,
			'depth'           =>  3,
			'show_count'      =>  false, // Show # listings
			'hide_empty'      =>  false, // Don't show businesses w/o listings
		));
	}

	/**
	 * @param $post_id
	 * @param $post
	 */
	public function save_taxonomy_details($post_id, $post){
		if ($post->post_type == 'userdocs')
			if (!empty($_REQUEST['cat']))
				wp_set_object_terms( $post_id, intval($_REQUEST['cat']), 'userdocs_taxonomy', false );
	}

}
