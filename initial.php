<?php
/*
	Plugin Name:       Institute Notice
	Plugin URI:        https://www.wpcoderpro.com/plugins/institute-notice/
	Description:       Institute Notice is the most reliable for showing institute notice in any place using shortcode.
	Version:           1.0.0
	Requires at least: 5.5.3
	Requires PHP:      7.1.28
	Author:            Md Dalwar
	Author URI:        https://www.learnwptech.com
	License:           GPL v2 or later
	License URI:       https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain:       ins_notice
	License:     	   GPL2
 
	Institute is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	any later version.
	 
	Institute is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	 
	You should have received a copy of the GNU General Public License
	along with Institute. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 */
	
	
	
	class Institute_Notice {
		public function __construct(){
			add_action('init', array($this, 'institute_notice_setup'));
			add_action('wp_enqueue_scripts', array($this, 'institute_notice_styles'));
			add_action('admin_enqueue_scripts', array($this, 'institute_admin_notice_scripts'));
			add_action('add_meta_boxes', array($this, 'ins_notice_custom_box'));			
			add_action('save_post', array($this, 'ins_notice_file_meta_save'));			
			add_shortcode( 'ins_notice', 'institute_notice_shortcode');	

			// Add Shortcode
			function institute_notice_shortcode($atts) {
				$atts = shortcode_atts(array(
					'notice_count'		=> 30
				), $atts);

				extract($atts);

				ob_start();
				?>
					<div class="ins-notices">
						<?php 
							$notices = new WP_Query(array(
								'post_type'			=> 'institute_notice',
								'posts_per_page'	=> $notice_count
							));

							while($notices->have_posts()) : $notices->the_post();

							$btn_text = get_post_meta(get_the_id(), '_download_btn_text_', true);
							$notice_file_url = get_post_meta(get_the_id(), '_notice_file_link_', true);
						?>
						<div class="single-notice">							
							<div class="notice-title">
								<p><?php the_title(); ?></p>
							</div>

							<?php if(!empty($btn_text) || !empty($notice_file_url)) : ?>
							<div class="download-btn">
								<a href="<?php echo $notice_file_url; ?>"><?php echo $btn_text; ?></a>
							</div>
							<?php endif; ?>

						</div>
						<?php endwhile; ?>
					</div>
				<?php
				return ob_get_clean();
			}
		}

		public function institute_notice_styles(){
		   	wp_register_style( 'notice_style', plugins_url( basename( __DIR__ )) . '/css/style.css', array(), null, 'all');
			wp_enqueue_style('notice_style');
		}
		public function institute_admin_notice_scripts(){
			wp_enqueue_media();
			wp_register_script( 'notice_script', plugins_url( basename( __DIR__ )) . '/js/notice.js', array('jquery'));
			wp_enqueue_script('notice_script');
		}
		
		public function ins_notice_custom_box(){
		    $meta_screen = 'institute_notice';
		   add_meta_box(
	            'notice_file',           // Unique ID
	            'Upload Notice File',  // Box title
	            'ins_notice_file_inputs',  // Content callback, must be of type callable
	            $meta_screen                   // Post type
		    );

			function ins_notice_file_inputs(){
				$btn_text = get_post_meta(get_the_id(), '_download_btn_text_', true);
				$notice_file_url = get_post_meta(get_the_id(), '_notice_file_link_', true);
				?>
					<p>
						<label for="download_btn_text" style="display: block;">Download Button Text</label>
						<input type="text" id="download_btn_text" class="download_btn_text widefat" name="download_btn_text" value="<?php if(!empty($btn_text)){echo $btn_text;}else{echo 'Download';} ?>">
					</p>
					<p>
						<label for="notice_file_link">Upload or Put File URL</label>
						<input type="text" id="notice_file_link" class="notice_file_link widefat" name="notice_file_link" value="<?php echo $notice_file_url; ?>" placeholder="Notice file link" style="margin-bottom: 1rem;">
						<button class="notice_file button">Upload File</button>
					</p>
				<?php
			}
		}

		function ins_notice_file_meta_save($post_id){
			$button_text = sanitize_text_field($_POST['download_btn_text']);
			$file_url = esc_url($_POST['notice_file_link']);

			update_post_meta($post_id, '_download_btn_text_', $button_text);
			update_post_meta($post_id, '_notice_file_link_', $file_url);		
		}

		public function institute_notice_setup(){
			load_plugin_textdomain('ins_notice');

			// Register Custom Post Type

			$notice_labels = array(
				'name'                  => _x( 'Notices', 'Notices', 'ins_notice' ),
				'singular_name'         => _x( 'Notice', 'Notice', 'ins_notice' ),
				'menu_name'             => __( 'Notices', 'ins_notice' ),
				'name_admin_bar'        => __( 'Notice', 'ins_notice' ),
				'archives'              => __( 'Notice Archives', 'ins_notice' ),
				'attributes'            => __( 'Notice Attributes', 'ins_notice' ),
				'parent_item_colon'     => __( 'Parent Notice:', 'ins_notice' ),
				'all_items'             => __( 'All Notices', 'ins_notice' ),
				'add_new_item'          => __( 'Add New Notice', 'ins_notice' ),
				'add_new'               => __( 'Add New', 'ins_notice' ),
				'new_item'              => __( 'New Notice', 'ins_notice' ),
				'edit_item'             => __( 'Edit Notice', 'ins_notice' ),
				'update_item'           => __( 'Update Notice', 'ins_notice' ),
				'view_item'             => __( 'View Notice', 'ins_notice' ),
				'view_items'            => __( 'View Notice', 'ins_notice' ),
				'search_items'          => __( 'Search Notice', 'ins_notice' ),
				'not_found'             => __( 'Not found', 'ins_notice' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'ins_notice' ),
				'featured_image'        => __( 'Featured Image', 'ins_notice' ),
				'set_featured_image'    => __( 'Set featured image', 'ins_notice' ),
				'remove_featured_image' => __( 'Remove featured image', 'ins_notice' ),
				'use_featured_image'    => __( 'Use as featured image', 'ins_notice' ),
				'insert_into_item'      => __( 'Insert into notice', 'ins_notice' ),
				'uploaded_to_this_item' => __( 'Uploaded to this notice', 'ins_notice' ),
				'items_list'            => __( 'Notices list', 'ins_notice' ),
				'items_list_navigation' => __( 'Notices list navigation', 'ins_notice' ),
				'filter_items_list'     => __( 'Filter notices list', 'ins_notice' ),
			);
			$notice_args = array(
				'label'                 => __( 'Notice', 'ins_notice' ),
				'description'           => __( 'Notice Description', 'ins_notice' ),
				'labels'                => $notice_labels,
				'supports'              => array( 'title' ),
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
				'menu_icon'				=> 'dashicons-megaphone'
			);
			register_post_type( 'institute_notice', $notice_args );

		}

	}

	$dinat_social_feed = new Institute_Notice();
