<?php
/**
 * Template Name: Quote Archive
 *
 * This template displays the archive page for the "Quotes" custom post type.
 */

get_header(); ?>

<?php 
$categories = get_terms( array(
	'taxonomy' => 'quotes_category',
	'hide_empty' => false
) );
?>
<ul class="cat-list">
  <li><a class="cat-list_item active" href="#!" data-slug="">All quotes</a></li>

  <?php foreach($categories as $category) : ?>
    <li>
      <a class="cat-list_item" href="#!" data-slug="<?= $category->slug; ?>" data-type="quotes">
        <?= $category->name; ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<!-- Querying over WordPress Posts and Custom Post Types -->
<?php 
  $projects = new WP_Query([
    'post_type' => 'quotes',
    'posts_per_page' => -1,
    'order_by' => 'date',
    'order' => 'desc',
  ]);
?>

<?php if($projects->have_posts()): ?>
  <ul class="project-tiles">
    <?php
      while($projects->have_posts()) : $projects->the_post();
        include('_components/quote-list-item.php');
      endwhile;
    ?>
  </ul>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>
