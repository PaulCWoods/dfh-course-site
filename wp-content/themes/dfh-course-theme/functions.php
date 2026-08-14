<?php
/**
 * DFH Course Theme Functions
 *
 * Organized sections:
 *  - Post Types
 *  - ACF Field Groups (conditional)
 *  - Helpers
 */

/**
 * -------------------------------------------------------------------------
 * Post Types
 * -------------------------------------------------------------------------
 */

/**
 * Register the hierarchical `lesson` custom post type.
 * Supports parent/child nesting for module/chapter/page structure.
 *
 * @return void
 */
function dfh_register_lesson_cpt() {
    $labels = array(
        'name'               => 'Lessons',
        'singular_name'      => 'Lesson',
        'menu_name'          => 'Course Lessons',
        'add_new'            => 'Add New Lesson',
        'add_new_item'       => 'Add New Lesson',
        'edit_item'          => 'Edit Lesson',
        'new_item'           => 'New Lesson',
        'view_item'          => 'View Lesson',
        'search_items'       => 'Search Lessons',
        'not_found'          => 'No lessons found',
        'not_found_in_trash' => 'No lessons found in trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true, // Enables Gutenberg block editor support
        'hierarchical'        => true, // Enables parent/child lesson nesting
        'menu_icon'           => 'dashicons-welcome-learn-more',
        'supports'            => array('title', 'editor', 'thumbnail', 'revisions', 'page-attributes'), // page-attributes adds Parent Lesson dropdown
        'rewrite'             => array('slug' => 'lessons', 'with_front' => false),
    );

    register_post_type('lesson', $args);
}
add_action('init', 'dfh_register_lesson_cpt', 0);

/**
 * Register the `course` custom post type.
 * Used for course landing pages and syllabi that reference Lessons.
 *
 * @return void
 */
function dfh_register_course_cpt() {
    $labels = array(
        'name'               => 'Courses',
        'singular_name'      => 'Course',
        'menu_name'          => 'Courses',
        'add_new'            => 'Add New Course',
        'add_new_item'       => 'Add New Course',
        'edit_item'          => 'Edit Course',
        'new_item'           => 'New Course',
        'view_item'          => 'View Course',
        'search_items'       => 'Search Courses',
        'not_found'          => 'No courses found',
        'not_found_in_trash' => 'No courses found in trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => 'courses',
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true, // Enables Gutenberg block editor
        'menu_icon'           => 'dashicons-welcome-add-page',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'),
        'rewrite'             => array('slug' => 'courses', 'with_front' => false),
    );

    register_post_type('course', $args);
}
add_action('init', 'dfh_register_course_cpt', 0);
/**
 * -------------------------------------------------------------------------
 * ACF Field Groups (registered only when ACF is available)
 * -------------------------------------------------------------------------
 * These are registered via `acf_add_local_field_group()` so they're available
 * when the Advanced Custom Fields plugin is active. They are intentionally
 * lightweight and self-documented in the `instructions` keys.
 */

if ( function_exists( 'acf_add_local_field_group' ) ) {
    // Lesson-specific fields and repeaters
    acf_add_local_field_group( array(
        'key'    => 'group_lesson_details',
        'title'  => 'Lesson Meta & Content',
        'fields' => array(
            // Video URL Field
            array(
                'key'          => 'field_lesson_video_url',
                'label'        => 'Video URL',
                'name'         => 'lesson_video_url',
                'type'         => 'url',
                'instructions' => 'Paste the secure video embed or hosting link (e.g., Mux or Vimeo).',
                'required'     => 0,
            ),
            // Stats Repeater Field
            array(
                'key'          => 'field_lesson_stats',
                'label'        => 'Lesson Stats',
                'name'         => 'lesson_stats',
                'type'         => 'repeater',
                'instructions' => 'Add key metrics or stats to display nicely alongside the lesson.',
                'button_label' => 'Add Stat',
                'layout'       => 'table',
                'sub_fields'   => array(
                    array(
                        'key'   => 'field_stat_label',
                        'label' => 'Label',
                        'name'  => 'stat_label',
                        'type'  => 'text',
                    ),
                    array(
                        'key'   => 'field_stat_value',
                        'label' => 'Value/Number',
                        'name'  => 'stat_value',
                        'type'  => 'text',
                    ),
                ),
            ),
            // Useful Links Repeater Field
            array(
                'key'          => 'field_lesson_links',
                'label'        => 'Useful Links',
                'name'         => 'lesson_links',
                'type'         => 'repeater',
                'instructions' => 'Add external resources or reference links for this lesson.',
                'button_label' => 'Add Link',
                'layout'       => 'row',
                'sub_fields'   => array(
                    array(
                        'key'   => 'field_link_title',
                        'label' => 'Link Title',
                        'name'  => 'link_title',
                        'type'  => 'text',
                    ),
                    array(
                        'key'   => 'field_link_url',
                        'label' => 'URL',
                        'name'  => 'link_url',
                        'type'  => 'url',
                    ),
                ),
            ),
            // Downloads File Field
            array(
                'key'          => 'field_lesson_downloads',
                'label'        => 'Downloadable Assets',
                'name'         => 'lesson_downloads',
                'type'         => 'file',
                'instructions' => 'Upload a PDF, workbook, or exercise file for students.',
                'return_format' => 'array',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'lesson',
                ),
            ),
        ),
    ) );

    // Course-level syllabus selector
    acf_add_local_field_group( array(
        'key'    => 'group_course_syllabus',
        'title'  => 'Course Syllabus',
        'fields' => array(
            array(
                'key'          => 'field_course_lessons',
                'label'        => 'Select Root Lessons',
                'name'         => 'course_root_lessons',
                'type'         => 'relationship',
                'instructions' => 'Select the top-level Lessons (Modules) for this course.',
                'post_type'    => array( 'lesson' ),
                'return_format'=> 'id',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'course',
                ),
            ),
        ),

    ) );
}

