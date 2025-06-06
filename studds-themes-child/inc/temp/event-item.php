<?php
/*
** Event page template
*/ 
?>

<div class="event-item">
    <div class="event-image">
        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium'); ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="event-details">
        <?php if ($date_obj): ?>
            <div class="event-details-left">
                <p class="text-orange"><?php echo esc_html($date_obj->format('M')); ?></p>
                <p class="text-black"><?php echo esc_html($date_obj->format('d')); ?></p>
            </div>
        <?php endif; ?>
        <div class="event-details-right">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p><?php the_excerpt(); ?></p>
        </div>
    </div>
</div>