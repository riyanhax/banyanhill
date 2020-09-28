<?php
	add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
	function theme_enqueue_styles() {
	    wp_enqueue_style( 'extra', get_template_directory_uri() . '/style.css' );

	}
	function remove_category_text_from_archive_title($title) {
		return is_category() ? single_cat_title('', false) : $title;
	}
	add_filter('get_the_archive_title', 'remove_category_text_from_archive_title');

	function get_custom_template($single_template) {
		 global $post;
		
		 parse_str($_SERVER['QUERY_STRING'], $query);

		 if ( in_category( 'sponsorship' )) {
			  $single_template = dirname( __FILE__ ) . '/single-category-sponsorship.php';
		 }
		
		 if ( !empty($query['email']) && $query['email'] === 'true' ) {
			 if ( in_category( 'sovereign-investor-daily' ) ) {
				 $category = 'sovereign-investor-daily';
			 }
			 
			 if ( in_category( 'smart-profits-daily' ) ) {
				 $category = 'smart-profits-daily';
			 }				 
			 
			 $single_template = dirname( __FILE__ ) . '/template-email-' . $category . '.php';
		 }
		
		 return $single_template;
	}
	add_filter( "single_template", "get_custom_template" );

	// Speed Optimizations
	if ( !is_admin() ) {
		// load CSS asynchronously
		wp_register_script( 'load-css-async', get_stylesheet_directory_uri() . '/js/loadCSS.min.js', array(), '', false );
		wp_enqueue_script( 'load-css-async' );
		wp_register_script( 'load-css-async-relpreload', get_stylesheet_directory_uri() . '/js/cssrelpreload.min.js', array(), '', false );
		wp_enqueue_script( 'load-css-async-relpreload' );

		// update link tags to allow async load
		function preload_style_tags($tag){
			if (strpos($tag, 'Extra/style.css') === false) {
				$tag = preg_replace("/='stylesheet'/", "='preload'", $tag);
				$tag = preg_replace("/\/>/", "as='style' onload='this.onload=null; this.rel=\"stylesheet\"' />", $tag);
				return $tag;
			} else {
				return $tag;
			}
		}
		add_filter('style_loader_tag', 'preload_style_tags');

		add_action( 'wp_enqueue_scripts', 'bs_dequeue_scripts', 999 );
		
		function bs_dequeue_scripts() {
			add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
			wp_deregister_script( 'plupload' );		
			wp_deregister_script('wp-mediaelement');
			remove_action('wp_head', 'print_emoji_detection_script', 7);
		}

		function defer_parsing_of_js ( $url ) {
			if ( FALSE === strpos( $url, '.js' ) ) return $url;
			if ( strpos( $url, 'jquery.js' ) ) return $url;
			
			return "$url' defer='defer";
		}
	}
	// End Speed Optimizations

	function bh_stock_ticker_db() {
		$stockdb = new mysqli('banyanhillweb.com', 'freedom7_stocks', 'UXJ?Fxc8Z2S0', 'freedom7_stocks');

		if ($stockdb->connect_error) {
			die("Connection failed: " . $stockdb->connect_error);
		}

		return $stockdb;
	}

	function bh_stock_ticker_top_five() {
		$stockdb = bh_stock_ticker_db();

		$builder = [];
		$rs = $stockdb->query("SELECT `symbol`,`name`,`marketcap` FROM companies WHERE id IN (SELECT id FROM (SELECT id FROM companies WHERE marketcap != 'N/A' AND topstock = 1 ORDER BY RAND() LIMIT 10) t)");
		while($r = $rs->fetch_assoc()){
			$builder[]= ['symbol' => $r['symbol'], 'name' => $r['name'], 'marketcap' => $r['marketcap']];
		}
		return $builder;
	}

	function bh_stock_ticker_top() {
		$stockdb = bh_stock_ticker_db();

		$builder = [];
		$rs = $stockdb->query("SELECT `symbol`,`name` FROM `companies` WHERE `topstock` = 1;");
		while($r = $rs->fetch_assoc()){
			$builder[]= ['label' => $r['symbol'] . ': ' . $r['name'], 'name' => $r['name'], 'value' => $r['symbol']];
		}
		return $builder;
	}

	function bh_ajax_company_ticker() {
		$stockdb = bh_stock_ticker_db();

		$search_escaped = $stockdb->real_escape_string($_POST['search']);

		$builder = [];
		$rs = $stockdb->query("SELECT `symbol`,`name` FROM `companies` WHERE `symbol` LIKE '$search_escaped%' LIMIT 10;");
		while($r = $rs->fetch_assoc()){
			$builder[]= ['label' => $r['symbol'] . ': ' . $r['name'], 'name' => $r['name'], 'value' => $r['symbol']];
		}

		if(count($builder) < 10) {
			$limit = 10 - count($builder);
			$rs = $stockdb->query("SELECT `symbol`,`name` FROM `companies` WHERE `name` LIKE '$search_escaped%' LIMIT $limit;");
			while($r = $rs->fetch_assoc()){
				if(array_search($r['symbol'], array_column($builder, 'value')) === false) {
					$builder[]= ['label' => $r['symbol'] . ': ' . $r['name'], 'name' => $r['name'], 'value' => $r['symbol']];
				}
			}
		}

		if(count($builder) < 10) {
			$limit = 10 - count($builder);
			$rs = $stockdb->query("SELECT `symbol`,`name` FROM `companies` WHERE `symbol` LIKE '%$search_escaped%' OR `name` LIKE '%$search_escaped%' LIMIT $limit;");
			while($r = $rs->fetch_assoc()){
				if(array_search($r['symbol'], array_column($builder, 'value')) === false) {
					$builder[]= ['label' => $r['symbol'] . ': ' . $r['name'], 'name' => $r['name'], 'value' => $r['symbol']];
				}
			}
		}

		wp_send_json_success( array(
			'success' => true,
			'values' => $builder
		) );
	}

	add_action( 'wp_ajax_bh_ajax_company_ticker', 'bh_ajax_company_ticker' );
	add_action( 'wp_ajax_nopriv_bh_ajax_company_ticker', 'bh_ajax_company_ticker' );

	function bh_ajax_company_ticker_tracker() {
		$stockdb = bh_stock_ticker_db();

		$search_escaped = $stockdb->real_escape_string($_POST['search']);

		$stockdb->query("INSERT INTO `searches`
			(`created`,`userid`,`search`)
				VALUES
			('".gmdate("Y-m-d H:i:s")."','".wp_get_current_user()->ID."','".$search_escaped."')");

		wp_send_json_success( array(
			'success' => true,
		) );
	}

	add_action( 'wp_ajax_bh_ajax_company_ticker_tracker', 'bh_ajax_company_ticker_tracker' );
	add_action( 'wp_ajax_nopriv_bh_ajax_company_ticker_tracker', 'bh_ajax_company_ticker_tracker' );

	function bh_ajax_ticker_check_symbol() {
		$handle = curl_init("https://www.tradingview.com/symbols/".$_POST['symbol']."/");
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

		$response = curl_exec($handle);

		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if($httpCode == 404) {
			$exists = false;
		} else {
			$exists = true;
		}

		curl_close($handle);

		wp_send_json_success( array(
			'exists' => $exists
		) );
	}

	add_action( 'wp_ajax_bh_ajax_ticker_check_symbol', 'bh_ajax_ticker_check_symbol' );
	add_action( 'wp_ajax_nopriv_bh_ajax_ticker_check_symbol', 'bh_ajax_ticker_check_symbol' );

	//Shortcode to add the signup form
	//[bh_signup_form buttontext="Button Text" emailtext="Email Text" xcode="XCODE123" position="CSS_CLASS" multivariate="BOOLEAN" nocoreg="BOOLEAN"]
	function bh_signup_form_func($atts) {
		// Set defaults if no value is passed
		if ( isset( $_GET['z'] ) && !empty( $_GET['z'] ) ) {
			$atts['xcode'] = $_GET['z'];
		}
		
		$a = shortcode_atts( array(
			'buttontext' => 'Join Now',
			'emailtext' => 'Enter Your Email Address',
			'xcode' => ( isset( $_GET['z'] ) && !empty( $_GET['z'] ) ) ? $_GET['z'] : ( get_post_meta(get_the_ID(), 'site_xcode', true) ? get_post_meta(get_the_ID(), 'site_xcode', true) : 'X190U401' ),
			'position' => '',
			'multivariate' => '',
			'nocoreg' => ''
		), $atts );
		
		$markup  = '<iframe class="loading" data-src="/wp-content/signup/?';
		$markup .= 'buttontext='.htmlspecialchars($a['buttontext'], ENT_QUOTES).'&';
		$markup .= 'emailtext='.htmlspecialchars($a['emailtext'], ENT_QUOTES).'&';
		$markup .= 'xcode='.htmlspecialchars($a['xcode'], ENT_QUOTES).'&';
		$markup .= $a['nocoreg'] === 'true' ? 'no_coreg=1&' : '';
		$markup .= $a['position'] ? 'position='.htmlspecialchars($a['position'], ENT_QUOTES).'&' : '';
		$markup .= $a['multivariate'] === 'true' ? 'multivariate='.htmlspecialchars($a['multivariate'], ENT_QUOTES).'&' : '';		
		$markup .= '" width="100%"></iframe>';
		
		return $markup;
	}

	add_shortcode( 'bh_signup_form', 'bh_signup_form_func' );

	add_shortcode( 'bh_quote', 'bh_blockquote_func');
	function bh_blockquote_func($atts, $content = null) {
		return '<div class="post-excerpt"><blockquote>' . $content . '</blockquote></div>';
	}

	add_shortcode( 'bh_accordion', 'bh_accordion_func');
	function bh_accordion_func($atts, $content = null) {
		return '<style>.accoridion-content {background: #eee;color: #222; overflow: hidden; padding: 10px;} .accordion-button {width: 100%; text-align: center; margin: 10px 0; background-color: rgba(0,0,0,0.25); border-radius: 3px; padding: 10px; color: #fff; text-transform: uppercase; cursor: pointer;} .blue {background-color: #113752;}</style><div class="accoridion-content" style="display: none;">' . $content . '</div><div class="accordion-button blue">Click Here to View Full Transcript</div><script>jQuery(".accordion-button").on("click", function(){if (jQuery(this).hasClass("active")) {	jQuery(this).removeClass("active").text("Click here to show full transcript"); jQuery("html, body").animate({scrollTop: accPos}, 1000); jQuery(".accoridion-content").slideUp(1000);} else {accPos = jQuery(".accordion-button").offset().top - 100; jQuery(this).addClass("active").text("Click here to hide full transcript"); jQuery(".accoridion-content").slideDown(1000);}});</script>';
	}

	add_shortcode( 'bh_transcript', 'bh_transcript_func');
	function bh_transcript_func($atts, $content = null) {
		$string = '<div class="tab blue transcript"><input name="tabs" type="checkbox" /><label>Click Here to View Full Transcript</label><div class="tab-content"><p>';
		$string .= $content;
		$string .= '</div></div><style>.single-post .tab.transcript input {width: 100%; height:100%; z-index:1; cursor: pointer; height: 51px; margin:0;}.tab input:checked ~ .tab-content {max-height: 600vh;}.blue .tab-content {background: #eee;color: #222;}</style>';
		
		return $string;
		
	}

	//Add button(s) to the tinyMCE visual editor
	add_action('admin_init', 'add_button');
	function add_button() {
	   if ( current_user_can('edit_posts') && current_user_can('edit_pages') ) {
			if ( in_array(basename($_SERVER['PHP_SELF']), array('post-new.php', 'page-new.php', 'post.php', 'page.php') ) ) {		   
		 		add_filter('mce_buttons', 'register_button');
		 		add_filter('mce_external_plugins', 'add_plugin');
	   		}
	   }
	}

	function register_button($buttons) {
	   array_push($buttons, "bh_quote", "bh_accordion", "bh_transcript");

	   return $buttons;
	}

	function add_plugin($plugin_array) {
	   $plugin_array['bh_quote'] = get_stylesheet_directory_uri() . '/js/customcodes.js';
	   $plugin_array['bh_accordion'] = get_stylesheet_directory_uri() . '/js/customcodes.js';
	   $plugin_array['bh_transcript'] = get_stylesheet_directory_uri() . '/js/customcodes.js';

	   return $plugin_array;
	}

	//Add button(s) to Standard WP Text (HTML) Editor
	add_action( 'admin_print_footer_scripts', function() {
		if (wp_script_is('quicktags')) { ?>
			<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() . '/js/customcodes.js'; ?>">
		<?php }
	}, 100 );

	//TotalPoll Hook to add email address to message central
	add_filter( 'totalpoll/filters/poll/log', 'add_email_to_list' );

	function add_email_to_list($log) {
		if ($log['fields']['__submission_date']) {
			$url = 'https://research.banyanhill.com/Content/SaveFreeSignups';
			$data = array('source' => $log['fields']['xcode'][0] ? $log['fields']['xcode'][0] : 'X190U5DP', 'NotSaveSignup' => 'False', 'email' => $log['fields']['email'], 'CoRegs' => $log['fields']['coreg'][0] ? $log['fields']['coreg'][0] : '172279');
			$options = array(
					'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data),
				)
			);

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);	

			return $log;			
		} else {
			return $log;
		}
	}

	/**
	 * Javascript for Load More
	 *
	 */
	function be_load_more_js() {
		$content = get_page( get_the_ID() )->post_content;
		$is_infinite_scroll = false;

		if (has_shortcode($content, 'display-posts')) {
			if ( preg_match( '/infinite_scroll="([^\s]+)"/', $content, $match ) )  {
				$is_infinite_scroll = (boolean) $match[1];
			}
		}

		if( ! is_singular( 'post' ) && ! $is_infinite_scroll )
			return;

		global $wp_query;
		if (is_singular( 'post' )) {
			$query = array(
				'post__not_in' => array( get_queried_object_id() ),
				'category_name' => get_post_primary_category(get_the_ID())['primary_category']->slug,
				'posts_per_page' => 1
			);

			$args = array(
				'nonce'         => wp_create_nonce( 'be-load-more-nonce' ),
				'url'           => admin_url( 'admin-ajax.php' ),
				'query'         => $query,
				'trigger'       => '.related-posts',
				'type'          => 'article',
				'loadMoreLimit' => 5,
			);
		} else {
			$query = array(
				'posts_per_page' => 5
			);
			if ( preg_match( '/author_id="([^\s]+)"/', $content, $match ) )  {
				$query['author'] = (int) $match[1];
			}

			$args = array(
				'nonce'         => wp_create_nonce( 'be-load-more-nonce' ),
				'url'           => admin_url( 'admin-ajax.php' ),
				'query'         => $query,
				'trigger'       => '#hook-infinite',
				'type'          => 'page',
				'loadMoreLimit' => 10,
			);
		}

		wp_enqueue_script( 'be-load-more', get_stylesheet_directory_uri() . '/js/load-more.js', array( 'jquery' ), true );
		wp_localize_script( 'be-load-more', 'beloadmore', $args );
		
		// Add specific CSS class by filter.
		add_filter( 'body_class', function( $classes ) {
			return array_merge( $classes, array( 'load-more-single' ) );
		} );		

	}
	add_action( 'wp_enqueue_scripts', 'be_load_more_js' );

	/**
	 * AJAX Load More 
	 *
	 */
	function be_post_summary($qs_source) {
		set_query_var( 'qs_source', $qs_source );
		get_template_part( 'single-post-loader' );
	}

	function be_ajax_load_more() {

		check_ajax_referer( 'be-load-more-nonce', 'nonce' );

		$args = isset( $_POST['query'] ) ? $_POST['query'] : array();
		$args['post_type'] = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
		$args['paged'] = $_POST['page'];
		$args['post_status'] = 'publish';
		$qs_source = isset( $_POST['qs_source'] ) ? $_POST['qs_source'] : array();

		$post_ids = [];
		$post_names = [];
		$post_titles = [];
		$post_excerpts = [];
		$post_permalinks = [];
		$post_dates = [];
		$post_thumbnails = [];
		$post_categories = [];
		$post_thumbnails_srcset = [];

		ob_start();
		$loop = new WP_Query( $args );

		if( $loop->have_posts() ) {
			while( $loop->have_posts() ) {
				$loop->the_post();
				be_post_summary($qs_source);

				$post_ids[]= $loop->post->ID;
				$post_names[]= $loop->post->post_name;
				$post_titles[]= $loop->post->post_title;
				$post_excerpts[]= get_the_excerpt($loop->post->ID);
				$post_permalinks[]= get_the_permalink($loop->post->ID);
				$post_dates[]= get_the_date('F j, Y', $loop->post->ID);

				$post_thumbnail = get_the_post_thumbnail_url($loop->post->ID, 'extra-image-small' );
				$post_thumbnail_src = wp_get_attachment_image_srcset(get_post_thumbnail_id( $loop->post->ID ));

				$post_thumbnails[]= $post_thumbnail;

				if($post_thumbnail_src){
					$post_thumbnails_srcset[]= $post_thumbnail_src;
				} else {
					$post_thumbnails_srcset[]= $post_thumbnail;
				}

				$post_categories_loop = [];
				$post_categories_builder = get_the_category($loop->post->ID);
				foreach($post_categories_builder as $cat) {
					$post_categories_loop[]= ['name' => $cat->name, 'slug' => $cat->slug];
				}
				$post_categories[]= $post_categories_loop;
			}
		}
		$data = ob_get_clean();

		$data = array(
			'content' => $data,
			'post_ID' => $post_ids,
			'post_name' => $post_names,
			'post_title' => $post_titles,
			'post_excerpt' => $post_excerpts,
			'post_permalink' => $post_permalinks,
			'post_date' => $post_dates,
			'post_thumbnails' => $post_thumbnails,
			'post_thumbnails_srcset' => $post_thumbnails_srcset,
			'post_categories' => $post_categories,
			'loop'=> $loop
		);
		wp_reset_postdata();	
		wp_send_json_success( $data );
	}
	add_action( 'wp_ajax_be_ajax_load_more', 'be_ajax_load_more' );
	add_action( 'wp_ajax_nopriv_be_ajax_load_more', 'be_ajax_load_more' );

	// Hide Infinite Scroll Pages from Indexing
	add_filter( 'wpseo_robots', 'yoast_seo_robots_remove_search' );
	function yoast_seo_robots_remove_search( $robots ) {
	  if ( isset( $_GET['post_ids'] ) || !empty( $_GET['post_ids'] ) ) {
		return 'noindex, nofollow';
	  } else {
		return $robots;
	  }
	}

	// Hide Yoast from CPTs
	function BH_remove_wp_seo_yoast_internal_linking() {
		remove_meta_box('yoast_internal_linking', ['archives', 'exclusives', 'expert', 'page', 'portfolio', 'testimonials', 'library'], 'side');
		remove_meta_box('wpseo_meta', ['archives', 'portfolio', 'testimonials', 'library'], 'normal');
	}
	//add_action('do_meta_boxes', 'BH_remove_wp_seo_yoast_internal_linking', 100);

	// Check authentication by post ID
	function check_auth_by_post_id( $post_id, $post_permalink ) {
		if ( class_exists( 'agora_auth_container' ) ) {
			// An auth container gets passed around to all the authentication methods until we know if the user can or can't see the content.
			// The auth container needs to know the post ID to look at.
			$auth_container = new agora_auth_container( $post_id );

			// The agora_middleware_check_permission filter fires up all the auth methods and passes the container to each one.
			$auth_container = apply_filters( 'agora_middleware_check_permission' , $auth_container );

			// When we get the auth container back, we should know if the user can or can't see the content.
			if ( $auth_container->is_allowed() ) {
				return $auth_container;
			}

			$query_string = $_SERVER[ 'QUERY_STRING' ];

			if ( ! empty( $query_string ) ) {
				$query_string = '?redirect_to=' . urlencode( $post_permalink . '?' . $query_string );
			} else {
				$query_string = '?redirect_to=' . urlencode( $post_permalink );
			}

			// do redirect
			$location = '/login/';

			wp_redirect( $location . $query_string );
			die;
		}
	}

	// Explicitly allow TWF subs
	// http://docs.threefoldsystems.com:8090/display/shareit/36601918/LNUaa5a8557fd8b4f019b5b28d9ccffe984IJR
	add_filter('agora_middleware_check_permission', 'check_twf_active', 1, 1);

	function check_twf_active($auth_container) {
		if (!is_user_logged_in()) {			
			// is this costing us cycles?
			return $auth_container;
		} else {
			$pubcode = get_the_terms( $auth_container->post_id, 'pubcode' )[0]->name;

			if ($pubcode === 'TWF') {
				$user_subscription_meta = wp_get_current_user()->middleware_data->subscriptionsAndOrders->subscriptions;

				foreach ( $user_subscription_meta as $sub ) {
					if ($sub->id->item->itemNumber === 'SOV' && $sub->memberCat === 'GM') {
						return $auth_container->allow();
					}
				}
				
				return $auth_container;
			} else {
				return $auth_container;
			}
		}
	}

	// Ajax callback - load more archives
	function load_more_archives() {
		$offset = $_POST['offset'];
		$number_post = 10;
		$terms_id = $_POST['term_id'];

		if($_POST['srh'] == 'search' && $_POST['archivedate'] != '')
		{
			$arg =array(
				'posts_per_page' => $number_post,
				'offset' => $offset,
				'post_type' => 'archives',
				'post_status' => 'publish',
				'meta_key' => 'order_date',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'archives-category',
						'field' => 'id',
						'terms' => $terms_id,
						'include_children' => false,
					),
				),
				'meta_query' => array(
					array(
						'key' => 'date_for_search_archive',
						'value' => $_POST['archivedate'],
						'compare' => 'LIKE',
					),
				)
			);
		}
		else
		{
			$arg =array(
				'posts_per_page' => $number_post,
				'offset' => $offset,
				'post_type' => 'archives',
				'post_status' => 'publish',
				'meta_key' => 'order_date',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'archives-category',
						'field' => 'id',
						'terms' => $terms_id,
						'include_children' => false,
					),
				),
			);
		}

		query_posts($arg);
		$total_posts = query_posts($arg);
		$count = count($total_posts);

		?>
		<?php  if ( have_posts() ) { ?>
			<?php $i = 0;
			while (have_posts()) : the_post(); ?>
				<?php if ($i % 2 == '0') { ?>
					<div class="archive-block">
					<div class="row ">
				<?php } ?>

				<div class="col-md-6 col-xs-12 cf <?php if ($i % 2 == '1') {
					echo "last";
				} ?>">
					<div class="archive_item <?php echo get_the_terms( get_the_ID(), 'pubcode' ) ? '' : 'missing-pubcode' ?>">
						<div class="mob_inner">
							<h2>
								<a onclick="<?php echo get_post_meta(get_the_ID(), 'event_tracking_code', true); ?>"
								   href="<?php the_permalink(); ?>">
									<?php if ( get_post_meta(get_the_ID(), 'page_title', true) ) {
										echo get_post_meta(get_the_ID(), 'page_title', true);
									} else {
										the_title();
									} ?>
								</a> 
							</h2>
							<div class="date_block">
								<i class="fa fa-clock-o"></i> <span
									class="archive_date"><?php if ( get_post_meta(get_the_ID(), 'archieve_date', true) ) {
										echo get_post_meta(get_the_ID(), 'archieve_date', true);
									} else {
										the_time('F j, Y');
									} ?>
								</span>
							</div>
							<p>
								<?php echo wp_strip_all_tags( get_the_excerpt() ); ?> <a href="<?php the_permalink(); ?>" class="readMore">Read More</a>
							</p>
						</div>
					</div>
				</div>
				<?php if ($i % 2 == '1' || ($i + 1) == $count) { ?>
					</div>
					</div>
				<?php } ?>
				<?php $i++; endwhile;
			wp_reset_query();
		} 

		die();
	}

	add_action("wp_ajax_nopriv_load_more_archives","load_more_archives");
	add_action("wp_ajax_load_more_archives","load_more_archives");

	// Ajax callback - load more portfolios
	function load_more_portfolio() {
		$offset = $_POST['offset'];
		$number_post = 50;
		$terms_id = $_POST['term_id'];


		if($_POST['srh'] == 'search' && $_POST['portfoliodate'] != '')
		{
			$arg =array(
				'posts_per_page' => $number_post,
				'offset' => $offset,
				'post_type' => 'portfolio',
				'post_status' => 'publish',
				'meta_key' => 'order_date',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'portfolio-category',
						'field' => 'id',
						'terms' => $terms_id,
						'include_children' => false,
					),
				),
				'meta_query' => array(
					array(
						'key' => 'date_for_search_portfolio',
						'value' => $_POST['portfoliodate'],
						'compare' => 'LIKE',
					),
				)
			);
		}
		else
		{
			$arg =array(
				'posts_per_page' => $number_post,
				'offset' => $offset,
				'post_type' => 'portfolio',
				'post_status' => 'publish',
				'meta_key' => 'order_date',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'portfolio-category',
						'field' => 'id',
						'terms' => $terms_id,
						'include_children' => false,
					),
				),
			);
		}
		query_posts($arg);
		//print_r($posts);

		$total_post = query_posts($arg);

		$count = count($total_post);

		if ( have_posts() ) {
			$i = 0;

			while (have_posts()) : the_post(); ?>

				<?php if ($i % 2 == '0') { ?>
					<div class="archive-block">
					<div class="row ">
				<?php } ?>

				<div class="col-md-6 col-xs-12 cf <?php if ($i % 2 == '1') {
					echo "last";
				} ?>">
				  <a class="pdf-block"
					   onclick="<?php echo get_post_meta(get_the_ID(), 'event_tracking_code', true); ?>"
					   target="_blank"
					   href="<?php if ( get_post_meta(get_the_ID(), 'pdf_link', true) ) {
						   echo get_post_meta(get_the_ID(), 'pdf_link', true);
					   } else {
						   echo "javascript;";
					   } ?>">

						<?php if ( get_post_meta(get_the_ID(), 'pdf_link', true) ) { ?>
							<h3><?php echo get_post_meta(get_the_ID(), 'pdf_title', true); ?></h3><?php } ?>
						<span><?php if ( get_post_meta(get_the_ID(), 'pdf_date', true) ) {
								echo get_post_meta(get_the_ID(), 'pdf_date', true);
							} else {
								the_time( 'F j, Y' );
							} ?>
					</a>
				</div>

				<?php
				if ($i % 2 == '1' || ($i + 1) == $count) {
					?>
					</div>
					</div>
					<?php
				}

				$i++;
			endwhile;
			wp_reset_query();
		}

		die();
	}

	add_action("wp_ajax_nopriv_load_more_portfolio","load_more_portfolio");
	add_action("wp_ajax_load_more_portfolio","load_more_portfolio");

	// Ajax callback - load more webinars
	function load_more_webinar() {
		$offset = $_POST['offset'];
		$totalcount = $_POST['total_count'];
		$number_post = $_POST['post_per_page'];
		$terms_id = $_POST['term_id'];
		$count_ebox1 = $_POST['count_ebox1'] + 1;
		$count_ebox2 = $_POST['count_ebox2'] + 1;
		$count_ebox3 = $_POST['count_ebox3'] + 1;

		if($_POST['srh'] == 'search' && $_POST['librarydate'] != '')
		{
			$arg =array(
				'posts_per_page' => $number_post,
				'offset' => $offset,
				'post_type' => 'library',
				'post_status' => 'publish',
				'meta_key' => 'order_date',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'library-category',
						'field' => 'id',
						'terms' => $terms_id,
						'include_children' => false,
					),
				),
				'meta_query' => array(
					array(
						'key' => 'date_for_search_library',
						'value' => $_POST['librarydate'],
						'compare' => 'LIKE',
					),
				)
			);
		}
		else
		{
			$arg =array(
				'posts_per_page' => $number_post,
				'offset' => $offset,
				'post_type' => 'library',
				'post_status' => 'publish',
				'meta_key' => 'order_date',
				'orderby' => 'meta_value_num',
				'order' => 'DESC',
				'tax_query' => array(
					array(
						'taxonomy' => 'library-category',
						'field' => 'id',
						'terms' => $terms_id,
						'include_children' => false,
					),
				),
			);
		}
		query_posts($arg);
		$total_post = query_posts($arg);

		$count = count($total_post);

		?>
		<?php  if ( have_posts() ) {
			$i = 0;
			while (have_posts()) : the_post(); ?>

				<?php if ($i % 2 == '0') { ?>
					<div class="archive-block">
					<div class="row ">
				<?php } ?>

				<div class="col-md-6 col-xs-12 cf <?php if ($i % 2 == '1') {
					echo "last";
				} ?>">
					<div class="archive_item <?php echo get_the_terms( get_the_ID(), 'pubcode' ) ? '' : 'missing-pubcode' ?>">
						<div class="mob_inner">
							<h2 <?php echo $postBckgndStyle ?>>
								<a <?php if ( get_post_meta(get_the_ID(), 'event_tracking_code', true) ) { ?>
								   onclick="<?php echo get_post_meta(get_the_ID(), 'event_tracking_code', true); ?>" 
								   <?php } ?>
								   rel="modal:open"
								   href="#<?php echo strtotime( get_post_meta(get_the_ID(), 'webinar_library_date', true) ); ?>">
									<?php if ( get_post_meta(get_the_ID(), 'webinar_library_description', true) ) {
										echo get_post_meta(get_the_ID(), 'webinar_library_description', true);
									} else {
										the_title();
									} ?>
								</a>
							</h2>
							<div class="row archive-details">
								<div class="col-lg-6 col-md-6 col-sm-12 col-12 date-container">		
									<div class="date_block">
										<i class="fa fa-clock-o"></i> <span 
											class="archive_date"><?php if ( get_post_meta(get_the_ID(), 'webinar_library_date', true) ) {
												echo get_post_meta(get_the_ID(), 'webinar_library_date', true);
											} else {
												the_time( 'F j, Y' );
											} ?>
										</span>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-12 col-12">					
									<p><a <?php if ( get_post_meta(get_the_ID(), 'event_tracking_code', true) ) { ?>
										   onclick="<?php echo get_post_meta(get_the_ID(), 'event_tracking_code', true); ?>" 
										   <?php } ?> 
										  href="<?php if(get_post_meta(get_the_ID(), 'transcrip_pdf_link', true)) { 
												echo get_post_meta(get_the_ID(), 'transcrip_pdf_link', true); 
											} else { 
												echo "javascript:;"; 
											} ?>" 
										  class="readMore" 
										  target="_blank"><i class="fa fa-file-pdf-o"></i> 
										Read transcript
										</a>
									</p>
								</div>
							</div>
						</div>
					</div>
					<div id="<?php echo strtotime( get_post_meta(get_the_ID(), 'webinar_library_date', true) ); ?>" class="modal">
						<div class="modalHeader">
							<div>
								<h1 class="sectionHead"><?php echo get_post_meta(get_the_ID(), 'webinar_library_description', true); ?></h1>
							</div>
							<div>
								<a href="<?php echo esc_url( get_post_meta(get_the_ID(), 'webinar_library_embed_link', true) ) ?>" target="_blank">(view page)</a>
							</div>
							<div class="clear"></div>
						</div>
						<hr class="sectionHR">
						<div id="modalContainer">
							<iframe class="loading-iframe" data-src="<?php if( get_post_meta(get_the_ID(), 'webinar_library_embed_link', true) ) { 
										echo str_replace('http:', 'https:', get_post_meta(get_the_ID(), 'webinar_library_embed_link', true) ); 
									} else { 
										echo "javascript:;"; 
									} ?>" 
									width="100%" 
									height="450" 
									frameborder="0"></iframe>
						</div>
					</div>						
				</div>

				<?php
				if ($i % 2 == '1' || ($i + 1) == $count) {
					?>
					</div>
					</div>
					<?php
				}

				$i++;
			endwhile;
			wp_reset_query();
		}
	?>
		<script type="application/javascript">
			jQuery(document).ready(function() {		
				jQuery('.modal').on(jQuery.modal.OPEN, function(event, modal) {
					var bLazy = new Blazy({ 
						selector: '.loading-iframe', //ad iframes
						success: function(element){
							setTimeout(function(){
								element.className = element.className.replace(/\bloading-iframe\b/,'');
							}, 200);
						}				
					});
				});
			});
		</script>
	<?php
		die();
	}

	add_action("wp_ajax_nopriv_load_more_webinar","load_more_webinar");
	add_action("wp_ajax_load_more_webinar","load_more_webinar");

	// Ajax callback - load more portfolios
	function ajax_instantSearch_filter() {
		$number_post = 99999;
		$terms_id    = $_POST['term_id'];
		$arg		 = $_POST['query_args'];
//		$arg         = array(
//			'posts_per_page' => $number_post,
//			'post_type'      => 'portfolio',
//			'meta_key'       => 'order_date',
//			'orderby'        => 'meta_value_num',
//			'order'          => 'DESC',
//			'tax_query'      => array(
//				array(
//					'taxonomy'         => 'portfolio-category',
//					'field'            => 'id',
//					'terms'            => $terms_id,
//					'include_children' => false,
//				),
//			),
//		);
		query_posts( $arg );
		$total_post = query_posts( $arg );
		$count = count( $total_post );
		$data_return = array();
		if ( have_posts() ) {
			$i = 0;
			while ( have_posts() ) {
				the_post();
				
				$pdf_tracking = get_post_meta( get_the_ID(), "event_tracking_code", true );
				if ( get_post_meta( get_the_ID(), "pdf_link", true ) ) {
					$pdf_link = get_post_meta( get_the_ID(), "pdf_link", true );
				} else {
					$pdf_link = "javascript;";
				}
				$pdf_title = get_post_meta( get_the_ID(), "pdf_title", true );
				if ( get_post_meta( get_the_ID(), "pdf_date", true ) ) {
					$pdf_date = get_post_meta( get_the_ID(), "pdf_date", true );
				} else {
					$pdf_date = '-';
				}
				$tags = get_the_tag_list('',' ','');
				if(!$tags){
					$tags = 'all';
				}

				array_push(
					$data_return,
					array(
						'event_tracking_code' => $pdf_tracking,
						'pdf_link'            => $pdf_link,
						'pdf_title'           => $pdf_title,
						'pdf_keyword'         => $tags,
						'pdf_date'            => $pdf_date
					)
				);
			}
			wp_reset_query();
		}
		echo json_encode($data_return);
		die();
	}

	add_action( "wp_ajax_instantSearch_filter", "ajax_instantSearch_filter" );
	add_action( "wp_ajax_nopriv_instantSearch_filter", "ajax_instantSearch_filter" );

	//Sovereign search filter
	function check_search() {
		global $wp_query;

		if (!$s = get_search_query())
			return false;

		if (preg_match('/sov*/', $s)) {
			$wp_query->set_404();
			status_header(404);
//			get_template_part(404);
//			exit();
		}
	}

	add_action('wp', 'check_search');

	//Fix sorting to DESC for Categories
    function my_change_sort_order($query){
        if(is_archive()):
         //If you wanted it for the archive of a custom post type use: is_post_type_archive( $post_type )
           //Set the order ASC or DESC
           $query->set( 'order', 'DESC' );
           //Set the orderby
           $query->set( 'orderby', 'date' );
        endif;    
    };

	add_action( 'pre_get_posts', 'my_change_sort_order'); 

	// Add image sizes
 	if ( function_exists( 'add_image_size' ) ) {
 		add_image_size( 'bh-daily-email-thumbnail', 250 , 100, true );
		add_image_size( 'bh-daily-email-content', 575 );		
 	}

	function debug_to_console( $data ) {
		if ( is_array( $data ) )
			$output = "<script>console.log( 'Debug Objects: " . trim(preg_replace('/\s+/', ' ', implode( ',', $data ))) . "' );</script>";
		else
			$output = "<script>console.log( 'Debug Objects: " . trim(preg_replace('/\s+/', ' ', $data )) . "' );</script>";
		echo $output;
	}

	// Display PostID in Admin Columns
