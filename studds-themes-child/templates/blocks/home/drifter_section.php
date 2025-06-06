<?php
/*
** Drifter Section
*/
$drifter_full_image = get_sub_field('drifter_full_image');
if (!empty($drifter_full_image)) :
?>
<section class="drifter-section" id="drifter-section">
    <img src="<?php echo esc_url($drifter_full_image); ?>" alt="Drifter Batman Edition Banner" class="desktop_view">
    <img src="<?php echo get_stylesheet_directory_uri().'/assets/img/drifter_edition_mobile.png'; ?>" alt="Drifter Batman Edition Banner" class="mobile_view">
</section>
<?php endif; ?>
