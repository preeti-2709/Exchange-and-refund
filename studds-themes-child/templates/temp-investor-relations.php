<?php /* Template Name: Investor Relations Page */ 
get_header(); 
?>

<?php 
    global $boxshop_theme_options;
    boxshop_breadcrumbs_title(true, 'Investor Relations'); 
?>

<div class="investor-relations-wrapper" style="display: flex; gap: 30px; padding: 40px; color: #fff; font-family: sans-serif;">
    
    <!-- Sidebar -->
    <aside class="investor-relations-sidebar" style="width: 250px;">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php
            $args = array(
                'post_type' => 'investor-relation',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            );
            $sidebar_query = new WP_Query($args);
            if ($sidebar_query->have_posts()) :
                while ($sidebar_query->have_posts()) : $sidebar_query->the_post();
                    $active = (get_the_ID() === get_queried_object_id()) ? 'style="color: #ff3366;"' : '';
                    echo '<li style="margin-bottom: 15px;"><a href="' . get_permalink() . '" ' . $active . '>' . get_the_title() . '</a></li>';
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="investor-relations-content" style="flex-grow: 1;">
        <h1 style="border-bottom: 2px solid #00bcd4; padding-bottom: 10px;"><?php the_title(); ?></h1>
        <div class="content-body" style="margin-top: 20px;">
            <?php
            while (have_posts()) : the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </main>

</div>


<?php get_footer(); ?>
