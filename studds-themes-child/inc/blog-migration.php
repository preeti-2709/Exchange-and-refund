<?php

/**
 * Migrate Laravel Blog Data to WordPress
 *
 * Usage:
 * Visit the URL as an admin: http://your-site.com/wp-admin/?start_migration=true
 *
 * This function connects to an external Laravel-based MySQL database and migrates
 * blog posts from the `std_blog` table to WordPress posts. It:
 * - Skips posts that already exist based on slug
 * - Imports title, content, excerpt, post date, and tags
 * - Downloads and sets the main image as the featured image
 * - Downloads and stores the thumbnail image as an ACF field (`blog_thumbnail_image`)
 *
 * Requirements:
 * - User must be an administrator
 * - Remote image URLs must be accessible
 * - ACF plugin should be active to store custom field values
 *
 * Note: For production, remove sensitive credentials and use environment variables or wp-config.
 */

// Uncomment the following line to enable migration:
// add_action('admin_init', 'migrate_std_blog_to_wordpress');
function migrate_std_blog_to_wordpress()
{
    if (!current_user_can('administrator')) {
        return;
    }

    if (isset($_GET['start_migration']) && $_GET['start_migration'] === 'true') {

        // Laravel DB connection
        $laravel_db_host = '127.0.0.1';
        $laravel_db_name = 'u574799938_studds_blogs'; 
        $laravel_db_user = 'u574799938_studds_blogs'; 
        $laravel_db_pass = 'Mywp@123';


        // Connect to Laravel database
        $laravel_conn = new mysqli($laravel_db_host, $laravel_db_user, $laravel_db_pass, $laravel_db_name);
        if ($laravel_conn->connect_error) {
            wp_die('Connection failed: ' . $laravel_conn->connect_error);
        }


        // Fetch blogs
        $sql = "SELECT * FROM std_blog WHERE status = 1";
        $result = $laravel_conn->query($sql);


        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $post_title = $row['blog_title'];
                $post_content = $row['content'];
                $post_excerpt = $row['excerpt'];
                $post_date = !empty($row['blog_date']) ? date('Y-m-d H:i:s', strtotime($row['blog_date'])) : current_time('mysql');
                $post_status = 'publish';
                $slug = sanitize_title($row['blog_url']);
                $tags = !empty($row['tags']) ? explode(',', $row['tags']) : [];

                // Check if the post already exists
                $existing_post = get_page_by_path($slug, OBJECT, 'post');
                if ($existing_post) {
                    continue;
                }

                // Insert new WordPress post
                $post_data = [
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_excerpt'  => $post_excerpt,
                    'post_status'   => $post_status,
                    'post_date'     => $post_date,
                    'post_author'   => get_current_user_id(),
                    'post_type'     => 'post',
                    'post_name'     => $slug,
                ];

                $post_id = wp_insert_post($post_data);

                if (!is_wp_error($post_id)) {
                    // Set tags
                    if (!empty($tags)) {
                        wp_set_post_tags($post_id, $tags);
                    }

                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');

                    if (!empty($row['mainpicture'])) {
                        $image_path = ltrim($row['mainpicture'], '/');
                        $image_url = 'https://www.studds.com/Adminpanel/' . $image_path;

                        // Download file to temp location
                        $tmp = download_url($image_url);

                        if (is_wp_error($tmp)) {
                            echo "Download error: " . $tmp->get_error_message();
                            return;
                        }

                        // Get file information
                        $mime_type = mime_content_type($tmp);
                        $valid_mime_types = ['image/jpeg', 'image/png', 'image/gif'];

                        if (!in_array($mime_type, $valid_mime_types)) {
                            @unlink($tmp); // Delete temp file
                            echo "Invalid image type.";
                            return;
                        }

                        $ext = '';
                        switch ($mime_type) {
                            case 'image/jpeg':
                                $ext = '.jpg';
                                break;
                            case 'image/png':
                                $ext = '.png';
                                break;
                            case 'image/gif':
                                $ext = '.gif';
                                break;
                        }

                        $filename = sanitize_file_name(pathinfo($image_path, PATHINFO_FILENAME)) . $ext;
                        $upload_dir = wp_upload_dir();
                        $new_file_path = $upload_dir['path'] . '/' . $filename;

                        if (!rename($tmp, $new_file_path)) {
                            @unlink($tmp);
                            echo "Failed to move file.";
                            return;
                        }
                        @chmod($new_file_path, 0644);
                        $attachment = array(
                            'post_mime_type' => $mime_type,
                            'post_title'     => sanitize_text_field($post_title),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );

                        $attach_id = wp_insert_attachment($attachment, $new_file_path, $post_id);

                        if (is_wp_error($attach_id)) {
                            echo "Attachment error: " . $attach_id->get_error_message();
                            return;
                        }

                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        set_post_thumbnail($post_id, $attach_id);
                    }

                    if (!empty($row['thumbnail'])) {
                        $image_path = ltrim($row['thumbnail'], '/');
                        $image_url = 'https://www.studds.com/Adminpanel/' . $image_path;

                        $tmp = download_url($image_url);

                        if (is_wp_error($tmp)) {
                            echo "Download error: " . $tmp->get_error_message();
                            return;
                        }

                        $mime_type = mime_content_type($tmp);
                        $valid_mime_types = ['image/jpeg', 'image/png', 'image/gif'];

                        if (!in_array($mime_type, $valid_mime_types)) {
                            @unlink($tmp);
                            echo "Invalid image type.";
                            return;
                        }

                        $ext = '';
                        switch ($mime_type) {
                            case 'image/jpeg':
                                $ext = '.jpg';
                                break;
                            case 'image/png':
                                $ext = '.png';
                                break;
                            case 'image/gif':
                                $ext = '.gif';
                                break;
                        }

                        $filename = sanitize_file_name(pathinfo($image_path, PATHINFO_FILENAME)) . $ext;
                        $upload_dir = wp_upload_dir();
                        $new_file_path = $upload_dir['path'] . '/' . $filename;

                        if (!rename($tmp, $new_file_path)) {
                            @unlink($tmp);
                            echo "Failed to move file.";
                            return;
                        }

                        @chmod($new_file_path, 0644);
                        $attachment = array(
                            'post_mime_type' => $mime_type,
                            'post_title'     => sanitize_text_field($post_title),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );

                        $attach_id = wp_insert_attachment($attachment, $new_file_path, $post_id);

                        if (is_wp_error($attach_id)) {
                            echo "Attachment error: " . $attach_id->get_error_message();
                            return;
                        }

                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        // Save to ACF field
                        update_field('blog_thumbnail_image', $attach_id, $post_id);
                    }
                } else {
                    echo "Error creating post: " . $post_id->get_error_message() . "<br>";
                }
            }
        } else {
            echo "No blogs found in std_blog table.";
        }

        $laravel_conn->close();
    }
}




