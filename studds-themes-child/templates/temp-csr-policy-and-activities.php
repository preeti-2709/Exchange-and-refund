<?php /* Template Name: CSR Policy & Activities Page */ 
get_header(); 
?>

<?php 
    global $boxshop_theme_options;
    boxshop_breadcrumbs_title(true, 'CSR Policy & Activities'); 
?>
<style>
.activity-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-top: 30px;
}

.activity-card {
  background: #fff;
  border: 1px solid #ddd;
  padding: 15px;
  text-align: center;
}

.activity-image img {
  max-width: 100%;
  height: auto;
}

</style>
<div class="csr-policy-template">
  <div class="container">

    <!-- Template Title & Description -->
    <div class="template-title-description">
      <?php if (!empty(get_the_title())): ?>
        <h2><?php echo esc_html(get_the_title()); ?></h2>
      <?php endif; ?>

      <?php if (!empty(get_the_content())): ?>
        <div class="description">
          <?php echo wp_kses_post(get_the_content()); ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- CSR Committee Section (ACF) -->
    <div class="csr-committee-section">
      <?php 
      $committee_title = get_field('committee_title');
      if (!empty($committee_title)): ?>
        <h3><?php echo esc_html($committee_title); ?></h3>
      <?php endif; ?>

      <?php if (have_rows('committee_members')): ?>
        <div class="csr-committee-table">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Director Identification Number</th>
                <th>Designation</th>
              </tr>
            </thead>
            <tbody>
              <?php while (have_rows('committee_members')): the_row(); 
                $name = get_sub_field('name');
                $director_id = get_sub_field('director_id');
                $designation = get_sub_field('designation');
              ?>
                <tr>
                  <td><?php echo esc_html($name); ?></td>
                  <td><?php echo esc_html($director_id); ?></td>
                  <td><?php echo esc_html($designation); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- ACF Buttons -->
      <div class="csr-buttons">
        <?php 
        $button1 = get_field('button_1');
        $button2 = get_field('button_2');

        if (!empty($button1)):
          $url = esc_url($button1['url']);
          $title = esc_html($button1['title']);
          $target = esc_attr($button1['target'] ?: '_self');
        ?>
          <a class="btn" href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo $title; ?></a>
        <?php endif; ?>

        <?php if (!empty($button2)):
          $url = esc_url($button2['url']);
          $title = esc_html($button2['title']);
          $target = esc_attr($button2['target'] ?: '_self');
        ?>
          <a class="btn" href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo $title; ?></a>
        <?php endif; ?>
      </div>
    </div>

    <!-- CSR Activities (Custom Post Type) -->
    <div class="csr-activities">
      <?php
      $args = array(
        'post_type' => 'csr-policy-activity',
        'posts_per_page' => -1,
        'post_status' => 'publish'
      );
      $activity_query = new WP_Query($args);
      if ($activity_query->have_posts()):
      ?>
        <div class="activity-grid">
        <?php while ($activity_query->have_posts()): $activity_query->the_post(); ?>
            <div class="activity-card">
            <?php if (has_post_thumbnail()): ?>
                <div class="activity-image">
                <?php the_post_thumbnail('medium'); ?>
                </div>
            <?php endif; ?>
            <h4 class="activity-title"><?php echo esc_html(get_the_title()); ?></h4>
            </div>
        <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
      <?php else: ?>
        <p>No CSR activities found.</p>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php get_footer(); ?>