//	add_filter( 'manage_posts_columns', 'revealid_add_id_column', 5 );
//	add_action( 'manage_posts_custom_column', 'revealid_id_column_content', 5, 2 );
//	add_action('admin_head', 'revealid_id_style');
//
//	function revealid_add_id_column( $columns ) {
//	   $columns['revealid_id'] = 'ID';
//	   return $columns;
//	}
//
//	function revealid_id_column_content( $column, $id ) {
//	  if( 'revealid_id' == $column ) {
//		echo $id;
//	  }
//	}
//
//	function revealid_id_style() {
//	  echo '<style>
//		td.revealid_id.column-revealid_id {
//    		background-color: #ffffe0;
//		}
//	  </style>';
//	}

	// Shortcode to display expert's subscriptions
	function tfs_bh_expert_subscriptions( $atts ) {
		global $post;

		// Ensure shortcode is used on expert page only
		if ( $post->post_type == 'expert' ) {
			// Get pages in 'subscriptions' category by the expert_id meta key
			$subscriptions_query = new WP_Query(
				array(
					'category_name' => 'subscriptions',
					'post_type' => 'page',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'meta_key'		=> 'expert_id',
					'meta_value'	=> $post->ID
				)
			);

			if ( $subscriptions_query->have_posts() ) {
				while ( $subscriptions_query->have_posts() ) {
					$subscriptions_query->the_post();

					$string_fav = wordwrap(get_the_excerpt(), 250);
					$i_fav = strpos($string_fav, "\n");

					if ( $i_fav ) {
						$string_fav = substr($string_fav, 0, $i_fav);
					}
					?>
					<?php
					if ( get_post_meta($subscriptions_query->post->ID, 'page_title', true) ) {
						$subscription_title = get_post_meta($subscriptions_query->post->ID, 'page_title', true);
					} else {
						$subscription_title = $subscriptions_query->post->post_title;
					}

					$event_tracking_code = get_post_meta($subscriptions_query->post->ID, 'event_tracking_code', true);
					?>

					<div class="col-md-6 col-xs-12">
						<div class="subscription_block">
							<div class="subscription_inner_block">
								<?php
								// Get subscription's thumbnail
								if ( has_post_thumbnail( $subscriptions_query->post->ID ) ) {
									$subscription_image_url = wp_get_attachment_url( get_post_thumbnail_id( $subscriptions_query->post->ID ) );

									?>
									<img src="<?php echo $subscription_image_url; ?>" alt="<?php echo $subscriptions_query->post->post_title; ?>">
									<?php
								}
								?>

								<h3><?php echo $subscription_title; ?></h3>
								<p><?php echo rtrim( $string_fav ); ?></p>
							</div>

							<div class="access-btn-content">
								<a onclick="<?php echo $event_tracking_code; ?>"
								   title="<?php echo $subscription_title; ?>"
								   href="<?php echo get_permalink( $subscriptions_query->post->ID ); ?>"
								   target="_self"
								   class="vc_general vc_btn3 vc_btn3-size-md vc_btn3-shape-square vc_btn3-style-modern vc_btn3-color-primary">
									<?php
									// Check if MW authentication plugin is active
									if ( class_exists( 'agora_auth_container' ) ) {
										$auth_container = new agora_auth_container( $subscriptions_query->post->ID );
										$auth_container = apply_filters('agora_middleware_check_permission', $auth_container);

										if ( $auth_container->is_allowed() || in_array( 'administrator', (array) wp_get_current_user()->roles ) ) {
											// If user has access to the subscription, show 'access' button
											echo "access";
										} else {
											// User has no access to the subscription, show 'learn more' button
											echo "Learn More &amp; Subscribe";
										}
									}
									?>
								</a>
							</div>
						</div>
					</div>

					<?php
				}

				wp_reset_postdata();

			}
		}

	}

	add_shortcode( 'bh_expert_subscriptions', 'tfs_bh_expert_subscriptions' );


	// Shortcode to display home subscriptions
	function tfs_bh_subscriptions( $atts ) {
		// Get pages in 'subscriptions' category
		$subscriptions_query = new WP_Query(
			array(
				'category_name' => 'subscriptions',
				'post_type' => 'page',
				'post_status' => 'publish',
				'posts_per_page' => 4,
				'orderby' => 'rand',
				'meta_query'	=> array(
					array(
						'key'	  	=> 'show_on_homepage',
						'value'	  	=> 'show',
						'compare' 	=> 'LIKE',
					)
				)
			)
		);

		if ( $subscriptions_query->have_posts() ) {
			$return_subscriptions = '<div class="home_subscriptions widget">';
			$return_subscriptions .= '<div class="row">';

			while ( $subscriptions_query->have_posts() ) {
				$subscriptions_query->the_post();

				$string_fav = wordwrap(get_the_excerpt(), 250);
				$i_fav = strpos($string_fav, "\n");

				if ( $i_fav ) {
					$string_fav = substr($string_fav, 0, $i_fav);
				}
				?>
						
									<?php if ( get_post_meta(get_the_ID(), 'page_title', true) ) {
										echo get_post_meta(get_the_ID(), 'page_title', true);
									} else {
										the_title();
									} ?>						
				<?php
				if ( get_post_meta($subscriptions_query->post->ID, 'page_title', true) ) {
					$subscription_title = get_post_meta($subscriptions_query->post->ID, 'page_title', true);
				} else {
					$subscription_title = $subscriptions_query->post->post_title;
				}

				$event_tracking_code = get_post_meta($subscriptions_query->post->ID, 'event_tracking_code', true);


				if ( class_exists( 'agora_auth_container' ) ) {
					$auth_container = new agora_auth_container( $subscriptions_query->post->ID );
					$auth_container = apply_filters('agora_middleware_check_permission', $auth_container);

					if ( $auth_container->is_allowed() || in_array( 'administrator', (array) wp_get_current_user()->roles ) ) {
						$button_url = get_permalink( $subscriptions_query->post->ID );
					} else {
						if ( get_post_meta($subscriptions_query->post->ID, 'sidebar_button_text', true) ) {
							if ( get_post_meta($subscriptions_query->post->ID, 'sidebar_button_url_link', true) ) {
								$button_url = get_post_meta($subscriptions_query->post->ID, 'sidebar_button_url_link', true);
							}
						}
					}
				}

				$return_subscriptions .= '<div class="col-md-3 col-xs-12">';
				$return_subscriptions .= '<div class="home_subscription">';
				$return_subscriptions .= '<h3 class="widget-title"><a href=" ' . $button_url . ' ">' . $subscription_title . '</a></h3>';
				$return_subscriptions .= '<p>' . rtrim( $string_fav ) . ' ';

				$button_text = "Read More...";

				// Check if MW authentication plugin is active
				if ( class_exists( 'agora_auth_container' ) ) {
					$auth_container = new agora_auth_container( $subscriptions_query->post->ID );
					$auth_container = apply_filters('agora_middleware_check_permission', $auth_container);

					if ( $auth_container->is_allowed() || in_array( 'administrator', (array) wp_get_current_user()->roles ) ) {
						// If user has access to the subscription, show 'access' button
						$return_subscriptions .= '<a onclick="' . $event_tracking_code . '" title="' . $subscription_title . '" href="' . $button_url . '" target="_self">';
						$return_subscriptions .= $button_text;
						$return_subscriptions .= '</a>';
					} else {
						// User has no access to the subscription, show 'learn more' button
						$return_subscriptions .= '<a onclick="' . $event_tracking_code . '" title="' . $subscription_title . '" href="' . $button_url . '" target="_self" class="accessbtn_bg">';
						$return_subscriptions .= $button_text;
						$return_subscriptions .= '</a>';
					}
				}

				$return_subscriptions .= '</p>';
				$return_subscriptions .= '</div>';
				$return_subscriptions .= '</div>';

			}

			$return_subscriptions .= '</div>';
			$return_subscriptions .= '</div>';

			wp_reset_postdata();

			return $return_subscriptions;
		}

		return '';
	}

	add_shortcode( 'bh_subscriptions', 'tfs_bh_subscriptions' );

	// Testimonials shortcode for side bar
	function tfs_bh_testimonials() {
		$arguments = array(
			'post_type' => 'testimonials',
			'post_status' => 'publish',
			'posts_per_page' => 3
		);

		$get_posts = new WP_Query( $arguments );

		$returned_posts = '<div class="bh_testimonials">';

		if ( $get_posts->have_posts() ) {
			while ( $get_posts->have_posts() ) {
				$get_posts->the_post();

				$returned_posts .= '<div class="bh_testimonial">';
				$returned_posts .= '<p><em>' . $get_posts->post->post_content . '</em></p>';
				$returned_posts .= '<p class="testimonialName"> - ' . get_post_meta( $get_posts->post->ID , "testimonial_author", true ) . '</p>';
				$returned_posts .= '</div>';
			}
		} else {
			$returned_posts .= '<h3>No Testimonials Found.</h3>';
		}

		$returned_posts .= '</div>';

		wp_reset_postdata();

		return $returned_posts;
	}

	add_shortcode( 'bh_testimonials', 'tfs_bh_testimonials' );

	// Shortcode to display experts on Sidebar
	function tfs_bh_experts( $atts = null ) {
		$attributes = shortcode_atts( array(
			'excerpt' => '80'
		), $atts );

		//global $post;

		$arguments = array(
			'post_type' => 'expert',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'rand',
			'post__not_in' => array( /*$post->ID,*/ 102710, 102698 )       
		);

		$get_posts = get_posts( $arguments );

		shuffle( $get_posts );

		$returned_posts = '<div class="verticalCarousel">';
			$returned_posts .= '<div class="verticalCarouselHeader">';
			$returned_posts .= '<a href="#" class="vc_goUp"><i class="fa fa-fw fa-angle-up"></i></a>';
			$returned_posts .= '</div>';

			$returned_posts .= '<ul class="verticalCarouselGroup vc_list">';
				if ( $get_posts ) {
					foreach ( $get_posts as $get_post ) {
						$returned_posts_image_url = get_the_post_thumbnail_url( $get_post->ID, array( 68, 90 ) );
						$returned_posts_image = '<img ';
						if ( strpos($returned_posts_image_url, '150x150' ) !== false ) {
							$returned_posts_image .= 'style="width: 68px!important; height: 68px!important;" ';	
						}
						
						$returned_posts_image .= 'alt="' . $get_post->post_title . '" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDBweCIgIGhlaWdodD0iNDBweCIgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0ibGRzLWVjbGlwc2UiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsgYmFja2dyb3VuZDogbm9uZTsiPjxwYXRoIG5nLWF0dHItZD0ie3tjb25maWcucGF0aENtZH19IiBuZy1hdHRyLWZpbGw9Int7Y29uZmlnLmNvbG9yfX0iIHN0cm9rZT0ibm9uZSIgZD0iTTEwIDUwQTQwIDQwIDAgMCAwIDkwIDUwQTQwIDQzIDAgMCAxIDEwIDUwIiBmaWxsPSJyZ2JhKDAlLDAlLDAlLDAuNikiIHRyYW5zZm9ybT0icm90YXRlKDM2MCAtOC4xMDg3OGUtOCAtOC4xMDg3OGUtOCkiIGNsYXNzPSIiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsiPjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgdHlwZT0icm90YXRlIiBjYWxjTW9kZT0ibGluZWFyIiB2YWx1ZXM9IjAgNTAgNTEuNTszNjAgNTAgNTEuNSIga2V5VGltZXM9IjA7MSIgZHVyPSIwLjVzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiBjbGFzcz0iIiBzdHlsZT0iYW5pbWF0aW9uLXBsYXktc3RhdGU6IHJ1bm5pbmc7IGFuaW1hdGlvbi1kZWxheTogMHM7Ij48L2FuaW1hdGVUcmFuc2Zvcm0+PC9wYXRoPjwvc3ZnPg==" class="loading" data-src="' . $returned_posts_image_url . '" />';

						$returned_posts .= '<li class="bh-expert">';
							$returned_posts .= '<div><a href="' . esc_url( get_permalink( $get_post->ID ) ) . '">' . $returned_posts_image . '</a></div>';
							$returned_posts .= '<div><p><strong><a href="' . esc_url( get_permalink( $get_post->ID ) ) . '">' . $get_post->post_title . '</a></strong>';
							$returned_posts .= '<p>' . the_excerpt_max_charlength( $get_post->ID, $attributes['excerpt'] ) . '</p></div>';
						$returned_posts .= '</li>';
					}
				} else {
					$returned_posts .= '<li>No Experts Found.</li>';
				}
			$returned_posts .= '</ul>';

			$returned_posts .= '<div class="verticalCarouselFooter">';
				$returned_posts .= '<a href="#" class="vc_goDown"><i class="fa fa-fw fa-angle-down"></i></a>';
			$returned_posts .= '</div>';

		$returned_posts .= '</div>';

		wp_reset_postdata();

		return $returned_posts;

	}

	add_shortcode( 'bh_experts', 'tfs_bh_experts' );


	// Shortcode to display experts on expert page
	function tfs_bh_other_experts() {
		$experts_return = '';

		$experts_return .= '<aside id="other_experts" class="widget widget_text">';
		$experts_return .= '<h2 class="widget-title">MEET OUR EXPERTS</h2>';
		$experts_return .= '<div class="textwidget"></div>';
		$experts_return .= tfs_bh_experts();
		$experts_return .= '</aside>';

		return $experts_return;
	}

	add_shortcode( 'bh_other_experts', 'tfs_bh_other_experts' );

	/**
	 * Disable divi code for CPT
	 * https://www.elegantthemes.com/blog/theme-releases/divi-custom-post-types
	 **/
	function disable_cptdivi()
	{
		remove_action( 'wp_enqueue_scripts', 'et_extra_replace_stylesheet', 99999998 );
	}
	add_action('init', 'disable_cptdivi');

	// Paid content search filter
	function bg_paid_content_search_filter( $postID ) {
		// Check if MW auth plugin is active
		if ( class_exists('agora_auth_container') ) {
			$auth_container = new agora_auth_container( $postID );
			$auth_container = apply_filters('agora_middleware_check_permission', $auth_container);

			// Check if user has access to the post
			if ( $auth_container->is_allowed() || in_array( 'administrator', (array)wp_get_current_user()->roles ) ) {
				// Auth container passes, show post excerpt
				return get_the_excerpt( $postID );
			} else {
				// Auth container fails, user does not have access to the post

				// Get post parent
				$post_parent = wp_get_post_parent_id( $postID );


				if ( has_category( 'subscriptions', $postID ) ) {
					// Check if post is subscription
					// Show link to promo
					return show_promo_link( $postID );
				} else if ( $post_parent ) {
					// Check if post's parent has 'subscriptions' category
					if ( has_category( 'subscriptions', $post_parent ) ) {
						// Show link to promo
						return show_promo_link($post_parent);
					}
				} else {
					// Not subscription or susbcription's subpage, could still be sub-sub-page
					$post_sub = get_post($postID);
					$post_parent_sub = false;

					$taxonomies = get_object_taxonomies($post_sub->post_type);
					$category = wp_get_post_terms($post_sub->ID, $taxonomies[0]);

					$arg = array(
						'posts_per_page' => '1',
						'post_type' => 'page',
						'order' => 'DESC',
						'tax_query' => array(
							array(
								'taxonomy' => $taxonomies[0],
								'field' => 'slug',
								'terms' => $category[0]->slug,
							),
						),
					);

					$query_posts = new WP_Query($arg);

					if ( $query_posts->have_posts() ) {
						while ( $query_posts->have_posts() ) {
							$query_posts->the_post();

							if ($query_posts->post->post_parent != '0') {
								$post_parent_sub = $query_posts->post->post_parent;
							}
						}

						wp_reset_postdata();
					}

					if ($post_parent_sub) {
						if (has_category('subscriptions', $post_parent_sub)) {
							// Show link to promo
							return show_promo_link( $post_parent_sub );
						}
					}
				}
			}
		}

		return get_the_excerpt( $postID );
	}


	// Show promo link
	function show_promo_link( $postID ) {
		$return_link = '';

		$button_url = get_permalink( $postID );

		if ( get_post_meta($postID, 'sidebar_button_url_link', true) ) {
			$button_url = get_post_meta($postID, 'sidebar_button_url_link', true);
		}

		$return_link .= '<a href="' . $button_url . '" style="padding: 10px;" class="bh_button">';
		$return_link .= 'Subscribe';
		$return_link .= '</a>';

		return $return_link;
	}

	add_action( 'show_user_profile', 'tfs_bh_author_signup_box' );
	add_action( 'edit_user_profile', 'tfs_bh_author_signup_box' );


	// Author signup box
	function tfs_bh_author_signup_box( $user ) {
		// If the user has the "author" role
		if ( in_array( 'author', (array) $user->roles ) OR in_array( 'administrator', (array) $user->roles ) OR in_array( 'editor', (array) $user->roles ) ) {
			$wp_editor_settings = array(
				'textarea_rows' => 10,
			);

			$author_signup_box = get_the_author_meta( 'tfs_bh_author_signup_box', $user->ID );

			?>

			<h3>Author Sign Up Box</h3>

			<table class="form-table">
				<tr>
					<th><label for="twitter">Sign Up Box</label></th>

					<td>
						<?php wp_editor( $author_signup_box, 'tfs_bh_author_signup_box', $wp_editor_settings ); ?>
						<span class="description">Area for a signup box.</span>
					</td>
				</tr>
			</table>
			<?php
		}
		?>
		<?php
	}

	add_action( 'personal_options_update', 'tfs_bh_save_author_signup_box' );
	add_action( 'edit_user_profile_update', 'tfs_bh_save_author_signup_box' );


	// Save author signup box
	function tfs_bh_save_author_signup_box( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
		update_user_meta( $user_id, 'tfs_bh_author_signup_box', $_POST['tfs_bh_author_signup_box'] );
	}

	// Show hidden Custom Fields
	// add_filter('acf/settings/remove_wp_meta_box', '__return_false');

	// Load authors into author dropdown in expert cpt
	function acf_load_experts_author_connection( $field ) {
		
		// reset choices
		$field['choices'] = array();

		$authors = BH_get_author_array();

		if ( $authors ) {
			foreach ( $authors as $author_id => $author_name ) {
				$field['choices'][$author_id] = $author_name;
			}
		}

		return $field;
	}

	add_filter('acf/load_field/name=author_connection', 'acf_load_experts_author_connection');


	// load subscription+expert connection
	function acf_load_experts_subscription_connection( $field ) {
		// reset choices
		$field['choices'] = array();

		$arguments = array(
			'post_type' => 'expert',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);

		$get_experts = get_posts( $arguments );

		if ( $get_experts ) {
			foreach ( $get_experts as $expert ) {
				$field['choices'][$expert->ID] = $expert->post_title;
			}
		}

		wp_reset_postdata();

		return $field;
	}

	add_filter('acf/load_field/name=expert_id', 'acf_load_experts_subscription_connection');

	// Populate default values for custom post type fields
	function acf_default_order_date($field) {
	  $field['default_value'] = date('Ymd');
	  return $field;
	}
	// Applies to Archives, Portfolios, Webinar Libraries
	add_filter('acf/load_field/name=order_date', 'acf_default_order_date');

	function acf_default_archive_date_for_search($field) {
	  $field['default_value'] = date('Y');
	  return $field;
	}
	add_filter('acf/load_field/name=date_for_search_archive', 'acf_default_archive_date_for_search');
	add_filter('acf/load_field/name=date_for_search_portfolio', 'acf_default_archive_date_for_search');
	add_filter('acf/load_field/name=date_for_search_library', 'acf_default_archive_date_for_search');

	function acf_default_archive_date($field) {
	  $field['default_value'] = date('F d, Y');
	  return $field;
	}
	add_filter('acf/load_field/name=archieve_date', 'acf_default_archive_date');		
	add_filter('acf/load_field/name=pdf_date', 'acf_default_archive_date');
	add_filter('acf/load_field/name=webinar_library_date', 'acf_default_archive_date');

	// https://github.com/billerickson/Display-Posts-Pagination/blob/master/display-posts-pagination.php
	function be_dps_pagination_links( $output, $atts, $listing ) {
		if( empty( $atts['pagination'] ) )
			return $output;
		global $wp;
		$base = home_url( $wp->request );
		$format = 'dps_paged';
		if( intval( $atts['pagination'] ) > 1 )
			$format .= '_' . intval( $atts['pagination'] );
		$format = '?' . $format . '=%#%';
		$current = !empty( $listing->query['paged'] ) ? $listing->query['paged'] : 1;
		$args = array(
			'base'		=> trailingslashit( $base ) . $format,
			'format'    => $format,
			'current'   => $current,
			'total'     => $listing->max_num_pages,
			'prev_text' => 'Previous',
			'next_text' => 'Next',
		);
		$nav_output = '';
		$navigation = paginate_links( apply_filters( 'display_posts_shortcode_paginate_links', $args, $atts ) );
		if( $navigation ) {
			$nav_output .= '<nav class="display-posts-pagination" role="navigation">';
				$nav_output .= '<h2 class="screen-reader-text">Navigation</h2>';
				$nav_output .= '<div class="nav-links">' . $navigation . '</div>';
			$nav_output .= '</nav>';
		}
		if( !empty( $atts['pagination_inside'] ) && filter_var( $atts['pagination_inside'], FILTER_VALIDATE_BOOLEAN ) )
			$output = $nav_output . $output;
		else
			$output .= $nav_output;

		return $output;
	}
	add_filter( 'display_posts_shortcode_wrapper_close', 'be_dps_pagination_links', 10, 3 );

	// Associate author with experts page for display_posts_shortcode plugin
	function jw_display_author_posts ( $args, $atts ) {
			
		$format = 'dps_paged';
		
		if( intval( $atts['pagination'] ) > 1 ) {
			$format .= '_' . intval( $atts['pagination'] );
		}
			
		if( !empty( $_GET[ $format ] ) ) {
			$args['paged'] = intval( $_GET[ $format ] );		
		}	
		
		if( empty( $atts['author_id'] ) ) {
			$args['author'] = get_post_meta(get_the_ID(), 'author_connection', true);
		}

		return $args;
	}
	add_filter( 'display_posts_shortcode_args', 'jw_display_author_posts', 10, 2 );

	// Get excerpt
	function the_excerpt_max_charlength( $post_id, $charlength ) {
		global $post;
		$save_post = $post;
		$post = get_post($post_id);
		$excerpt = get_the_excerpt();
		$post = $save_post;

		$charlength++;
		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				return mb_substr( $subex, 0, $excut ) . '[...]';
			} else {
				return $subex . '[...]';
			}
		} else {
			return $excerpt;
		}
	}

	// https://wordpress.stackexchange.com/a/269209
	/**
	 * Removes the original author meta box and replaces it
	 * with a customized version.
	 */
	add_action( 'add_meta_boxes', 'wpse_replace_post_author_meta_box' );
	function wpse_replace_post_author_meta_box() {
		$post_type = get_post_type();
		$post_type_object = get_post_type_object( $post_type );

		if ( post_type_supports( $post_type, 'author' ) ) {
			if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) ) {
				remove_meta_box( 'authordiv', $post_type, 'core' );
				add_meta_box( 'authordiv', __( 'Author', 'text-domain' ), 'wpse_post_author_meta_box', null, 'normal' );
			}
		}
	}

	function BH_get_author_array() {
		if ( false === ( $BH_author_query_results = get_transient( 'BH_author_query_results' ) ) ) {
			$BH_author_query_results = get_users( array(
				'role__in' => [ 'administrator', 'author', 'contributor', 'editor' ], // Add desired roles here.
				'role__not_in'	=> ['subscriber'],
           		'order' => 'ASC',
				'orderby' => 'display_name'
			) );
			
			if ( $BH_author_query_results ) {
				$authors = array();
				foreach ( $BH_author_query_results as $BH_author_query_result ) {
					$authors[strval($BH_author_query_result->data->ID)] = $BH_author_query_result->data->display_name;
				}

				$BH_author_query_results = $authors;
				set_transient( 'BH_author_query_results', $BH_author_query_results, DAY_IN_SECONDS );
			}	
		} else {
			$BH_author_query_results = get_transient( 'BH_author_query_results' );
		}

		return $BH_author_query_results;
	}

	add_filter( 'bulk_actions-edit-post', '__return_empty_array' );
	add_filter( 'bulk_actions-edit-page', '__return_empty_array' );

	function BH_dropdown_users_callback( $post ) {
		global $user_ID;
		
		$author_option = BH_get_author_array();
	?>
	<label class="inline-edit-author"><span class="title"><?php _e( 'Author', 'text-domain' ); ?></span>
	<select name="post_author" class="authors">
		<option value="-1">— No Change —</option>
	<?php foreach ( $author_option as $option_value => $option_title ) { ?>
		<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $post->post_author ); ?> > <?php echo esc_html( $option_title ); ?></option>
	<?php } ?>
	</select>
	</label>
	<?php
	}

	/**
	 * Display form field with list of authors.
	 * Modified version of post_author_meta_box().
	 * Fixes slow Query when checking database
	 *
	 * @global int $user_ID
	 *
	 * @param object $post
	 */
	function wpse_post_author_meta_box( $post ) {
		global $user_ID;
		
		$author_option = BH_get_author_array();
	?>
	<label class="screen-reader-text" for="post_author_override"><?php _e( 'Author', 'text-domain' ); ?></label>
	<select name="post_author_override" id="post_author_override" class="">
		<?php foreach ( $author_option as $option_value => $option_title ) { ?>
			<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $post->post_author ); ?> > <?php echo esc_html( $option_title ); ?></option>
		<?php } ?>
	</select>						
	<?php
	}

	add_filter( 'post_row_actions', 'BH_disable_quick_edit', 10, 2 );
	add_filter( 'page_row_actions', 'BH_disable_quick_edit', 10, 2 );

	function BH_disable_quick_edit( $actions = array(), $post = null ) {

		// Remove the Quick Edit link
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		// Return the set of links without Quick Edit
		return $actions;

	}

	// Remove Meta Boxes with no use for editors
	add_action( 'do_meta_boxes', 'BH_remove_post_author_meta_boxes' );
	function BH_remove_post_author_meta_boxes() {
		//remove_meta_box( 'agora-pubcode-picker', ['post'], 'advanced' ); // Doesnt seem to work
		
		// Remove Social Share Info
		remove_meta_box( 'et_monarch_settings', ['archives', 'exclusives', 'expert', 'page', 'post', 'portfolio', 'testimonials', 'library'], 'advanced' );
		remove_meta_box( 'et_monarch_sharing_stats', ['archives', 'exclusives', 'expert', 'page', 'post', 'portfolio', 'testimonials', 'library'], 'advanced' );
		
		// Remove Divi Review Box
		remove_meta_box( 'post-review-box', 'post', 'advanced' );
		
		// Remove hidden Divi authors query for non -JS
		remove_meta_box( 'authors-page-template', ['page', 'expert'], 'normal' );
		remove_meta_box( 'sitemap-page-template', ['page', 'expert'], 'normal' );
	}

	// Premium Content Feature
	function get_current_user_meta($atts) {
		$current_user = wp_get_current_user();
		// take first item in array only
		$desired_att = 'user_' . array_shift( $atts );
		return $current_user->$desired_att; 
	}

	add_shortcode( 'bh_user', 'get_current_user_meta' );

	function add_login_logout_link( $menu, $args ) {
		if( $args->theme_location == 'secondary-menu' ) {
			$welcome_message = '';
			$my_account_link = '';
			
			if (is_user_logged_in()) {
				$welcome_message = '<li class="menu-item welcome-message">Welcome, ' . wp_get_current_user()->user_firstname . '!</li>';
				$loginoutlink = '<li class="menu-item login-logout"><a href="' . wp_logout_url( home_url() ) . '"><i class="fa fa-unlock"></i> Logout</a></li>';
				$my_account_link = '<li class="menu-item my-account"><a href="/customer-self-service/"><i class="fa fa-user"></i> My Account</a></li>';
			} else {
				if (strpos($_SERVER['REQUEST_URI'], 'login/') === false && !is_home()) {
					$redirect = htmlspecialchars( $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8' );
				} else {
					$redirect = '/premium-content/';
				}
				
				$loginoutlink = '<li class="menu-item login-logout"><a href="/customer-login?redirect_to=' . $redirect . '"><i class="fa fa-lock"></i> Login</a>';
			}
			
			$menu = $welcome_message . $my_account_link . $loginoutlink . $menu;
		}
		
		return $menu;
	}

	add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);


	// Build premium service menu from child pages
	function jw_list_children( $atts = null ) { 
		// Set defaults if no value is passed
		$a = shortcode_atts( array(
			'parent' => null
		), $atts );
		
		global $post; 
		
		$args = array(
			'post_parent' => $a['parent'],		
			'post_type'   => 'page', 
			'numberposts' => -1,
			'post_status' => 'publish',
			'orderby' => 'menu_order',
			'order' => 'ASC'			
		);
		
		$children = get_children( $args );
			
		if ( $children ) {
			$string = '
			<div class="widget_text et_pb_widget widget_custom_html">
				<div class="textwidget custom-html-widget">
					<nav role="navigation" class="premiumMenu">
						<div id="menuToggle">
							<input type="checkbox" />
							<span></span>
							<span></span>
							<span></span>
							<ul id="menu">
								<li><a href="/premium-content" class="backSub">&#171; Back to My Subscriptions</a></li>';

					foreach ($children as $child) {
						$grand_children_string = '';
						$grand_children_args = array(
							'post_parent' => $child->ID,		
							'post_type'   => 'page', 
							'numberposts' => -1,
							'post_status' => 'publish',
							'orderby' => 'menu_order',
							'order' => 'ASC'			
						);						
						$grand_children = get_children( $grand_children_args );
						
						if( !empty($grand_children) ) {
							
							$grand_children_string .= '<ul class="step1-menu">';
							
							foreach ($grand_children as $grand_child) {
								$grand_children_string .= '<li><a href="' . get_permalink( $grand_child->ID ) . '">' . get_post_meta($grand_child->ID, 'page_title', true) . '</a></li>';
							}
							
							$grand_children_string .= '</ul>';
						}
						
						$string .= '<li' . ($grand_children_string !== '' ? ' class="menu-item-has-children" ' : '') . '><a href="' . get_permalink( $child->ID ) . '">' . get_post_meta($child->ID, 'page_title', true) . '</a>' . $grand_children_string . '</li>';
					}			

				$string .= '</ul>
						</div>
					</nav>
				</div>
			</div>';
		}
		
		return $string;
	}

	add_shortcode('bh_submenu', 'jw_list_children');

	// ID Resolution for Lytics
	// TODO: Extend & Move to login page only
	function get_subscriber_meta_data_var() {
		if ( !is_user_logged_in() ) return;
		
		$userdata = get_user_meta( wp_get_current_user()->ID, 'agora_middleware_aggregate_data', true );
		
		if ( $userdata ) {
		?>
		<script type="text/javascript">
		  !function (l, a) { a.liosetup = a.liosetup || {}, a.liosetup.callback = a.liosetup.callback || [], a.liosetup.addEntityLoadedCallback = function (l, o) { if ("function" == typeof a.liosetup.callback) { var i = []; i.push(a.liosetup.callback), a.liosetup.callback = i } a.lio && a.lio.loaded ? l(a.lio.data) : o ? a.liosetup.callback.unshift(l) : a.liosetup.callback.push(l) } }(document, window);
			
		  window.liosetup.addEntityLoadedCallback(function (data) {			  
			var subscriberMetaData = {userid: <?php echo json_encode( $userdata->accounts[0]->customerNumber ); ?>, email: <?php echo json_encode( end($userdata->emailAddresses)->emailAddress ); ?>};
			  
			jstag.send(subscriberMetaData);			  
		  });
		</script>						
		<?php
		}			
	
	}

	add_action('wp_footer', 'get_subscriber_meta_data_var');

	// Set & retrieve the last login
	// Set cookie for first time users
	// http://www.kvcodes.com/2015/12/how-to-set-user-last-login-date-and-time-in-wordpress/
	function set_last_login($login) {
		$user = get_userdatabylogin($login);
		$curent_login_time = get_user_meta(	$user->ID , 'current_login', true );
		
		$login_count = get_login_count($user);
		$login_streak = get_login_streak($user);
		$login_streak_max = get_login_streak_max($user);
		$login_window = strtotime( current_time('mysql') ) - strtotime( get_user_meta( $user->ID, 'last_login', true ) );

		//add or update the login count value for logged in user
		if( !empty($login_count) ){
			update_user_meta( $user->ID, 'login_count', $login_count + 1);
		} else {
			update_user_meta( $user->ID, 'login_count', 1 );
		}

		//add or update the login streak value for logged in user
		if( !empty($login_streak) ){
			if( $login_window > 86400 && $login_window < 172800 ) {// More than 24 hrs & less than 48
				update_user_meta( $user->ID, 'login_streak', $login_streak + 1);
				update_user_meta( $user->ID, 'login_streak_max', $login_streak_max + 1);
			} else {
				update_user_meta( $user->ID, 'login_streak', $login_streak);
				update_user_meta( $user->ID, 'login_streak_max', $login_streak_max);
			}
		} else {
			update_user_meta( $user->ID, 'login_streak', 1 );
			update_user_meta( $user->ID, 'login_streak_max', 1);
		}		
		
		//add or update the last login value for logged in user
		if( !empty($curent_login_time) ){
			if ($login_window > 86400) {
				update_user_meta( $user->ID, 'last_login', $curent_login_time );	
			}
			
			update_user_meta( $user->ID, 'current_login', current_time('mysql') );
		} else {
			// User has never logged in
			update_user_meta( $user->ID, 'current_login', current_time('mysql') );
			update_user_meta( $user->ID, 'last_login', current_time('mysql') );
		}
		
		if ( get_user_meta( $user->ID, 'last_login', true ) == get_user_meta( $user->ID, 'current_login', true ) ) {
			// set cookie for 1 year
			setcookie("is_prem_first_time_user", '0', time()+60*60*24*365, '/', '.banyanhill.com');			
		} else {
			unset($_COOKIE['is_returning_user']); // what is this?
			setcookie("is_prem_first_time_user", '', time()-3600, '/', '.banyanhill.com');
		}
	}

	add_action('wp_login', 'set_last_login');

	function get_last_login($atts = null) {
		if ( !is_user_logged_in() ) return;
		
		$a = shortcode_atts( array(
			'format' => 'M j, Y, g:iA'
		), $atts );		
		
		$user_id = get_userdata( wp_get_current_user()->ID )->ID;
	   	$last_login = get_user_meta( $user_id, 'last_login', true );
	   	$date_format = get_option('date_format') . ' ' . get_option('time_format');

		$the_last_login = date( ''. $a['format'] . '', strtotime($last_login));  

	   	return $the_last_login;
	}

	add_shortcode( 'bh_last_login', 'get_last_login' );

	function get_login_count($user) {
		if( empty($user) ){
			$user = wp_get_current_user();
		}
		return get_user_meta( $user->ID , 'login_count', true );
	}

	add_shortcode( 'bh_login_count', 'get_login_count' );

	function get_login_streak($user) {
		if( empty($user) ){
			$user = wp_get_current_user();
		}
		return get_user_meta( $user->ID , 'login_streak', true );		
	}

	add_shortcode( 'bh_login_streak', 'get_login_streak' );

	function get_login_streak_max($user) {
		if( empty($user) ){
			$user = wp_get_current_user();
		}
		return get_user_meta( $user->ID , 'login_streak_max', true );		
	}

	add_shortcode( 'bh_login_streak_max', 'get_login_streak_max' );

	function get_reg_date($atts = null) {
		if ( !is_user_logged_in() ) return;		
		
		$a = shortcode_atts( array(
			'format' => 'F j, Y'
		), $atts );	
		
		return date( ''. $a['format'] . '', strtotime( get_userdata( wp_get_current_user()->ID )->user_registered ) );
	}

	add_shortcode( 'bh_reg_date', 'get_reg_date' );

	function add_social_profile() {
		$payload = wp_parse_args( sanitize_text_field( urldecode($_POST['css_data']) ) );
		$user = wp_get_current_user();

		$current_data = get_user_meta( $user->ID , 'social_link', true );	
		
		if ( !array_key_exists( $payload['network'], $current_data ) ) {
			
			$current_data[$payload['network']] = $payload['url'];
			
			update_user_meta( $user->ID, 'social_link', $current_data );
			
			if ( !array_key_exists( $payload['network'], get_user_meta( $user->ID , 'social_link', true ) ) ) {
    			echo json_encode( array('status' => 'error', 'content' => 'An error occured saving the data.', 'social_data' => get_user_meta( $user->ID , 'social_link', true ), 'submitted_data' => $payload, 'current_data' => $current_data) );
			} else {
				echo json_encode( array('status' => 'success', 'content' => '<div class="social_display ' . $payload['network'] . '">' . $payload['url'] . '</div><div class="subs_delete_social"><a href="#" class="delete_social" data-network="' . $payload['network'] . '">Remove</a></div>', 'social_data' => get_user_meta( $user->ID , 'social_link', true ) ) );				
			}			
		} else {
			echo json_encode( array('status' => 'error', 'content' => 'Please delete your previous entry for ' . ucfirst($payload['network']) . ' before adding another.') );
		}
		
		die();		
	}

	add_action( 'wp_ajax_add_social_profile', 'add_social_profile' );
	add_action( 'wp_ajax_nopriv_add_social_profile', 'add_social_profile' );

	function get_social_profile() {
		$user = wp_get_current_user();
		
		return get_user_meta( $user->ID , 'social_link', true );
	}

	function remove_social_profile() {
		$payload = wp_parse_args( sanitize_text_field( urldecode($_POST['css_data']) ) );
		$user = wp_get_current_user();
		
		$current_data = get_user_meta( $user->ID , 'social_link', true );
		
		unset($current_data[$payload['network']]);
		
		update_user_meta( $user->ID, 'social_link', $current_data );	
		
		echo json_encode( array('status' => 'success', 'network' => $payload['network']) );
		
		die();
	}

	add_action( 'wp_ajax_remove_social_profile', 'remove_social_profile' );
	add_action( 'wp_ajax_nopriv_remove_social_profile', 'remove_social_profile' );

	// Shortcode to display catogery description 
	function tfs_bh_taxDescription() {
		return category_description (); 
	}

	add_shortcode( 'bh_taxDescription', 'tfs_bh_taxDescription' );

	function load_css_portal() {		
		echo do_shortcode( '[tfs_customer_self_service]' );			
	}
	
	add_action( 'init', 'service_page_settings' );

	// Remove tags support from posts
	// TODO: Escape function if not page/post https://wordpress.stackexchange.com/a/7281
	function BH_unregister_tags() {
		$user = wp_get_current_user();

		if ( in_array( 'author', (array) $user->roles ) || in_array( 'editor', (array) $user->roles ) ) {
			
			unregister_taxonomy_for_object_type('post_tag', 'post');	
		}
	}
	add_action('init', 'BH_unregister_tags');

	// Display Category Select on Pages
	function service_page_settings() {  
		// Add category metabox to page
		register_taxonomy_for_object_type('category', 'page');  
	}

	// https://www.lab21.gr/blog/wordpress-get-primary-category-post
	function get_post_primary_category($post_id, $term = 'category', $return_all_categories = false){
		$return = array();

		if (class_exists('WPSEO_Primary_Term')){
			// Show Primary category by Yoast if it is enabled & set
			$wpseo_primary_term = new WPSEO_Primary_Term( $term, $post_id );
			$primary_term = get_term($wpseo_primary_term->get_primary_term());

			if (!is_wp_error($primary_term)){
				$return['primary_category'] = $primary_term;
			}
		}

		if (empty($return['primary_category']) || $return_all_categories){
			$categories_list = get_the_terms($post_id, $term);

			if (empty($return['primary_category']) && !empty($categories_list)){
				$return['primary_category'] = $categories_list[0];  //get the first category
			}
			if ($return_all_categories){
				$return['all_categories'] = array();

				if (!empty($categories_list)){
					foreach($categories_list as &$category){
						$return['all_categories'][] = $category->term_id;
					}
				}
			}
		}

		return $return;
	}

	// Display Password prompt in login box
	add_action( 'login_form_middle', 'add_lost_password_link' );

	function add_lost_password_link() {
		return '<a href="/login/forgot-password/">Forgot Your Password?</a>';
	}

	// Remove Private/Protected text from posts
	function clean_private_title_format( $format ) {
		return preg_replace('/(Protected:|Private:)/','','%s');
	}

	add_filter( 'private_title_format', 'clean_private_title_format' );
	add_filter( 'protected_title_format', 'clean_private_title_format' );

	// Allows bypassing the password prompt on proteted pages/posts
	function BH_password_form($output) {
		$post = get_post();

		if ( sanitize_text_field( $_GET['f'] ) === esc_attr( $post->post_password ) ) {
			return apply_filters( 'the_content', get_page( get_the_ID() )->post_content );
		}
		// Or return the output as normal
		return $output;
	}

	add_filter( 'the_password_form', 'BH_password_form' );

	// Allow iframes in custom metaboxes
	// https://github.com/cferdinandi/gmt-allow-iframes/blob/master/gmt-allow-iframes.php
	function bh_allow_iframes_filter( $allowedposttags ) {
		// Only change for users who can publish posts
		if ( !is_admin() ) return $allowedposttags;
		// Allow iframes and the following attributes
		$allowedposttags['iframe'] = array(
			'align' => true,
			'width' => true,
			'height' => true,
			'frameborder' => true,
			'name' => true,
			'src' => true,
			'id' => true,
			'class' => true,
			'style' => true,
			'scrolling' => true,
			'marginwidth' => true,
			'marginheight' => true,
		);	
		
		return $allowedposttags;
	}
	add_filter( 'wp_kses_allowed_html', 'bh_allow_iframes_filter' );

	// Remove the Content Aware Sidebar meta box from all admin sidebars
	//https://developer.wordpress.org/reference/functions/remove_meta_box/
	function remove_cas_meta_box() {
		remove_meta_box( 'cas-content-sidebars' , $post->post_type , 'side' ); 
	}

	add_action( 'do_meta_boxes' , 'remove_cas_meta_box' );

	// https://documentation.onesignal.com/docs/web-push-wordpress-faq#section-customizing-wordpress-plugin-behavior
	// https://documentation.onesignal.com/reference#section-appearance
	// Schedule the OneSignal notification to be delivered at the specific time
	function onesignal_send_notification_filter($fields, $new_status, $old_status, $post)
	{
		
	  $fields['delayed_option'] = 'last-active';
	  $fields['ttl'] = 3600;	
	//   $fields['delivery_time_of_day'] = '9:00AM';

	  return $fields;
	}

	add_filter('onesignal_send_notification', 'onesignal_send_notification_filter', 10, 4);

	// Increase subscriber cookie expiration time
	function auth_cookie_expiration_filter($expiration, $user_id, $remember) {
		$user_meta = get_userdata( $user_id );
		$user_roles = $user_meta->roles;

		// Set cookie for 1 year 
		setcookie("is_signed_up", '1', time()+60*60*24*365, '/', '.banyanhill.com');			
		
		if ( $remember || in_array( 'subscriber', $user_roles, true ) ) {
			return YEAR_IN_SECONDS;
			// return MONTH_IN_SECONDS;
			// return DAY_IN_SECONDS;
			// return HOUR_IN_SECONDS;
		} else {
			return $expiration;			
		}
	}

	add_filter('auth_cookie_expiration', 'auth_cookie_expiration_filter', 10, 3);

	function BH_reading_time() {
		$content = get_post_field( 'post_content', get_the_ID() );
		$word_count = str_word_count( strip_tags( $content ) );
		
		$m = floor($word_count / 200);
		$s = floor($word_count % 200 / (200 / 60));
		$totalreadingtime = ( $m == 0 ? '' : $m . ' minute' ) . ($m != 0 && $s != 0 ? ', ' : '' ) . ( $s == 0 ? '' : $s . ' second' );		

		return $totalreadingtime;
	}

	// post, page post type
	add_filter( 'post_link', 'future_permalink', 10, 3 );
	// custom post types
	add_filter( 'post_type_link', 'future_permalink', 10, 4 );

	// https://sw33t.com/articles/wordpress-permalinks-incorrect-for-scheduled-posts/
	function future_permalink( $permalink, $post, $leavename, $sample = false ) {
		/* for filter recursion (infinite loop) */
		static $recursing = false;

		if ( empty( $post->ID ) ) {
			return $permalink;
		}

		if ( !$recursing ) {
			if ( isset( $post->post_status ) && ( 'future' === $post->post_status ) ) {
				// set the post status to publish to get the 'publish' permalink
				$post->post_status = 'publish';
				$recursing = true;
				$future_permalink = get_permalink( $post, $leavename );
				
				// change revert changed status
				$post->post_status = 'future';
				return $future_permalink;
			}
		}

		$recursing = false;
		return $permalink;
	}

