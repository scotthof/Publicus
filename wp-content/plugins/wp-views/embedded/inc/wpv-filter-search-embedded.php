<?php

/**
* -------------------------------------------------
* Filtering for Views listing posts
* -------------------------------------------------
*/

add_filter('wpv_filter_query', 'wpv_filter_post_search', 10, 2);
function wpv_filter_post_search($query, $view_settings) {
    
    if ( isset( $view_settings['post_search_value'] ) && $view_settings['post_search_value'] != '' && isset( $view_settings['search_mode'] ) && $view_settings['search_mode'][0] == 'specific' ) {
        $query['s'] = $view_settings['post_search_value'];
    }
    if ( isset( $view_settings['search_mode'] ) && isset( $_GET['wpv_post_search'] ) ) {
        $search_term = rawurldecode( sanitize_text_field( $_GET['wpv_post_search'] ) );
        if ( !empty( $search_term ) ) {
			$query['s'] = $search_term;
		}
    }
    if ( isset( $view_settings['post_search_content'] ) && 'just_title' == $view_settings['post_search_content'] && isset( $query['s'] ) ) {
		add_filter( 'posts_search', 'wpv_search_by_title_only', 500, 2 );
    }
    
    return $query;
}

function wpv_search_by_title_only( $search, &$wp_query ) {
    global $wpdb;
    if ( empty( $search ) )
        return $search; // skip processing - no search term in query
    $q = $wp_query->query_vars;
    $n = ! empty( $q['exact'] ) ? '' : '%';
    $search = '';
    $searchand = "";
    foreach ( (array) $q['search_terms'] as $term ) {
		$term = $n . wpv_esc_like( $term ) . $n;
		$search .= $wpdb->prepare( $searchand . "( $wpdb->posts.post_title LIKE %s )", $term );
		$searchand = " AND ";
    }
    if ( ! empty( $search ) ) {
        $search = " AND ( {$search} ) ";
        if ( ! is_user_logged_in() )
            $search .= " AND ( $wpdb->posts.post_password = '' ) ";
    }
    return $search;
}

/**
* -------------------------------------------------
* Filtering for Views listing taxonomy terms
* -------------------------------------------------
*/

add_filter( 'wpv_filter_taxonomy_query', 'wpv_filter_taxonomy_search', 10, 2 );
function wpv_filter_taxonomy_search( $tax_query_settings, $view_settings ) {
    if ( isset( $view_settings['taxonomy_search_mode'] ) ) {
		if ( $view_settings['taxonomy_search_mode'][0] == 'specific' ) {
			if (
				isset( $view_settings['taxonomy_search_value'] ) 
				&& $view_settings['taxonomy_search_value'] != '' 
			) {
				$tax_query_settings['search'] = sanitize_text_field( $view_settings['taxonomy_search_value'] );
			}
		} else if ( isset( $_GET['wpv_taxonomy_search'] ) ) {
			$search_term = rawurldecode( sanitize_text_field( $_GET['wpv_taxonomy_search'] ) );
			if ( ! empty( $search_term ) ) {
				$tax_query_settings['search'] = $search_term;
			}
		}
	}
    
    return $tax_query_settings;
}
