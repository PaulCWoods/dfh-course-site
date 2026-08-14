<?php
/**
 * Template for displaying single course lessons
 */

get_header(); ?>

<main id="primary" class="site-main lesson-container">
    <?php while (have_posts()) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('lesson-article'); ?>>
            
            <!-- Lesson Header -->
            <header class="lesson-header">
                <h1 class="lesson-title"><?php the_title(); ?></h1>
            </header>

            <!-- Video Embed Section -->
            <?php 
            $video_url = get_field('lesson_video_url');
            if ($video_url): 
            ?>
                <div class="lesson-video-wrapper">
                    <div class="responsive-video">
                        <iframe src="<?php echo esc_url($video_url); ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Stats Grid Section -->
            <?php 
            $stats = get_field('lesson_stats');
            if ($stats): 
            ?>
                <section class="lesson-stats-grid">
                    <?php foreach ($stats as $stat): ?>
                        <div class="stat-card">
                            <span class="stat-value"><?php echo esc_html($stat['stat_value']); ?></span>
                            <span class="stat-label"><?php echo esc_html($stat['stat_label']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <!-- Main Lesson Text Content (WordPress Editor) -->
            <div class="lesson-content-body">
                <?php the_content(); ?>
            </div>

            <!-- Sidebar / Footer Resources (Links & Downloads) -->
            <footer class="lesson-resources">
                
                <!-- Useful Links -->
                <?php 
                $links = get_field('lesson_links');
                if ($links): 
                ?>
                    <div class="resource-group useful-links">
                        <h3>Useful Links</h3>
                        <ul>
                            <?php foreach ($links as $link): ?>
                                <li>
                                    <a href="<?php echo esc_url($link['link_url']); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html($link['link_title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Downloadable Asset -->
                <?php 
                $download = get_field('lesson_downloads');
                if ($download): 
                ?>
                    <div class="resource-group lesson-download">
                        <h3>Downloadable Asset</h3>
                        <a href="<?php echo esc_url($download['url']); ?>" class="button-download" download>
                            Download <?php echo esc_html($download['filename']); ?> (<?php echo size_format($download['filesize']); ?>)
                        </a>
                    </div>
                <?php endif; ?>

            </footer>

        </article>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>