/**
 * -------------------------------------------------------------------------
 * Helpers
 * -------------------------------------------------------------------------
 */

/**
 * Dynamic structural numbering for a `lesson` post based on its position
 * in the parent/child tree. Produces strings like `1.2.3` where each number
 * is the 1-based index among siblings at that level.
 *
 * @param int|null $post_id Post ID to inspect. Defaults to current post.
 * @return string Empty string when not a lesson or if no numbers found.
 */
function dfh_get_lesson_hierarchy_number( $post_id = null ) {
    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'lesson' ) {
        return '';
    }

    // Get ancestor chain from root down to current post
    $ancestors = get_post_ancestors( $post->ID );
    $ancestors = array_reverse( $ancestors ); // Root first
    $ancestors[] = $post->ID; // Append current post

    $numbers = array();

    foreach ( $ancestors as $index => $ancestor_id ) {
        $current_post = get_post( $ancestor_id );
        if ( ! $current_post ) {
            continue;
        }

        // Find position among siblings with the same parent
        $siblings = get_posts( array(
            'post_type'      => 'lesson',
            'posts_per_page' => -1,
            'post_parent'    => $current_post->post_parent,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ) );

        $position = array_search( $current_post->ID, $siblings );
        $numbers[] = ( false !== $position ) ? ( $position + 1 ) : 1;
    }

    // Return formatted string if it fits our structural depth (e.g., 1.2.3)
    if ( ! empty( $numbers ) ) {
        return implode( '.', $numbers );
    }

    return '';
}

/**
 * Backwards-compatible lesson code helper. Prefers explicit module/chapter/page
 * fields (from ACF or post meta), falling back to the hierarchy-based number.
 *
 * Examples: `1.2.3`
 *
 * @param int|null $post_id Post ID to inspect. Defaults to current post.
 * @return string
 */
function dfh_get_lesson_code( $post_id = null ) {
    if ( ! $post_id ) {
        $post = get_post();
        $post_id = $post ? $post->ID : null;
    }

    if ( ! $post_id ) {
        return '';
    }

    // Try ACF first, then post meta
    if ( function_exists( 'get_field' ) ) {
        $m = get_field( 'lesson_module', $post_id );
        $c = get_field( 'lesson_chapter', $post_id );
        $p = get_field( 'lesson_page', $post_id );
    } else {
        $m = get_post_meta( $post_id, 'lesson_module', true );
        $c = get_post_meta( $post_id, 'lesson_chapter', true );
        $p = get_post_meta( $post_id, 'lesson_page', true );
    }

    if ( $m && $c && $p ) {
        return sprintf( '%d.%d.%d', (int) $m, (int) $c, (int) $p );
    }

    // Fall back to structural/hierarchy numbering
    return dfh_get_lesson_hierarchy_number( $post_id );
}

/**
 * Recursively renders a nested list of child lessons for a given parent lesson.
 *
 * @param int $parent_id The ID of the parent lesson.
 * @return string HTML unordered list of child lessons, or empty string if none.
 */

function dfh_render_lesson_children($parent_id) {
    $children = get_posts(array(
        'post_type'      => 'lesson',
        'post_parent'    => $parent_id,
        'posts_per_page' => -1,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ));

    if (!$children) return '';

    $output = '<ul class="sub-lesson-list">';
    foreach ($children as $child) {
        $output .= '<li><a href="' . get_permalink($child->ID) . '">' . get_the_title($child->ID) . '</a>';
        $output .= dfh_render_lesson_children($child->ID); // Recursion
        $output .= '</li>';
    }
    $output .= '</ul>';

    return $output;
}