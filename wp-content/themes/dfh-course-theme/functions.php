<?php
/**
 * DFH Course Theme Functions
 *
 * This file contains theme-specific functionality for the DFH Course Theme.
 * It is organized into logical sections:
 *  - Post Types: register CPTs used by the theme.
 *  - META Boxes: admin meta boxes (Mux playback ID).
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
function dfh_register_lesson_cpt()
{
    $labels = array(
        'name' => 'Lessons',
        'singular_name' => 'Lesson',
        'menu_name' => 'Course Lessons',
        'add_new' => 'Add New Lesson',
        'add_new_item' => 'Add New Lesson',
        'edit_item' => 'Edit Lesson',
        'new_item' => 'New Lesson',
        'view_item' => 'View Lesson',
        'search_items' => 'Search Lessons',
        'not_found' => 'No lessons found',
        'not_found_in_trash' => 'No lessons found in trash'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true, // Enables Gutenberg block editor support
        'hierarchical' => true, // Enables parent/child lesson nesting
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'page-attributes'), // page-attributes adds Parent Lesson dropdown
        'rewrite' => array('slug' => 'lessons', 'with_front' => false),
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
function dfh_register_course_cpt()
{
    $labels = array(
        'name' => 'Courses',
        'singular_name' => 'Course',
        'menu_name' => 'Courses',
        'add_new' => 'Add New Course',
        'add_new_item' => 'Add New Course',
        'edit_item' => 'Edit Course',
        'new_item' => 'New Course',
        'view_item' => 'View Course',
        'search_items' => 'Search Courses',
        'not_found' => 'No courses found',
        'not_found_in_trash' => 'No courses found in trash'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => 'courses',
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true, // Enables Gutenberg block editor
        'menu_icon' => 'dashicons-welcome-add-page',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'),
        'rewrite' => array('slug' => 'courses', 'with_front' => false),
    );

    register_post_type('course', $args);
}
add_action('init', 'dfh_register_course_cpt', 0);

/**
 * -------------------------------------------------------------------------
 * META Boxes
 * -------------------------------------------------------------------------
 */

function dfh_add_lesson_mux_meta_box()
{
    add_meta_box(
        'dfh_mux_playback_id_box',           // Unique ID
        'Mux Video Settings',                 // Box title
        'dfh_render_mux_meta_box_html',       // Callback function to render HTML
        'lesson',                             // Post type (adjust if your CPT slug is different)
        'normal',                             // Context (normal, side, advanced)
        'high'                                // Priority
    );
}
add_action('add_meta_boxes', 'dfh_add_lesson_mux_meta_box');

/**
 * Render the Mux Playback ID meta box on the Lesson edit screen.
 *
 * Displays a simple input for the Mux `playback_id`. The value is stored
 * in post meta under the key `mux_playback_id` by {@see dfh_save_lesson_mux_meta()}.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function dfh_render_mux_meta_box_html($post)
{
    // Add a nonce field for security verification
    wp_nonce_field('dfh_save_mux_meta_box', 'dfh_mux_nonce');

    // Retrieve existing value if it already exists
    $playback_id = get_post_meta($post->ID, 'mux_playback_id', true);
    ?>
    <div style="margin-bottom: 15px;">
        <label for="mux_playback_id" style="display: block; font-weight: 600; margin-bottom: 5px;">Mux Playback ID:</label>
        <input type="text" id="mux_playback_id" name="mux_playback_id" value="<?php echo esc_attr($playback_id); ?>"
            style="width: 100%; padding: 8px;" placeholder="e.g. C500v0293J6j02K029F301t02K...">
        <p style="font-size: 12px; color: #666; margin-top: 5px;">Paste the playback ID generated from your Mux dashboard
            for this lesson.</p>
    </div>
    <?php
}

/**
 * Save the Mux playback ID when a Lesson is saved.
 *
 * Verifies nonce, autosave status, and capability before updating post meta.
 *
 * @param int $post_id Post ID being saved.
 * @return void
 */
