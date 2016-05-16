<?php

/**
* -------------------------------------------------
* Filtering by postmeta fields (custom fields)
* -------------------------------------------------
*/

add_filter( 'wpv_filter_query', 'wpv_filter_post_custom_field', 10, 2 );

function wpv_filter_post_custom_field( $query, $view_settings ) {

	global $WP_Views;
	
	$meta_keys = array();
	$meta_queries = array();
	$view_id = $WP_Views->get_current_view();
	foreach ( array_keys( $view_settings ) as $key ) {
		if ( 
			strpos( $key, 'custom-field-' ) === 0 
			&& strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' )
		) {
			if ( empty( $meta_keys ) ) {
				$meta_keys = $WP_Views->get_meta_keys();
			}
			$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
			$name = substr( $name, strlen( 'custom-field-' ) );
			$type = $view_settings['custom-field-' . $name . '_type'];
			$compare = $view_settings['custom-field-' . $name . '_compare'];
			$value = $view_settings['custom-field-' . $name . '_value'];
			
			// TODO add filter here: what happens when a meta_name contains a space AND an underscore?
			// We need a final solution, I prefer to use a %%SPACE%% placeholder and avoid the above mapping (which we should keepfor backwards compatibility)
			$meta_name = $name;
			if ( ! in_array( $meta_name, $meta_keys ) ) { // this is needed for fields with keys containing spaces - we map those spaces to underscores when creating the filter
				$meta_name = str_replace( '_', ' ', $meta_name );
			}
			
			
			/**
			* Filter wpv_filter_custom_field_filter_original_value
			*
			* @param $value			string	The value coming from the View settings filter before passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $meta_name		string	The key of the custom field being used to filter by
			* @param $view_id		integer	The ID of the View being displayed
			*
			* $value comes from the View settings. It's a string containing a single-value or a comma-separated list of single-values if the filter needs more than one value (for IN, NOT IN, BETWEEN and NOT BETWEEN comparisons)
			* Each individual single-value element in the list can use any of the following formats:
			* (string|numeric) if the single-value item is fixed
			* (string) URL_PARAM(parameter) if the filter is done via a URL param "parameter"
			* (string) VIEW_PARAM(parameter) if the filter is done via a [wpv-view] shortcode attribute "parameter"
			* (string) NOW() | TODAY() | FUTURE_DAY() | PAST_DAY() | THIS_MONTH() | FUTURE_MONTH() | PAST_MONTH() | THIS_YEAR() | FUTURE_YEAR() | PAST_YEAR() | SECONDS_FROM_NOW() | MONTHS_FROM_NOW() | YEARS_FROM_NOW() | DATE()
			*
			* @since 1.4
			*/
			
			$value = apply_filters( 'wpv_filter_custom_field_filter_original_value', $value, $meta_name, $view_id );
			
			/**
			* Filter wpv_resolve_variable_values
			*
			* @param $value the value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $resolve_attr Array containing the filters that need to be applied as resolvers
			*
			* @since 1.8
			*/
			
			$resolve_attr = array(
				'filters' => array( 'url_parameter', 'shortcode_attribute', 'date_timestamp', 'framework_value' )
			);
			$value = apply_filters( 'wpv_resolve_variable_values', $value, $resolve_attr );

			/**
			* Filter wpv_filter_custom_field_filter_processed_value
			*
			* @param $value			string	The value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $meta_name		string	The key of the custom field being used to filter by
			* @param $view_id		integer	The ID of the View being displayed
			*
			* @since 1.4
			*/
			
			$value = apply_filters( 'wpv_filter_custom_field_filter_processed_value', $value, $meta_name, $view_id );
			
			/**
			* Filter wpv_filter_custom_field_filter_type
			*
			* @param $type the type coming from the View settings filter: <CHAR>, <NUMERIC>, <BINARY>, <DATE>, <DATETIME>, <DECIMAL>, <SIGNED>, <TIME>, <UNSIGNED>
			* @param $meta_name the key of the custom field being used to filter by
			* @param $view_id the ID of the View being displayed
			*
			* @since 1.6
			*/
			
			$type = apply_filters( 'wpv_filter_custom_field_filter_type', $type, $meta_name, $view_id );
			
			$has_meta_query = wpv_resolve_meta_query( $meta_name, $value, $type, $compare );
			if ( $has_meta_query ) {
				$meta_queries[] = $has_meta_query;
			}
		}
	}
	
	//Set field relation
    if ( count( $meta_queries ) ) {
		$query['meta_query'] = $meta_queries;
        $query['meta_query']['relation'] = isset( $view_settings['custom_fields_relationship'] ) ? $view_settings['custom_fields_relationship'] : 'AND';
    }

    return $query;
}

