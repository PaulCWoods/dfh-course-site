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
    <article id="post-<?php the_ID(); ?>" <?php post_class('lesson-article'); ?>>

        <header class="lesson-header prose pd-fl-x2">
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

            <div class="ct lesson-main">
                <div class="lesson-body prose">
                    <?php
                    the_content();
                    ?>
                </div>
                <?php
                // Render lesson stats from meta box post meta (`lesson_stats`) only.
                $stats_meta = get_post_meta(get_the_ID(), 'lesson_stats', true);
                if (!empty($stats_meta)):
                    $slines = explode("\n", $stats_meta);
                    ?>
                    <aside class="lesson-stats">
                        <h2>Lesson statistics</h2>
                        <dl class="lesson-stats-list">
                            <?php foreach ($slines as $sline) {
                                $sline = trim($sline);
                                if (empty($sline))
                                    continue;
                                $parts = explode('|', $sline);
                                $label = trim($parts[0]);
                                $value = isset($parts[1]) ? trim($parts[1]) : '';
                                if ($label === '' && $value === '')
                                    continue;
                                echo '<div class="stat">';
                                echo '<dt class="stat-label">' . esc_html($label) . '</dt>';
                                echo '<dd class="stat-value">' . esc_html($value) . '</dd>';
                                echo '</div>';
                            } ?>
                        </dl>
                    </aside>
                <?php endif; ?>
            </div>
        </div>



        </div><!-- .lesson-content-container -->

    </article><!-- #post-<?php the_ID(); ?> -->

    <?php
    $external_links = get_post_meta(get_the_ID(), 'lesson_external_links', true);
    if (!empty($external_links)):
        ?>
        <section class="lesson-resources lesson-links pd-fl-x2" aria-describedby="lesson-links-heading">
            <div class="ct">
                <h2 class="heading" id="lesson-links-heading">Further reading & links</h2>
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

    <?php
    // Downloadable Assets - use newline meta saved by the Downloads meta box (`lesson_downloads`).
    $download_meta = get_post_meta(get_the_ID(), 'lesson_downloads', true);
    if (!empty($download_meta)):
        $dlines = explode("\n", $download_meta);
        ?>
        <section class="lesson-resources lesson-downloads pd-fl-x2" aria-describedby="lesson-downloads-heading">
            <h2 class="heading" id="lesson-downloads-heading">Downloadable Resources</h2>
            <ul class="download-list">
                <?php
                foreach ($dlines as $dline) {
                    $dline = trim($dline);
                    if (empty($dline))
                        continue;
                    $parts = explode('|', $dline);
                    $title = trim($parts[0]);
                    $url = isset($parts[1]) ? trim($parts[1]) : '';
                    if (empty($url))
                        continue;
                    echo '<li><a href="' . esc_url($url) . '" class="button download-button" download>' . esc_html($title) . '</a></li>';
                }
                ?>
            </ul>
        </section>
    <?php endif; ?>
</main><!-- #primary -->

<?php
get_footer();