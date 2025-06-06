<?php
/* Template Name: Thank You Page */
get_header();
?>

<div class="thank-you-page" style="text-align: center; padding: 50px;">
    <h1>🎉 Thank You for Your Order!</h1>
    <p>Your order has been received and is being processed.</p>

    <div style="margin-top: 30px;">
        <a href="<?php echo home_url(); ?>" class="button" style="background-color: #000; color: #fff; padding: 10px 20px; text-decoration: none;">
            Continue Shopping
        </a>
    </div>
</div>

<?php get_footer(); ?>
