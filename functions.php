<?php

	/**
	 * Bones Theme
	 */

	if ( ! defined( 'ABSPATH' ) )
		exit;

	require get_template_directory() . '/inc/vite-assets.php'; // vite-related functions
	require get_template_directory() . '/inc/tools.php';
	require get_template_directory() . '/inc/block-patterns.php';

	// Declutter
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

	// Actions
	add_action( 'init', 'bones_name_register_block_styles', 100 );
	add_action( 'wp_head', 'bones_theme_js_data_object', 5 );
	add_action( 'wp_head', 'bones_theme_load_favicons', 20 );
	add_action( 'current_screen', 'bones_theme_add_editor_styles', 20 );
	add_action( 'init', 'bones_theme_init', 0 );
	// add_action( 'wp_head', 'theme_fonts', 20 );

	// Frontend Actions
	if ( ! is_admin() ) {
		add_action( 'render_block', 'bones_theme_render_block', 5, 2 );
	}

	function bones_theme_init() {
		// Hide default WP patters
		remove_theme_support('core-block-patterns');
	}

	// Entry Points
	function bones_theme_entry_points(): array {
		return [ 
			'src/index.js',
			'src/style.scss',
		];
	}

	// Inline Data
	function bones_theme_js_data_object() {
		$data = [ 
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		];

		print "<script type=\"text/javascript\">const phpData = " . wp_json_encode( $data ) . ";</script>";
	}

	// Favicons
	function bones_theme_load_favicons() {
		print '<link rel="icon" href="' . get_theme_file_uri( 'assets/favicon/favicon.svg' ) . '" type="image/svg+xml">';
	}

	// Block greps
	function bones_theme_render_block( $block_content, $block ) {
		// Copyright Year
		if( $block['blockName'] === "core/paragraph" ) {
			$year_regex = "/({YEAR})/i";
			$block_content = preg_replace( $year_regex, date('Y'), $block_content );
		}

		// Change Hamburger
		// if( $block['blockName'] === "core/navigation" ) {
		// 	$svg_regex = "/<svg.*?\/svg>/i";
		// 	$svg = '<svg class="open" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 12H21M3 6H21M9 18H21" stroke="#38332F" stroke-width="2" stroke-linecap="square" stroke-linejoin="round"/></svg><svg class="close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 2L2 22M2 2L22 22" stroke="#38332F" stroke-width="2" stroke-linecap="square" stroke-linejoin="round"/></svg>';
		// 	$block_content = preg_replace( $svg_regex, $svg, $block_content );
		// }

		return $block_content;
	}

	// Fonts
	// function theme_fonts() {
	// 	print '<link rel="preconnect" href="https://fonts.googleapis.com">';
	// 	print '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
	// 	print '<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">';
	// }

	// Load Editor Styles
	function bones_theme_add_editor_styles( WP_Screen $screen ) {
		if ( $screen->base !== 'post' && $screen->base !== 'site-editor' ) {
			return;
		}
		
		$main_entry = 'src/index.js';

		try {
			$frontend_config = bones_theme_get_frontend_config(); // shared variables between js and php
			$manifest = theme_get_vite_manifest_data( $frontend_config['distFolder'] );// vite manifest
			$css_files = bones_theme_get_styles_for_entry( $main_entry, $manifest );
			if ( pathinfo( $manifest[ $main_entry ]['file'], PATHINFO_EXTENSION ) === 'css' ) {
				$css_files[] = $manifest[ $main_entry ]['file']; // add if your entry is css-only
			}

			foreach ( $css_files as $css_file ) {
				add_editor_style( "{$frontend_config['distFolder']}/$css_file" ); // path relative to the theme!
			}
		} catch (Exception $e) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions -- intentional trigger_error for admin area
			trigger_error( $e->getMessage(), E_USER_WARNING );// don't break the entire admin page
		}
	}

	// Custom Block Types
	function bones_name_register_block_styles() {
		register_block_style( 'core/image', [
			'name' => 'special-appearance',
			'label' => __( 'Special', 'bones_name' ),
		] );

		register_block_style( 'core/group', [
			'name' => 'clip-bg-top-right',
			'label' => 'Clip ◥'
		] );

		register_block_style( 'core/group', [
			'name' => 'clip-bg-top-left',
			'label' => 'Clip ◤'
		] );

		register_block_style( 'core/group', [
			'name' => 'clip-bg-bottom-right',
			'label' => 'Clip ◢'
		] );

		register_block_style( 'core/group', [
			'name' => 'clip-bg-bottom-left',
			'label' => 'Clip ◣'
		] );

		//  

		register_block_style( 'core/cover', [
			'name' => 'clip-bg-right',
			'label' => 'Clip ◥'
		] );
	}

	// Add am icon
	function add_an_icon( $icons ) {
		// Icon must include width + height in both the SVG and array values
		$icons[] = [
			'label' => 'Arrow Left',
			'icon' 	=> "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 23 14' width='23' height='14'><path d='M23.56-15.34l-6.99,6.99-.87-.87,5.52-5.51H1.11v-1.22h20.11l-5.52-5.52.87-.87,6.99,6.99ZM7.27,13.99l.87-.87L2.62,7.61h20.11v-1.22H2.62L8.13.87,7.27,0,.27,7l6.99,6.99ZM62.52-13.36l-5.52,5.52v-16.11h-1.22V-7.85l-5.51-5.52-.87.87,6.99,6.99,6.99-6.99-.87-.87Z'/></svg>",
			'width' => '23',
			'height' => '14',
			'value' => 'markson-arrow-left'
		];
		$icons[] = [
			'label' => 'Arrow Right',
			'icon' 	=> "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 23 14' width='23' height='14'><path d='M22.73,7l-6.99,6.99-.87-.87,5.52-5.51H.27v-1.22h20.11L14.87.87,15.73,0l6.99,6.99ZM6.43,36.34l.87-.87-5.52-5.52h20.11v-1.22H1.78l5.52-5.51-.87-.87L-.56,29.34l6.99,6.99ZM61.68,8.98l-5.52,5.52V-1.61h-1.22V14.49l-5.51-5.52-.87.87,6.99,6.99,6.99-6.99-.87-.87Z'/></svg>",
			'width' => '23',
			'height' => '14',
			'value' => 'markson-arrow-right'
		];
		$icons[] = [
			'label' => 'Arrow Down',
			'icon' 	=> "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 19' width='14' height='19'><path d='M-25.83,8.89l-6.99,6.99-.87-.87,5.52-5.51h-20.11v-1.22h20.11l-5.52-5.52.87-.87,6.99,6.99ZM-42.12,38.22l.87-.87-5.52-5.52h20.11v-1.22h-20.11l5.52-5.51-.87-.87-6.99,6.99,6.99,6.99ZM13.13,10.87l-5.52,5.52V.27h-1.22v16.11L.87,10.87,0,11.73l6.99,6.99,6.99-6.99-.87-.87Z'/></svg>",
			'width' => '14',
			'height' => '19',
			'value' => 'markson-arrow-down'
		];
		return $icons;
	}
	add_filter( 'enable_button_icons_icons_update', 'add_an_icon' ,  20, 1 );

