<?php
//Your access to and use of BrightEdge Link Equity Manager is governed by the 
//Infrastructure Product Terms located at: www.brightedge.com/infrastructure-product-terms. 
//Customer acknowledges and agrees it has read, understands and agrees to be bound by the 
//Infrastructure Product Terms.

//IXF: save the be_ixf_client.php file to your server, then use "require" to include it in your template. 
require 'be_ixf_client.php';
use BrightEdge\BEIXFClient;

//IXF: the following array and constructor must be placed before any HTML is written to the page.
$be_ixf_config = array(
    BEIXFClient::$CAPSULE_MODE_CONFIG => BEIXFClient::$REMOTE_PROD_CAPSULE_MODE,
    BEIXFClient::$ACCOUNT_ID_CONFIG => "f00000000164305",  
	
    //BEIXFClient::$API_ENDPOINT_CONFIG => "https://ixfd-api.bc0a.com",	
	//BEIXFClient::$CANONICAL_HOST_CONFIG => "www.domain.com",
	//BEIXFClient::$CANONICAL_PROTOCOL_CONFIG  => "https",
	
	// IXF: By default, all URL parameters are ignored. If you have URL parameters that add value to
	// page content.  Add them to this config value, separated by the pipe character (|).
    BEIXFClient::$WHITELIST_PARAMETER_LIST_CONFIG => "ixf",

);
$be_ixf = new BEIXFClient($be_ixf_config);
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<?php
		//IXF: place getHeadOpen just inside of the HTML head, used for to append SEO-related header elements.
		print $be_ixf->getHeadOpen(); 
	?>
	
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="google-site-verification" content="Evk1ArEBLA7juoIU9zNj5CZ78id4FLPFSfrZ-a7tBJw" />	
	<?php elegant_description(); ?>
	<?php elegant_keywords(); ?>
	<?php elegant_canonical(); ?>

	<?php do_action( 'et_head_meta' ); ?>

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php $template_directory_uri = get_template_directory_uri(); ?>
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( $template_directory_uri . '/scripts/ext/html5.js"' ); ?>" type="text/javascript"></script>
	<![endif]-->

	<script type="text/javascript">
		document.documentElement.className = 'js';

        var admin_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";		
	</script>
	<link rel='preload' href='//s3.amazonaws.com/cdn.banyanhill.com/wp-content/themes/BanyanHill_3_0/css/bootstrap-grid.min.css' as='style' onload='this.onload=null; this.rel="stylesheet"' />
	<link rel="preload" href="/wp-content/themes/Extra/fonts/ET-Extra.woff" as="font" type="font/ttf" crossorigin>
	<link rel="preload" href="/wp-content/themes/Extra/core/admin/fonts/modules.ttf" as="font" type="font/ttf" crossorigin>
	<script type="text/javascript" src="//script.crazyegg.com/pages/scripts/0090/2883.js" async="async"></script>
	<?php wp_head(); ?>
	
	<?php
	if ( is_front_page() && is_home() ) {
	  // Default homepage		
	  $id_page_data = 'Default';
	  $id_page_data_type = 'category';
	} elseif ( is_front_page() ) {
	  // static homepage
	  $id_page_data = 'Static';
	  $id_page_data_type = 'category';
	} elseif ( is_home() ) {
	  // blog page
	  $id_page_data = 'Home';
	  $id_page_data_type = 'category';
	} elseif ( is_category() ) {
	  // category page
	  $id_page_data = $wp_query->get_queried_object()->name;
 	  $id_page_data_type = 'category';		
	} elseif ( is_singular( 'expert' ) ) {
	  // expert page
	  $id_page_data = get_the_author_meta( 'display_name', get_post_meta( $post->ID, 'author_connection', true ) );
	  $id_page_data_type = 'author';		
	} elseif ( is_singular( 'archives' ) ) {
	  // cpt post
		
		// query all CPTs
		// not needed since archives is the only CPT that can be single
		//get_post_type() = 'archive' or 'portfolios' or 'library'
		//is_singular( array( 'archives', 'portfolios', 'library' ) )
		$archive_terms = get_the_terms($post->ID, 'archives-category')[0];

		$product_page_args = array(
			'posts_per_page' => 1,
			'post_type' => 'page',
			'order' => 'ASC',
			'orderby' => 'menu_order',
			'tax_query' => array(
				array(
					'taxonomy' => $archive_terms->taxonomy,
					'field' => 'id',
					'terms' => $archive_terms->term_id,
					'include_children' => false,
				),
			),	
		);

		$cpt_query = new WP_Query( $product_page_args );

		if (get_post_meta($cpt_query->post->post_parent, 'expert_id', true)) {
			$id_page_data = get_the_title( get_post_meta($cpt_query->post->post_parent, 'expert_id', true) );
		} else {
			$id_page_data = get_the_title( get_post_meta(end(get_ancestors($cpt_query->post->ID, 'page')), 'expert_id', true) );
		}
		
		$id_page_data_type = 'author';
	} else {
	  //everything else
		if ( $post->post_parent ) {
			// protected child pages			
			$ancestors = get_post_ancestors( $post->ID );
			$root = count( $ancestors ) - 1;
			$parent = $ancestors[$root];
		} else {
			$parent = $post->ID;
		}
		
		if ( get_post_meta($parent, 'expert_id', true) ) {
			// protected child pages
			$id_page_data = get_the_title( get_post_meta($parent, 'expert_id', true) );
			$id_page_data_type = 'author';
		} else {
			if ( $post->post_type === 'page' || is_search() ) {
				// top level pages
				$id_page_data = 'General';
				$id_page_data_type = 'category';
			} else {
				// standard posts
				$id_page_data = get_the_author_meta('display_name', $post->post_author);
				$id_page_data_type = 'author';
			}			
		}
	}	
	?>
	<meta property="lytics:<?php echo $id_page_data_type; ?>" content="<?php echo $id_page_data; ?>" />
	<meta property="lytics:url" content="<?php echo home_url( $wp->request ); ?>" />
	<script type="text/javascript">
		var lyticsIDPageMetaData = {
			<?php echo $id_page_data_type; ?>: '<?php echo $id_page_data; ?>',
			url: '<?php echo home_url( $wp->request ); ?>'
		};		
	</script>	
