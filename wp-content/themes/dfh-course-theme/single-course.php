<?php
get_header();

// Fetch the roots selected in ACF (safe when ACF is not active)
$root_lessons = function_exists('get_field') ? get_field('course_root_lessons') : get_post_meta(get_the_ID(), 'course_root_lessons', true);
if (!$root_lessons) {
    $root_lessons = array();
}

?>

<header class="site-header">
    <div class="container site-header-inner">
        <nav class="site-navigation">
            <ul class="site-breadcrumb container">
                <li><a href="https://designforhumans.blog" class="link site-title">Design For Humans</a></li>
                <li><a href="<?php echo esc_url( home_url( '/courses' )); ?>" class="nav-link link">Courses</a></li>
            </ul>
        </nav>
    </div>
</header>
<main class="course-landing site-main">
    <header>
        <h1><?php the_title(); ?></h1>
        <?php if (has_post_thumbnail()) the_post_thumbnail(); ?>
        <div class="course-description">
            <?php the_content(); ?>
        </div>
    </header>

    <section class="course-syllabus">
        <h2>Course Plan</h2>
        <?php if ($root_lessons) : ?>
            <ul class="syllabus-list">
                <?php foreach ($root_lessons as $root_id) : ?>
                    <li>
                        <a href="<?php echo get_permalink($root_id); ?>"><?php echo get_the_title($root_id); ?></a>
                        <?php echo dfh_render_lesson_children($root_id); // Recursive helper ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>