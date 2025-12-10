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

		register_block_style( 'core/cover', [
			'name' => 'clip-bg-right',
			'label' => 'Clip ◥'
		] );

		register_block_style( 'core/gallery', [
			'name' => 'slider-gallery',
			'label' => 'Slider'
		] );

		register_block_style( 'core/button', [
			'name' => 'minimal',
			'label' => 'Minimal'
		] );

		register_block_style( 'core/table', [
			'name' => 'project',
			'label' => 'Project'
		] );

		register_block_style( 'core/columns', [
			'name' => 'stacked-reverse',
			'label' => 'Stacked Reverse '
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

function bones_theme_live_query_formatted( $html, $post_id ) {
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
		"<h4 class=\"post-title\">" . 
			"<a href=\"" . get_permalink( $post_id ) . "\">" . get_the_title($post_id) . "</a>" . 
		"</h4>" . 
	"</div>";
}

add_filter( "live_query_formatted", "bones_theme_live_query_formatted", 20, 2 );

function bones_theme_live_query_people_formatted( $html, $post_id ) {
	// featured image 
	$post_thumbnail = ( has_post_thumbnail( $post_id ) ) ? "<div class=\"post-image\">" . 
		"<a href=\"" . get_permalink( $post_id ) . "\">" . 
			get_the_post_thumbnail( $post_id, 'full' ) . 
		"</a>" . 
	"</div>" : "";

	$terms_html = "";
	if( $terms = get_the_terms( $post_id, 'role' ) ) {
		$terms_html = implode( ", ", array_map( function( $t ) {
			return $t->name;
		}, $terms ) );
	}

	return $post_thumbnail . "<div class=\"post-content\">" . 
		"<h6 class=\"post-title\">" . 
			"<a href=\"" . get_permalink( $post_id ) . "\">" . get_the_title($post_id) . "</a>" . 
		"</h6>" . 
		"<h6 class=\"taxonomy-terms terms-role\">$terms_html</h6>" .
	"</div>";
}

add_filter( "live_query_people_formatted", "bones_theme_live_query_people_formatted", 20, 2 );

function bones_theme_default_project_content( $content, $post ) {
    // Check if it's the desired custom post type (e.g., 'product')
    if ( 'project' === $post->post_type ) {
			$site_url = get_bloginfo( 'url' );
			$content = '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"0.7rem"},"blockGap":"0.5rem"},"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"custom-red","textColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-color has-custom-red-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:0.7rem"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"75%"} -->
<div class="wp-block-column" style="flex-basis:75%"><!-- wp:heading {"fontSize":"small"} -->
<h2 class="wp-block-heading has-small-font-size">Project<br>Name</h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:group {"style":{"spacing":{"blockGap":"4px","padding":{"top":"0.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:0.5rem"><!-- wp:paragraph {"align":"left"} -->
<p class="has-text-align-left"><strong>Location</strong></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":6,"style":{"typography":{"fontSize":"1rem","textTransform":"uppercase"}}} -->
<h6 class="wp-block-heading" style="font-size:1rem;text-transform:uppercase">City, country</h6>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"full","className":"is-style-clip-bg-top-left","layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group alignfull is-style-clip-bg-top-left"><!-- wp:image {"id":462,"aspectRatio":"2/1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"negative-margin"} -->
<figure class="wp-block-image size-full negative-margin"><img src="' . $site_url . '/wp-content/uploads/2025/12/project-placeholder-1.avif" alt="" class="wp-image-462" style="aspect-ratio:2/1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"var:preset|spacing|20","blockVisibility":{"controlSets":[{"id":1,"enable":true,"controls":{"screenSize":{"hideOnScreenSize":{"extraSmall":true,"small":true}}}}]}} -->
<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|80"}}}} -->
<div class="wp-block-columns" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--80)"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"project-attributes","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"380px","justifyContent":"right"}} -->
<div class="wp-block-group project-attributes"><!-- wp:heading {"level":6} -->
<h6 class="wp-block-heading">Type<br>...</h6>
<!-- /wp:heading -->

<!-- wp:heading {"level":6,"fontSize":"medium"} -->
<h6 class="wp-block-heading has-medium-font-size">Capital Value<br>...</h6>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"project-description","layout":{"type":"constrained"}} -->
<div class="wp-block-group project-description"><!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos. Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos. Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons {"lock":{"move":true,"remove":true},"className":"expanded-read-more"} -->
<div class="wp-block-buttons expanded-read-more"><!-- wp:button {"textColor":"custom-grey","className":"is-style-minimal","style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-grey"}}}},"icon":"markson-arrow-down"} -->
<div class="wp-block-button is-style-minimal"><a class="wp-block-button__link has-custom-grey-color has-text-color has-link-color wp-element-button">Read more</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"var:preset|spacing|60","blockVisibility":{"controlSets":[{"id":1,"enable":true,"controls":{"screenSize":{"hideOnScreenSize":{"extraSmall":true,"small":true}}}}]}} -->
<div style="height:var(--wp--preset--spacing--60)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-bottom:var(--wp--preset--spacing--40)"><!-- wp:gallery {"imageCrop":false,"linkTo":"none","className":"is-style-slider-gallery"} -->
<figure class="wp-block-gallery has-nested-images columns-default is-style-slider-gallery"><!-- wp:image {"id":462,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="' . $site_url . '/wp-content/uploads/2025/12/project-placeholder-1-1024x640.avif" alt="" class="wp-image-462"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":464,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $site_url . '/wp-content/uploads/2025/12/project-placeholder-3.avif" alt="" class="wp-image-464"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":463,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $site_url . '/wp-content/uploads/2025/12/project-placeholder-2.avif" alt="" class="wp-image-463"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":466,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $site_url . '/wp-content/uploads/2025/12/project-placeholder-5.avif" alt="" class="wp-image-466"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":465,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="' . $site_url . '/wp-content/uploads/2025/12/project-placeholder-4.avif" alt="" class="wp-image-465"/></figure>
<!-- /wp:image --></figure>
<!-- /wp:gallery --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","backgroundColor":"custom-grey","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-custom-grey-background-color has-background"><!-- wp:spacer {"height":"var:preset|spacing|20","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}},"blockVisibility":{"controlSets":[{"id":1,"enable":true,"controls":{"screenSize":{"hideOnScreenSize":{"small":true,"extraSmall":true}}}}]}} -->
<div style="margin-bottom:var(--wp--preset--spacing--40);height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"bottom":"var:preset|spacing|80","top":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Lorem ipsum dolor sit amet consectetur adipiscing elit quisque faucibus</h3>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"var:preset|spacing|30","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"blockVisibility":{"controlSets":[{"id":1,"enable":true,"controls":{"screenSize":{"hideOnScreenSize":{"small":true,"extraSmall":true}}}}]}} -->
<div style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"layout":{"type":"constrained","contentSize":"460px","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:table {"hasFixedLayout":false,"className":"is-style-project","fontSize":"small"} -->
<figure class="wp-block-table is-style-project has-small-font-size"><table><tbody><tr><td><strong>Type</strong></td><td>Lorem ipsum dolor sit amet consectetur</td></tr><tr><td><strong>Completion</strong></td><td>...</td></tr><tr><td><strong>Location </strong></td><td>...</td></tr><tr><td><strong>Builder</strong></td><td>...</td></tr><tr><td><strong>Developer</strong></td><td>...</td></tr><tr><td><strong>Architecture</strong></td><td>...</td></tr><tr><td><strong>Engineering</strong></td><td>...</td></tr></tbody></table></figure>
<!-- /wp:table --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"var:preset|spacing|30","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"blockVisibility":{"controlSets":[{"id":1,"enable":true,"controls":{"screenSize":{"hideOnScreenSize":{"small":true,"extraSmall":true}}}}]}} -->
<div style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"border":{"radius":{"topLeft":"6px","topRight":"6px","bottomLeft":"6px","bottomRight":"6px"}}},"layout":{"type":"constrained","contentSize":"936px"}} -->
<div class="wp-block-group" style="border-top-left-radius:6px;border-top-right-radius:6px;border-bottom-left-radius:6px;border-bottom-right-radius:6px"><!-- wp:embed {"url":"https://youtu.be/-uGLTxwJ7LY?list=TLGGOmnRualmls4wMzEyMjAyNQ","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://youtu.be/-uGLTxwJ7LY?list=TLGGOmnRualmls4wMzEyMjAyNQ
</div></figure>
<!-- /wp:embed --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"constrained","contentSize":"932px"}} -->
<div class="wp-block-group"><!-- wp:spacer {"height":"var:preset|spacing|30","blockVisibility":{"controlSets":[{"id":1,"enable":true,"controls":{"screenSize":{"hideOnScreenSize":{"extraSmall":true,"small":true}}}}]}} -->
<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:quote {"style":{"spacing":{"margin":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|20"}}} -->
<blockquote class="wp-block-quote" style="margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--80)"><!-- wp:heading {"level":3,"style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-red"}}}},"textColor":"custom-red","fontFamily":"graphik-condensed"} -->
<h3 class="wp-block-heading has-custom-red-color has-text-color has-link-color has-graphik-condensed-font-family">“Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus.”</h3>
<!-- /wp:heading --><cite> Full Name, Role, Company</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group -->';
    }
    return $content;
	}
	add_filter( 'default_content', 'bones_theme_default_project_content', 10, 2 );