/**
* wpv_get_custom_field_view_params
*
* This might be deprecated, but does not hurt
* Maybe add a _doing_it_wrong call_user_func
*/

function wpv_get_custom_field_view_params( $view_settings ) {
    $pattern = '/VIEW_PARAM\(([^(]*?)\)/siU';
	$results = array();
	foreach ( array_keys( $view_settings ) as $key ) {
		if (
			strpos( $key, 'custom-field-' ) === 0 
			&& strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' )
		) {
			$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
			$name = substr( $name, strlen( 'custom-field-' ) );
			$value = $view_settings[ 'custom-field-' . $name . '_value' ];
		    if ( preg_match_all( $pattern, $value, $matches, PREG_SET_ORDER ) ) {
		        foreach ( $matches as $match ) {
					$results[] = $match[1];
				}
			}
		}
	}
	return $results;
}

/**
* -------------------------------------------------
* Filtering by termmeta fields
* -------------------------------------------------
*/

add_filter( 'wpv_filter_taxonomy_query', 'wpv_taxonomy_query_termmeta_filters', 40, 2 );

function wpv_taxonomy_query_termmeta_filters( $tax_query_settings, $view_settings ) {

	global $WP_Views;
	
	$termmeta_queries = array();
	$view_id = $WP_Views->get_current_view();
	foreach ( $view_settings as $index => $value ) {
		if ( preg_match( "/termmeta-field-(.*)_type/", $index, $match ) ) {
			$field = $match[1];
			$type = $value;
			$compare = $view_settings['termmeta-field-' . $field . '_compare'];
			$value = $view_settings['termmeta-field-' . $field . '_value'];
			
			/**
			* Filter wpv_filter_termmeta_field_filter_original_value
			*
			* @param $value		string	The value coming from the View settings filter before passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $field		string	The key of the termmeta field being used to filter by
			* @param $view_id	integer	The ID of the View being displayed
			*
			* $value comes from the View settings. It's a string containing a single-value or a comma-separated list of single-values if the filter needs more than one value (for IN, NOT IN, BETWEEN and NOT BETWEEN comparisons)
			* Each individual single-value element in the list can use any of the following formats:
			* (string|numeric) if the single-value item is fixed
			* (string) URL_PARAM(parameter) if the filter is done via a URL param "parameter"
			* (string) VIEW_PARAM(parameter) if the filter is done via a [wpv-view] shortcode attribute "parameter"
			* (string) NOW() | TODAY() | FUTURE_DAY() | PAST_DAY() | THIS_MONTH() | FUTURE_MONTH() | PAST_MONTH() | THIS_YEAR() | FUTURE_YEAR() | PAST_YEAR() | SECONDS_FROM_NOW() | MONTHS_FROM_NOW() | YEARS_FROM_NOW() | DATE()
			*
			* @since 1.12
			*/
			
			$value = apply_filters( 'wpv_filter_termmeta_field_filter_original_value', $value, $field, $view_id );
			
			/**
			* Filter wpv_resolve_variable_values
			*
			* @param $value the value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $resolve_attr Array containing the filters that need to be applied as resolvers
			*
			* @since 1.8
			*/
			
			$resolve_attr = array(
				'filters' => array( 'url_parameter', 'shortcode_attribute', 'date_timestamp', 'framework_value' )
			);
			$value = apply_filters( 'wpv_resolve_variable_values', $value, $resolve_attr );
			
			/**
			* Filter wpv_filter_termmeta_field_filter_processed_value
			*
			* @param $value			string	The value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $field			string	The key of the termmeta field being used to filter by
			* @param $view_id		integer	The ID of the View being displayed
			*
			* @since 1.12
			*/
			
			$value = apply_filters( 'wpv_filter_termmeta_field_filter_processed_value', $value, $field, $view_id );
			
			/**
			* Filter wpv_filter_termmeta_field_filter_type
			*
			* @param $type the type coming from the View settings filter: <CHAR>, <NUMERIC>, <BINARY>, <DATE>, <DATETIME>, <DECIMAL>, <SIGNED>, <TIME>, <UNSIGNED>
			* @param $field the key of the termmeta field being used to filter by
			* @param $view_id the ID of the View being displayed
			*
			* @since 1.12
			*/
			
			$type = apply_filters( 'wpv_filter_termmeta_field_filter_type', $type, $field, $view_id );
			
			$has_meta_query = wpv_resolve_meta_query( $field, $value, $type, $compare );
			if ( $has_meta_query ) {
				$termmeta_queries[] = $has_meta_query;
			}
		}
	}
	//Set termmeta relation
    if ( count( $termmeta_queries ) ) {
		$tax_query_settings['meta_query'] = $termmeta_queries;
        $tax_query_settings['meta_query']['relation'] = isset( $view_settings['termmeta_fields_relationship'] ) ? $view_settings['termmeta_fields_relationship'] : 'AND';
    }
	
	return $tax_query_settings;
}

