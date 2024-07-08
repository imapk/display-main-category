<?php
// this code can be added either in theme's function.php file or you can also create a new file in /plugins/ folder
// Add a meta box to dispaly in dashboard
function add_primary_category_meta_box() {
    add_meta_box(
        'primary_category_meta_box', // ID
        __('Primary Category', 'text_domain'), // Title
        'display_primary_category_meta_box', // Callback
        'post', // Post type
        'side', // Context
        'high' // Priority
    );
}
add_action('add_meta_boxes', 'add_primary_category_meta_box');

// Display the meta box
function display_primary_category_meta_box($post) {
    $primary_category = get_post_meta($post->ID, '_primary_category', true);
    $categories = get_the_category($post->ID);
    wp_nonce_field(basename(__FILE__), 'primary_category_nonce');
    ?>
    <p>
        <label for="primary-category"><?php _e('Select Primary Category', 'text_domain'); ?></label>
        <select name="primary-category" id="primary-category" class="widefat">
            <option value=""><?php _e('None', 'text_domain'); ?></option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($primary_category, $category->term_id); ?>>
                    <?php echo esc_html($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

// Save the primary category meta data
function save_primary_category_meta_data($post_id) {
    if (!isset($_POST['primary_category_nonce']) || !wp_verify_nonce($_POST['primary_category_nonce'], basename(__FILE__))) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    if (isset($_POST['post_type']) && 'post' === $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }

    $new_primary_category = (isset($_POST['primary-category']) ? sanitize_text_field($_POST['primary-category']) : '');
    update_post_meta($post_id, '_primary_category', $new_primary_category);
}
add_action('save_post', 'save_primary_category_meta_data');


 
// Use Code to show the selected Primary Category on front end
// To display the primary category on the front end, you can add the following code to your theme's template files, such as single.php or content.php.
$primary_category_id = get_post_meta(get_the_ID(), '_primary_category', true);
if ($primary_category_id) {
    $primary_category = get_category($primary_category_id);
    if ($primary_category) {
        echo '<p class="primary-category">Primary Category: <a href="' . esc_url(get_category_link($primary_category)) . '">' . esc_html($primary_category->name) . '</a></p>';
    }
}


    
    // Show the selected Primary Category on front end without adding any code
    // this code can be added either in theme's function.php file or you can also create a new file in /plugins/ folder
    // Filter the category list to display only the primary category
function filter_the_category_list($thelist, $separator = '', $parents = '', $post_id = false) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Get the primary category ID
    $primary_category_id = get_post_meta($post_id, '_primary_category', true);

    if ($primary_category_id) {
        $primary_category = get_category($primary_category_id);
        if ($primary_category) {
            return '<a href="' . get_category_link($primary_category->term_id) . '">' . esc_html($primary_category->name) . '</a>';
        }
    }

    return ''; // Return an empty string if no primary category is set
}
add_filter('the_category', 'filter_the_category_list', 10, 4);
?>