</head>
<body <?php body_class(); ?>>
<?php
	//IXF: place getBodyOpen and close just inside of the HTML body, used for to append SEO-related body elements.
	print $be_ixf->getBodyOpen(); 
	print $be_ixf->close(); 	
	
	if ( et_builder_is_product_tour_enabled() ) {
		return;
	}
?>
	<div id="page-container" class="page-container">
		<?php $header_vars = extra_get_header_vars(); ?>
		<!-- Header -->
		<header class="header <?php echo $header_vars['header_classes']; ?>">
			<?php if ( $header_vars['top_info_defined'] || is_customize_preview() ) { ?>
			<!-- #top-header -->
			<div id="top-header" style="<?php extra_visible_display_css( $header_vars['top_info_defined'] ); ?>">
				<div class="container">

					<!-- Secondary Nav -->
					<?php if ( '' !== $header_vars['secondary_nav'] ) { ?>
						<div id="et-secondary-nav" class="<?php echo extra_customizer_selector_classes( extra_get_dynamic_selector( 'top_navigation' ), false ); ?>">
						<?php if ( $header_vars['output_header_trending_bar'] ) { ?>

							<!-- ET Trending -->
							<div id="et-trending">

								<!-- ET Trending Button -->
								<a id="et-trending-button" href="#" title="">
									<span></span>
									<span></span>
									<span></span>
								</a>

								<!-- ET Trending Label -->
								<h4 id="et-trending-label">
									<?php esc_html_e( 'TRENDING:', 'extra' ); ?>
								</h4>

								<!-- ET Trending Post Loop -->
								<div id='et-trending-container'>
								<?php if ( $header_vars['trending_posts']->have_posts() ) : ?>
									<?php
									$trending_post_count = 0;
									while ( $header_vars['trending_posts']->have_posts() ) : $header_vars['trending_posts']->the_post();

										$trending_post_latest_class = $trending_post_count == 0 ? 'et-trending-latest' : '';

										$trending_post_classes = extra_classes( array( 'et-trending-post', $trending_post_latest_class ), 'et-trending-post', false );
									?>
										<div id="et-trending-post-<?php the_ID(); ?>" class="<?php echo esc_attr( $trending_post_classes ); ?>">
											<a href="<?php the_permalink(); ?>"><?php echo esc_html( truncate_title( 55 ) ); ?></a>
										</div>
									<?php
										$trending_post_count++;
									endwhile;

									wp_reset_postdata();
									?>
								<?php else : ?>
									<div id="et-trending-post-sample" class="et-trending-post et-trending-latest">
										<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Sample Post Title', 'extra' ); ?></a>
									</div>
								<?php endif; ?>
								</div>
							</div>
							<?php echo $header_vars['secondary_nav']; ?>

						<?php } else {
							echo $header_vars['secondary_nav'];
} ?>
						</div>
					<?php } ?>

					<!-- #et-info -->
					<div id="et-info">

						<?php if ( $header_vars['output_header_social_icons'] ) { ?>

						<!-- .et-extra-social-icons -->
						<ul class="et-extra-social-icons" style="<?php extra_visible_display_css( $header_vars['show_header_social_icons'] ); ?>">
							<?php $social_icons = extra_get_social_networks(); ?>
							<?php foreach ( $social_icons as $social_icon => $social_icon_title ) { ?>
								<?php $social_icon = esc_attr( $social_icon ); ?>
								<?php $social_icon_url = et_get_option( sprintf( '%s_url', $social_icon ), '' ); ?>
								<?php if ( '' != $social_icon_url && 'on' === et_get_option( "show_{$social_icon}_icon", 'on' ) ) { ?>
								<li class="et-extra-social-icon <?php echo $social_icon; ?>">
									<a href="<?php echo esc_url( $social_icon_url ); ?>" class="et-extra-icon et-extra-icon-background-hover et-extra-icon-<?php echo $social_icon; ?>"></a>
								</li>
								<?php } ?>
							<?php } ?>
						</ul>
						<?php } ?>

						<!-- .et-top-search -->
						<?php if ( $header_vars['output_header_search_field'] ) { ?>
						<div class="et-top-search" style="<?php extra_visible_display_css( $header_vars['show_header_search_field'] ); ?>">
							<?php extra_header_search_field(); ?>
						</div>
						<?php } ?>

						<!-- cart -->
						<?php if ( $header_vars['output_header_cart_total'] ) { ?>
						<span class="et-top-cart-total" style="<?php extra_visible_display_css( $header_vars['show_header_cart_total'] ); ?>">
							<?php extra_header_cart_total(); ?>
						</span>
						<?php } ?>
					</div>
				</div><!-- /.container -->
			</div><!-- /#top-header -->

			<?php } // end if( $et_top_info_defined ) ?>

			<!-- Main Header -->
			<div id="main-header-wrapper">
				<div id="main-header" data-fixed-height="<?php echo esc_attr( et_get_option( 'fixed_nav_height', '80' ) ); ?>">
					<div class="container">
					<!-- ET Ad -->
						<?php if ( !empty( $header_vars['header_ad'] ) ) { ?>
						<div class="etad">
							<?php echo $header_vars['header_ad']; ?>
						</div>
						<?php } ?>

						<?php
						$logo = ( $user_logo = et_get_option( 'extra_logo' ) ) && '' != $user_logo ? $user_logo : $template_directory_uri . '/images/logo.svg';
						$show_logo = extra_customizer_el_visible( extra_get_dynamic_selector( 'logo' ) ) || is_customize_preview();
						if ( $show_logo ) {
						?>

						<!-- Logo -->
						<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" data-fixed-height="<?php echo esc_attr( et_get_option( 'fixed_nav_logo_height', '51' ) ); ?>">
							<img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="logo" />
						</a>

						<?php
						}
						$et_navigation_classes = extra_classes( array(
							extra_customizer_selector_classes( extra_get_dynamic_selector( 'main-navigation' ) ),
						), 'main-navigation', false );
						?>

						<!-- ET Navigation -->
						<div id="et-navigation" class="<?php echo $et_navigation_classes; ?>">
							<?php
							$menu_class = 'nav';
							if ( 'on' == et_get_option( 'extra_disable_toptier' ) ) {
								$menu_class .= ' et_disable_top_tier';
							}

							$primary_nav = wp_nav_menu( array(
								'theme_location'            => 'primary-menu',
								'container'                 => '',
								'fallback_cb'               => '',
								'menu_class'                => $menu_class,
								'menu_id'                   => 'et-menu',
								'echo'                      => false,
								'walker'                    => new Extra_Walker_Nav_Menu,
								'header_search_field_alone' => $header_vars['header_search_field_alone'],
								'header_cart_total_alone'   => $header_vars['header_cart_total_alone'],
							) );

							if ( !$primary_nav ) {
							?>
								<ul id="et-menu" class="<?php echo esc_attr( $menu_class ); ?>">
									<?php if ( 'on' == et_get_option( 'extra_home_link' ) ) { ?>
										<li <?php if ( is_home() ) echo 'class="current_page_item"'; ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'extra' ); ?></a></li>
									<?php }; ?>

									<?php show_page_menu( $menu_class, false, false ); ?>
									<?php show_categories_menu( $menu_class, false ); ?>
								</ul>
							<?php
							} else {
								echo $primary_nav;
							}
							?>
							<?php do_action( 'et_header_top' ); ?>
						</div><!-- /#et-navigation -->
					</div><!-- /.container -->
				</div><!-- /#main-header -->
			</div><!-- /#main-header-wrapper -->

		</header>

		<?php $header_below_ad = extra_display_ad( 'header_below', false ); ?>
		<?php if ( !empty( $header_below_ad ) ) { ?>
		<div class="container">
			<div class="et_pb_extra_row etad header_below">
				<?php echo $header_below_ad; ?>
			</div>
		</div>
		<?php }
