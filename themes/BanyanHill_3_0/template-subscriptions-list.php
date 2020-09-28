<?php
/**
 * Template Name: Subscriptions List
 *
 * @package DW Focus
 * @since DW Focus 1.3.1
 */

bh_check_refresh_data();

get_header(); 

$path = plugins_url() . '/tfs-portfolio-tracker/portfolio-tracker-theme/assets/';

wp_enqueue_script('data-tables', $path . 'js/jquery.dataTables.min.js', array('jquery'));
wp_enqueue_script('custom-data-tables', $path . 'js/custom.dataTables.js', array('jquery'));

wp_enqueue_style( 'jquery-data-tables-css', $path . 'css/jquery.dataTables.min.css', false );
wp_enqueue_style( 'jquery-data-tables-custom-css', $path . 'css/jquery.custom.css', false );

?>

<div id="main-content" <?php if ( isset( $_COOKIE['is_prem_first_time_user'] ) && is_user_logged_in() ) echo 'class="prem-first-time"'; ?>>
	<div class="bootstrap-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-log-12 col-md-12 col-sm-12 col-12">
				<div id="primary" class="content-area">
					<?php
						if ( function_exists('yoast_breadcrumb') ) {
							yoast_breadcrumb('<p id="breadcrumbs">','</p>');
						}					
					?>
					<main id="main" class="site-main" role="main">
						<?php while ( have_posts() ) : the_post(); ?>
							<div class="page-content-subscriptions" id="premCont">
							<?php 
								
								the_content(); 
								
								// Get PAGES in 'subscriptions' category
								$subscriptions_query = new WP_Query(
									array(
										'category_name' => 'subscriptions',
										'post_type' => 'page',
										'post_status' => 'publish',
										'posts_per_page' => -1
									)
								);
								
								$user_subscription_meta = wp_get_current_user()->middleware_data->subscriptionsAndOrders->subscriptions;
								$renewals = array();

								if ($user_subscription_meta) {
									$active_circ_status = array('Q', 'R', 'W');
									$active_bulk_pubcode = array('MBB', 'PMB', 'SOV');
									$active_bulk_sub =  array(); // Inner Circle Pubcodes pushed here

									foreach ( $user_subscription_meta as $sub ) {
										// Active Subscriptions Loop
										if ( $sub->id->item->itemNumber == wp_get_post_terms($subscriptions_query->post->ID, 'pubcode')[0]->name && in_array($sub->circStatus, $active_circ_status)) {
											// Push Subscription Renewals to Array											
											// Disregard Expired or Perpetual Subscriptions												
											$item->issuesRemaining = $sub->issuesRemaining;
											$item->renewMethod = $sub->renewMethod;
											$item->is_lifetime = false;
											$item->rate = $sub->rate;

											if( $sub->circStatus === "P" || strtoupper( $sub->subType ) === 'LIFE' ) {
												$item->is_lifetime = true;
											}

											// Renewal date less than 30 days
											if ( (strtotime($sub->finalExpirationDate) - time()) < 25920000 ) {
												$renewal_class = 'class="renewal-threshold"';
												$renewal_alert = '<span class="trade-alert"><span class="trade-alert-modal">This subscription expire';
												(strtotime($sub->finalExpirationDate) - time() < 0) ? $renewal_alert .= 'd ' : $renewal_alert .= 's ';
												$renewal_alert .= date('M. d, Y', strtotime($sub->finalExpirationDate)) . '</span><i class="fa fa-exclamation-triangle"></i></span>';
											} else {
												unset($renewal_class, $renewal_alert);
											}																

											$renewal_content .= '<div class="renewalAlert"><h1><a href="' . esc_url( get_permalink( $subscriptions_query->post->ID ) ) . '" target="_blank">' . $subscription_title . '</a>' . $renewal_alert . '</h1>';
											$renewal_content .= '<section class="tradeAlert"><div>';
											$renewal_content .= '<h1>Expiration Date: <span ' . $renewal_class . '>' . date('M. d, Y', strtotime($sub->finalExpirationDate)) . '</span></h1>';
											$renewal_content .= '<p><a class="signup-button" href="' . esc_url( $item->purchaseLink ) . '" target="_blank">Renew Now</a></p>';
//															$renewal_content .= '</div><div>';
//															$renewal_content .= '<h1>Automatic Renewal:</h1>';
//
//															if ($item->renewMethod === 'C') {
//																$renewal_text = 'Enabled';
//															} else {
//																$renewal_text = 'Disabled';
//															}
//
//															$renewal_content .= '<p><a class="signup-button" href="' . esc_url( $item->purchaseLink ) . '" target="_blank">' . $renewal_text . '</a></p>';													

											$renewal_content .= '</div></section>';
											$renewal_content .= '</div>';

											array_push( $renewals, $renewal_content );	
										}

										// Identify Inner Circle Subscriptions
										if ( in_array($sub->id->item->itemNumber, $active_bulk_pubcode) ) {
											
											if ( $sub->id->item->itemNumber === 'SOV' && $sub->memberCat === 'GM' ) {
												array_push($active_bulk_sub, 'TWF');	
											} else {
												array_push($active_bulk_sub, $sub->id->item->itemNumber);	
											}
										}
									}
								}

								if ( $subscriptions_query->have_posts() ) {

									$subscriptions_have_access = array();
									$subscriptions_bulk_access = array();
									$subscription_bulk_files = false;									
									$subscriptions_no_access = array();
									$subscriptions_front_end = array();
									$new_alerts = array();
									$new_transcripts = array();
									$new_alert_notify = array();
									$new_transcript_notify = array();
									$renewals = array();
									$new_alert_count = 0;
									$new_transcript_count = 0;

									while ( $subscriptions_query->have_posts() ) {
										$subscriptions_query->the_post();

										$subscription_content = '';
										$accordion_content = '';
										$standard_content = '';
										$excerpt_content = '';
										$renewal_content = '';
										$subscription_access_yes_no = false;
										$subscription_bulk_access = false;
										$item = new stdClass();
										
										// Get CUSTOM POST TYPE 'subscriptions' with pubcode from subscriptions query
										$query_pubcodes = new WP_Query(
											array(
												'post_type' => 'subscriptions',
												'post_status' => 'publish',
												'posts_per_page' => 1,
												'meta_query' => array(
													array(
														'key' => 'tfs_subs_pubcode',
														'value' => wp_get_post_terms($subscriptions_query->post->ID, 'pubcode')[0]->name
													)
												)
											)
										);

										if ( $query_pubcodes->have_posts() ) {
											// For each subscription											
											while ($query_pubcodes->have_posts()) {
												$query_pubcodes->the_post();
												
												$item->homeLink = get_post_meta($query_pubcodes->post->ID, 'tfs_subs_home_link', true);
												$item->purchaseLink = get_post_meta($query_pubcodes->post->ID, 'tfs_subs_purchase_link', true);
												$item->renewalPrice = get_post_meta($query_pubcodes->post->ID, 'tfs_subscription_renewal_price', true);
											}
											
											wp_reset_postdata();
										}										

										// Should this subscription be hidden on this page?
										$hide_sub_on_premium_content_page = false;
										$hide_on_premium_content_page = get_post_meta($subscriptions_query->post->ID, 'hide_on_premium_content', false);
										$is_front_end_subscription = get_post_meta($subscriptions_query->post->ID, 'is_front_end_subscription', true);
																				
										// RWE returns an array here for some reason
										if ( is_array( $is_front_end_subscription ) ) {
											$is_front_end_subscription = $is_front_end_subscription[0];
										}										

										if ( $hide_on_premium_content_page ) {
											if ( is_array( $hide_on_premium_content_page ) ) {
												foreach($hide_on_premium_content_page as &$hide_on_premium_content_page_each) {
													if ( in_array( 'hide', $hide_on_premium_content_page_each ) ) {
														$hide_sub_on_premium_content_page = true;
													}
												}
											}
											
											if ( $hide_on_premium_content_page == 'hide' ) {
												$hide_sub_on_premium_content_page = true;
											}
										}

										if ( $hide_sub_on_premium_content_page != true ) {
											if ( get_post_meta($subscriptions_query->post->ID, 'page_title', true) ) {
												$subscription_title = get_post_meta($subscriptions_query->post->ID, 'page_title', true);
											} else {
												$subscription_title = $subscriptions_query->post->post_title;
											}

											$event_tracking_code = get_post_meta($subscriptions_query->post->ID, 'event_tracking_code', true);

											// Insert Service Links
											if ( class_exists( 'agora_auth_container' ) ) {
												$auth_container = new agora_auth_container( $subscriptions_query->post->ID );
												$auth_container = apply_filters('agora_middleware_check_permission', $auth_container);			
												
												if ( $auth_container->is_allowed() ) {
													if (in_array(wp_get_post_terms($subscriptions_query->post->ID, 'pubcode')[0]->name, $active_bulk_sub)) {
														$subscription_bulk_access = true;
													}													
													
													$accordion_content = '<div class="col-lg-12 accordion-container ';
													$accordion_content .= $subscription_bulk_access ? 'bulk-container' : '';
													$accordion_content .= '">';
													
													$children_args = array(
														'post_type'      => 'page',
														'posts_per_page' => -1,
														'post_parent'    => $subscriptions_query->post->ID,
														'post_status'    => 'publish',
														'order'          => 'ASC',
														'orderby'        => 'menu_order'
													 );

													$children_query = new WP_Query( $children_args );

													if ( $children_query->have_posts() ) {
														$subscription_links = '';
														$subscription_links_feat = '';

														$is_bauman = $post->ID == '39460' || $post->ID == '219789' ? true : false ;
														$bauman_content = '';

														while ( $children_query->have_posts() ) { 
															$children_query->the_post();
															$post_page_title = trim( strtolower( get_post_meta($children_query->post->ID, 'page_title', true) ) );
															
															// TODO: find/create a common key for these page types
															// Page Attributes Order?
															if ( $post_page_title === 'model portfolio' || $post_page_title === 'trade alerts' || $is_bauman ) {
																
																if ( $subscription_bulk_access ) {
																	$subscription_links_feat .= '<div><div class="bulk-portfolio">';
																	$subscription_links_feat .= '<div class="portfolio-placeholder">';
																		
																	$subscription_links_feat .= trim( get_post_meta($subscriptions_query->post->ID, 'bundle_product_welcome_text', true) );
																	
																	$subscription_links_feat .= '</div><div class="portfolio-links">';
																	$iframe_content = '';
																	$portfolio_content = $children_query->post->post_content;
																	$portfolio_content = str_replace('src=', 'class="iframe-data" data-src=', $portfolio_content);
																	
																	$portfolio_content = apply_filters('the_content', $portfolio_content);

																	$subscription_links_feat .= $portfolio_content;
																	$subscription_links_feat .= '</div>';
																	
																	if (!$subscription_bulk_files) {
																		$subscription_links_feat .= '<script async type="text/javascript" src="/wp-content/themes/BanyanHill_3_0/js/bulk-portfolio.js"></script>';
																		
																		$subscription_bulk_files = true;
																	}
																	
																	//$iframe_content .= file_get_contents('https://publishers.tradesmith.com/Preview/Preview?guid=796eebb0-e4c3-4e14-9469-e48a3120233e');

																	$archive_args = array();																	
																} else {
																	$subscription_links_feat .= '<a href="' . $children_query->post->post_name . '" rel="modal:open" class="' . ($post_page_title === 'trade alerts' ? 'modalTrades' : 'modalPortfolio') .  '" data-post-id="' . $children_query->post->ID . '"><div class="modelPortWrp"><p>' . $post_page_title . ' <span>(click to view)</span>';

																	$subscription_links_feat .= $post_page_title === 'trade alerts' ? '<span class="trade-alert"><span class="trade-alert-modal">Trade Alerts have been posted since your last visit!</span><i class="fa fa-exclamation-triangle"></i></span>' : '';

																	$subscription_links_feat .= '</p></div></a>';
																	$subscription_links_feat .= '<div id="' . $children_query->post->post_name . '" class="modal" data-post-id="' . $children_query->post->ID . '">';
																	$subscription_links_feat .= '<div class="modalHeader"><div><h1 class="sectionHead"><a href="' . esc_url( get_permalink( $children_query->post->ID ) ) . '" target="_blank">' . $post_page_title . '</a></h1></div><div><a href="' . esc_url( get_permalink( $children_query->post->ID ) ) . '" target="_blank"><i class="fa fa-external-link"></i></a></div><div class="clear"></div></div><hr class="sectionHR"><div class="modalContent">';
																}

																if ( $post_page_title === 'model portfolio' && !$subscription_bulk_access ) {
																	$iframe_content = '';
																	$portfolio_content = $children_query->post->post_content;
																	$portfolio_content = str_replace('src=', 'class="loading-iframe" data-src=', $portfolio_content);

//																	$portfolio_content = apply_filters('the_content', $portfolio_content);

																	$subscription_links_feat .= $portfolio_content;
																	
																	//$iframe_content .= file_get_contents('https://publishers.tradesmith.com/Preview/Preview?guid=796eebb0-e4c3-4e14-9469-e48a3120233e');

																	$archive_args = array();
																} else if ( $is_bauman ) {
																	$terms = wp_get_post_terms($post->ID, 'portfolio-category');
																	$terms_id = $terms[0]->term_id;

																	$archive_args = array(
																		'tax_query' => array(
																			array(
																				'taxonomy' => 'portfolio-category',
																				'field' => 'id',
																				'terms' => $terms_id,
																				'include_children' => false
																			)
																		),
																		'post_type' => 'portfolio',
																		'post_status' => 'publish',
																		'posts_per_page' => 3,
																		'meta_key' => 'order_date',																	
																		'orderby' => [
																			'meta_value_num' => 'DESC',
																			'date'=> 'DESC'
																		]
																	);
																} else {
																	$terms = wp_get_post_terms($post->ID, 'archives-category');
																	$terms_id = $terms[0]->term_id;

																	$archive_args = array(
																		'tax_query' => array(
																			array(
																				'taxonomy' => 'archives-category',
																				'field' => 'id',
																				'terms' => $terms_id,
																				'include_children' => false
																			)
																		),
																		'post_type' => 'archives',
																		'post_status' => 'publish',
																		'posts_per_page' => 3,
																		'meta_key' => 'order_date',																	
																		'orderby' => [
																			'meta_value_num' => 'DESC',
																			'date'=> 'DESC'
																		]
																	);
																}

																if ( !empty($archive_args) ) {
																	$archive_query = new WP_Query( $archive_args );

																	if ( $archive_query->have_posts() ) {
																		$trade_alert_content = '';
																		$new_alert_content = '';
																		$transcript_alert_content ='';

																		while ( $archive_query->have_posts() ) { 
																			$archive_query->the_post();

																			$archive_page_title = get_post_meta(get_the_ID(), 'page_title', true) ? get_post_meta(get_the_ID(), 'page_title', true) : get_post_meta(get_the_ID(), 'pdf_title', true);
																			$archive_page_date = get_post_meta(get_the_ID(), 'archieve_date', true) ? get_post_meta(get_the_ID(), 'archieve_date', true) : get_post_meta(get_the_ID(), 'pdf_date', true);

																			if ( get_post_meta(get_the_ID(), 'pdf_link', true) ) {
																				$archive_page_url = get_post_meta(get_the_ID(), 'pdf_link', true);
																				$archive_page_link = '<p><a class="readMore" href="' . $archive_page_url . '" target="_blank">Download PDF</a></p>';
																			} else {
																				$archive_page_url = get_permalink(get_the_ID());
																				$archive_page_link = '<p>' . get_the_excerpt() . ' <a class="readMore" href="' . $archive_page_url . '" target="_blank">Read More</a></p>';
																			}																			

																			$trade_alert_content .= '<section class="tradeAlert">';				
																			$trade_alert_content .= '<h1>';

																			if ( strtotime( get_post_meta(get_the_ID(), 'archieve_date', true) ) >= strtotime( get_last_login() ) ) {
																				$trade_alert_content .= '<span><i class="fa fa-exclamation-triangle"></i> NEW TRADE ALERT</span>';

																				$new_alert_content .= '<section class="tradeAlert">';
																				$new_alert_content .= '<h1><p><a href="' . $archive_page_url . '" target="_blank">' . $archive_page_title . '</a></p></h1>';
																				$new_alert_content .= '</section>';

																				array_push( $new_alert_notify, $children_query->post->post_name );
																				$new_alert_count++;					
																			}
																			
																			$trade_alert_content .= '<a href="' . $archive_page_url . '" target="_blank">' . $archive_page_title . '</a>';													
																			$trade_alert_content .= '</h1>';
																			$trade_alert_content .= '<h2>' . get_post_meta(get_the_ID(), 'archieve_date', true) . '</h2>';
																			$trade_alert_content .= $archive_page_link;
																			$trade_alert_content .= '</section>';
																		}
																		if ($new_alert_content !== '') {
																			$new_alert_container = '<div class="subscriptionAlert"><h1><a href="' . esc_url( get_permalink( $subscriptions_query->post->ID ) ) . '" target="_blank">' . $subscription_title . '</a></h1>' . $new_alert_content . '</div>';

																			array_push( $new_alerts, $new_alert_container );	
																		}

																		if ($is_bauman) {
																			$bauman_content = $trade_alert_content;
																		} else {
																			$subscription_links_feat .= $trade_alert_content;
																		}

																		wp_reset_postdata();
																	}																			
																}

																$subscription_links_feat .= $bauman_content . '</div></div>';
															} else if ($post_page_title === 'transcripts') {
																$terms = wp_get_post_terms($post->ID, 'portfolio-category');
																$terms_id = $terms[0]->term_id;

																$transcript_args = array(
																	'tax_query' => array(
																		array(
																			'taxonomy' => 'portfolio-category',
																			'field' => 'id',
																			'terms' => $terms_id,
																			'include_children' => false
																		)
																	),
																	'post_type' => 'portfolio',
																	'post_status' => 'publish',
																	'posts_per_page' => 3,
																	'meta_key' => 'order_date',																	
																	'orderby' => [
																		'meta_value_num' => 'DESC',
																		'date'=> 'DESC'
																	]
																);
																
																$transcript_query = new WP_Query( $transcript_args );
																
																if ( $transcript_query->have_posts() ) {
																	$new_transcript_content = '';
																	$transcript_alert_content ='';

																	while ( $transcript_query->have_posts() ) { 
																		$transcript_query->the_post();

																		$transcript_page_title = get_post_meta(get_the_ID(), 'pdf_title', true);
																		$transcript_page_url = get_post_meta(get_the_ID(), 'pdf_link', true);
																		$transcript_page_link = '<p><a class="readMore" href="' . $archive_page_url . '" target="_blank">Download PDF</a></p>';
																		
																		$transcript_alert_content .= '<section class="tradeAlert">';				
																		$transcript_alert_content .= '<h1>';
																		
																		if ( strtotime( get_post_meta(get_the_ID(), 'pdf_date', true) ) >= strtotime( get_last_login() ) ) {
																			$transcript_alert_content .= '<span><i class="fa fa-exclamation-triangle"></i> NEW TRANSCRIPT</span>';

																			$new_transcript_content .= '<section class="tradeAlert">';
																			$new_transcript_content .= '<h1><p><a href="' . $transcript_page_url . '" target="_blank">' . $transcript_page_title . '</a></p></h1>';
																			$new_transcript_content .= '</section>';

																			array_push( $new_transcript_notify, $children_query->post->post_name );
																			$new_transcript_count++;					
																		}
																		
																		$transcript_alert_content .= '<a href="' . $archive_page_url . '" target="_blank">' . $archive_page_title . '</a>';													
																		$transcript_alert_content .= '</h1>';
																		$transcript_alert_content .= '<h2>' . get_post_meta(get_the_ID(), 'archieve_date', true) . '</h2>';
																		$transcript_alert_content .= $archive_page_link;
																		$transcript_alert_content .= '</section>';																		
																	}
																	
																	if ($new_transcript_content !== '') {
																		$new_transcript_container = '<div class="subscriptionAlert"><h1><a href="' . esc_url( get_permalink( $subscriptions_query->post->ID ) ) . '" target="_blank">' . $subscription_title . '</a></h1>' . $new_transcript_content . '</div>';

																		array_push( $new_transcripts, $new_transcript_container );	
																	}																	
																	
																	wp_reset_postdata();
																}
																
																$subscription_links .= '<a ' . ($new_transcript_content !== '' ? 'class="has-alert" ' : '') . 'href="' . esc_url( get_permalink( $children_query->post->ID ) ) . '" target="_blank"><div class="modelPortWrp"><p>' . $post_page_title . ' <span>(click to view)</span><span class="trade-alert"><span class="trade-alert-modal">Transcripts have been posted since your last visit!</span><i class="fa fa-exclamation-triangle"></i></span></p></div></a>';																
															} else {
																$subscription_links .= '<a href="' . esc_url( get_permalink( $children_query->post->ID ) ) . '" target="_blank"><div class="modelPortWrp"><p>' . $post_page_title . ' <span>(click to view)</span></p></div></a>';
															}
														}
														wp_reset_postdata();
														$accordion_content .= $subscription_links_feat . $subscription_links;
													}
													
														if ( $subscription_bulk_access ) {
															$accordion_content .= '<a href="#premiumServices"><div style="border-top: 1px solid #ccc;margin-bottom: 15px;"></div><div class="modelPortWrp"><p>My Services</p></div></a>';
															
//															if ( $sub->id->item->itemNumber === 'SOV' && $sub->memberCat === 'GM' ) {
//																$active_bulk_sub = array_diff( $active_bulk_sub, ['SOV'] );
//																
////																$accordion_content .= '</div></div></div></div>';
//															}
														}
														$accordion_content .= '</div>'; // accordion-container END
													
													if ( $children_query->post_count > 2 && !$subscription_bulk_access ) {
														$accordion_content .= '<div class="col-lg-12">';
														$accordion_content .= '<button class="btnSubAccess view-more-btn" value="Access Subscription"><span>View More Options</span></button>';
														$accordion_content .= '</div>';
													}															
														$accordion_content .= '<div class="col-lg-12">';
														$accordion_content .= '<a href="css-subscriptions" rel="modal:open" data-link-pubcode="' . wp_get_post_terms($subscriptions_query->post->ID, 'pubcode')[0]->name . '"  class="accordionModifySub">Modify Subscription<i class="fa fa-edit"></i></a>';
														$accordion_content .= '</div>';

														$subscription_access_yes_no = true;
												} else {
													// User Does Not Have Access to Subscription
													if ( get_post_meta($subscriptions_query->post->ID, 'premium_content_text', true) ) {
														$excerpt_content = get_post_meta($subscriptions_query->post->ID, 'premium_content_text', true);
													} else {
														$excerpt_content = wordwrap(get_the_excerpt($subscriptions_query->post->ID), 250);
														$i_fav = strpos($excerpt_content, "\n");

														if ( $i_fav ) {
															$excerpt_content = substr($excerpt_content, 0, $i_fav);
														}
													}														

													if ( is_user_logged_in() ) {
														$standard_content = '<div class="col-lg-12">';
														$standard_content .= '<div class="btnSubAccess learn-more-btn"><span>Learn More</span></div>';
														$standard_content .= '</div>';
													} else {
														if (strtolower($is_front_end_subscription) === 'yes') {
															$cc_phone_number = '1-866-584-4096';
														} else {
															$cc_phone_number = '1-877-422-1888';
														}															

														$standard_content = '<div class="col-lg-12">';
														$standard_content .= '<div class="btnSubAccess learn-more-btn"><a href="tel:+' . $cc_phone_number . '"><span>Contact Us: ' . $cc_phone_number . '</span></a></div>';
														$standard_content .= '</div>';															
													}	
												}														
											}

											// Build grids based on content
											// If accordion is available user has access to service
											if ($accordion_content && !$subscription_bulk_access) {
												$wrapper_class = 'col-lg-6 col-md-6 col-sm-12 col-12';
												$expert_image_class = 'col-lg-2 col-md-2 col-sm-3 col-3';
												$content_class = 'col-lg-10 col-md-9 col-sm-9 col-9';
												$excerpt_class = 'col-lg-1 col-md-1 col-sm-1 col-1';
											} else {
												$wrapper_class = 'col-lg-12 col-md-12 col-sm-12 col-12';
												$expert_image_class = 'col-xl-1 col-lg-2 col-md-2 col-sm-2 col-3';
												$content_class = 'col-xl-4 col-lg-5 col-md-5 col-sm-10 col-9';
												$excerpt_class = 'col-xl-6 col-lg-5 col-md-5 col-sm-12 col-12 excerptContent';
											}

											$subscription_content .= '<div class="' . $wrapper_class . ' expertContain ' . wp_get_post_terms($subscriptions_query->post->ID, 'pubcode')[0]->name . '-container">';
											$subscription_content .= '<a name="' . wp_get_post_terms($subscriptions_query->post->ID, 'pubcode')[0]->name . '"></a>';
											$subscription_content .= '<div class="subscription_block row currentSubScript">';

											if ( isset($item->issuesRemaining) && $item->issuesRemaining < 3 ) {
												$issues_text = $item->issuesRemaining  === 1 ? $item->issuesRemaining . ' Issue Left!' : $item->issuesRemaining  === 0 ? 'Renew Now!' : $item->issuesRemaining . ' Issues Left!' ;
												
												$subscription_content .= '<div class="corner-ribbon top-right red shadow" data-renew-url="' . $item->purchaseLink . '">' . $issues_text . '</div>';

												foreach($item as $key => $value) {
													unset($item->$key);
												}
											}

											$expert_image_url = false;

											// Get value of expert ID in the subscription

											if ( get_post_meta($subscriptions_query->post->ID, 'expert_id', true) ) {
												$expert_attached_id = get_post_meta($subscriptions_query->post->ID, 'expert_id', true);

												// Check if expert has thumbnail
												if ( has_post_thumbnail( $expert_attached_id ) ) {
													$expert_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $expert_attached_id ), array('68','90') );
												}
											}

											if ( $expert_image_url ) {
												$subscription_content .= '<div class="' . $expert_image_class . ' expertProfile">';
												$subscription_content .= '<a href="' . get_permalink( $expert_attached_id ) . '" target="_blank"><img class="loading" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDBweCIgIGhlaWdodD0iNDBweCIgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0ibGRzLWVjbGlwc2UiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsgYmFja2dyb3VuZDogbm9uZTsiPjxwYXRoIG5nLWF0dHItZD0ie3tjb25maWcucGF0aENtZH19IiBuZy1hdHRyLWZpbGw9Int7Y29uZmlnLmNvbG9yfX0iIHN0cm9rZT0ibm9uZSIgZD0iTTEwIDUwQTQwIDQwIDAgMCAwIDkwIDUwQTQwIDQzIDAgMCAxIDEwIDUwIiBmaWxsPSJyZ2JhKDAlLDAlLDAlLDAuNikiIHRyYW5zZm9ybT0icm90YXRlKDM2MCAtOC4xMDg3OGUtOCAtOC4xMDg3OGUtOCkiIGNsYXNzPSIiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsiPjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgdHlwZT0icm90YXRlIiBjYWxjTW9kZT0ibGluZWFyIiB2YWx1ZXM9IjAgNTAgNTEuNTszNjAgNTAgNTEuNSIga2V5VGltZXM9IjA7MSIgZHVyPSIwLjVzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiBjbGFzcz0iIiBzdHlsZT0iYW5pbWF0aW9uLXBsYXktc3RhdGU6IHJ1bm5pbmc7IGFuaW1hdGlvbi1kZWxheTogMHM7Ij48L2FuaW1hdGVUcmFuc2Zvcm0+PC9wYXRoPjwvc3ZnPg==" data-src="' . $expert_image_url[0] . '" alt="' . get_the_title( $expert_attached_id ) . '"></a>';
												$subscription_content .= '</div>';
											}

											$subscription_content .= '<div class="' . $content_class . ' expertContent">';

											// Get subscription's thumbnail
											if ( has_post_thumbnail( $subscriptions_query->post->ID ) ) {
												$subscription_image_url = wp_get_attachment_url( get_post_thumbnail_id( $subscriptions_query->post->ID ) );

												$subscription_content .= '<a href="' . get_permalink( $subscriptions_query->post->ID ) . '" target="_blank"><img class="loading" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDBweCIgIGhlaWdodD0iNDBweCIgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0ibGRzLWVjbGlwc2UiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsgYmFja2dyb3VuZDogbm9uZTsiPjxwYXRoIG5nLWF0dHItZD0ie3tjb25maWcucGF0aENtZH19IiBuZy1hdHRyLWZpbGw9Int7Y29uZmlnLmNvbG9yfX0iIHN0cm9rZT0ibm9uZSIgZD0iTTEwIDUwQTQwIDQwIDAgMCAwIDkwIDUwQTQwIDQzIDAgMCAxIDEwIDUwIiBmaWxsPSJyZ2JhKDAlLDAlLDAlLDAuNikiIHRyYW5zZm9ybT0icm90YXRlKDM2MCAtOC4xMDg3OGUtOCAtOC4xMDg3OGUtOCkiIGNsYXNzPSIiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsiPjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgdHlwZT0icm90YXRlIiBjYWxjTW9kZT0ibGluZWFyIiB2YWx1ZXM9IjAgNTAgNTEuNTszNjAgNTAgNTEuNSIga2V5VGltZXM9IjA7MSIgZHVyPSIwLjVzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiBjbGFzcz0iIiBzdHlsZT0iYW5pbWF0aW9uLXBsYXktc3RhdGU6IHJ1bm5pbmc7IGFuaW1hdGlvbi1kZWxheTogMHM7Ij48L2FuaW1hdGVUcmFuc2Zvcm0+PC9wYXRoPjwvc3ZnPg==" data-src="' . $subscription_image_url . '" alt="' . $subscriptions_query->post->post_title . '"></a>';
											}

											$subscription_content .= '</div>';
											$subscription_content .= '<div class="' . $excerpt_class . '"><span></span>' . $excerpt_content . '</div>';
											
											if ($subscription_access_yes_no === false && is_user_logged_in()) {
												$subscription_content .= '<div class="col-12 fullExcerptContent">';
												$subscription_content .= '<div class="row">';
												$subscription_content .= '<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">';
												$subscription_content .= get_the_excerpt($subscriptions_query->post->ID);
												$subscription_content .= '</div>';
												$subscription_content .= '<div class="col-lg-3 col-md-3 d-sm-none d-xs-none">';
												$subscription_content .= '</div>';
												$subscription_content .= '</div>';
												$subscription_content .= '<div class="row">';
												$subscription_content .= '<div class="col-12">';

												if ( isset($item->purchaseLink) ) {
													$subscription_content .= '<div class="signup-button">';
													$subscription_content .= '<a href="' . $item->purchaseLink . '">' . get_post_meta($subscriptions_query->post->ID, 'sidebar_button_text', true) . '</a>';
													$subscription_content .= '</div>';									
												}

												$subscription_content .= '</div>';
												$subscription_content .= '</div>';											
												$subscription_content .= '</div>'; // full excerpt content END												
											}
											
											$subscription_content .= $accordion_content . $standard_content;

											$subscription_content .= '</div>'; // subscription_block END
											$subscription_content .= '</div>'; // expertContain END

											// Push into appropriate access/no access/front end subscription array
											if ( $subscription_access_yes_no === true ) {
												if ( $subscription_bulk_access === true ) {
													array_push( $subscriptions_bulk_access, $subscription_content );	
												} else {
													array_push( $subscriptions_have_access, $subscription_content );
												}
											} else {
												if ( strtolower($is_front_end_subscription) === 'yes' ) {
													array_push( $subscriptions_front_end, $subscription_content );
												} else {
													array_push( $subscriptions_no_access, $subscription_content );	
												}
											}													
										}
									}

									wp_reset_postdata();

									// Display subscriptions the user has access to.
									if ( $subscriptions_bulk_access ) {
									?>
									<div class="row sub-access">
										<div class="col-lg-12 col-md-12 col-sm-12 col-12">
											<h1 class="sectionHead">Your Elite Bundle Memberships</h1>
											<hr class="sectionHR">
										</div>										
									<?php										
										foreach ( $subscriptions_bulk_access as $item ) {
											echo $item;
										}
									?>
									</div>
									<?php
									}									
									
									// Display subscriptions the user has access to.
									if ( $subscriptions_have_access ) {
									?>
									<div class="row sub-access">
										<div class="col-lg-12 col-md-12 col-sm-12 col-12">
											<a id="premiumServices"></a>												
											<h1 class="sectionHead">Your Premium Services</h1>
											<hr class="sectionHR">										
										</div>										
									<?php										
										foreach ( $subscriptions_have_access as $subscription_have_access ) {
											echo $subscription_have_access;
										}
									?>
									</div>
									<?php
									}
									?>
									<!--div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-12">
											<ins data-revive-zoneid="41" data-revive-id="623abf93e179094d5059d128355ac65e"></ins>
											<script async src="//myalphaspace.com/rva/www/dlv/bhsyncjs.php"></script>
										</div>
									</div-->
									<?php
									// Display the rest of the subscriptions
									if ( $subscriptions_front_end ) { 
									?>
									<div class="row no-access">
										<div class="col-lg-12 col-md-12 col-sm-12 col-12">
											<h1 class="sectionHead">Our Other Research Services</h1>
											<hr class="sectionHR">
										</div>
									<?php 
										shuffle($subscriptions_front_end);
										
										foreach ( $subscriptions_front_end as $subscription_front_end ) {
											echo $subscription_front_end;
										}
									?>
									</div>
									<?php 
									}
									?>
									<!--div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-12">
											<ins data-revive-zoneid="41" data-revive-id="623abf93e179094d5059d128355ac65e"></ins>
											<script async src="//myalphaspace.com/rva/www/dlv/bhsyncjs.php"></script>
										</div>
									</div-->								
									<?php
									if ( $subscriptions_no_access ) {
									?>
									<div class="row no-access">
										<div class="col-lg-12 col-md-12 col-sm-12 col-12">
											<h1 class="sectionHead">Our Premium Research Services</h1>
											<hr class="sectionHR">
										</div>								
									<?php 
									shuffle($subscriptions_no_access);	
										
									foreach ( $subscriptions_no_access as $subscription_no_access ) {
										echo $subscription_no_access;
									}
									?>
									</div>
									<?php
									}									
								 } ?>
							</div>
							<?php 
								if ($new_alert_count > 0) {
							?>
							<div id="newAlertContainer"><a href="newAlertContent" rel="modal:open">You have <?php echo $new_alert_count; ?> New Alert<?php echo $new_alert_count === 1 ? '' : 's' ?>!</a>
								<div id="newAlertContent" class="modal">
									<div class="modalHeader">
										<h1 class="sectionHead">Your Latest trade alerts</h1>
										<h2>Posted Since: <?php echo get_last_login( array('format' => 'M j, Y') ); ?></h2>
									</div>
									<hr class="sectionHR">
									<div id="newAlertContentContainer">
										<?php
											foreach ( $new_alerts as $new_alerts ) {
												echo $new_alerts;
											}								
										?>
									</div>									
								</div>
								<span><i class="fa fa-close"></i></span>
							</div>						
							<?php
								}
							?>
							<?php 
								if ($new_transcript_count > 0) {
							?>
							<div id="newTranscriptContainer"><a href="newTranscriptContent" rel="modal:open">You have <?php echo $new_transcript_count; ?> New Transcript<?php echo $new_transcript_count === 1 ? '' : 's' ?>!</a>
								<div id="newTranscriptContent" class="modal">
									<div class="modalHeader">
										<h1 class="sectionHead">Your Latest Transcripts</h1>
										<h2>Posted Since: <?php echo get_last_login( array('format' => 'M j, Y') ); ?></h2>
									</div>
									<hr class="sectionHR">
									<div id="newTranscriptContentContainer">
										<?php
											foreach ( $new_transcripts as $new_transcripts ) {
												echo $new_transcripts;
											}								
										?>
									</div>									
								</div>
								<span><i class="fa fa-close"></i></span>
							</div>						
							<?php
								}
							?>						
							<div id="renewalsContainer" class="modal">
								<div class="modalHeader">
									<h1 class="sectionHead">Your Renewals</h1>
									<h2>As of: <?php echo get_last_login( array('format' => 'M j, Y') ); ?></h2>
								</div>
								<hr class="sectionHR">
								<div id="renewalsContentContainer">
									<?php
										foreach ( $renewals as $renewals ) {
											echo $renewals;
										}								
									?>
								</div>
							</div>						
							<div id="css-subscriptions" class="modal">
								<?php load_css_portal(); ?>
							</div>						
						<?php endwhile; ?>
					</main>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
