<?php
/**
 * Template Name: Single Lesson
 * Template Post Type: lesson
 * 
 * The template for displaying all single lesson posts.
 */

get_header();
?>
<main class="course-home site-main">
    <article class="prose container">
        <header class="article-header">
            <h1 class="display-title">Design for Humans: Courses</h1>
        </header>
        <?php the_content(); ?>
    </article>
</main>
<?php
get_footer();