// Shortcode to display content for logged in users only
//function ths_bh_li_content(  $atts = null, $content = null ) {
    // If user is logged in
    //if ( is_user_logged_in() ) {
        //return $content;
    //}
//}

//add_shortcode( 'bh_li_content', 'ths_bh_li_content' );


// Shortcode to display content for logged out users only
//function ths_bh_nli_content(  $atts = null, $content = null ) {
    // If user is logged in
    //if ( ! is_user_logged_in() ) {
        //return $content;
    //}
//}

//add_shortcode( 'bh_nli_content', 'ths_bh_nli_content' );

// uncomment below to see the enqueued scripts and stylesheets
// function se_inspect_styles() {
//     global $wp_styles;
//     echo "<h2>Enqueued CSS Stylesheets</h2><ul>";
//     foreach( $wp_styles->queue as $handle ) :
//         echo "<li>" . $handle . "</li>";
//     endforeach;
//     echo "</ul>";
// }
// add_action( 'wp_print_styles', 'se_inspect_styles' );
// function se_inspect_scripts() {
//     global $wp_scripts;
//     echo "<h2>Enqueued JS Scripts</h2><ul>";
//     foreach( $wp_scripts->queue as $handle ) :
//         echo "<li>" . $handle . "</li>";
//     endforeach;
//     echo "</ul>";
// }
// add_action( 'wp_print_scripts', 'se_inspect_scripts' );
?>