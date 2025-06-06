<?php 
global $post, $wp_query, $boxshop_theme_options;
$post_format = get_post_format(); /* Video, Audio, Gallery, Quote */
$post_class = 'post-item hentry ';
$show_blog_thumbnail = $boxshop_theme_options['ts_blog_thumbnail'];
?>
<article <?php post_class($post_class) ?>>

	<?php if( $post_format != 'quote' ): ?>
	
		<div class="entry-format">
			<?php 
			
			if( $show_blog_thumbnail ){
			
				if( $post_format == 'gallery' || $post_format === false || $post_format == 'standard' ){
					if( $post_format != 'gallery'){
					?>
					<a class="thumbnail <?php echo esc_attr($post_format); ?> <?php echo ('gallery' == $post_format)?'loading':''; ?>" href="<?php the_permalink() ?>">
					<?php }else{ ?>
					<div class="thumbnail <?php echo esc_attr($post_format); ?> <?php echo ('gallery' == $post_format)?'loading':''; ?>">	
					<?php } ?>
						<figure>
						<?php 
							if( $post_format == 'gallery' ){
								$gallery = get_post_meta($post->ID, 'ts_gallery', true);
								if( $gallery != '' ){
									$gallery_ids = explode(',', $gallery);
								}
								else{
									$gallery_ids = array();
								}
								
								if( has_post_thumbnail() ){
									array_unshift($gallery_ids, get_post_thumbnail_id());
								}
								foreach( $gallery_ids as $gallery_id ){
									echo '<a class="thumbnail '.$post_format.'" href="'.esc_url(get_the_permalink()).'">';
									echo wp_get_attachment_image( $gallery_id, 'boxshop_blog_thumb', 0, array('class' => 'thumbnail-blog') );
									echo '</a>';
								}
								
								if( !has_post_thumbnail() && empty($gallery) ){
									$show_blog_thumbnail = 0;
								}
							}
						
							if( $post_format === false || $post_format == 'standard' ){
								// $listing_page_minipicture = the_post_thumbnail('boxshop_blog_thumb', array('class' => 'thumbnail-blog'));

								$listing_page_thumbnail = get_field('blog_thumbnail_image', $post->ID) ?  get_field('blog_thumbnail_image', $post->ID) : 'https://studds-revamp.postyoulike.com/wp-content/uploads/2021/04/75.jpg';
								
								//  echo $listing_page_minipicture;
								// if ( has_post_thumbnail() ) {
								// 	$image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
								// 	// echo '<img src="' . esc_url( $image_url ) . '" alt="Featured Image">';
								// }
								?> 
								<img src="<?php echo $listing_page_thumbnail; ?>" alt="">
								<?php
					
							}
						?>
						</figure>
					<?php 
					if( $post_format != 'gallery'){
					?>
					</a>
					<?php }else{ ?>
					</div>
					<?php } ?>
				<?php
					
				}
				
				if( $post_format == 'video' ){
					$video_url = get_post_meta($post->ID, 'ts_video_url', true);
					if( $video_url != '' ){
						echo do_shortcode('[ts_video src="'.esc_url($video_url).'"]');
					}
				}
				
				if( $post_format == 'audio' ){
					$audio_url = get_post_meta($post->ID, 'ts_audio_url', true);
					if( strlen($audio_url) > 4 ){
						$file_format = substr($audio_url, -3, 3);
						if( in_array($file_format, array('mp3', 'ogg', 'wav')) ){
							echo do_shortcode('[audio '.$file_format.'="'.$audio_url.'"]');
						}
						else{
							echo do_shortcode('[ts_soundcloud url="'.$audio_url.'" width="100%" height="166"]');
						}
					}
				}
									
			}
			?>
		</div>
		<div class="entry-content <?php echo !$show_blog_thumbnail?'no-featured-image':'' ?>">
			<?php
			// echo "<pre>";
			// print_r($boxshop_theme_options);
			// echo "</pre>";
			// die();
			?>
			<div class="entry-info">
				<!-- Blog Title -->
				<?php if( $boxshop_theme_options['ts_blog_title'] ): ?>
					<div class="date_time">
						<span><?php echo get_the_time('d'); ?></span>
						<span><?php echo get_the_time('M'); ?></span>
						<span><?php echo get_the_time('Y'); ?></span>
					</div>
				<header>
					<h3 class="heading-title entry-title">
						<a class="post-title heading-title" href="<?php the_permalink() ; ?>"><?php the_title(); ?></a>
					</h3>
				</header>
				<?php endif; ?>
				
				<div class="entry-summary">
					<!-- Blog Excerpt -->
					<?php if( $boxshop_theme_options['ts_blog_excerpt'] || $boxshop_theme_options['ts_blog_read_more']): ?>
					<div class="short-content">
						<?php 
							$max_chars = isset($boxshop_theme_options['ts_blog_excerpt_max_words']) ? (int)$boxshop_theme_options['ts_blog_excerpt_max_words'] : 140;
							$strip_tags = !empty($boxshop_theme_options['ts_blog_excerpt_strip_tags']);
							boxshop_the_excerpt_max_chars($max_chars, $post, $strip_tags, '...', true); 

							// $max_words = (int)$boxshop_theme_options['ts_blog_excerpt_max_words']?(int)$boxshop_theme_options['ts_blog_excerpt_max_words']:150;
							// $strip_tags = $boxshop_theme_options['ts_blog_excerpt_strip_tags']?true:false;
							// boxshop_the_excerpt_max_words($max_words, $post, $strip_tags, '...', true); 
// echo "<pre>";
// print_r($max_chars);
// echo "</pre>";
// die();

						?>
						<!-- Blog Read More Button -->
						<?php if( $boxshop_theme_options['ts_blog_read_more'] ): ?>
						<a class="read-more-text" href="<?php the_permalink() ; ?>"><?php esc_html_e('Read More', 'boxshop'); ?></a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					
				</div>
			</div>
			
			<?php
			$categories_list = get_the_category_list(', ');
			if ( ($categories_list && $boxshop_theme_options['ts_blog_categories'])): 
			?>
			<div class="entry-bottom">		
				<!-- Blog Categories -->
				<?php 
				if ($categories_list && $boxshop_theme_options['ts_blog_categories']): ?>
				<div class="cats-link">
					<span><?php esc_html_e('Categories: ', 'boxshop'); ?></span>
					<span class="cat-links"><?php echo trim($categories_list); ?></span>
				</div>
				<?php endif; ?>
			</div>
			
			<?php endif; ?>
			
		</div>
		
	<?php else: /* Post format is quote */ ?>
		
		<blockquote>
			<?php 
			$quote_content = get_the_excerpt();
			if( !$quote_content ){
				$quote_content = get_the_content();
			}
			echo do_shortcode($quote_content);
			?>
		</blockquote>
		
		<div class="entry-meta">
			<!-- Blog Date Time -->
			<?php if( $boxshop_theme_options['ts_blog_date'] ) : ?>
			<div class="date-time">
				<i class="pe-7s-date"></i>
				<?php echo get_the_time( get_option('date_format') ); ?>
			</div>
			<?php endif; ?>
			
			<!-- Blog Author -->
			<?php if( $boxshop_theme_options['ts_blog_author'] ): ?>
			<span class="vcard author"><?php esc_html_e('Post by ', 'boxshop'); ?><?php the_author_posts_link(); ?></span>
			<?php endif; ?>
		</div>
		
	<?php endif; ?>
	
</article>