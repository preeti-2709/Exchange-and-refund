<?php
/*
** Media page template
*/ 
?>

<div class="media-item">
    <div class="media-dates">
        <span class="text-orange"><?php echo wp_date('M', strtotime($Mdate)); ?></span>
        <span class="text-black"><?php echo wp_date('d', strtotime($Mdate)); ?></span>
    </div>
    <div class="media-image">
        <?php if (has_post_thumbnail()) : ?>
            <a href="javascript:void(0);"><?php the_post_thumbnail('medium'); ?></a>
        <?php endif; ?>
    </div>
    <div class="media-details">
        <h3><a href="javascript:void(0);"><?php the_title(); ?></a></h3>
        <p><?php the_excerpt(); ?></p>
        <a href="<?php echo esc_url($moreLink); ?>" class="know_more_btn">Know MORE</a>
    </div>
</div>