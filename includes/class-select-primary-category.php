<?php

/**
 * Class Select_Primary_Category
 */
class Select_Primary_Category {

    /**
     * Select_Primary_Category constructor.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'spc_add_metabox' ) );
        add_action( 'save_post', array( $this, 'spc_save_metabox' ) );
    }

    /**
     * add metabox function
     */
    public function spc_add_metabox() {
		// get the generated lists
		$items = $this->spc_build_lists();

		// get post type list
		$post_types = $items->post_type_list;

		// if there is any post type in the array
		if ( ! empty( $post_types ) ) {
		    // add metabox for each post type
            foreach ( $post_types as $post_type ) {
                add_meta_box (
                    'select_primary_category', // metabox id
                    'Select Primary Category', // metabox title
                    array( $this, 'spc_metabox_callback' ), // metabox render callback function
                    $post_type, // screen to display metabox e.g post type
                    'side', // context
                    'high' // priority
                );
            }
        }
    }

    /**
     * metabox render callback function
     *
     * @param $post current post type object
     */
    public function spc_metabox_callback( $post ) {

        // add hidden nonce field to verify the save metabox action. (security)
        wp_nonce_field( 'spc_category_nonce', 'spc_category_nonce_field' );

        // get the generated lists
        $items = $this->spc_build_lists();

    	$primary_category = '';
    
    	// get saved primary category id
        $primary_selected_category = get_post_meta( $post->ID, 'select_primary_category', true );
    
    	// if primary selected category is not empty
    	if ( $primary_selected_category != '' ) {
    	    // assign selected category to primary category
            // used for selectbox metabox selected category
    		$primary_category = $primary_selected_category;
    	}
    
    	// get list of categories associated with post
        $post_categories = $items->categories_list;

        // create html for the selectbox select
        $html = '';
        $html .= '<select class="widefat" name="select_primary_category" id="select_primary_category">';
        $html .= '<option value="0" >-- select category --</option>';
    	// if there are categories then create the selectbox options from each category
        if ( ! empty( $post_categories ) ) {
            foreach( $post_categories as $category ) {
                $html .= '<option value="' . $category->name . '" ' . selected( $primary_category, $category->name, false ) . '>' . $category->name . '</option>';
            }
        }
        $html .= '</select>';
        // metabox select description.
        $html .= '<small>Select a primary category for your post.</small>';

        // render the created selectbox
    	echo $html;
    }

    /**
     * Save the metabox on update or publish post
     *
     * @param $post_id current post id.
     */
    public function spc_save_metabox( $post_id ) {

        // return if nonce field is not set
        // or nonce is not verified
        if( ! isset( $_POST['spc_category_nonce_field'] ) || ! wp_verify_nonce( $_POST['spc_category_nonce_field'],'spc_category_nonce' ) ) {
           return;
        }

        // return if current use does not have capabilities to edit post
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // check if select_primary_category selectbox has any selected option
        if ( isset( $_POST[ 'select_primary_category' ] ) ) {
            // sanatize text field value
    		$primary_category = sanitize_text_field( $_POST[ 'select_primary_category' ] );
    		// update post meta and store new selected selectbox option
    		update_post_meta( $post_id, 'select_primary_category', $primary_category );
    	}
    }

    /**
     * Build item lists for use in other functions
     *
     * @return $item stdClass
     */
	public function spc_build_lists() {
        // create new stdClass
		$item = new stdClass();

		// post type args
		$args = array(
			'public' => true, // only get publically accessable post types
			'_builtin' => false // remove builtin post types
		);
		// generate post type list
		$item->post_type_list = get_post_types( $args, 'names' );
		// add buildin 'post' post type to post_type_list
		$item->post_type_list['post'] = 'post';

		// generate categories list
		$item->categories_list = get_the_category();

		// return item containg all lists
		return $item;
	}
}