/**
* -------------------------------------------------
* Filtering by usermeta fields
* -------------------------------------------------
*/

/**
* wpv_users_query_usermeta_filters
*
* Filter hooked before query and user basic fields
*
* @param $args				array	Arguments to be passed to WP_User_Query
* @param $view_settings		array
*
* @return $args
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_user_query', 'wpv_users_query_usermeta_filters', 70, 2 );

function wpv_users_query_usermeta_filters( $args, $view_settings ) {
	
	global $WP_Views;
	
	$usermeta_queries = array();
	$view_id = $WP_Views->get_current_view();
	foreach ( $view_settings as $index => $value ) {
		if ( preg_match( "/usermeta-field-(.*)_type/", $index, $match ) ) {
			$field = $match[1];
			$type = $value;
			$compare = $view_settings['usermeta-field-' . $field . '_compare'];
			$value = $view_settings['usermeta-field-' . $field . '_value'];
			
			/**
			* Filter wpv_filter_usermeta_field_filter_original_value
			*
			* @param $value		string	The value coming from the View settings filter before passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $field		string	The key of the usermeta field being used to filter by
			* @param $view_id	integer	The ID of the View being displayed
			*
			* $value comes from the View settings. It's a string containing a single-value or a comma-separated list of single-values if the filter needs more than one value (for IN, NOT IN, BETWEEN and NOT BETWEEN comparisons)
			* Each individual single-value element in the list can use any of the following formats:
			* (string|numeric) if the single-value item is fixed
			* (string) URL_PARAM(parameter) if the filter is done via a URL param "parameter"
			* (string) VIEW_PARAM(parameter) if the filter is done via a [wpv-view] shortcode attribute "parameter"
			* (string) NOW() | TODAY() | FUTURE_DAY() | PAST_DAY() | THIS_MONTH() | FUTURE_MONTH() | PAST_MONTH() | THIS_YEAR() | FUTURE_YEAR() | PAST_YEAR() | SECONDS_FROM_NOW() | MONTHS_FROM_NOW() | YEARS_FROM_NOW() | DATE()
			*
			* @since 1.12
			*/
			
			$value = apply_filters( 'wpv_filter_usermeta_field_filter_original_value', $value, $field, $view_id );
			
			/**
			* Filter wpv_resolve_variable_values
			*
			* @param $value the value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $resolve_attr Array containing the filters that need to be applied as resolvers
			*
			* @since 1.8
			*/
			
			$resolve_attr = array(
				'filters' => array( 'url_parameter', 'shortcode_attribute', 'date_timestamp', 'framework_value' )
			);
			$value = apply_filters( 'wpv_resolve_variable_values', $value, $resolve_attr );
			
			/**
			* Filter wpv_filter_usermeta_field_filter_processed_value
			*
			* @param $value			string	The value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $field			string	The key of the usermeta field being used to filter by
			* @param $view_id		integer	The ID of the View being displayed
			*
			* @since 1.12
			*/
			
			$value = apply_filters( 'wpv_filter_usermeta_field_filter_processed_value', $value, $field, $view_id );
			
			/**
			* Filter wpv_filter_usermeta_field_filter_type
			*
			* @param $type the type coming from the View settings filter: <CHAR>, <NUMERIC>, <BINARY>, <DATE>, <DATETIME>, <DECIMAL>, <SIGNED>, <TIME>, <UNSIGNED>
			* @param $field the key of the usermeta field being used to filter by
			* @param $view_id the ID of the View being displayed
			*
			* @since 1.8
			*/
			
			$type = apply_filters( 'wpv_filter_usermeta_field_filter_type', $type, $field, $view_id );
			
			$has_meta_query = wpv_resolve_meta_query( $field, $value, $type, $compare );
			if ( $has_meta_query ) {
				$usermeta_queries[] = $has_meta_query;
			}
		}
	}
	//Set usermeta relation
    if ( count( $usermeta_queries ) ) {
		$args['meta_query'] = $usermeta_queries;
        $args['meta_query']['relation'] = isset( $view_settings['usermeta_fields_relationship'] ) ? $view_settings['usermeta_fields_relationship'] : 'AND';
    }
	
	return $args;
}

