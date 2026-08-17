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
                    // Prefer ACF repeater when available, otherwise use newline post meta saved by the Stats meta box
                    $acf_stats = (function_exists('get_field')) ? get_field('lesson_stats') : false;
                    if ($acf_stats && is_array($acf_stats)) : ?>
                                    <aside class="lesson-stats">
    <ul class="lesson-stats-list">
                            <?php foreach ($acf_stats as $row) :
                                $label = isset($row['stat_label']) ? $row['stat_label'] : '';
                                $value = isset($row['stat_value']) ? $row['stat_value'] : '';
                                if (trim($label) === '' && trim($value) === '') continue; ?>
                                <li><span class="stat-label"><?php echo esc_html($label); ?></span>: <span class="stat-value"><?php echo esc_html($value); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        </aside>
                    <?php else:
                        $stats_meta = get_post_meta(get_the_ID(), 'lesson_stats', true);
                        if (!empty($stats_meta)) :
                            $slines = explode("\n", $stats_meta);
                            ?>
                            <aside class="lesson-stats">
                            <ul class="lesson-stats-list">
                                <?php foreach ($slines as $sline) {
                                    $sline = trim($sline);
                                    if (empty($sline)) continue;
                                    $parts = explode('|', $sline);
                                    $label = trim($parts[0]);
                                    $value = isset($parts[1]) ? trim($parts[1]) : '';
                                    if ($label === '' && $value === '') continue;
                                    echo '<li><span class="stat-label">' . esc_html($label) . '</span>: <span class="stat-value">' . esc_html($value) . '</span></li>';
                                } ?>
                            </ul>
                            </aside>
                        <?php endif;
                    endif; ?>
                </div>
            </div>

            

        </div><!-- .lesson-content-container -->

    </article><!-- #post-<?php the_ID(); ?> -->

    <?php 
    $external_links = get_post_meta( get_the_ID(), 'lesson_external_links', true );
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
            // Downloadable Assets - prefer ACF file array when available, otherwise use newline meta saved by the Downloads meta box
            $acf_download = (function_exists('get_field')) ? get_field('lesson_downloads') : false;
            if ($acf_download && is_array($acf_download)):
                ?>
                <section class="lesson-resources lesson-downloads pd-fl-x2" aria-describedby="lesson-downloads-heading">
                    <h2 class="heading" id="lesson-downloads-heading">Downloadable Resources</h2>
                    <a href="<?php echo esc_url($acf_download['url']); ?>" class="button download-button" download>
                        Download <?php echo esc_html($acf_download['title']); ?>
                        (<?php echo strtoupper(esc_html($acf_download['subtype'])); ?>)
                    </a>
                </section>
            <?php else:
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
                            if (empty($dline)) continue;
                                $parts = explode('|', $dline);
                                $title = trim($parts[0]);
                                $url = isset($parts[1]) ? trim($parts[1]) : '';
                                if (empty($url)) continue;
                                echo '<li><a href="' . esc_url($url) . '" class="button download-button" download>' . esc_html($title) . '</a></li>';
                            }
                            ?>
                        </ul>
                    </section>
                <?php
                endif;
            endif; ?>
</main><!-- #primary -->

<?php
get_footer();