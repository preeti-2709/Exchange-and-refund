<?php
/**
 * Template Name: Gloves Sizing Chart
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
    <?php boxshop_breadcrumbs_title(true, 'Gloves Sizing Chart'); ?>
</div>

<div class="gloves-sizing-chart-content">
    <?php echo get_the_content(); ?>
</div>

<div class="gloves-sizing-chart-page">
    <?php if (have_rows('full_glove_sizing')): ?>
        <div class="container my-5">
            <?php while (have_rows('full_glove_sizing')): the_row(); 
            $title = get_sub_field('title'); 
            ?>
            <?php if (!empty($title)): ?>
                <h4 class="text-center fw-bold text-uppercase mb-4"><?php echo esc_html($title); ?></h4>
            <?php endif; ?>

            <?php if (have_rows('gloves_sizes')): ?>
                <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-danger">
                    <tr>
                        <th scope="col">Measurement</th>
                        <th scope="col">S</th>
                        <th scope="col">M</th>
                        <th scope="col">L</th>
                        <th scope="col">XL</th>
                        <th scope="col">XXL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while (have_rows('gloves_sizes')): the_row(); 
                        $measurement = get_sub_field('measurement_title');
                        $s = get_sub_field('size_s');
                        $m = get_sub_field('size_m');
                        $l = get_sub_field('size_l');
                        $xl = get_sub_field('size_xl');
                        $xxl = get_sub_field('size_xxl');

                        // Only show row if any value exists
                        if ($measurement || $s || $m || $l || $xl || $xxl): ?>
                        <tr>
                            <td class="fw-semibold text-start"><?php echo esc_html($measurement); ?></td>
                            <td><?php echo $s !== '' ? esc_html($s) : '-'; ?></td>
                            <td><?php echo $m !== '' ? esc_html($m) : '-'; ?></td>
                            <td><?php echo $l !== '' ? esc_html($l) : '-'; ?></td>
                            <td><?php echo $xl !== '' ? esc_html($xl) : '-'; ?></td>
                            <td><?php echo $xxl !== '' ? esc_html($xxl) : '-'; ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
