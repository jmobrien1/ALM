<?php

/*

	Design Theme's Meta Options

	Copyright: 2012, D5 Creation, www.d5creation.com

	Based on the Simplest D5 Framework for WordPress

	Since D5 CORPORATE 1.0

*/





$prefix = 'sb_';

$meta_box = array(

    'id' => 'sb-meta-box',

    'title' => 'Design Theme Options',

    'page' => 'post',

	'context' => 'normal',

    'priority' => 'high',

    'fields' => array(

        array(

            'name' => 'Show in Slides',

            'id' => $prefix . 'ss',

            'type' => 'checkbox'

        )

    )

);



add_action('admin_menu', 'design_add_box');

// Add meta box

function design_add_box() {

    global $meta_box;

    add_meta_box($meta_box['id'], $meta_box['title'], 'design_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);

}





// Callback function to show fields in meta box

function design_show_box() {

    global $meta_box, $post;

    // Use nonce for verification

    echo '<input type="hidden" name="design_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<table class="form-table">';

    foreach ($meta_box['fields'] as $field) {

        // get current post meta data

        $meta = get_post_meta($post->ID, $field['id'], true);

        echo '<tr>',

                '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',

                '<td>';

        switch ($field['type']) {

            case 'text':

                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];

                break;

			

            case 'checkbox':

                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="unchecked"' : '', ' />';

                break;			

				

        }

        echo     '</td><td>',

            '</td></tr>';

    }

    echo '</table>';

}





add_action('save_post', 'design_save_data');

// Save data from meta box

function design_save_data($post_id) {

    global $meta_box;

    // verify nonce

    if (!wp_verify_nonce($_POST['design_meta_box_nonce'], basename(__FILE__))) {

        return $post_id;

    }

    // check autosave

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

        return $post_id;

    }

    // check permissions

    if ('page' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_id)) {

            return $post_id;

        }

    } elseif (!current_user_can('edit_post', $post_id)) {

        return $post_id;

    }

    foreach ($meta_box['fields'] as $field) {

        $old = get_post_meta($post_id, $field['id'], true);

        $new = $_POST[$field['id']];

        if ($new && $new != $old) {

            update_post_meta($post_id, $field['id'], $new);

        } elseif ('' == $new && $old) {

            delete_post_meta($post_id, $field['id'], $old);

        }

    }

}





?>