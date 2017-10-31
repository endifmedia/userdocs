<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://endif.media
 * @since      1.0.0
 *
 * @package    Userdocs
 * @subpackage Userdocs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Userdocs
 * @subpackage Userdocs/public
 * @author     Ethan Allen <yourfriendethan@gmail.com>
 */
class Userdocs_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Userdocs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Userdocs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/userdocs-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Add shortcode support.
	 */
	public function register_shortcodes(){
		add_shortcode('userdocs', array($this, 'userdocs_shortcode')); //backwards compatability
		add_shortcode('userdocs_menu', array($this, 'userdocs_shortcode'));
		add_shortcode('userdocs_support', array($this, 'userdocs_support_shortcode'));
	}

	/**
	 * Filter out content from links.
	 * @param $content
	 *
	 * @return mixed
	 */
	public function filter_archive_content($content){
		if (!is_post_type_archive('userdocs') && !is_tax('userdocs_taxonomy')) {
			return $content;
		}
	}

	/**
	 * Filter out title from links.
	 * @param $title
	 *
	 * @return mixed
	 */
	public function filter_archive_title($title){
		if (is_post_type_archive('userdocs') && !is_tax('userdocs_taxonomy')) {
			return __('All Documentation', $this->plugin_name);
		}
		if (is_tax('userdocs_taxonomy')) {
			$queried_object = get_queried_object();
			return $queried_object->name;
		}
	}

	/**
	 * Function that handles shortcode display.
	 */
	function userdocs_shortcode(){
		$cats = get_terms('userdocs_taxonomy');
		echo '<div class="userdocs-topics-container"><h4><a href="' . esc_attr(home_url('userdocs')) .'">' . __('All Topics', $this->plugin_name) . '</a></h4>';
		foreach ($cats as $cat) {
			echo '<a href="' . get_category_link($cat->term_id) . '">' . $cat->name . '</a> (' . $cat->count . ')<br>';
		}
		echo '</div>';
	}

	/**
	 * Function that handles support shortcode display.
	 *
	 * @param $atts
	 */
	function userdocs_support_shortcode($atts){

		extract(shortcode_atts(
			array(
				'excerpt' => __('Can\'t find an answer or stuck on any of the docs? Email me and I\'ll do my best to help', $this->plugin_name),
				'link' => '',
				'link_text' => ''
				), $atts, 'userdocs_support'
			)
		);

		echo '<div class="userdocs-topics-container">';
		echo '<h4>' . __('Support', $this->plugin_name) . '</h4>';
		echo '<p>' . esc_html($excerpt) . '</p>';
		if (!empty($link)) {
			echo '<p><a href="' . esc_url( $link ) . '">' . esc_html($link_text) . ' &rarr;</a></p>';
		}
		echo '</div>';
	}

	/**
	 * Change default urls for UserDocs.
	 */
	public function slug_rewrite() {
		global $wp_rewrite;
		$rules = array();
		// get all custom taxonomies
		$taxonomies = get_taxonomies(array('_builtin' => false), 'objects');
		// get all custom post types
		$post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

		foreach ($post_types as $post_type) {
			foreach ($taxonomies as $taxonomy) {

				// go through all post types which this taxonomy is assigned to
				foreach ($taxonomy->object_type as $object_type) {

					// check if taxonomy is registered for this custom type
					if ($object_type == $post_type->rewrite['slug']) {

						// get category objects
						$terms = get_categories(array('type' => $object_type, 'taxonomy' => $taxonomy->name, 'hide_empty' => 0));

						// make rules
						foreach ($terms as $term) {
							$rules[$object_type . '/' . $term->slug . '/?$'] = 'index.php?' . $term->taxonomy . '=' . $term->slug;
						}
					}
				}
			}
		}
		// merge with global rules
		$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	}

}