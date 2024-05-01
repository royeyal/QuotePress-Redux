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
?>

<div class="quotes-archive-container">
                <?php
                // Start the Loop.
                while ( have_posts() ) :
                    the_post();
                ?>
                    <div class="quote-archive-item">
                        <?php
                        if ( has_post_thumbnail() ) {
                            // Display the featured image as a square with a class for styling.
                            the_post_thumbnail( 'thumbnail', array( 'class' => 'quote-archive-thumbnail' ) );
                        } else {
                            // Display a blank square image if no featured image is set.
                            echo '<img src="' . esc_url( QUOTEPRESSREDUX_PLUGIN_URL . 'images/blank-square.webp' ) . '" alt="Blank Square" class="quote-archive-thumbnail blank-square">';
                        }

                        $quote_text = get_post_meta(get_the_ID(), 'quote_text', TRUE);
                        $quote_author = get_post_meta(get_the_ID(), 'quote_author', TRUE);
                        ?>
                        <blockquote class="wp-block-quote quotepress-block">
                            <p class="quote-archive-text"><?php echo esc_html($quote_author); ?></p>
                            <p class="quote-archive-author"><?php echo esc_html($quote_text); ?></p>
                        </blockquote>
                    </div><!-- .quote-archive-item -->
                <?php
                endwhile;
                ?>
            </div><!-- .quote-archive-grid -->

<?php
      endwhile;
    ?>
  </ul>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>
