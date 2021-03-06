<?php

/**
 * Add a filter to add the query by author to the $query
 *
 * We need to set a higher priority than the limit filter has because we use $query['post__in'] = array('0') on failure
 */

add_filter( 'wpv_filter_query', 'wpv_filter_post_id', 13, 2 );

function wpv_filter_post_id( $query, $view_settings ) {
	// @todo are IDs adjusted in WPML? Maybe yes, because it filters the post__in and post__not_in arguments
	if ( isset( $view_settings['id_mode'][0] ) ) {
		$include = true;
		$show_id_array = array();		
		if ( 
			isset( $view_settings['id_in_or_out'] ) 
			&& 'out' == $view_settings['id_in_or_out'] 
		) {
			$include = false;
		}
		switch ( $view_settings['id_mode'][0] ) {
			case 'by_ids':
				if (
					isset( $view_settings['post_id_ids_list'] ) 
					&& '' != $view_settings['post_id_ids_list']
				) {
					$id_ids_list = explode( ',', $view_settings['post_id_ids_list'] );
					foreach ( $id_ids_list as $id_candidate ) {
						$id_candidate = (int) trim( $id_candidate );
						$id_candidate = apply_filters( 'translate_object_id', $id_candidate, 'any', true, null );
						$show_id_array[] = $id_candidate;
					}
				}
				else {
					$show_id_array = null;
				}
				break;
			case 'by_url':
				if (
					isset( $view_settings['post_ids_url'] ) 
					&& '' != $view_settings['post_ids_url']
				) {
					$id_parameter = $view_settings['post_ids_url'];	
					if ( isset( $_GET[$id_parameter] ) ) {
						$ids_to_load = $_GET[$id_parameter];
						if ( is_array( $ids_to_load ) ) {
							if ( 
								0 == count( $ids_to_load ) 
								|| '' == $ids_to_load[0] 
							) {
								$show_id_array = null;
							} else {
								foreach ( $ids_to_load as $id_candidate ) {
									$id_candidate = (int) trim( $id_candidate );
									$id_candidate = apply_filters( 'translate_object_id', $id_candidate, 'any', true, null );
									$show_id_array[] = $id_candidate;
								}
							}
						} else {
							if ( '' == $ids_to_load ) {
								$show_id_array = null;
							} else {
								$id_candidate = (int) trim( $ids_to_load );
								$id_candidate = apply_filters( 'translate_object_id', $id_candidate, 'any', true, null );
								$show_id_array[] = $id_candidate;
							}
						}
					} else {
						$show_id_array = null;
					}
				}
				break;
			case 'shortcode':
				global $WP_Views;
				if (
					isset( $view_settings['post_ids_shortcode'] ) 
					&& '' != $view_settings['post_ids_shortcode']
				) {
					$id_shortcode = $view_settings['post_ids_shortcode'];	
					$view_attrs = $WP_Views->get_view_shortcodes_attributes();
					if ( 
						isset( $view_attrs[$id_shortcode] ) 
						&& '' != $view_attrs[$id_shortcode]
					) {
						$ids_to_load = explode( ',', $view_attrs[$id_shortcode] );
						if ( count( $ids_to_load ) > 0 ) {
							foreach ( $ids_to_load as $id_candidate ) {
								$id_candidate = (int) trim( $id_candidate );
								$id_candidate = apply_filters( 'translate_object_id', $id_candidate, 'any', true, null );
								$show_id_array[] = $id_candidate;
							}
						}
					} else {
						$show_id_array = null;
					}
				}
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( $WP_Views_fapi->framework_valid ) {
					if (
						isset( $view_settings['post_ids_framework'] ) 
						&& '' != $view_settings['post_ids_framework']
					) {
						$post_ids_framework = $view_settings['post_ids_framework'];
						$post_ids_candidates = $WP_Views_fapi->get_framework_value( $post_ids_framework, array() );
						if ( ! is_array( $post_ids_candidates ) ) {
							$post_ids_candidates = explode( ',', $post_ids_candidates );
						}
						if ( count( $post_ids_candidates ) > 0 ) {
							foreach ( $post_ids_candidates as $id_candidate ) {
								if ( is_numeric( $id_candidate ) ) {
									$id_candidate = (int) trim( $id_candidate );
									$id_candidate = apply_filters( 'translate_object_id', $id_candidate, 'any', true, null );
									$show_id_array[] = $id_candidate;
								}
							}
						}
					}
				} else {
					$show_id_array = null;
				}
				break;
		}
		if ( isset( $show_id_array ) ) {
			if ( count( $show_id_array ) > 0 ) {
				if ( $include ) {
					if ( isset( $query['post__in'] ) ) {
						$query['post__in'] = array_intersect( (array) $query['post__in'], $show_id_array );
						$query['post__in'] = array_values( $query['post__in'] );
						if ( empty( $query['post__in'] ) ) {
							$query['post__in'] = array( '0' );
						}
					} else {
						$query['post__in'] = $show_id_array;
					}
				} else {
					if ( isset( $query['post__not_in'] ) ) {
						$query['post__not_in'] = array_merge( (array) $query['post__not_in'], $show_id_array );
					} else {
						$query['post__not_in'] = $show_id_array;
					}
				}
			} else {
				// @todo review this, we might not want to apply only when ( ! isset )
				if ( $include ) {
					if ( ! isset( $query['post__in'] ) ) {
						$query['post__in'] = array('0');
					}
				} else {
					if ( ! isset( $query['post__not_in'] ) ) {
						$query['post__not_in'] = array('0');
					}
				}
			}
		}	
    }
	return $query;
}

