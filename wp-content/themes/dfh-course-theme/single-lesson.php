<?php
/**
 * Template Name: Single Lesson
 * Template Post Type: lesson
 * 
 * The template for displaying all single lesson posts.
 */

get_header();
?>


<main id="primary" class="site-main lesson">
    <article id="post-<?php the_ID(); ?>" <?php post_class('lesson-article prose'); ?>>

        <header class="lesson-header pd-fl-x2">
            <div class="ct">
                <h1 class="lesson-title">
                    <?php
                    $lesson_code = dfh_get_lesson_hierarchy_number();
                    if ($lesson_code):
                        ?>
                        <span class="lesson-code-badge"><?php echo esc_html($lesson_code); ?></span>
                    <?php endif; ?>
                    <?php the_title(); ?>
                </h1>
            </div>
        </header>

        <?php
        // Video playback: Mux only (legacy fallback removed)
        $playback_id = get_post_meta(get_the_ID(), 'mux_playback_id', true);
        if ($playback_id): ?>
            <div class="lesson-video">
                <div class="lesson-video-container ct">
                    <mux-player playback-id="<?php echo esc_attr($playback_id); ?>"
                        metadata-video-title="<?php echo esc_attr(get_the_title()); ?>" style="width:100%;height:auto;">
                    </mux-player>
                </div>
            </div>
        <?php endif; ?>
        <div class="lesson-content pd-fl-x2">


            <div class="lesson-body ct">
                <?php
                the_content();
                ?>
            </div>

            <?php
            // Downloadable Assets
            $download = get_field('lesson_downloads');
            if ($download && is_array($download)):
                ?>
                <div class="lesson-downloads-box">
                    <h3>Downloadable Resources</h3>
                    <a href="<?php echo esc_url($download['url']); ?>" class="button download-button" download>
                        Download <?php echo esc_html($download['title']); ?>
                        (<?php echo strtoupper(esc_html($download['subtype'])); ?>)
                    </a>
                </div>
            <?php endif; ?>

        </div><!-- .lesson-content-container -->

    </article><!-- #post-<?php the_ID(); ?> -->

    <?php 
    $external_links = get_post_meta( get_the_ID(), 'lesson_external_links', true );
    if (!empty($external_links)):
        ?>
        <section class="lesson-resources-section pd-fl-x2">
            <div class="ct">
                <h2 class="heading">Further reading & links</h2>
                <ul class="resource-list">
                    <?php
                    $lines = explode("\n", $external_links);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line))
                            continue;

                        // Split by pipe character |
                        $parts = explode('|', $line);
                        $link_title = trim($parts[0]);
                        $link_url = isset($parts[1]) ? trim($parts[1]) : '#';

                        echo '<li><a href="' . esc_url($link_url) . '" target="_blank" class="link" rel="noopener noreferrer">' . esc_html($link_title) . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>
</main><!-- #primary -->

<?php
get_footer();