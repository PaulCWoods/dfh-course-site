<?php
/**
 * DFH Course Theme Functions
 */

// 1. Register the Lesson Custom Post Type
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
        'menu_icon'           => 'dashicons-welcome-learn-more',
        'supports'            => array('title', 'editor', 'thumbnail', 'revisions'),
        'rewrite'             => array('slug' => 'lessons'),
    );

    register_post_type('lesson', $args);
}
add_action('init', 'dfh_register_lesson_cpt');


// 2. Register Advanced Custom Fields (ACF) for Lessons
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
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
    ));
}