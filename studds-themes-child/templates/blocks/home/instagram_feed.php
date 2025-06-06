<?php
/*
** Instagram Feed Section
*/

$add_shortcode = get_sub_field('add_shortcode'); 
?>
    <section class="instagram-feed-section container">
        <div class="instagram_title">
            <img src="http://studds-revamp.postyoulike.com/wp-content/uploads/2025/04/instagram_img.svg" alt="">
            <h2>STUDDSHELMETS</h2>
        </div>
        <div class="shortcode">
            <?php echo do_shortcode($add_shortcode); ?>
        </div>
    </section>