<?php
	if ( !empty( $new_alert_notify ) ) {
		echo 'var newAlertNotify = ' . json_encode($new_alert_notify) . ';';
	} else {
		echo 'var newAlertNotify;';
	}
	
	if ( !empty( $new_transcript_notify ) ) {
		echo 'var newTranscriptNotify = ' . json_encode($new_transcript_notify) . ';';
	} else {
		echo 'var newTranscriptNotify;';
	}	
	
	if ( !empty( $user_subscription_meta ) ) {
		echo 'var userSubMeta = ' . json_encode($user_subscription_meta) . ';';
	} else {
		echo 'var userSubMeta;';
	}
	
	echo 'var bulk_sub = ' . json_encode($active_bulk_sub) . ';';
?>

	var targetPubcode = '';
	var standardHeaderHeight = jQuery('#main-header-wrapper').height();
	
	function resizeRVAContainer() {
		jQuery('#premCont .rva-container').css('width', jQuery('.bootstrap-wrapper').width());
		jQuery('#premCont .rva-container').css('margin-left', -(jQuery('.bootstrap-wrapper').innerWidth() - jQuery('#main').innerWidth())/2);		
	}
	
	jQuery(document).ready(function() {	
		if (jQuery('#newTranscriptContainer').length > 0) {
			jQuery('#newTranscriptContainer').prependTo('#alertPlaceholder');

			for (var i = 0; i < newTranscriptNotify.length; i++) {
				jQuery('.accordion-container a[href="#' + newTranscriptNotify[i] + '"]').addClass('has-alert');
			}
		}		
		
		if (jQuery('#newAlertContainer').length > 0) {
			jQuery('#newAlertContainer').prependTo('#alertPlaceholder');

			for (var i = 0; i < newAlertNotify.length; i++) {
				jQuery('.accordion-container a[href="#' + newAlertNotify[i] + '"]').addClass('has-alert');
			}
		}		

		jQuery('.access-btn-content a').each(function() {
			if(jQuery(this).text().toLowerCase() === 'learn more') {
				jQuery(this).parent('div').hide();
			}
		});

		jQuery('.view-more-btn').click(function() {
			// container animation is done in CSS
			jQuery(this).children('span').fadeToggle(400, function() {
				jQuery(this).text(jQuery(this).text().toLowerCase() === 'view more options' ? 'Hide Options' : 'View More Options');
				jQuery(this).fadeToggle();
			});
			jQuery(this).parent().siblings('.accordion-container').toggleClass('view-more');
			
			if (jQuery('.introjs-helperLayer').height() < 350 ) {
				jQuery('.introjs-helperLayer').height(jQuery('#blueSectionHeader').outerHeight() + 274 + 8);
			} else {
				jQuery('.introjs-helperLayer').height(jQuery('#blueSectionHeader').outerHeight() + 8);
			}			
		});
		
		jQuery('.currentSubScript .corner-ribbon').click(function() {
			window.open(jQuery(this).data('renewUrl'), '_blank');
		});		

		jQuery('#blueSectionHeader').click(function() {
			jQuery('#blueSection').slideToggle(300);
		
			jQuery('#blueSectionHeader span').children('i').fadeToggle(function () {
				jQuery(this).toggleClass('fa-minus fa-plus').fadeToggle();
			});
			
			jQuery('#blueSectionHeader .plus-minus-text').fadeToggle(function() {					
				jQuery(this).text(jQuery(this).text().toLowerCase() === 'show' ? 'HIDE' : 'SHOW');
				jQuery(this).fadeToggle();
			});
			
			if (jQuery(this).hasClass('introjs-showElement')) {
				//jQuery('#blueSection').outerHeight() = 274
				jQuery('#blueSection').addClass('introjs-showElement');
				
				if (jQuery('.introjs-helperLayer').height() < 50 ) {
					jQuery('.introjs-helperLayer').height(jQuery('#blueSectionHeader').outerHeight() + 274 + 8);
				} else {
					jQuery('.introjs-helperLayer').height(jQuery('#blueSectionHeader').outerHeight() + 8);
				}
			}
		});

		jQuery('#newAlertContainer > span, #newTranscriptContainer > span').click(function() {
			jQuery(this).parent().fadeOut();
		});
		
		jQuery('#blueWelcome span').click(function() {
			jQuery('#blueWelcome').fadeOut();
		});
		
		// Customer Service Portal depends on hash values
		jQuery('#css-subscriptions').on(jQuery.modal.OPEN, function(event, modal) {
			if (jQuery('#tfs_css_body #tfs_css_my_subscriptions').length === 0 || targetPubcode !== '') {
				jQuery('li[title="tfs_css_my_subscriptions"]').click();
			}
		});
		
		jQuery('#css-subscriptions').on(jQuery.modal.BEFORE_CLOSE, function(event, modal) {
			jQuery('li#css_my_account').click();
			targetPubcode = '';
		});
		
		jQuery('a.accordionModifySub').click(function() {
			targetPubcode = jQuery(this).data('linkPubcode');
		});
		
		jQuery('.modal').on(jQuery.modal.OPEN, function(event, modal) {
			if (jQuery('a[data-post-id="' + event.target.dataset.postId + '"]').hasClass('modalPortfolio') && !modal.elm.data('loaded')) {
			  modal.elm.css('max-width', '1200px');				
			  var targetDiv = jQuery(modal.elm.selector).find('.modalContent');
				
				jQuery.ajax({
					url: '/wp-admin/admin-ajax.php',
					type: "POST",
					data: {
						pid: event.target.dataset.postId,
						action: 'load_eportfolio',

					},

					beforeSend: function () {
						jQuery(targetDiv.selector).html('<img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDBweCIgIGhlaWdodD0iNDBweCIgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0ibGRzLWVjbGlwc2UiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsgYmFja2dyb3VuZDogbm9uZTsiPjxwYXRoIG5nLWF0dHItZD0ie3tjb25maWcucGF0aENtZH19IiBuZy1hdHRyLWZpbGw9Int7Y29uZmlnLmNvbG9yfX0iIHN0cm9rZT0ibm9uZSIgZD0iTTEwIDUwQTQwIDQwIDAgMCAwIDkwIDUwQTQwIDQzIDAgMCAxIDEwIDUwIiBmaWxsPSJyZ2JhKDAlLDAlLDAlLDAuNikiIHRyYW5zZm9ybT0icm90YXRlKDM2MCAtOC4xMDg3OGUtOCAtOC4xMDg3OGUtOCkiIGNsYXNzPSIiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsiPjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgdHlwZT0icm90YXRlIiBjYWxjTW9kZT0ibGluZWFyIiB2YWx1ZXM9IjAgNTAgNTEuNTszNjAgNTAgNTEuNSIga2V5VGltZXM9IjA7MSIgZHVyPSIwLjVzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiBjbGFzcz0iIiBzdHlsZT0iYW5pbWF0aW9uLXBsYXktc3RhdGU6IHJ1bm5pbmc7IGFuaW1hdGlvbi1kZWxheTogMHM7Ij48L2FuaW1hdGVUcmFuc2Zvcm0+PC9wYXRoPjwvc3ZnPg==" style="margin: 0 auto; width: 100px; height: 100px; display: flex;" />')
					},

					success: function (rsp) {
					  rsp = JSON.parse(rsp);
					  if (rsp.success) {
						targetDiv.html(rsp.data);
						targetDiv.parent().attr('data-loaded', true);

						//Get script & execute
						if (typeof(initPortfolioEvents) === 'undefined') {
							jQuery.getScript( '/wp-content/plugins/tfs-portfolio-tracker/portfolio-tracker-theme/assets/js/custom.dataTables.js');
							//TODO: proper callback, no polling
							var initTimer = setInterval(function() {
								if (typeof(initPortfolioEvents) !== 'undefined') {
									initPortfolioEvents();
									clearInterval(initTimer);
								}
							}, 500);
						} else {
							initPortfolioEvents();
						}
					  } else {
						//Error state
						targetDiv.html(rsp.data);
					  }					
					}
				});				
			} else {
				var bLazy = new Blazy({
					selector: '.loading-iframe', //ad iframes
					success: function(element){
						setTimeout(function(){
							element.className = element.className.replace(/\bloading-iframe\b/,'');
						}, 200);
					}
				});				
			}

			jQuery('body').addClass('modal-open');
		});
		
		jQuery('a[rel="modal:open"]').on('click', function(event) {
			event.preventDefault();

			if (jQuery(this).data('postId') === undefined) {
				jQuery('#' + jQuery(this).attr('href')).modal();
			} else {
				jQuery('.modal[data-post-id="' + jQuery(this).data('postId') + '"]').modal();				
			}

			return false;
		});		

		jQuery('.modal').on(jQuery.modal.CLOSE, function(event, modal) {
			jQuery('body').removeClass('modal-open').removeAttr('style');
		});
		
		//TODO: Handle in PHP
		jQuery('.modalContent').each(function() {
		  if (jQuery.trim(jQuery(this).html()) === '') {
			jQuery(this).html('<section class="tradeAlert"><h1><a href="#close-modal" rel="modal:close">No Trade Alerts Currently Available</a></h1></section>');
		  }
		});		
		
		// Resize RVA container
		window.onresize = function () {
			resizeRVAContainer();
		}
		
<?php 
	if ( is_user_logged_in() ) {
?>
		if (!isMobile.matches) {
			if(!Cookies.get('is_prem_first_time_user')) {
				Cookies.set('is_prem_first_time_user', '0', { expires: 365 });
				jQuery('body').addClass('prem-first-time');
			}
			
			jQuery.getScript('<?php echo get_stylesheet_directory_uri() ?>/js/intro-js/intro.min.js', function() {
				jQuery.getScript('<?php echo get_stylesheet_directory_uri() ?>/js/intro-js/intro.premium-content.js', function() {
					initTour();
				});
			});
			jQuery('head').append( jQuery('<link rel="stylesheet" type="text/css" />').attr('href', '/wp-content/themes/BanyanHill_3_0/js/intro-js/introjs.min.css') );			
		}
		
		jQuery('.learn-more-btn').click(function() {
			var that = jQuery(this);
			var container = that.parents('.currentSubScript');
			
			container.toggleClass('open');
			that.children('span').fadeToggle(400, function() {
				that.children('span').text(that.children('span').text().toLowerCase() === 'learn more' ? 'Hide Details' : 'Learn More');
				jQuery(this).fadeToggle();
			});
			container.find('.fullExcerptContent').slideToggle(300);
			container.find('.excerptContent').fadeToggle();
			
			jQuery([document.documentElement, document.body]).animate({
				// menu height + 10px
				scrollTop: (container.offset().top - 47)
			}, 400);			
		});
<?php 
	}
?>		
	});
</script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.modal.min.js"></script>
<style>.page-template-template-subscriptions-list .7FF-container {display:none;}</style>
<?php get_footer(); ?>
