<?php
/**
 * Template Name: Single Lesson
 * Template Post Type: lesson
 * 
 * The template for displaying all single lesson posts.
 */

get_header();
?>


<main id="primary" class="site-main lesson-single-main">
    <article id="post-<?php the_ID(); ?>" <?php post_class('lesson-article prose ct'); ?>>
        
        <header class="lesson-header article-header">
            <div class="container">
                <?php 
                $lesson_code = dfh_get_lesson_hierarchy_number();
                if ( $lesson_code ) : 
                ?>
                    <span class="lesson-code-badge"><?php echo esc_html( $lesson_code ); ?></span>
                <?php endif; ?>

                <h1 class="lesson-title"><?php the_title(); ?></h1>
            </div>
        </header>

        <div class="lesson-content-container container">
            
            <?php 
            // Optional Video Embed or Link
            $video_url = get_field('lesson_video_url');
            if ( $video_url ) : 
            ?>
                <div class="lesson-video-wrapper">
                    <?php 
                    // Use WordPress embed handler if it's a oEmbed URL (Vimeo, YouTube, etc.)
                    $embedded_video = wp_oembed_get( $video_url );
                    if ( $embedded_video ) {
                        echo $embedded_video;
                    } else {
                        echo '<a href="' . esc_url( $video_url ) . '" target="_blank" rel="noopener">Watch Lesson Video</a>';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <div class="lesson-body-content">
                <?php 
                the_content(); 
                ?>
            </div>

            <?php 
            // Downloadable Assets
            $download = get_field('lesson_downloads');
            if ( $download && is_array( $download ) ) : 
            ?>
                <div class="lesson-downloads-box">
                    <h3>Downloadable Resources</h3>
                    <a href="<?php echo esc_url( $download['url'] ); ?>" class="button download-button" download>
                        Download <?php echo esc_html( $download['title'] ); ?> (<?php echo strtoupper( esc_html( $download['subtype'] ) ); ?>)
                    </a>
                </div>
            <?php endif; ?>

        </div><!-- .lesson-content-container -->

    </article><!-- #post-<?php the_ID(); ?> -->
</main><!-- #primary -->

<?php
get_footer();