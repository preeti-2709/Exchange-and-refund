<?php
/**
 * Template Name: Helmet Sizing Chart
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
    <?php boxshop_breadcrumbs_title(true, 'Helmet Sizing Chart'); ?>
</div>

<div class="helmet-sizing-chart-content">
    <?php echo get_the_content(); ?>
</div>

<div class="helmet-sizing-chart-page">
    <div class="container py-5">
        <?php if( have_rows('helmet_steps') ): ?>
            <?php $step = 1; ?>
            <?php while( have_rows('helmet_steps') ): the_row(); ?>
                <div class="mb-4">
                    <div class="bg-danger text-white p-2 font-weight-bold">STEP-<?php echo $step; ?></div>

                    <?php 
                        $description = get_sub_field('steps_description'); 
                        if ($description): 
                    ?>
                        <div class="border p-3 mb-3"><?php echo $description; ?></div>
                    <?php endif; ?>

                    <?php if ( have_rows('step_sizes') ): ?>
                        <div class="border p-3">
                            <table class="table table-bordered text-center">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>HELMET SIZE</th>
                                        <th>INDIA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ( have_rows('step_sizes') ): the_row(); 
                                        $size = get_sub_field('helmet_size');
                                        $range = get_sub_field('size_range');
                                        if( $size && $range ):
                                    ?>
                                        <tr>
                                            <td><?php echo esc_html($size); ?></td>
                                            <td><?php echo esc_html($range); ?></td>
                                        </tr>
                                    <?php endif; endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
                <?php $step++; ?>
            <?php endwhile; ?>
        <?php endif; ?>

        <?php 
            $video_title = get_field('video_title');    
            $video = get_field('helmet_video');  
            if($video): 
        ?>
            <div class="mb-5">
                <div class="border p-3">
                    <h3 class="text-center mb-3"><?php echo $video_title;  ?></h3>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe src="<?php echo esc_url($video); ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
