<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
/*
Plugin Name: Pricing Table – Responsive & Easy Pricing Table
Plugin URI: http://awplife.com/code
Description: A clean and responsive pricing table plugin for WordPress. Easily create comparison tables with multiple templates and shortcode support.
Version: 1.5.4
Requires PHP: 7.2
Author: A WP Life
Author URI: http://awplife.com/
License: GPLv2 or later
Text Domain: abc-pricing-table
Domain Path: /languages
*/
if (!class_exists('apt_pricingtable')) {
	class apt_pricingtable
	{

		public function __construct()
		{
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants()
		{
			// Plugin Version
			define('APT_PLUGIN_VER', '1.5.4');

			// Plugin Text Domain
			define('APT_TXTDM', 'abc-pricing-table');

			// Plugin Name
			define('APT_PLUGIN_NAME', 'Pricing Table');

			// Plugin Slug
			define('APT_PLUGIN_SLUG', 'abc-pricing');

			// Plugin Directory Path
			define('APT_PLUGIN_DIR', plugin_dir_path(__FILE__));

			// Plugin Directory URL
			define('APT_PLUGIN_URL', plugin_dir_url(__FILE__));



		} // end of constructor function

		protected function _hooks()
		{
			// add testimonial menu item, change menu filter for multisite
			add_action('admin_menu', array($this, 'pricing_menu'), 10);

			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

			// Create pricing table Custom Post
			add_action('init', array($this, 'Pricing'));

			// Add meta box to custom post
			add_action('add_meta_boxes', array($this, 'admin_add_meta_box'));

			add_action('save_post', array($this, '_apt_save_settings'));

			// add pfg cpt shortcode column - manage_{$post_type}_posts_columns
			add_filter('manage_abc-pricing_posts_columns', array($this, 'set_abc_pricing_shortcode_column_name'));

			// add pfg cpt shortcode column data - manage_{$post_type}_posts_custom_column
			add_action('manage_abc-pricing_posts_custom_column', array($this, 'custom_abc_pricing_shodrcode_data'), 10, 2);

		} // end of hook function


		// end of hook function

		// Pricing table cpt shortcode column before date columns
		public function set_abc_pricing_shortcode_column_name($defaults)
		{
			$new = array();

			unset($defaults['tags']);   // remove it from the columns list

			foreach ($defaults as $key => $value) {
				if ($key == 'date') {  // when we find the date column
					$new['abc_pricing_shortcode'] = __('Shortcode', 'abc-pricing-table');  // put the tags column before it
				}
				$new[$key] = $value;
			}
			return $new;
		}

		// abc cpt shortcode column data
		public function custom_abc_pricing_shodrcode_data($column, $post_id)
		{
			switch ($column) {
				case 'abc_pricing_shortcode':
					echo "<input type='text' class='button button-primary' id='abc-pricing-shortcode-" . esc_attr($post_id) . "' value='[APT id=" . esc_attr($post_id) . "]' style='font-weight:bold; background-color:#32373C; color:#FFFFFF; text-align:center;' />";
					echo "<input type='button' class='button button-primary' onclick='return ABC_PT_CopyShortcode(" . esc_attr($post_id) . ");' readonly value='Copy' style='margin-left:4px;' />";
					echo "<span id='copy-msg-" . esc_attr($post_id) . "' class='button button-primary' style='display:none; background-color:#32CD32; color:#FFFFFF; margin-left:4px; border-radius: 4px;'>copied</span>";
					break;
			}
		}

		public function pricing_menu()
		{
			add_submenu_page('edit.php?post_type=' . APT_PLUGIN_SLUG, __('Our Plugins', 'abc-pricing-table'), __('Our Plugins', 'abc-pricing-table'), 'manage_options', 'abc-pricing-our-plugins', array($this, '_abcpt_our_plugins_page'));
			add_submenu_page('edit.php?post_type=' . APT_PLUGIN_SLUG, __('Our Themes', 'abc-pricing-table'), __('Our Themes', 'abc-pricing-table'), 'manage_options', 'abc-pricing-our-themes', array($this, '_abcpt_our_themes_page'));
		}

		public function admin_enqueue_scripts($hook)
		{
			if ('edit.php' === $hook && isset($_GET['post_type']) && 'abc-pricing' === $_GET['post_type']) {
				wp_enqueue_script('apt-admin-list-js', APT_PLUGIN_URL . 'assets/js/apt-admin-list.js', array('jquery'), APT_PLUGIN_VER, true);
			}

			if ('post-new.php' === $hook || 'post.php' === $hook) {
				if ('abc-pricing' === get_post_type()) {
					wp_enqueue_script('apt-popper-min-js', APT_PLUGIN_URL . 'assets/js/popper.min.js', array('jquery'), '2.0', true);
					wp_enqueue_script('apt-bootstrap-min-js', APT_PLUGIN_URL . 'assets/js/bootstrap.min.js', array('jquery'), '4.3.1', true);
					wp_enqueue_script('apt-bootstrap-iconset-all-min-js', APT_PLUGIN_URL . 'assets/js/bootstrap-iconpicker-iconset-all.min.js', array('jquery'), '1.10.0', true);
					wp_enqueue_script('apt-bootstrap-iconpicker-min-js', APT_PLUGIN_URL . 'assets/js/bootstrap-iconpicker.min.js', array('jquery'), '1.10.0', true);
					wp_enqueue_script('apt-color-picker-js', APT_PLUGIN_URL . 'assets/js/apt-color-picker.js', array('wp-color-picker'), '1.0.0', true);
					wp_enqueue_script('apt-admin-js', APT_PLUGIN_URL . 'assets/js/apt-admin.js', array('jquery', 'wp-color-picker', 'apt-bootstrap-min-js', 'apt-bootstrap-iconpicker-min-js'), APT_PLUGIN_VER, true);
					wp_localize_script('apt-admin-js', 'apt_admin_i18n', array(
						'yes' => __('Yes', 'abc-pricing-table'),
						'no' => __('No', 'abc-pricing-table'),
						'name' => __('Name', 'abc-pricing-table'),
						'pricing' => __('Pricing', 'abc-pricing-table'),
						'pricing_plan' => __('Pricing Plan', 'abc-pricing-table'),
						'pricing_features' => __('Pricing Features', 'abc-pricing-table'),
						'button_text' => __('Button Text', 'abc-pricing-table'),
						'button_url' => __('Button Url', 'abc-pricing-table'),
						'confirm_delete' => __('Are sure to delete this columns from table?', 'abc-pricing-table'),
					));
					wp_enqueue_style('wp-color-picker');
					wp_enqueue_style('apt-bootstrap-css', APT_PLUGIN_URL . 'assets/css/pricing-admin-bootstrap.css', array(), '4.3.1');
					wp_enqueue_style('apt-bootstrap-iconpicker-css', APT_PLUGIN_URL . 'assets/css/bootstrap-iconpicker.min.css', array(), '1.10.0');
					wp_enqueue_style('apt-toogle-button-css', APT_PLUGIN_URL . 'assets/css/toogle-button.css');
					wp_enqueue_style('apt-styles-css', APT_PLUGIN_URL . 'assets/css/styles.css');
					wp_enqueue_style('metabox-css', APT_PLUGIN_URL . 'assets/css/metabox.css');
					wp_enqueue_style('apt-all-css', APT_PLUGIN_URL . 'assets/css/all.css', array(), '5.15.2');
					wp_enqueue_style('team-setting-css', APT_PLUGIN_URL . 'assets/css/team-setting.css');
				}
			}

			if ('abc-pricing_page_abc-pricing-our-plugins' === $hook || 'abc-pricing_page_abc-pricing-our-themes' === $hook) {
				wp_enqueue_style('abc-pricing-our-plugins-style', APT_PLUGIN_URL . 'assets/css/our-plugins-style.css');
			}
		}

		public function Pricing()
		{
			$labels = array(
				'name' => __('Pricing Table', 'abc-pricing-table'),
				'singular_name' => __('Pricing Table', 'abc-pricing-table'),
				'menu_name' => __('Pricing Table', 'abc-pricing-table'),
				'name_admin_bar' => __('Pricing Table', 'abc-pricing-table'),
				'add_new' => __('Add Pricing Table', 'abc-pricing-table'),
				'add_new_item' => __('Add Pricing Table', 'abc-pricing-table'),
				'new_item' => __('New Pricing Table', 'abc-pricing-table'),
				'edit_item' => __('Edit Pricing Table', 'abc-pricing-table'),
				'view_item' => __('View Pricing Table', 'abc-pricing-table'),
				'all_items' => __('All Pricing Table', 'abc-pricing-table'),
				'search_items' => __('Search Pricing Table', 'abc-pricing-table'),
				'parent_item_colon' => __('Parent Pricing Table', 'abc-pricing-table'),
				'not_found' => __('No Pricing Table', 'abc-pricing-table'),
				'not_found_in_trash' => __('No Pricing Table found in Trash', 'abc-pricing-table'),
			);

			$args = array(
				'description' => __('Description', 'abc-pricing-table'),
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				// 'rewrite'            => array( 'slug' => 'pricing' ),
				'capability_type' => 'page',
				'has_archive' => true,
				'hierarchical' => false,
				'menu_icon' => 'dashicons-cart',
				'menu_position' => null,
				'supports' => array('title'),
			);
			register_post_type('abc-pricing', $args);
		}

		public function admin_add_meta_box()
		{
			add_meta_box('apt-pricing-table-settings', __('Add Pricing Table', 'abc-pricing-table'), array($this, 'apt_pricing_upload'), 'abc-pricing', 'normal', 'default');
			add_meta_box('apt-upgrade-pro', __('Upgrade Pricing Table Pro', 'abc-pricing-table'), array($this, 'apt_upgrade_pro'), 'abc-pricing', 'side', 'default');
			add_meta_box('apt-rate-plugin', __('Rate Our Plugin', 'abc-pricing-table'), array($this, 'apt_rate_plugin'), 'abc-pricing', 'side', 'default');
		}
		/** meta upgrade pro **/
		public function apt_upgrade_pro()
		{ ?>
			<img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'assets/img/2017-12-08_20-43-13.png'); ?>" width="250"
				height="280">
			<a href="https://awplife.com/demo/pricing-table-premium/" target="_new" class="button button-primary button-large"
				style="background: #496481; text-shadow: none; margin-top:10px"><span class="dashicons dashicons-search"
					style="line-height:1.4;"></span> Live Demo</a>
			<a href="https://awplife.com/wordpress-plugins/pricing-table-wordpress-plugin/" target="_new"
				class="button button-primary button-large" style="background: #496481; text-shadow: none; margin-top:10px"><span
					class="dashicons dashicons-unlock" style="line-height:1.4;"></span> Upgrade Pro</a>
			<?php
		}
		/** meta rate us **/
		public function apt_rate_plugin()
		{
			?>
			<div style="text-align:center">
				<p>If you like our plugin then please <b>Rate us</b> on WordPress</p>
			</div>
			<div style="text-align:center">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</div>
			<br>
			<div style="text-align:center">
				<a href="https://wordpress.org/support/plugin/abc-pricing-table/reviews/" target="_new"
					class="button button-primary button-large" style="background: #496481; text-shadow: none;"><span
						class="dashicons dashicons-heart" style="line-height:1.4;"></span> Please Rate Us</a>
			</div>
		<?php }

		public function apt_pricing_upload($post)
		{

			require_once 'include/add-new-pricing.php';

			wp_nonce_field('apt_post_save_settings', 'apt_post_save_nonce');
		}

		public function _apt_save_settings($post_id)
		{
			if (isset($_POST['apt_post_save_nonce'])) {
				$nonce = wp_unslash($_POST['apt_post_save_nonce']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if (wp_verify_nonce($nonce, 'apt_post_save_settings') && current_user_can('edit_post', $post_id)) {

					$total_cols = isset($_POST['total_cols']) ? sanitize_text_field(wp_unslash($_POST['total_cols'])) : '';
					$pricing_table_design = isset($_POST['pricing_table_design']) ? sanitize_text_field(wp_unslash($_POST['pricing_table_design'])) : '';
					$currency_icon = isset($_POST['currency_icon']) ? sanitize_text_field(wp_unslash($_POST['currency_icon'])) : '';
					$heading_text_color = isset($_POST['heading_text_color']) ? sanitize_text_field(wp_unslash($_POST['heading_text_color'])) : '';
					$heading_background_color = isset($_POST['heading_background_color']) ? sanitize_text_field(wp_unslash($_POST['heading_background_color'])) : '';
					$button_color = isset($_POST['button_color']) ? sanitize_text_field(wp_unslash($_POST['button_color'])) : '';
					$button_hover_color = isset($_POST['button_hover_color']) ? sanitize_text_field(wp_unslash($_POST['button_hover_color'])) : '';
					$background_hover_color = isset($_POST['background_hover_color']) ? sanitize_text_field(wp_unslash($_POST['background_hover_color'])) : '';
					$button_heading_color = isset($_POST['button_heading_color']) ? sanitize_text_field(wp_unslash($_POST['button_heading_color'])) : '';
					$feature_heading_text_color = isset($_POST['feature_heading_text_color']) ? sanitize_text_field(wp_unslash($_POST['feature_heading_text_color'])) : '';
					$feature_button_color = isset($_POST['feature_button_color']) ? sanitize_text_field(wp_unslash($_POST['feature_button_color'])) : '';
					$feature_heading_background_color = isset($_POST['feature_heading_background_color']) ? sanitize_text_field(wp_unslash($_POST['feature_heading_background_color'])) : '';
					$feature_button_heading_color = isset($_POST['feature_button_heading_color']) ? sanitize_text_field(wp_unslash($_POST['feature_button_heading_color'])) : '';
					$feature_background_hover_color = isset($_POST['feature_background_hover_color']) ? sanitize_text_field(wp_unslash($_POST['feature_background_hover_color'])) : '';
					$feature_button_hover_color = isset($_POST['feature_button_hover_color']) ? sanitize_text_field(wp_unslash($_POST['feature_button_hover_color'])) : '';

					$apt_custom_css = isset($_POST['apt_custom_css']) ? wp_kses(wp_unslash($_POST['apt_custom_css']), array(), array()) : '';



					$pricing_name = array();
					$featured_button = array();
					$pricing_price = array();
					$pricing_plan = array();
					$pricing_features = array();
					$pricing_btn_text = array();
					$pricing_btn_url = array();
					$pricing_icon_pick = array();
					$pricing_name_val = isset($_POST['pricing_name']) ? array_map('sanitize_text_field', wp_unslash((array) $_POST['pricing_name'])) : array();
					$apt_i = 0;

					foreach ($pricing_name_val as $pricing_name_v) {

						$featured_button[] = isset($_POST['featured_button'][$apt_i]) ? sanitize_text_field(wp_unslash($_POST['featured_button'][$apt_i])) : '';
						$pricing_name[] = isset($_POST['pricing_name'][$apt_i]) ? sanitize_text_field(wp_unslash($_POST['pricing_name'][$apt_i])) : '';
						$pricing_price[] = isset($_POST['pricing_price'][$apt_i]) ? sanitize_text_field(wp_unslash($_POST['pricing_price'][$apt_i])) : '';
						$pricing_plan[] = isset($_POST['pricing_plan'][$apt_i]) ? sanitize_text_field(wp_unslash($_POST['pricing_plan'][$apt_i])) : '';
						$pricing_features[] = isset($_POST['pricing_features'][$apt_i]) ? sanitize_textarea_field(wp_unslash($_POST['pricing_features'][$apt_i])) : '';
						$pricing_btn_text[] = isset($_POST['pricing_btn_text'][$apt_i]) ? sanitize_text_field(wp_unslash($_POST['pricing_btn_text'][$apt_i])) : '';
						$pricing_btn_url[] = isset($_POST['pricing_btn_url'][$apt_i]) ? sanitize_text_field(wp_unslash($_POST['pricing_btn_url'][$apt_i])) : '';
						$pricing_icon_pick[] = isset($_POST['pricing_icon_pick'][$apt_i]) ? sanitize_text_field(wp_unslash($_POST['pricing_icon_pick'][$apt_i])) : '';



						$apt_i++;
					}

					$pricing_post_settings = array(

						'featured_button' => $featured_button,
						'pricing_name' => $pricing_name,
						'pricing_price' => $pricing_price,
						'pricing_plan' => $pricing_plan,
						'pricing_features' => $pricing_features,
						'pricing_btn_text' => $pricing_btn_text,
						'pricing_btn_url' => $pricing_btn_url,
						'pricing_icon_pick' => $pricing_icon_pick,
						'total_cols' => $total_cols,
						'pricing_table_design' => $pricing_table_design,
						'currency_icon' => $currency_icon,
						'heading_text_color' => $heading_text_color,
						'heading_background_color' => $heading_background_color,
						'button_color' => $button_color,
						'button_hover_color' => $button_hover_color,
						'background_hover_color' => $background_hover_color,
						'button_heading_color' => $button_heading_color,
						'feature_heading_text_color' => $feature_heading_text_color,
						'feature_button_color' => $feature_button_color,
						'feature_heading_background_color' => $feature_heading_background_color,
						'feature_button_heading_color' => $feature_button_heading_color,
						'feature_background_hover_color' => $feature_background_hover_color,
						'feature_button_hover_color' => $feature_button_hover_color,
						'apt_custom_css' => $apt_custom_css,

					);

					$meta_key = 'apt_pricing_table_data_' . $post_id;
					update_post_meta($post_id, $meta_key, $pricing_post_settings);

				} else {
					wp_die(esc_html__('Sorry, your nonce did not verify.', 'abc-pricing-table'));
				}
			}
		}
		public function _abcpt_our_plugins_page()
		{
			require_once 'include/our-plugins.php';
		}

		public function _abcpt_our_themes_page()
		{
			require_once 'include/our-themes.php';
		}
	}

	// register sf scripts
	function awplife_apt_register_scripts()
	{

		// css & JS
		wp_enqueue_script('jquery');
		wp_register_script('apt-bootstrap-min-js', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js');
		wp_enqueue_style('apt-pricing-frontend-bootstrap-css', plugin_dir_url(__FILE__) . 'assets/css/pricing-frontend-bootstrap.css');
		wp_enqueue_style('apt-all-css', plugin_dir_url(__FILE__) . 'assets/css/all.css');
		// css & JS
	}
	add_action('wp_enqueue_scripts', 'awplife_apt_register_scripts');

	$new_pricingtable_object = new apt_pricingtable();
	require_once 'shortcode.php';
}
?>