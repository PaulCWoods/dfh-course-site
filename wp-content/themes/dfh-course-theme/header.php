<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php wp_head(); ?>

    <!-- Shared Design Tokens & Styles from your main blog -->
    <link rel="stylesheet" href="https://designforhumans.blog/styles/css/dfh-shared.css">
    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/css/dfh-course.css' ); ?>">

    <script src="https://cdn.jsdelivr.net/npm/@mux/mux-player"></script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site">
<header class="site-header">
    <div class="container">
        <nav class="site-navigation">
            <ul class="site-breadcrumb ct">
                <li><a href="https://designforhumans.blog" class="link site-title">Design For Humans</a></li>
                <li><a href="<?php echo esc_url( home_url( '/courses' )); ?>" class="nav-link link">Courses</a></li>
            </ul>
        </nav>
    </div>
</header>