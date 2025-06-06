<?php
/**
 * Template Name: Shopping, Shipping and Delivery
 */
get_header();

$bg_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="breadcrumbs">
    <?php boxshop_breadcrumbs_title(true, 'Stay Protected'); ?>
</div>

<?php if (have_rows('shipping_policy')): ?>
  <section class="shipping-policy py-5">
    <div class="container">
      <div class="row head-title">
        <div class="col-md-12 justify-content-center"><h1><?php echo get_the_title(); ?></h1></div>
        <div class="col-md-12 mb-4 justify-content-center"><h4><?php echo get_the_content(); ?></h4></div>
      </div>
        <?php $count = 0; ?>
        <div class="body-data background-ligh-grey">
      <?php while (have_rows('shipping_policy')): the_row();
            $title = get_sub_field('title');
            $description = get_sub_field('description');
        ?>
        <div class="row">
          <div class="col-md-12">
        <h3><?php
            if($count > 0){
                echo esc_html($count.". ".$title);
            }else{
                echo ". ".esc_html($title);
            }
             ?></h3>
          </div>
        </div>
        <?php if ($description != ''): ?>
          <div class="row">
                <div class="col-md-12 mb-4 bottom-border">
                  <div class="description"><?php echo $description; ?></div>
                </div>
          </div>
        <?php endif; ?>
      <?php $count++; ?>
      <?php endwhile; ?>
        </div>
    </div>
  </section>
  <?php endif; ?>

<?php get_footer(); ?>
