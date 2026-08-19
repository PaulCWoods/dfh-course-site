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
    <div class="lesson-head-inner container">
        <?php
        $lesson_code = dfh_get_lesson_hierarchy_number();
        if ($lesson_code):
            ?>
            <span class="lesson-code-badge" title="Lesson code"><span class="sr">Lesson code:</span><?php echo esc_html($lesson_code); ?></span>
        <?php endif; ?>
        <nav class="lesson-nav">
            <?php
            $post_id = get_the_ID();
            $parent_id = wp_get_post_parent_id($post_id);

            // Breadcrumb: parent lesson when available; otherwise try to find parent Course
            if ($parent_id): ?>
                <a class="lesson-breadcrumb link"
                    href="<?php echo esc_url(get_permalink($parent_id)); ?>"><?php echo esc_html(get_the_title($parent_id)); ?></a>
            <?php else:
                // Attempt to find a Course that references this lesson as a root (ACF or postmeta)
                $course_id = null;
                $all_courses = get_posts(array('post_type' => 'course', 'posts_per_page' => -1, 'fields' => 'ids'));
                if (!empty($all_courses)) {
                    foreach ($all_courses as $c_id) {
                        $roots = function_exists('get_field') ? get_field('course_root_lessons', $c_id) : get_post_meta($c_id, 'course_root_lessons', true);
                        if (is_string($roots)) {
                            $maybe = @unserialize($roots);
                            if ($maybe !== false) $roots = $maybe;
                        }
                        if (!empty($roots) && in_array($post_id, (array) $roots)) {
                            $course_id = $c_id;
                            break;
                        }
                    }
                }

                if ($course_id): ?>
                    <a class="lesson-breadcrumb link"
                        href="<?php echo esc_url(get_permalink($course_id)); ?>"><?php echo esc_html(get_the_title($course_id)); ?></a>
                <?php else: ?>
                    <a class="lesson-breadcrumb link" href="<?php echo esc_url(home_url()); ?>">Home</a>
                <?php endif;
            endif; ?>

            <!-- Syllabus overlay toggle -->

        </nav>
        <button class="progress-toggle lesson-progress-toggle button" command="toggle-popover"
            commandfor="lesson-progress">Progress</button>
    </div>

</header>
<main id="primary" class="site-main lesson">
    <!-- Syllabus overlay panel (hidden by default) -->
    <aside id="lesson-progress" class="lesson-progress progress-panel" popover>
        <?php
        // Determine a course to display in the panel: prefer any already-found $course_id,
        // otherwise check the current lesson and its ancestors for a course that lists them as roots.
        $panel_course_id = isset($course_id) && $course_id ? $course_id : null;
        if (empty($panel_course_id)) {
            $ancestors = get_post_ancestors(get_the_ID());
            array_unshift($ancestors, get_the_ID());

            $all_courses = get_posts(array('post_type' => 'course', 'posts_per_page' => -1, 'fields' => 'ids'));
            if (!empty($all_courses)) {
                foreach ($all_courses as $c_id) {
                    $roots = function_exists('get_field') ? get_field('course_root_lessons', $c_id) : get_post_meta($c_id, 'course_root_lessons', true);
                    if (is_string($roots)) {
                        $maybe = @unserialize($roots);
                        if ($maybe !== false) $roots = $maybe;
                    }
                    if (!empty($roots)) {
                        foreach ($ancestors as $anc) {
                            if (in_array($anc, (array) $roots)) {
                                $panel_course_id = $c_id;
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        ?>
        <header class="progress-panel-header">
            <a class="link syllabus-course" href="<?php echo esc_url(home_url()); ?>">Home</a>

            <button class="syllabus-close button" command="hide-popover" commandfor="lesson-progress">
                Close navigation
            </button>
        </header>
            <nav class="progress-panel-course" aria-label="Course syllabus">
            <?php
            // Show the current Course title and link when available
            $display_course_id = !empty($panel_course_id) ? $panel_course_id : (!empty($course_id) ? $course_id : null);
            if ($display_course_id): ?>
                <h2 class="heading progress-panel-course-name"><a href="<?php echo esc_url(get_permalink($display_course_id)); ?>"><?php echo esc_html(get_the_title($display_course_id)); ?></a></h2>
            <?php else: ?>
                <h2 class="heading progress-panel-course-name">Course</h2>
            <?php endif; ?>
                <?php
                // If we have a course, get its roots; otherwise leave null to render top-level lessons
                $roots = null;
                if (!empty($course_id)) {
                    $roots = function_exists('get_field') ? get_field('course_root_lessons', $course_id) : get_post_meta($course_id, 'course_root_lessons', true);
                }

                echo dfh_render_lesson_tree($roots, 1);
                ?>
        </nav>
        <footer class="progress-panel-footer">
            <a href="https://designforhumans.blog" class="progress-panel-brand">
                Design for Humans
            </a>
        </footer>
    </aside>
    <article id="post-<?php the_ID(); ?>" <?php post_class('lesson-article'); ?>>

        <header class="lesson-header prose">
            <div class="container">
                <h1 class="lesson-title">
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
                    <mux-player playback-id="<?php echo esc_attr($playback_id); ?>" accent-color="#2eab93"
                        metadata-video-title="<?php echo esc_attr(get_the_title()); ?>" style="width:100%;height:auto;">
                    </mux-player>
                </div>
            </div>
        <?php endif; ?>
        <div class="lesson-content">


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

                if (!empty($stats_meta) || !empty($external_links)): ?>
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
                                        if (empty($sline))
                                            continue;
                                        $parts = explode('|', $sline);
                                        $label = trim($parts[0]);
                                        $value = isset($parts[1]) ? trim($parts[1]) : '';
                                        $pc = isset($parts[2]) ? trim($parts[2]) : '';
                                        if ($label === '' && $value === '')
                                            continue;
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
                                    <h2 class="subheading lesson-resources-heading" id="lesson-links-heading">Further reading &
                                        links</h2>
                                    <ul class="resource-list">
                                        <?php
                                        $lines = explode("\n", $external_links);
                                        foreach ($lines as $line) {
                                            $line = trim($line);
                                            if (empty($line))
                                                continue;
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
</main><!-- #primary -->

<?php
get_footer();