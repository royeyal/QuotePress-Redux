<?php get_header(); ?>

<div class="quotes-archive-container">
    <?php if ( have_posts() ) : ?>
        <div class="quotes-list">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="quote-item">
                    <div class="quote-text"><?php the_content(); ?></div>
                    <div class="quote-author"><?php echo get_post_meta(get_the_ID(), 'author', true); ?></div>
                    <!-- Add featured image support -->
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="quote-image">
                            <?php the_post_thumbnail(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="pagination">
            <?php
                // Pagination
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => __( 'Back', 'text_domain' ),
                    'next_text' => __( 'Next', 'text_domain' ),
                ) );
            ?>
        </div>
    <?php else : ?>
        <p><?php _e( 'No quotes found.', 'text_domain' ); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
