<?php 
	if (!empty($_GET['utm_campaign']) && $_GET['utm_campaign'] === 'Daily-Article-Traffic' ) {
		// set cookie for 7 days
		setcookie("is_signed_up", '1', time()+60*60*24*7, '/', '.banyanhill.com');		
	}
	get_header(); 

	$post_categories = get_post_primary_category($post->ID); 
	$primary_category = $post_categories['primary_category']->name;
?>


<div id="main-content">
	<?php
		if ( et_builder_is_product_tour_enabled() ):

			while ( have_posts() ): the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-url="<?php echo get_post($post->ID)->post_name; ?>" data-title="<?php echo get_post($post->ID)->post_title; ?>" data-category="<?php echo $primary_category ?>" data-scroll-posts="<?php echo get_post_meta(get_the_ID(), 'scrollPosts', true); ?>">
					<div class="entry-content">
					<?php
						the_content();
					?>
					</div> <!-- .entry-content -->

				</article> <!-- .et_pb_post -->

		<?php endwhile;
		else:
	?>
	<div class="container">
		<div id="content-area" class="clearfix">
			<div class="et_pb_extra_column_main">
				<?php
				do_action( 'et_before_post' );
				if ( function_exists('yoast_breadcrumb') ) {
					yoast_breadcrumb('<p id="breadcrumbs">','</p>');
				}
				if ( have_posts() ) :
					while ( have_posts() ) : the_post(); ?>
						<?php
							$post_category_color = extra_get_post_category_color();
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'module single-post-module' ); ?> data-url="<?php echo get_post($post->ID)->post_name; ?>" data-title="<?php echo get_post($post->ID)->post_title; ?>" data-category="<?php echo $primary_category; ?>" data-scroll-posts="<?php echo get_post_meta(get_the_ID(), 'scrollPosts', true); ?>">
							<?php if ( is_post_extra_title_meta_enabled() ) { ?>
							<div class="post-header">
								<h1 class="entry-title"><?php the_title(); ?></h1>
								<div class="post-meta vcard">
									<?php
										$post_expert_slug = str_replace( ' ', '-', strtolower( get_the_author_meta( 'display_name' ) ) );
										$post_expert_ID = get_page_by_path( $post_expert_slug, OBJECT, 'expert' )->ID;

										if ( get_the_post_thumbnail_url( $post_expert_ID, array( 150, 150 ) ) !== false && !empty($post_expert_ID) ) {
											$post_expert_thumbnail = '<img src="' . get_the_post_thumbnail_url( $post_expert_ID, array( 150, 150 ) ) . '" alt="' . get_the_title( $post_expert_ID ) .'" />';
										} else {
											$post_expert_thumbnail = get_avatar( get_the_author_meta( 'ID' ), 150 );
										}
									?>
									<p class="post-meta-author-avatar"><?php echo $post_expert_thumbnail; ?></p>
									<div>
										<p><?php echo extra_display_single_post_meta(); ?></p>
										<p><?php echo BH_reading_time(); ?> read</p>
									</div>
								</div>
							</div>
							<?php } ?>
							<?php 
							if ( get_post_meta(get_the_ID(), 'post_summary', true) ) {
								echo '<div class="post-summary">' . get_post_meta(get_the_ID(), 'post_summary', true) . '</div>';
							}
							?>
							<?php if ( ( et_has_post_format() && et_has_format_content() ) || ( has_post_thumbnail() && is_post_extra_featured_image_enabled() ) ) { ?>
							<div class="post-thumbnail header">
								<?php
								$score_bar = extra_get_the_post_score_bar();
								
								if ($primary_category === 'Great Stuff') {
									$thumb_args = array( 'size' => 'full', 'link_wrapped' => false );
								} else {
									$thumb_args = array( 'size' => 'extra-image-small', 'link_wrapped' => false );	
								}	

								require locate_template( 'post-top-content.php' );
								?>
							</div>
							<?php } ?>

							<?php $post_above_ad = extra_display_ad( 'post_above', false ); ?>
							<?php if ( !empty( $post_above_ad ) ) { ?>
							<div class="et_pb_extra_row etad post_above">
								<?php echo $post_above_ad; ?>
							</div>
							<?php } ?>

							<div class="post-wrap">
							<?php if ( !extra_is_builder_built() ) { ?>
								<div class="post-content entry-content">
									<?php the_content(); ?>
									<?php
										wp_link_pages( array(
											'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'extra' ),
											'after'  => '</div>',
										) );
									?>
								</div>
							<?php } else { ?>
								<?php et_builder_set_post_type(); ?>
								<?php the_content(); ?>
							<?php } ?>
								<!-- Revive Adserver Asynchronous JS Tag - Generated with Revive Adserver v4.1.4 -->
								<!--ins data-revive-zoneid="22" data-revive-id="623abf93e179094d5059d128355ac65e" data-revive-keyword="<?php echo get_post_meta(get_the_ID(), 'mas_keywords', true); ?>"></ins-->
								<script type="text/javascript">
									<!--
									function setReviveIframe() {
									  var rendomRevInt = Math.floor((Math.random() * 8999) + 1000);
										
									  // Create IE + others compatible event handler
									  var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
									  var eventer = window[eventMethod];
									  var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

									  // Listen to message from child window
									  eventer(messageEvent,function(e) {
									  	if (typeof e.data !== 'number') return;

									  	document.querySelector('iframe.resize-iframe').height = e.data;
									  },false);
									  // This needs a new zoneid created on MAS1
									  // document.write("<iframe class='resize-iframe loading-iframe' id='a50d8b27' name='a50d8b27' data-src='https://myalphaspace1.com/www/dlv/bhfr.php?refresh=22&amp;zoneid=21&amp;cb=" + rendomRevInt + "' frameborder='0' scrolling='no' width='728' height='250' style='margin: 0 auto 1.6em auto; display: flex;'><a href='https://myalphaspace1.com/www/dlv/ck.php?n=a7ea2419&amp;cb=" + rendomRevInt + "' target='_blank'><img class='loading-iframe' data-src='https://myalphaspace1.com/www/dlv/bhvw.php?zoneid=21&amp;cb=" + rendomRevInt + "&amp;n=a7ea2419' border='0' alt='' /></a></iframe>");

									}
									//setReviveIframe();

									//document.querySelector('.post-content.entry-content').insertBefore(document.querySelector('.post-wrap iframe#a50d8b27'), document.querySelector('.entry-content > *:nth-child(6)'));
									
									//document.querySelector('.post-content.entry-content').insertBefore(document.querySelector('ins[data-revive-zoneid="22"]'), document.querySelector('.entry-content > *:nth-child(4)'));										
									//-->
								</script>								
							</div>
							<?php if ( $review = extra_post_review() ) { ?>
							<div class="post-wrap post-wrap-review">
								<div class="review">
									<div class="review-title">
										<h3><?php echo esc_html( $review['title'] ); ?></h3>
									</div>
									<div class="review-content">
										<div class="review-summary clearfix">
											<div class="review-summary-score-box" style="background-color:<?php echo esc_attr( $post_category_color ); ?>">
												<h4><?php printf( et_get_safe_localization( __( '%d%%', 'extra' ) ), absint( $review['score'] ) ); ?></h4>
											</div>
											<div class="review-summary-content">
												<?php if ( !empty( $review['summary'] ) ) { ?>
												<p>
													<?php if ( !empty( $review['summary_title'] ) ) { ?>
														<strong><?php echo esc_html( $review['summary_title'] ); ?></strong>
													<?php } ?>
													<?php echo $review['summary']; ?>
												</p>
												<?php } ?>
											</div>
										</div>
										<div class="review-breakdowns">
											<?php foreach ( $review['breakdowns'] as $breakdown ) { ?>
											<div class="review-breakdown">
												<h5 class="review-breakdown-title"><?php echo esc_html( $breakdown['title'] ); ?></h5>
												<div class="score-bar-bg">
													<span class="score-bar" style="background-color:<?php echo esc_attr( $post_category_color ); ?>; width:<?php printf( '%d%%', max( 4, absint( $breakdown['rating'] ) ) );  ?>">
														<span class="score-text"><?php printf( et_get_safe_localization( __( '%d%%', 'extra' ) ), absint( $breakdown['rating'] ) ); ?></span>
													</span>
												</div>
											</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
							<?php $post_below_ad = extra_display_ad( 'post_below', false ); ?>
							<?php if ( !empty( $post_below_ad ) ) { ?>
							<div class="et_pb_extra_row etad post_below">
								<?php echo $post_below_ad; ?>
							</div>
							<?php } ?>
						</article>

						<?php
						if ( extra_is_post_author_box() ) { ?>
						<div class="et_extra_other_module author-box vcard">
							<div class="author-box-header">
								<h3><?php esc_html_e( 'About The Author', 'extra' ); ?></h3>
							</div>
							<div class="author-box-content clearfix">
								<div class="author-box-avatar">
									<?php /* echo get_avatar( get_the_author_meta( 'user_email' ), 170, 'mystery', esc_attr( get_the_author() ) ); */ ?>
									<?php echo $post_expert_thumbnail; ?>
								</div>
								<div class="author-box-description">
									<h4><a class="author-link url fn" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author" title="<?php printf( et_get_safe_localization( __( 'View all posts by %s', 'extra' ) ), get_the_author() ); ?>"><?php echo get_the_author(); ?></a></h4>
									<p class="note"><?php the_author_meta( 'description' ); ?></p>
									<ul class="social-icons">
										<?php foreach ( extra_get_author_contact_methods() as $method ) { ?>
											<li><a href="<?php echo esc_url( $method['url'] ); ?>" target="_blank"><span class="et-extra-icon et-extra-icon-<?php echo esc_attr( $method['slug'] ); ?> et-extra-icon-color-hover"></span></a></li>
										<?php } ?>
									</ul>
								</div>
							</div>
						</div>
				
						<?php } ?>
						<?php if (!$_COOKIE["is_signed_up"]) { ?>
						<div class="Newsletter_new well" style="margin-bottom: 30px;">
							<h3>Get Our Best Newsletters, Absolutely FREE!</h3>
							<div class="Newsletter_copy">
							<p>Editor’s Note: I, along with a few esteemed colleagues, publish our insight in our e-letters <strong><em>Winning Investor Daily</em></strong>, <strong><em>Smart Profits Daily</em></strong>, <strong><em>Bold Profits Daily</em></strong>, <strong><em>Great Stuff</em></strong> &amp; <strong><em>Bauman Daily</em></strong>. Every day, we send you our very best ideas to help protect and grow your wealth. Sign up below for free.</p>
							</div>
							<?php echo do_shortcode( '[bh_signup_form buttontext="Sign Up" xcode="X190V914" position="well"]' ); ?>
						</div>
						<?php } ?>
				
						<nav class="post-nav">
							<div class="nav-links clearfix">
								<?php 
									$previous_thumb = !wp_is_mobile() ? get_the_post_thumbnail(get_previous_post(false, '31', 'category')->ID, 'extra-image-square-small', array ( 'class' => 'attachment-extra-image-small size-extra-image-small loading' )) : '' ;
								if ( !empty($previous_thumb) ) {
								?>
								<div class="nav-link nav-link-prev"><?php
									$previous_thumb = str_replace('src=', 'data-src=', $previous_thumb);
									previous_post_link( '%link', __( '<span class="text"><span>&#171;</span> Previous</span><span class="title">' . $previous_thumb . '%title</span>', 'extra' ), false, '31' );
								?></div>
								<?php 
								}
									$next_thumb = !wp_is_mobile() ? get_the_post_thumbnail(get_next_post(false, '31', 'category')->ID, 'extra-image-square-small', array ( 'class' => 'attachment-extra-image-small size-extra-image-small loading' )) : '' ;
								if ( !empty($next_thumb) ) {
								?>
								<div class="nav-link nav-link-next"><?php
									$next_thumb = str_replace('src=', 'data-src=', $next_thumb);
									next_post_link( '%link', __( '<span class="text">Next <span>&#187;</span></span><span class="title">%title' . $next_thumb . '</span>', 'extra' ), false, '31' ); 
								?></div>
								<?php 
								}
								?>
							</div>
						</nav>
				
				<!-- Related Posts -->
						<div class="et_extra_other_module related-posts">
						<?php 							
							function getRandomId($exclude_ids) {
								$args = array( 
									'numberposts' => 10,
									'category_name' => get_the_category( get_the_ID() )[0]->cat_name,
									'exclude'		=> $exclude_ids
								);
								$posts = get_posts( $args );
								
								// Get IDs of posts retrieved from get_posts
								$ids = array();
								foreach ( $posts as $thepost ) {
									$ids[] = $thepost->ID;	
								}
								
								return $ids[array_rand($ids)];
							}							
							// Get and echo previous and next post in the same category
							// If we are at the beginning/end of the category get a random post
							$previd    = get_adjacent_post( true, '', true, 'category' ) ? get_adjacent_post( true, '', true, 'category' )->ID : NULL;
							$nextid    = get_adjacent_post( true, '', false, 'category' ) ? get_adjacent_post( true, '', false, 'category' )->ID : NULL;
							$excluded_ids = array(get_previous_post(false, '31', 'category')->ID, get_next_post(false, '31', 'category')->ID, $previd, $nextid, $post->ID);
							$previd    = isset( $previd ) && $previd !== $excluded_ids[0] ? $previd : getRandomId($excluded_ids);							
							$nextid    = isset( $nextid ) && $nextid !== $excluded_ids[1] ? $nextid : getRandomId($excluded_ids);							
							
							if ($primary_category !== 'Sponsorship') {
								
						?>				
							<div class="related-posts-header"><h3>Recommended For You</h3></div>
							<div class="related-posts-content clearfix">
								<div class="related-post">
									<div class="featured-image">
										<a href="<?php echo get_permalink($previd) ?>" rel="bookmark" title="<?php echo get_the_title($previd); ?>" class="post-thumbnail">
											<?php 
												$prev_related_thumb = get_the_post_thumbnail($previd, 'extra-image-small', array ( 'class' => 'attachment-extra-image-small size-extra-image-small loading' ));
												$prev_related_thumb = str_replace('src=', 'data-src=', $prev_related_thumb);
												echo $prev_related_thumb;
											?>
											<span class="et_pb_extra_overlay"></span>
										</a>
									</div>
									<h4 class="title">
										<a href="<?php echo get_permalink($previd) ?>" rel="bookmark" title="<?php echo get_the_title($previd); ?>"><?php echo get_the_title($previd); ?></a>
									</h4>
								</div>							
								<!--ins class="related-post" data-revive-zoneid="29" data-revive-id="623abf93e179094d5059d128355ac65e"></ins-->
								<div class="related-post">
									<div class="featured-image">
										<a href="<?php echo get_permalink($nextid) ?>" rel="bookmark" title="<?php echo get_the_title($nextid); ?>" class="post-thumbnail">
											<?php 
												$next_related_thumb = get_the_post_thumbnail($nextid, 'extra-image-small', array ( 'class' => 'attachment-extra-image-small size-extra-image-small loading' ));
												$next_related_thumb = str_replace('src=', 'data-src=', $next_related_thumb);
												echo $next_related_thumb;
											?>
											<span class="et_pb_extra_overlay"></span>
										</a>
									</div>
									<h4 class="title">
										<a href="<?php echo get_permalink($nextid) ?>" rel="bookmark" title="<?php echo get_the_title($nextid); ?>"><?php echo get_the_title($nextid); ?></a>
									</h4>
								</div>
								<!--ins class="related-post" data-revive-zoneid="30" data-revive-id="623abf93e179094d5059d128355ac65e"></ins-->
							</div>
						<?php
						}
						?>
						</div>
						<div id="disqus_thread"></div>
				<?php
					endwhile;
				else :
					?>
					<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
					<?php
				endif;
				wp_reset_query();

				do_action( 'et_after_post' );
				?>

				<?php
				if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'extra_show_postcomments', 'on' ) ) {
					comments_template( '', true );
				}
				?>
			</div><!-- /.et_pb_extra_column.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
	<?php endif; ?>
</div> <!-- #main-content -->
<?php 

include(locate_template( 'template-parts/marketing-sms-campaigns.php' ));

get_footer();
