<?php


class Wp_Pargo_Deactivator
{
    /**
     * On activation create a page and remember it.
     *
     * Create a page named "Saved", add a shortcode that will show the saved items
     * and remember page id in our database.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        // Saved Page Arguments
        $saved_page_args = array(
            'post_title' => __('Saved', 'toptal-save'),
            'post_content' => '[toptal-saved]',
            'post_status' => 'publish',
            'post_type' => 'page'
        );
        // Insert the page and get its id.
        $saved_page_id = wp_insert_post($saved_page_args);
        // Save page id to the database.
        add_option('toptal_save_saved_page_id', $saved_page_id);
    }
}