/**
* wpv_filter_id_requires_framework_values
*
* Whether the current View requires framework data for the filter by post ID
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_framework_values', 'wpv_filter_id_requires_framework_values', 20, 2 );

function wpv_filter_id_requires_framework_values( $state, $view_settings ) {
	if ( $state ) {
		return $state;
	}
	if ( isset( $view_settings['id_mode'] ) && isset( $view_settings['id_mode'][0] ) && $view_settings['id_mode'][0] == 'framework' ) {
		$state = true;
	}
	return $state;
}

/**
* wpv_filter_register_post_id_filter_shortcode_attributes
*
* Register the filter by post IDs on the method to get View shortcode attributes
*
* @since 1.10
*/

add_filter( 'wpv_filter_register_shortcode_attributes_for_posts', 'wpv_filter_register_post_id_filter_shortcode_attributes', 10, 2 );

function wpv_filter_register_post_id_filter_shortcode_attributes( $attributes, $view_settings ) {
	if (
		isset( $view_settings['id_mode'] ) 
		&& isset( $view_settings['id_mode'][0] ) 
		&& $view_settings['id_mode'][0] == 'shortcode' 
	) {
		$attributes[] = array (
			'query_type'	=> $view_settings['query_type'][0],
			'filter_type'	=> 'post_id',
			'filter_label'	=> __( 'Post ID', 'wpv-views' ),
			'value'			=> 'post_id',
			'attribute'		=> $view_settings['post_ids_shortcode'],
			'expected'		=> 'numberlist',
			'placeholder'	=> '10, 13, 21',
			'description'	=> __( 'Please type a comma separated list of post IDs', 'wpv-views' )
		);
	}
	return $attributes;
}

/**
* wpv_filter_register_post_id_filter_url_parameters
*
* Register the filter by post IDs on the method to get View URL parameters
*
* @since 1.11
*/

add_filter( 'wpv_filter_register_url_parameters_for_posts', 'wpv_filter_register_post_id_filter_url_parameters', 10, 2 );

function wpv_filter_register_post_id_filter_url_parameters( $attributes, $view_settings ) {
	if (
		isset( $view_settings['id_mode'] ) 
		&& isset( $view_settings['id_mode'][0] ) 
		&& $view_settings['id_mode'][0] == 'by_url' 
	) {
		$attributes[] = array (
			'query_type'	=> $view_settings['query_type'][0],
			'filter_type'	=> 'post_id',
			'filter_label'	=> __( 'Post ID', 'wpv-views' ),
			'value'			=> 'post_id',
			'attribute'		=> $view_settings['post_ids_url'],
			'expected'		=> 'numberlist',
			'placeholder'	=> '10, 13, 21',
			'description'	=> __( 'Please type a comma separated list of post IDs', 'wpv-views' )
		);
	}
	return $attributes;
}