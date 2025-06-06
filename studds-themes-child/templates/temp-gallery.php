<?php
/**
 * Template Name: Gallery Page
 */
get_header();

  $active_tab = isset($_GET['vid-page']) ? 'video' : 'image';
  $paged = isset($_GET['vid-page']) ? (int) $_GET['vid-page'] : (isset($_GET['img-page']) ? (int) $_GET['img-page'] : 1);
  $posts_per_page = 6;

  $gallery_image = get_field('gallery_image');
  $gallery_video = get_field('gallery_video');

  $args = [
    'post_type' => 'gallery',
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
  ];

  if ($active_tab === 'image') {
    $args['meta_query'] = [
      [
        'key' => '_thumbnail_id',
        'compare' => 'EXISTS',
      ],
    ];
  } else {
      $args['meta_query'] = [
          'relation' => 'OR',
          [
              'key' => 'single_gallery_video',
              'value' => '',
              'compare' => '!=',
          ],
          [
              'key' => 'youtube_video_link',
              'value' => '',
              'compare' => '!=',
          ],
      ];

  }

  $query = new WP_Query($args);
  $bg_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
  ?>

      <div class="category-banner event_section" style="background: url('<?php echo esc_url($bg_image_url); ?>');">
          <div class="category-overlay">
              <h1 class="container category-title"><?php echo get_the_title(); ?></h1>
          </div>
      </div>

      <div class="breadcrumbs">
          <?php boxshop_breadcrumbs_title(true, 'Gallery'); ?>
      </div>

  <div class="container py-5">
    <!-- Nav Tabs -->
    <div class="tabs_faq gallery-page">
      <ul class="nav nav-tabs" id="galleryTab" role="tablist">
        <li class="nav-item">
          <button class="nav-link <?php echo ($active_tab === 'image') ? 'active' : ''; ?>" data-tab="image">
            <?php echo (!empty($gallery_image)) ? $gallery_image : 'Images'; ?>
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link <?php echo ($active_tab === 'video') ? 'active' : ''; ?>" data-tab="video">
            <?php echo (!empty($gallery_video)) ? $gallery_video : 'Videos'; ?>
          </button>
        </li>
      </ul>
    </div>


    <!-- Content Grid -->
    <div class="gallery_temp_wrap">
      <div class="row mt-4" id="gallery-container">
        <?php if ($query->have_posts()) :
          while ($query->have_posts()) : $query->the_post(); ?>
            <div class="col-md-4 col-6 mb-4">
              <div class="card h-100">
                <?php if ($active_tab === 'image' && has_post_thumbnail()) : ?>
                  <?php
                  $img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                  ?>
                  <a href="<?php echo esc_url($img_url); ?>" data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>">
                    <?php the_post_thumbnail('full', ['class' => 'card-img-top']); ?>
                  </a>
                  <?php elseif ($active_tab === 'video') :
                      $youtube_link = get_field('youtube_video_link');
                      $video_file = get_field('single_gallery_video');

                      if ($youtube_link) :
                        // Extract YouTube ID
                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/', $youtube_link, $matches);
                        $youtube_id = isset($matches[1]) ? $matches[1] : '';
                        $youtube_thumbnail = "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg";
                        ?>
                        
                        <a data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>" data-type="iframe" href="https://www.youtube.com/embed/<?php echo esc_attr($youtube_id); ?>?autoplay=1">
                          <div class="ratio ratio-16x9">
                            <img src="<?php echo esc_url($youtube_thumbnail); ?>" class="card-img-top" alt="YouTube Video">
                          </div>
                        </a>

                      <?php elseif ($video_file) : 
                        $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: get_template_directory_uri() . '/assets/img/video-placeholder.jpg';
                        ?>
                        
                        <a data-fancybox="gallery" data-caption="<?php the_title_attribute(); ?>" href="<?php echo esc_url($video_file); ?>">
                          <div class="ratio ratio-16x9">
                            <img src="<?php echo esc_url($thumbnail); ?>" class="card-img-top" alt="Video File">
                          </div>
                        </a>
                      <?php endif; ?>
                    <?php endif; ?>


                <div class="card-body">
                  <h5 class="card-title"><?php the_title(); ?></h5>
                </div>
              </div>
            </div>
          <?php endwhile;
        else :
          echo '<p>No content found.</p>';
        endif;
        ?>
      </div>
    </div> 

    <!-- Pagination -->
    <?php if ($query->max_num_pages > 1): ?>
      <div class="text-center mt-4">
        <button id="load-more-gallery-page" class="btn btn-primary" 
                data-page="1" 
                data-tab="<?php echo esc_attr($active_tab); ?>">
          Load More
        </button>
      </div>
    <?php endif; ?>

  </div>

<?php get_footer(); ?>
