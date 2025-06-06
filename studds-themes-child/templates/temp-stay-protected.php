<?php
/**
 * Template Name: Stay Protected
 */
get_header();

$bg_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<div class="category-banner event_section" style="background: url('<?php echo esc_url($bg_image_url); ?>');">
    <div class="category-overlay">
        <h1 class="container category-title"><?php echo get_the_title(); ?></h1>
    </div>
</div>

<div class="breadcrumbs">
    <?php boxshop_breadcrumbs_title(true, 'Stay Protected'); ?>
</div>

<div class="stay-protected-content">
    <?php echo get_the_content(); ?>
</div>

<div class="stay-protected-page">
    <?php if (have_rows('stay_protected')): ?>
        <section class="stay-protected py-5">
            <div class="container">

                <?php while (have_rows('stay_protected')): the_row(); 
                    $heading_tag = get_sub_field('heading_text_size');
                    $heading = get_sub_field('heading');
                    $contents = get_sub_field('content');
                ?>

                    <?php if ($heading): ?>
                       <?php if($heading_tag == 'h2'): ?>
                            <h2><?php echo esc_html($heading); ?></h2>
                        <?php else: ?> 
                            <h3><?php echo esc_html($heading); ?></h3>
                       <?php endif; ?>
                    <?php endif; ?>

                    <?php if (have_rows('content')): ?>
                        <div class="row justify-content-center">
                            <?php while (have_rows('content')): the_row(); 
                                $type = get_sub_field('select_content_type');
                                $description = get_sub_field('description');
                                $image = get_sub_field('add_image');
                            ?>
                                <div class="col-md-4 mb-4">
                                    <div class="p-3 bg-white rounded shadow text-center h-100">
                                        <?php if ($type == 'text' && $description): ?>
                                            <div class="description"><?php echo $description; ?></div>

                                        <?php elseif ($type == 'image' && $image): ?>
                                            <img src="<?php echo esc_url($image); ?>" class="img-fluid" alt="Protective Info Image">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>

            </div>
        </section>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