/**
* -------------------------------------------------
* Common functions
* -------------------------------------------------
*/

/**
* wpv_resolve_meta_query
*
* Resolves if a meta_query is indeed needed, for filters by meta fields
*
* @param $key (string) The field key
* @param $value (string) The resolved value to filter by
* @param $type (string) The filtering data type
* @param $compare (string) The filtering comparison type
*
* @return (array|boolean) The meta_query instance on success, false otherwise
*
* @since 1.8.0
*/

function wpv_resolve_meta_query( $key, $value, $type, $compare ) {
	global $no_parameter_found;
	$return = false;
	if ( $value == $no_parameter_found ) {
		return false;
	}
	if (
		$compare == 'BETWEEN' 
		|| $compare == 'NOT BETWEEN'
	) {
		// We need to make sure we have values for min and max.
		// If any of the values is missing we will transform into lower-than or greater-than filters
		// TODO: Note that we are not covering the case where min or max is an empty constant value, we might want to review that
		$values = explode( ',', $value );
		$values = array_map( 'trim', $values );
		if ( count( $values ) == 0 ) {
			return false;
		}
		if ( count( $values ) == 1 ) {
			if ( $values[0] == $no_parameter_found ) {
				return false;
			}
			if ( $compare == 'BETWEEN' ) {
				$compare =  '>=';
			} else {
				$compare =  '<=';
			}
			$value = $values[0];
		} else {
			if (
				$values[0] == $no_parameter_found 
				&& $values[1] == $no_parameter_found
			) {
				return false;
			}
			if ( $values[0] == $no_parameter_found ) {
				if ( $compare == 'BETWEEN' ) {
					$compare = '<=';
				} else {
					$compare = '>=';
				}
				$value = $values[1];
			} elseif ( $values[1] == $no_parameter_found ) {
				if ( $compare == 'BETWEEN' ) {
					$compare = '>=';
				} else {
					$compare = '<=';
				}
				$value = $values[0];
			}
		}
	}
	
	// If $value still contains a $no_parameter_found value, no filter should be applied
	// Because it means there is a non-existing or empty URL parameter
	// TODO: on shortcode attributes, an empty value as two commas will pass this test
	// Maybe this is OK, as we might want to filter by an empty value too, which is not possible on filters by URL parameter
	
	if ( strpos( $value, $no_parameter_found ) !== false ) {
		return false;
	}
	
	// Now that we are sure that the filter should be applied, even for empty values, let's do it
	
	if ( 
		$compare == 'IN' 
		|| $compare == 'NOT IN' 
	) {
		// WordPress query expects an array in this case
		$original_value = $value;
		$value = explode( ',', $value );
		if ( count( $value ) > 1 ) {
			// Add comma-separated combinations of meta values, since a legit value containing a comma might have been removed
			$value = wpv_recursive_add_comma_meta_values( $value );
			// Also add the original one, as it might be a legitimate value containing several commas instead of a comma-separated list
			$value[] = $original_value;
		}
	}
	
	// Sanitization
	if ( is_array( $value ) ) {
		foreach ( $value as $v_key => $val ) {
			$value[$v_key] = stripslashes( rawurldecode( sanitize_text_field( trim( $val ) ) ) );
		}
	} else {
		$value = stripslashes( rawurldecode( sanitize_text_field( trim( $value ) ) ) );
	}
	
	if ( 
		in_array( $compare, array( '>=', '<=', '>', '<' ) )
		&& (
			empty( $value ) 
			&& ! is_numeric( $value ) 
		)
	) {
		// do nothing as we are comparing greater than / lower than to an empty value
		return false;
	} else {
		$return = array(
			'key'		=> $key,
			'value'		=> $value,
			'type'		=> $type,
			'compare'	=> $compare
		);
	}
	
	return $return;
}