function dfh_save_lesson_mux_meta($post_id)
{
    // Check if nonce is set
    if (!isset($_POST['dfh_mux_nonce'])) {
        return;
    }

    // Verify nonce for security
    if (!wp_verify_nonce($_POST['dfh_mux_nonce'], 'dfh_save_mux_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (isset($_POST['post_type']) && 'lesson' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Sanitize and save the data
    if (isset($_POST['mux_playback_id'])) {
        $sanitized_data = sanitize_text_field($_POST['mux_playback_id']);
        update_post_meta($post_id, 'mux_playback_id', $sanitized_data);
    }
}
add_action('save_post', 'dfh_save_lesson_mux_meta');

/**
 * Register meta box for external lesson links and its renderer/saver.
 */
function dfh_add_lesson_links_meta_box()
{
    add_meta_box(
        'dfh_lesson_links_box',
        'Lesson External Links',
        'dfh_render_links_meta_box_html',
        'lesson',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dfh_add_lesson_links_meta_box');

/**
 * Render simple textarea for lesson external links (one per line: Title | URL).
 *
 * @param WP_Post $post
 */
function dfh_render_links_meta_box_html($post)
{
    wp_nonce_field('dfh_save_links_meta', 'dfh_links_nonce');

    $external_links = get_post_meta($post->ID, 'lesson_external_links', true);
    ?>

    <div style="margin-bottom: 10px;">
        <label style="display: block; font-weight: 600; margin-bottom: 5px;">External Links (One per line: Title |
            URL):</label>
        <textarea name="lesson_external_links" rows="4" style="width: 100%; padding: 8px;"
            placeholder="W3C Accessibility Guidelines | https://www.w3.org/WAI/standards-guidelines/&#10;A11y Project Checklist | https://www.a11yproject.com/checklist/"><?php echo esc_textarea($external_links); ?></textarea>
        <p style="font-size: 12px; color: #666; margin-top: 5px;">Format each link as `Link Title | https://url` on a new
            line.</p>
    </div>
    <?php
}

/**
 * Save lesson links meta (and keep mux ID saving here for consistency).
 *
 * @param int $post_id
 */
function dfh_save_lesson_links_meta($post_id)
{
    if (!isset($_POST['dfh_links_nonce']) || !wp_verify_nonce($_POST['dfh_links_nonce'], 'dfh_save_links_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save Mux ID (if present in the same post form)
    if (isset($_POST['mux_playback_id'])) {
        update_post_meta($post_id, 'mux_playback_id', sanitize_text_field($_POST['mux_playback_id']));
    }

    // Save External Links
    if (isset($_POST['lesson_external_links'])) {
        update_post_meta($post_id, 'lesson_external_links', sanitize_textarea_field($_POST['lesson_external_links']));
    }
}
add_action('save_post', 'dfh_save_lesson_links_meta');

/**
 * Register meta box for downloadable assets (simple textarea: one per line Title | URL)
 */
function dfh_add_lesson_downloads_meta_box()
{
    add_meta_box(
        'dfh_lesson_downloads_box',
        'Lesson Downloadable Assets',
        'dfh_render_downloads_meta_box_html',
        'lesson',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dfh_add_lesson_downloads_meta_box');

function dfh_render_downloads_meta_box_html($post)
{
    wp_nonce_field('dfh_save_downloads_meta', 'dfh_downloads_nonce');

    $downloads = get_post_meta($post->ID, 'lesson_downloads', true);
    ?>
    <div style="margin-bottom: 10px;">
        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Downloads (one per line: Title | URL):</label>
        <textarea name="lesson_downloads" rows="4" style="width: 100%; padding: 8px;" placeholder="Workbook | https://example.com/workbook.pdf"><?php echo esc_textarea($downloads); ?></textarea>
        <p style="font-size: 12px; color: #666; margin-top: 5px;">Each line should contain a title, a pipe, then the URL.</p>
    </div>
    <?php
}

function dfh_save_lesson_downloads_meta($post_id)
{
    if (!isset($_POST['dfh_downloads_nonce']) || !wp_verify_nonce($_POST['dfh_downloads_nonce'], 'dfh_save_downloads_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['lesson_downloads'])) {
        update_post_meta($post_id, 'lesson_downloads', sanitize_textarea_field($_POST['lesson_downloads']));
    }
}
add_action('save_post', 'dfh_save_lesson_downloads_meta');

/**
 * Register meta box for lesson stats (one per line: Label | Value)
 */
function dfh_add_lesson_stats_meta_box()
{
    add_meta_box(
        'dfh_lesson_stats_box',
        'Lesson Stats',
        'dfh_render_stats_meta_box_html',
        'lesson',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'dfh_add_lesson_stats_meta_box');

function dfh_render_stats_meta_box_html($post)
{
    wp_nonce_field('dfh_save_stats_meta', 'dfh_stats_nonce');

    $stats = get_post_meta($post->ID, 'lesson_stats', true);
    ?>
    <div style="margin-bottom: 10px;">
        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Lesson Stats (one per line: Label | Value):</label>
        <textarea name="lesson_stats" rows="4" style="width: 100%; padding: 8px;" placeholder="Students | 120"><?php echo esc_textarea($stats); ?></textarea>
        <p style="font-size: 12px; color: #666; margin-top: 5px;">Each line should contain a label, a pipe, then the number/value.</p>
    </div>
    <?php
}

function dfh_save_lesson_stats_meta($post_id)
{
    if (!isset($_POST['dfh_stats_nonce']) || !wp_verify_nonce($_POST['dfh_stats_nonce'], 'dfh_save_stats_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['lesson_stats'])) {
        update_post_meta($post_id, 'lesson_stats', sanitize_textarea_field($_POST['lesson_stats']));
    }
}
add_action('save_post', 'dfh_save_lesson_stats_meta');


/**
 * -------------------------------------------------------------------------
 * ACF Field Groups (registered only when ACF is available)
 * -------------------------------------------------------------------------
 * These are registered via `acf_add_local_field_group()` so they're available
 * when the Advanced Custom Fields plugin is active. They are intentionally
 * lightweight and self-documented in the `instructions` keys.
 */

if (function_exists('acf_add_local_field_group')) {
    // Lesson-specific fields and repeaters
    acf_add_local_field_group(array(
        'key' => 'group_lesson_details',
        'title' => 'Lesson Meta & Content',
        // Fields for stats and downloads have moved to custom meta-boxes
        // (dfh_lesson_stats_box, dfh_lesson_downloads_box) to avoid ACF
        // plugin dependency. Keep the group intentionally lightweight.
        'fields' => array(),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'lesson',
                ),
            ),
        ),
    ));

    // Course-level syllabus selector
    acf_add_local_field_group(array(
        'key' => 'group_course_syllabus',
        'title' => 'Course Syllabus',
        'fields' => array(
            array(
                'key' => 'field_course_lessons',
                'label' => 'Select Root Lessons',
                'name' => 'course_root_lessons',
                'type' => 'relationship',
                'instructions' => 'Select the top-level Lessons (Modules) for this course.',
                'post_type' => array('lesson'),
                'return_format' => 'id',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'course',
                ),
            ),
        ),

    ));
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
function dfh_get_lesson_hierarchy_number($post_id = null)
{
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'lesson') {
        return '';
    }

    // Get ancestor chain from root down to current post
    $ancestors = get_post_ancestors($post->ID);
    $ancestors = array_reverse($ancestors); // Root first
    $ancestors[] = $post->ID; // Append current post

    $numbers = array();

    foreach ($ancestors as $index => $ancestor_id) {
        $current_post = get_post($ancestor_id);
        if (!$current_post) {
            continue;
        }

        // Find position among siblings with the same parent
        $siblings = get_posts(array(
            'post_type' => 'lesson',
            'posts_per_page' => -1,
            'post_parent' => $current_post->post_parent,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'fields' => 'ids',
        ));

        $position = array_search($current_post->ID, $siblings);
        $numbers[] = (false !== $position) ? ($position + 1) : 1;
    }

    // Return formatted string if it fits our structural depth (e.g., 1.2.3)
    if (!empty($numbers)) {
        return implode('.', $numbers);
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
function dfh_get_lesson_code($post_id = null)
{
    if (!$post_id) {
        $post = get_post();
        $post_id = $post ? $post->ID : null;
    }

    if (!$post_id) {
        return '';
    }

    // Try ACF first, then post meta
    if (function_exists('get_field')) {
        $m = get_field('lesson_module', $post_id);
        $c = get_field('lesson_chapter', $post_id);
        $p = get_field('lesson_page', $post_id);
    } else {
        $m = get_post_meta($post_id, 'lesson_module', true);
        $c = get_post_meta($post_id, 'lesson_chapter', true);
        $p = get_post_meta($post_id, 'lesson_page', true);
    }

    if ($m && $c && $p) {
        return sprintf('%d.%d.%d', (int) $m, (int) $c, (int) $p);
    }

    // Fall back to structural/hierarchy numbering
    return dfh_get_lesson_hierarchy_number($post_id);
}

/**
 * Recursively renders a nested list of child lessons for a given parent lesson.
 *
 * @param int $parent_id The ID of the parent lesson.
 * @return string HTML unordered list of child lessons, or empty string if none.
 */

function dfh_render_lesson_children($parent_id)
{
    $children = get_posts(array(
        'post_type' => 'lesson',
        'post_parent' => $parent_id,
        'posts_per_page' => -1,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    ));

    if (!$children)
        return '';

    $output = '<ul class="sub-lesson-list">';
    foreach ($children as $child) {
        $output .= '<li><a href="' . get_permalink($child->ID) . '">' . get_the_title($child->ID) . '</a>';
        $output .= dfh_render_lesson_children($child->ID); // Recursion
        $output .= '</li>';
    }
    $output .= '</ul>';

    return $output;
}

