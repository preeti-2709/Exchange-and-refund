<?php /* Template Name: Our Company Page */ 
get_header(); 
?>

<?php 
    global $boxshop_theme_options;
    boxshop_breadcrumbs_title(true, 'Our Company'); 

    /* All the fields */
    $first_section_title = get_field('first_section_title');  
    $first_section_image = get_field('first_section_image');  
    $first_section_description = get_field('first_section_description');  
    
    $second_section_title = get_field('second_section_title');  
    $second_section_description = get_field('second_section_description');  
    $second_section_image = get_field('second_section_image');  
    
    $slider_section_main_title = get_field('slider_section_main_title');  

    $video_title = get_field('video_title');  
    $select_video = get_field('select_video');  
    //slider = slider_section
    /* All the fields */ 
?>

<div class="our-company-page">
  <div class="container py-5">

      <?php if ( $first_section_title || $first_section_description || $first_section_image ): ?>
      <div class="row content-box">
          <?php if ($first_section_image): ?>
          <div class="col-md-6 mt-3 img-box">
              <img src="<?php echo esc_url($first_section_image); ?>" alt="Factory Image">
          </div>
          <?php endif; ?>

          <div class="col-md-6 mt-3">
          <?php if ($first_section_title): ?>
              <h2 class="section-title"><?php echo esc_html($first_section_title); ?></h2>
          <?php endif; ?>
          <?php echo $first_section_description; ?>
          </div>
      </div>
      <?php endif; ?>


      <?php if ( $second_section_title || $second_section_description || $second_section_image ): ?>
      <div class="row content-box">
          <div class="col-md-6">
          <?php if ($second_section_title): ?>
              <h3 class="sub-section-title"><?php echo esc_html($second_section_title); ?></h3>
          <?php endif; ?>
          <?php echo $second_section_description; ?>
          </div>
          <?php if ($second_section_image): ?>
          <div class="col-md-6 img-box">
              <img src="<?php echo esc_url($second_section_image); ?>" alt="Executives Image">
          </div>
          <?php endif; ?>
      </div>
      <?php endif; ?>


      <?php if( have_rows('slider_section') ): ?>
      <div class="container content-box mt-5">
          <?php if ($slider_section_main_title): ?>
          <h2 class="section-title text-center mb-4"><?php echo esc_html($slider_section_main_title); ?></h2>
          <?php endif; ?>

          <!-- Swiper -->
          <div class="swiper main_custom_slider">
          <div class="swiper-wrapper">

              <?php while( have_rows('slider_section') ): the_row(); 
              $img = get_sub_field('slider_image');
              $title = get_sub_field('slider_title');
              $desc = get_sub_field('slider_description');
              ?>
              <div class="swiper-slide">
              <div class="row align-items-center">
                  <?php if ($img): ?>
                  <div class="col-md-6">
                      <img src="<?php echo esc_url($img); ?>" class="img-fluid rounded" alt="Slide Image">
                  </div>
                  <?php endif; ?>
                  <div class="col-md-6 text-white">
                  <?php if ($title): ?>
                      <h4 class="text-uppercase text-info"><?php echo esc_html($title); ?></h4>
                  <?php endif; ?>
                  <?php echo $desc; ?>
                  </div>
              </div>
              </div>
              <?php endwhile; ?>

          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-pagination"></div>
          </div>
      </div>
      <?php endif; ?>


      <?php if ($video_title || $select_video): ?>
      <div class="row content-box">
          <div class="col-md-12">
          <?php if ($video_title): ?>
              <h3 class="sub-section-title"><?php echo esc_html($video_title); ?></h3>
          <?php endif; ?>
          </div>

          <?php if ($select_video): 
          // Get the file extension
          $ext = pathinfo($select_video, PATHINFO_EXTENSION);
          $mime_type = '';

          // Basic MIME type mapping
          if ($ext === 'mp4') $mime_type = 'video/mp4';
          elseif ($ext === 'webm') $mime_type = 'video/webm';
          elseif ($ext === 'ogg' || $ext === 'ogv') $mime_type = 'video/ogg';
          ?>
          <div class="col-md-6">
              <video class="w-100 rounded" controls autoplay muted loop>
              <source src="<?php echo esc_url($select_video); ?>" type="<?php echo esc_attr($mime_type); ?>">
              </video>
          </div>
          <?php endif; ?>
      </div>
      <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>

