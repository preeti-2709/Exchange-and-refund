<?php
/*
** Find Studds Store Near You
*/

$title = get_sub_field('title'); 
$sub_title = get_sub_field('sub_title'); 
$cta_button = get_sub_field('cta_button'); 
$cta_button_text = !empty($cta_button['title']) ? $cta_button['title'] : '';  
$cta_button_link = !empty($cta_button['url']) ? $cta_button['url'] : '';  
$right_image = get_sub_field('right_image'); 
?>
<section class="near_strore_sec">
    <div class="container_store">
        <div class="row">
            <div class="col-md-5">
                <div class="near_strore_left">
                    <?php if (!empty($title)): ?>
                        <h2><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($sub_title)): ?>
                        <p><?php echo esc_html($sub_title); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($cta_button_text) && !empty($cta_button_link)): ?>
                        <a href="<?php echo esc_url($cta_button_link); ?>">                            
                            <svg class="desktop_view" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20.0176 5.84981C18.5401 2.75231 15.4951 0.794809 12.0676 0.749809C8.63255 0.704809 5.58755 2.59481 4.05755 5.69981C2.46755 8.91731 2.84255 12.6448 5.04755 15.4423L10.7401 22.6723C11.0326 23.0398 11.4676 23.2498 11.9326 23.2498C12.3976 23.2498 12.8326 23.0398 13.1251 22.6723L18.9676 15.2473C21.0901 12.5473 21.4876 8.95481 20.0176 5.85731V5.84981ZM17.7976 14.3173L11.9251 21.7423L6.23255 14.5123C4.39505 12.1798 4.08005 9.05231 5.40755 6.36731C6.68255 3.78731 9.12005 2.25731 11.9401 2.25731H12.0526C14.9476 2.30231 17.4226 3.88481 18.6676 6.50231C19.9126 9.11981 19.5901 12.0373 17.7976 14.3173Z" fill="white"/>
                                <path d="M11.9324 5.37012C9.6749 5.37012 7.8374 7.20762 7.8374 9.46512C7.8374 11.7226 9.6749 13.5601 11.9324 13.5601C14.1899 13.5601 16.0274 11.7226 16.0274 9.46512C16.0274 7.20762 14.1899 5.37012 11.9324 5.37012ZM11.9324 12.0526C10.4999 12.0526 9.3374 10.8901 9.3374 9.45762C9.3374 8.02512 10.4999 6.86262 11.9324 6.86262C13.3649 6.86262 14.5274 8.02512 14.5274 9.45762C14.5274 10.8901 13.3649 12.0526 11.9324 12.0526Z" fill="white"/>
                            </svg>
                            <?php echo esc_html($cta_button_text); ?>                            
                            <svg class="mobile_view" width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.7071 7.29289C16.0976 7.68342 16.0976 8.31658 15.7071 8.70711L9.34315 15.0711C8.95262 15.4616 8.31946 15.4616 7.92893 15.0711C7.53841 14.6805 7.53841 14.0474 7.92893 13.6569L13.5858 8L7.92893 2.34315C7.53841 1.95262 7.53841 1.31946 7.92893 0.928932C8.31946 0.538408 8.95262 0.538408 9.34315 0.928932L15.7071 7.29289ZM0 8L0 7L15 7V8V9L0 9L0 8Z" fill="white"/>
                            </svg>

                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-7">
                <div class="near_strore_right">
                    <?php if (!empty($right_image)): ?>
                        <img src="<?php echo esc_url($right_image); ?>" alt="Store Image">
                    <?php else: ?>
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/near_store.jpg'); ?>" alt="Default Image">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