function wpv_recursive_add_comma_meta_values( $values ) {
	$values_orig = array_reverse( $values );
	$values_aux = array();
	$values_end = array();
	if ( count( $values ) > 1 ) {
		foreach ( $values_orig as $v_key => $v_val ) {
			if ( count( $values_aux ) > 0 ) {
				foreach ( $values_aux as &$v_aux ) {
					$values_end[] = $v_val . ',' . $v_aux;
					$v_aux = $v_val . ',' . $v_aux;
				}
			}
			$values_end[] = $v_val;
			$values_aux[] = $v_val;
		}
	} else {
		$values_end = $values;
	}
	return $values_end;
}

/**
* wpv_filter_meta_field_requires_framework_values
*
* Whether the current View requires framework data for the filter by meta fields
*
* @param $state				boolean	The state until this filter is applied
* @param $view_settings
*
* @return $state			boolean
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_framework_values', 'wpv_filter_meta_field_requires_framework_values', 20, 2 );

function wpv_filter_meta_field_requires_framework_values( $state, $view_settings ) {
	if ( $state ) {
		return $state;
	}
	if ( $view_settings['query_type'][0] == 'posts' ) {
		foreach ( $view_settings as $key => $value ) {
			if ( 
				preg_match( "/custom-field-(.*)_value/", $key, $res )
				&& preg_match( "/FRAME_KEY\(([^\)]+)\)/", $value, $shortcode ) 
			) {
				$state = true;
				break;
			}
		}
	}
	if ( $state ) {
		return $state;
	}
	if ( $view_settings['query_type'][0] == 'taxonomy' ) {
		foreach ( $view_settings as $key => $value ) {
			if ( 
				preg_match( "/termmeta-field-(.*)_value/", $key, $res )
				&& preg_match( "/FRAME_KEY\(([^\)]+)\)/", $value, $shortcode ) 
			) {
				$state = true;
				break;
			}
		}
	}
	if ( $state ) {
		return $state;
	}
	if ( $view_settings['query_type'][0] == 'users' ) {
		foreach ( $view_settings as $key => $value ) {
			if ( 
				preg_match( "/usermeta-field-(.*)_value/", $key, $res )
				&& preg_match( "/FRAME_KEY\(([^\)]+)\)/", $value, $shortcode ) 
			) {
				$state = true;
				break;
			}
		}
	}
	return $state;
}