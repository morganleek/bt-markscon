<?php
/**
 * Title: page-home
 * Slug: /page-home
 * Inserter: no
 */
?>
<!-- wp:template-part {"slug":"menu-expanded","tagName":"div","className":"navigation-modal"} /-->

<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"custom-red","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-custom-red-background-color has-background" style="margin-top:0;margin-bottom:0"><!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/home-banner__2x.avif","dimRatio":0,"customOverlayColor":"#acabab","isUserOverlayColor":false,"minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","isDark":false,"sizeSlug":"full","align":"full","className":"is-style-clip-bg-right","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-cover alignfull is-light is-style-clip-bg-right" style="margin-top:0;margin-bottom:0;min-height:100vh"><img class="wp-block-cover__image-background size-full" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/home-banner__2x.avif" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#acabab"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"dimensions":{"minHeight":"100%"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"space-between","justifyContent":"stretch","flexWrap":"wrap"}} -->
<div class="wp-block-group" style="min-height:100%"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:image {"width":"196px","height":"22px","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large is-resized"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/markscon-logo.svg" alt="" class="" style="object-fit:cover;width:196px;height:22px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:heading {"textAlign":"left","level":1,"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white"} -->
<h1 class="wp-block-heading has-text-align-left has-white-color has-text-color has-link-color">Legacy built.</h1>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"custom-red","className":"is-style-fill","icon":"markson-arrow-down"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-custom-red-background-color has-background wp-element-button">Discover</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0"><!-- wp:post-content {"layout":{"type":"constrained"}} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->