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
                <li><a href="<?php echo esc_url( home_url( '/' )); ?>" class="nav-link link">Courses</a></li>
            </ul>
        </nav>
    </div>
</header>
<main class="course-landing site-main">
    <article class="article container course-about prose">
    <header class="article-header">
        <h1><?php the_title(); ?></h1>
    </header>
    <?php if (has_post_thumbnail()) the_post_thumbnail(); ?>
    <?php the_content(); ?>
   </article>

    <section class="course-syllabus container">
        <h2 class="heading">Course Plan</h2>
        <?php if ($root_lessons) : ?>
            <?php echo dfh_render_lesson_tree($root_lessons, 1); ?>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>