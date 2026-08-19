<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta
        name="viewport"
        content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, viewport-fit=cover"
    >
    <?php
    // Build page title as "[Page title] | Design for Humans Courses"
    if (function_exists('is_singular') && is_singular()) {
        $page_title = single_post_title('', false);
    } elseif (function_exists('is_home') && (is_home() || is_front_page())) {
        $page_title = get_bloginfo('name');
    } elseif (function_exists('is_archive') && is_archive()) {
        $page_title = get_the_archive_title();
    } else {
        $page_title = function_exists('wp_get_document_title') ? wp_get_document_title() : get_bloginfo('name');
    }
    ?>
    <title><?php echo esc_html($page_title); ?> | Design for Humans Courses</title>
    <?php wp_head(); ?>

    <!-- Shared Design Tokens & Styles from your main blog -->
    <link rel="stylesheet" href="https://designforhumans.blog/styles/css/dfh-shared.css">
    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/css/dfh-course.css' ); ?>">

    <script src="https://cdn.jsdelivr.net/npm/@mux/mux-player"></script>
</head>
<?php
// Compute a top-level syllabus index for lesson pages (1-based). Defaults to 0.
$syllabus_index = 0;
if (function_exists('is_singular') && is_singular('lesson')) {
    if (function_exists('dfh_get_lesson_hierarchy_number')) {
        $code = dfh_get_lesson_hierarchy_number(get_the_ID());
        if (!empty($code)) {
            $parts = explode('.', $code);
            $syllabus_index = (int) $parts[0];
        }
    }
}
?>
<body <?php body_class(); ?> data-syllabus="<?php echo esc_attr($syllabus_index); ?>">
<?php wp_body_open(); ?>
<div class="site">