function bones_theme_live_query_formatted( $post_id ) {
	// featured image 
	$post_thumbnail = ( has_post_thumbnail( $post_id ) ) ? "<div class=\"post-image\">" . 
		"<div class=\"post-excerpt\"><span>" . get_the_excerpt( $post_id ) . "</span></div>" . 
		"<a href=\"" . get_permalink( $post_id ) . "\">" . 
			get_the_post_thumbnail( $post_id, 'full' ) . 
		"</a>" . 
	"</div>" : "";

	$terms_html = "";
	if( $terms = get_the_terms( $post_id, 'project-category' ) ) {
		$terms_html = implode( ", ", array_map( function( $t ) {
			return $t->name;
		}, $terms ) );
	}

	return $post_thumbnail . "<div class=\"post-content\">" . 
		"<h6 class=\"taxonomy-terms terms-project-category\">$terms_html</h6>" .
		"<div class=\"post-excerpt\">" . get_the_excerpt( $post_id ) . "</div>" . 
		"<h5 class=\"post-title\">" . 
			"<a href=\"" . get_permalink( $post_id ) . "\">" . get_the_title($post_id) . "</a>" . 
		"</h5>" . 
	"</div>";
}

add_filter( "live_query_formatted", "bones_theme_live_query_formatted", 20, 1 );