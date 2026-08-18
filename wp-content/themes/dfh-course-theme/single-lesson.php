<?php
/**
 * Template Name: Single Lesson
 * Template Post Type: lesson
 * 
 * The template for displaying all single lesson posts.
 */

get_header();
?>

    <header class="lesson-head site-header" aria-label="Lesson navigation">
        <nav class="container lesson-nav">
        <?php
        $post_id = get_the_ID();
        $parent_id = wp_get_post_parent_id($post_id);

        // Breadcrumb: parent lesson when available; otherwise try to find parent Course
        if ($parent_id) : ?>
            <a class="lesson-breadcrumb link" href="<?php echo esc_url(get_permalink($parent_id)); ?>"><?php echo esc_html(get_the_title($parent_id)); ?></a>
        <?php else:
            // Attempt to find a Course that references this lesson as a root (ACF or postmeta)
            $course_id = null;
            $courses = get_posts(array(
                'post_type' => 'course',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array('key' => 'course_root_lessons', 'value' => '"' . $post_id . '"', 'compare' => 'LIKE'),
                ),
            ));
            if ($courses) {
                $course_id = $courses[0]->ID;
            }

            if ($course_id) : ?>
                <a class="lesson-breadcrumb link" href="<?php echo esc_url(get_permalink($course_id)); ?>"><?php echo esc_html(get_the_title($course_id)); ?></a>
            <?php else: ?>
                <a class="lesson-breadcrumb link" href="<?php echo esc_url(home_url()); ?>">Home</a>
            <?php endif;
        endif; ?>

        <!-- Syllabus overlay toggle -->
        <button class="syllabus-toggle" command="toggle-popover" commandfor="lesson-syllabus-panel">Open course navigation</button>

    </nav>

    </header>
<main id="primary" class="site-main lesson">
<!-- Syllabus overlay panel (hidden by default) -->
    <div id="lesson-syllabus-panel" class="lesson-syllabus-panel syllabus-panel" popover>
        <button class="syllabus-close" command="hide-popover" commandfor="lesson-syllabus-panel">Close navigation</button>
        <nav class="syllabus-nav" aria-label="Course syllabus">
            <ul class="syllabus-list">
                <?php
                // If we have a course, render its selected roots; otherwise render top-level lessons
                if (empty($course_id)) {
                    $top_roots = get_posts(array('post_type' => 'lesson', 'post_parent' => 0, 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC'));
                    foreach ($top_roots as $root) {
                        echo '<li><a href="' . esc_url(get_permalink($root->ID)) . '">' . esc_html(get_the_title($root->ID)) . '</a>';
                        echo dfh_render_lesson_children($root->ID);
                        echo '</li>';
                    }
                } else {
                    // Try ACF first, otherwise postmeta (expects array of IDs)
                    $roots = function_exists('get_field') ? get_field('course_root_lessons', $course_id) : get_post_meta($course_id, 'course_root_lessons', true);
                    if (!empty($roots) && is_array($roots)) {
                        foreach ($roots as $r) {
                            echo '<li><a href="' . esc_url(get_permalink($r)) . '">' . esc_html(get_the_title($r)) . '</a>';
                            echo dfh_render_lesson_children($r);
                            echo '</li>';
                        }
                    }
                }
                ?>
            </ul>
        </nav>
    </div>
    <article id="post-<?php the_ID(); ?>" <?php post_class('lesson-article'); ?>>

        <header class="lesson-header prose">
            <div class="container">
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
                <div class="lesson-video-container container">
                    <mux-player playback-id="<?php echo esc_attr($playback_id); ?>"
                        accent-color="#2eab93"
                        metadata-video-title="<?php echo esc_attr(get_the_title()); ?>" style="width:100%;height:auto;">
                    </mux-player>
                </div>
            </div>
        <?php endif; ?>
        <div class="lesson-content pd-fl-x2">


            <div class="container lesson-main">
                <div class="lesson-body prose">
                    <?php
                    the_content();
                    ?>
                </div>
                            <?php
                            // Gather stats and external links; render aside only if either exists
                            $stats_meta = get_post_meta(get_the_ID(), 'lesson_stats', true);
                            $external_links = get_post_meta(get_the_ID(), 'lesson_external_links', true);

                            if (!empty($stats_meta) || !empty($external_links)) : ?>
                                <aside class="lesson-aside">
                                    <?php
                                    // Stats
                                    if (!empty($stats_meta)):
                                        $slines = explode("\n", $stats_meta);
                                        ?>
                                        <section class="lesson-resources lesson-stats" aria-describedby="lesson-stats-heading">
                                            <h2 class="subheading lesson-resources-heading" id="lesson-stats-heading">Key statistics</h2>
                                            <dl class="lesson-stats-list">
                                                <?php foreach ($slines as $sline) {
                                                    $sline = trim($sline);
                                                    if (empty($sline)) continue;
                                                    $parts = explode('|', $sline);
                                                    $label = trim($parts[0]);
                                                    $value = isset($parts[1]) ? trim($parts[1]) : '';
                                                    $pc = isset($parts[2]) ? trim($parts[2]) : '';
                                                    if ($label === '' && $value === '') continue;
                                                    ?>
                                                    <div class="stat" <?php echo ($pc ? 'style="--stat-progress:' . esc_attr($pc) . ';" data-pc="' . esc_attr($pc) . '"' : ''); ?>>
                                                        <dt class="stat-label"><?php echo esc_html($label); ?></dt>
                                                        <dd class="stat-value"><?php echo esc_html($value); ?></dd>
                                                    </div>
                                                <?php } ?>
                                            </dl>
                                        </section>
                                    <?php
                                    endif;

                                    // External links
                                    if (!empty($external_links)):
                                        ?>
                                        <section class="lesson-resources lesson-links" aria-describedby="lesson-links-heading">
                                            <div class="container">
                                                <h2 class="subheading lesson-resources-heading" id="lesson-links-heading">Further reading & links</h2>
                                                <ul class="resource-list">
                                                    <?php
                                                    $lines = explode("\n", $external_links);
                                                    foreach ($lines as $line) {
                                                        $line = trim($line);
                                                        if (empty($line)) continue;
                                                        $parts = explode('|', $line);
                                                        $link_title = trim($parts[0]);
                                                        $link_url = isset($parts[1]) ? trim($parts[1]) : '#';
                                                        echo '<li><a href="' . esc_url($link_url) . '" target="_blank" class="link" rel="noopener noreferrer">' . esc_html($link_title) . '</a></li>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </section>
                                    <?php
                                    endif;
                                    ?>
                                </aside>
                            <?php endif; ?>
        </div>
        </div>



        </div><!-- .lesson-content-container -->

    </article><!-- #post-<?php the_ID(); ?> -->

    

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