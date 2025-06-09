<?php 
global $boxshop_theme_options, $post;

get_header();

$post_format = get_post_format(); /* Video, Audio, Gallery, Quote */

$show_blog_thumbnail = $boxshop_theme_options['ts_blog_details_thumbnail'];

$extra_class = "";

$page_column_class = boxshop_page_layout_columns_class($boxshop_theme_options['ts_blog_details_layout']);

// boxshop_breadcrumbs_title(true, $boxshop_theme_options['ts_blog_details_title'], get_the_title());
if(isset($boxshop_theme_options['ts_breadcrumb_layout']) ){
	$extra_class = 'show_breadcrumb_'.$boxshop_theme_options['ts_breadcrumb_layout'];
}

if( $post_format == 'gallery' ){
	$gallery = get_post_meta($post->ID, 'ts_gallery', true);
	$gallery_ids = explode(',', $gallery);
	if( is_array($gallery_ids) && has_post_thumbnail() ){
		array_unshift($gallery_ids, get_post_thumbnail_id());
	}
	
	if( !has_post_thumbnail() && empty($gallery) ){
		$show_blog_thumbnail = 0;
	}
}

if( ($post_format === false || $post_format == 'standard') && !is_singular('ts_feature') ){
	if( !has_post_thumbnail() ){
		$show_blog_thumbnail = 0;
	}
}
$page_title = get_the_title();
?>
<div id="content" class="container blog_dls_sec child-theme container-post <?php echo esc_attr($extra_class) ?>">
	<!-- Left Sidebar -->
	<?php if( $page_column_class['left_sidebar'] ): ?>
		<aside id="left-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['left_sidebar_class']); ?>">
		<?php if( is_active_sidebar($boxshop_theme_options['ts_blog_details_left_sidebar']) ): ?>
			<?php dynamic_sidebar( $boxshop_theme_options['ts_blog_details_left_sidebar'] ); ?>
		<?php endif; ?>
		</aside>
	<?php endif; ?>	
	<!-- end left sidebar -->
	
	<!-- main-content -->
	<div id="main-content" class="left_blog_dls <?php echo esc_attr($page_column_class['main_class']); ?>">

	<!-- Breadcrumbs -->
	 <?php custom_breadcrumbs();?>
	<!-- Breadcrumbs -->

		<article class="single single-post blog-detail-page">
			<!-- Blog Thumbnail -->
			<?php if( $show_blog_thumbnail ): ?>
				<div class="entry-format">
					<?php if( $post_format == 'gallery' || $post_format === false || $post_format == 'standard' ){ ?>
						<figure class="<?php echo ('gallery' == $post_format)?'gallery loading':'' ?>">
							<?php 
							
							if( $post_format == 'gallery' ){
								foreach( $gallery_ids as $gallery_id ){
									echo wp_get_attachment_image( $gallery_id, 'boxshop_blog_thumb', 0, array('class' => 'thumbnail-blog') );
								}
							}
						
							if( ($post_format === false || $post_format == 'standard') && !is_singular('ts_feature') ){
								the_post_thumbnail('boxshop_blog_thumb', array('class' => 'thumbnail-blog'));
							}
							
							?>
						</figure>
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
								echo do_shortcode('[audio '.$file_format.'="'.esc_url($audio_url).'"]');
							}
							else{
								echo do_shortcode('[ts_soundcloud url="'.esc_url($audio_url).'" width="100%" height="166"]');
							}
						}
					}

					?>
				</div>
			<?php endif; ?>

			<div class="post-meta">
				<span class="author">
					<img src="<?php echo get_stylesheet_directory_uri().'/assets/img/blog_list_user.png'; ?>" alt="Author Image">
					<?php echo get_the_author(); ?>
				</span> 
					<span class="date">
					<img src="<?php echo get_stylesheet_directory_uri().'/assets/img/blog_list_calendar.png'; ?>" alt="Date">
					<?php echo get_the_time('d/m/Y'); ?>
				</span>
			</div>

			<h1 class="heading-title page-title entry-title"><?php echo $page_title; ?></h1>
			<div class="entry-content <?php echo !$show_blog_thumbnail?'no-thumbnail':'' ?>">
				<div class="entry-meta <?php echo esc_attr($boxshop_theme_options['ts_blog_details_date']?'has-datetime':''); ?> <?php echo esc_attr($boxshop_theme_options['ts_blog_details_author']?'has-author':''); ?>">
					<!-- Blog Date Time -->
					<?php if( $boxshop_theme_options['ts_blog_details_date'] ) : ?>
					<div class="date-time">
						<span><?php echo get_the_time('d'); ?></span>
						<span><?php echo get_the_time('M'); ?></span>
					</div>
					<?php endif; ?>
					
					<!-- Blog Comment -->
					<?php if( $boxshop_theme_options['ts_blog_details_comment'] ): ?>
						<span class="comment-count">
							<i class="pe-7s-chat"></i>
							<span class="number">
								<?php boxshop_post_comment_count(); ?>
							</span>
						</span>
					<?php endif; ?>
					
					<!-- Blog view -->
					<?php if( $boxshop_theme_options['ts_blog_details_view'] && function_exists('ts_post_view_count') ): ?>
					<span class="view-count">
						<i class="pe-7s-look"></i>
						<span class="number">
							<?php ts_post_view_count(); ?>
						</span>
					</span>
					<?php endif; ?>
					
					<!-- Blog Author -->
					<?php if( $boxshop_theme_options['ts_blog_details_author'] ): ?>
					<span class="vcard author"><?php esc_html_e('Post by ', 'boxshop'); ?><?php the_author_posts_link(); ?></span>
					<?php endif; ?>
				
				</div>
				
				<!-- Blog Content -->
				<?php if( $boxshop_theme_options['ts_blog_details_content'] ): ?>
				<div class="content-wrapper">
					<div class="full-content"><?php the_content(); ?></div>
					<?php wp_link_pages(); ?>
				</div>
				<?php endif; ?>
			
				<div class="meta-bottom-wrapper">
					<?php if( $boxshop_theme_options['ts_blog_details_categories'] != 0 || $boxshop_theme_options['ts_blog_details_sharing'] != 0 ): ?>
					<div class="meta-bottom-1 <?php echo esc_attr($boxshop_theme_options['ts_blog_details_categories']?'has-categories':''); ?> <?php echo esc_attr($boxshop_theme_options['ts_blog_details_sharing']?'has-social':''); ?>">
						<!-- Blog Categories -->
						<?php
						$categories_list = get_the_category_list(', ');
						if ( $categories_list && $boxshop_theme_options['ts_blog_details_categories'] ):
						?>
						<div class="cats-link">
							<span class="cat-title"><?php esc_html_e('Categories: ', 'boxshop'); ?></span>
							<span class="cat-links"><?php echo trim($categories_list); ?></span>
						</div>
						<?php endif; ?>
						
						<!-- Blog Sharing -->
						<?php if( $boxshop_theme_options['ts_blog_details_sharing'] && function_exists('ts_template_social_sharing') ): ?>
						<div class="social-sharing">
							<?php ts_template_social_sharing(); ?>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					
					<?php 
					$tags_list = get_the_tag_list('', ' '); 
					if ( $tags_list && $boxshop_theme_options['ts_blog_details_tags'] ):
					?>
					<div class="meta-bottom-2">
						<!-- Blog Tags -->
						<div class="tags-link">
							<span class="tag-links">
								<?php echo trim($tags_list); ?>
							</span>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<!-- Blog Author -->
			<?php if( $boxshop_theme_options['ts_blog_details_author_box'] && get_the_author_meta('description') ) : ?>
			<div class="entry-author">
				<div class="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), 100, 'mystery' ); ?>
				</div>	
				<div class="author-info">		
					<span class="author"><?php the_author_posts_link();?></span>
					<span class="role"><?php echo boxshop_get_user_role( get_the_author_meta('ID') ); ?></span>
					<p><?php the_author_meta( 'description' ); ?></p>
				</div>
			</div>
			<?php endif; ?>	

			<!-- Blog Post Tags -->
			<?php 
				$tags = get_the_tags();
				if ( $tags ) {
					echo '<ul class="for-particular-post post-tags">';
					foreach ( $tags as $tag ) {
						echo '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></li>';
					}
					echo '</ul>';
				}
			?>
			<!-- Blog Post Tags End -->
			
			<!-- Next Prev Blog -->
			<div class="single-navigation">
				<?php
					$prev_post = get_next_post();
					$next_post = get_previous_post();

					if ($prev_post) {
						echo '<a href="' . get_permalink($prev_post) . '" class="prev-post">
						<span class="arrow-left">
						<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M15 18.5L9 12.5L15 6.5" stroke="#010101" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg></span><div class="prev-next-link"><p>' . esc_html__('Prev post', 'boxshop') . '</p>  <p>' . esc_html($prev_post->post_title) . '</p></div></a>';
					}

					if ($next_post) {
						echo '<a href="' . get_permalink($next_post) . '" class="next-post"><div class="prev-next-link"><p>' . esc_html__('Next post', 'boxshop') . '</p> <p>' . esc_html($next_post->post_title) . '</p></div>
						<span class="arrow-right">
									<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9 6.5L15 12.5L9 18.5" stroke="#010101" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span></a>';
					}
					
				?>
			</div>
			
			<!-- Related Posts-->
			<?php 
			if( !is_singular('ts_feature') && $boxshop_theme_options['ts_blog_details_related_posts'] ){
				get_template_part('templates/related-posts');
			}
			?>
			
			<!-- Comment Form -->
			<?php 
			if( $boxshop_theme_options['ts_blog_details_comment_form'] && ( comments_open() || get_comments_number() ) ){
				comments_template( '', true );
			}
			?>
		</article>
	</div><!-- end main-content -->
	
	<!-- Right Sidebar -->
	<?php if( $page_column_class['right_sidebar'] ): ?>
		<aside id="right-sidebar" class="ts-sidebar ts_rightbar <?php echo esc_attr($page_column_class['right_sidebar_class']); ?>">
			<div class="widget-container widget_search">
				<form method="get" action="<?php echo esc_url( home_url( '/blog/' ) ); ?>" id="blog-search-form">
					<div class="search-table">
						<div class="search-field search-content">
							<input type="text" name="search" id="search" placeholder="search Blogs" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>" autocomplete="off">
							<input type="hidden" name="post_type" value="post">
						</div>
						<div class="search-button">
							<input type="submit" id="searchsubmit" value="">
						</div>
					</div>
				</form>
			</div>
		<?php if( is_active_sidebar($boxshop_theme_options['ts_blog_details_right_sidebar']) ): ?>
			<?php dynamic_sidebar( $boxshop_theme_options['ts_blog_details_right_sidebar'] ); ?>
		<?php endif; ?>
		</aside>
	<?php endif; ?>	
	<!-- end right sidebar -->	
</div>
<?php get_footer(); ?>