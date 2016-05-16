/**
* Views Meta Field Filter GUI - script
*
* Adds basic interaction for the Meta Field Filter
*
* One instance for each Meta filter: custom (postmeta), termmeta and usermeta
*
* @package Views
*
* @since 1.12
*/

var WPViews = WPViews || {};

WPViews.MetaFieldFilterGUI = function( $, meta ) {
	
	var self = this;
	
	self.meta = meta;
	self.meta_field = self.meta + '-field';
	
	self.view_id = $('.js-post_ID').val();
	
	self.spinner = '<span class="wpv-spinner ajax-loader">';
	
	self.filter_row							= '.js-wpv-filter-row-' + self.meta + '-field';
	self.filter_options_container_selector	= '.js-wpv-filter-row-' + self.meta + '-field .js-wpv-filter-edit';
	self.filter_edit_open_selector			= '.js-wpv-filter-' + self.meta + '-field-edit-open';
	self.filter_close_save_selector			= '.js-wpv-filter-' + self.meta + '-field-edit-ok';
	
	self.filter_current_options = $( self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select' ).serialize();
	
	//--------------------
	// Functions
	//--------------------
	
	self.manage_filter_changes = function() {
		WPViews.query_filters.clear_validate_messages( self.filter_row );
		if ( self.filter_current_options != $( self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select' ).serialize() ) {
			$( self.filter_close_save_selector )
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary js-wpv-section-unsaved')
				.html(
					WPViews.query_filters.icon_save + $( self.filter_close_save_selector ).data('save')
				);
			setConfirmUnload( true );
		} else {
			$( self.filter_close_save_selector )
			.addClass( 'button-secondary' )
			.removeClass( 'button-primary js-wpv-section-unsaved' )
			.html(
				WPViews.query_filters.icon_edit + $( self.filter_close_save_selector ).data('close')
			);
			$( self.filter_row  ).find('.unsaved').remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		}
	};
	
	self.meta_field_initialize_compare = function() {
		var wpv_allowed_values = 0;
		WPViews.query_filters.clear_validate_messages( self.filter_row );
		$( '.js-wpv-' + self.meta + '-field-compare-select' ).each( function() {
			var wpv_single_row = $( this ).parents( '.js-wpv-filter-multiple-element' ),
			thiz_inner = $( this ),
			thiz_inner_item = thiz_inner.parents( '.js-wpv-filter-multiple-element' );
			if ( thiz_inner.val() == 'BETWEEN' || thiz_inner.val() == 'NOT BETWEEN' ) {
				wpv_allowed_values = 2;
				thiz_inner_item
					.find( '.js-wpv-' + self.meta + '-field-add-value, .js-wpv-' + self.meta + '-field-remove-value' )
					.hide();
				divs = thiz_inner_item.find('.js-wpv-' + self.meta + '-field-value-div');
				if ( divs.length < 2 ) {
					// add another one.
					var clone = $( divs[0] ).clone();
					clone.find( '.js-wpv-' + self.meta + '-field-value-text' ).val('');
					clone.insertAfter( divs[0] );
					self.meta_field_initialize_compare_mode();
				}
			} else if ( thiz_inner.val() == 'IN' || thiz_inner.val() == 'NOT IN' ) {
				wpv_allowed_values = 100000;
				thiz_inner_item
					.find( '.js-wpv-' + self.meta + '-field-add-value' )
					.show();
				thiz_inner_item
					.find( '.js-wpv-' + self.meta + '-field-value-div' )
						.each( function( index ) {
							if ( index > 0 ) {
								$( this )
									.find( '.js-wpv-' + self.meta + '-field-remove-value' )
									.show();
							} else {
								$( this )
									.find( '.js-wpv-' + self.meta + '-field-remove-value' )
									.hide();
							}
						});
			} else {
				wpv_allowed_values = 1;
				thiz_inner_item
					.find( '.js-wpv-' + self.meta + '-field-add-value, .js-wpv-' + self.meta + '-field-remove-value' )
					.hide();
			}
			thiz_inner_item
				.find( '.js-wpv-' + self.meta + '-field-value-div' )
					.each( function() {
						if ( wpv_allowed_values > 0 ) {
							$( this ).show();
						} else {
							$( this ).remove();
						}
						wpv_allowed_values--;
					});
		});
	};
	
	self.meta_field_initialize_compare_mode = function() {
		$( '.js-wpv-' + self.meta + '-field-compare-mode' ).each( function() {
			self.meta_field_adjust_value_controls( this );
		});
	};
	
	self.meta_field_adjust_value_controls = function( item ) {
		// Show the text control depending on the compare function.
		var mode = $( item ).val(),
		value_div = $( item ).parents( '.js-wpv-' + self.meta + '-field-value-div' ),
		value_input = value_div.find('.js-wpv-' + self.meta + '-field-value-text');
		value_input
			.removeClass( 'js-wpv-filter-validate' )
			.data('type', 'none');
		value_div
			.find( '.js-wpv-' + self.meta + '-field-value-combo-input, .js-wpv-' + self.meta + '-field-value-combo-date, .js-wpv-' + self.meta + '-field-value-combo-framework' )
				.hide();
		switch( mode ) {
			case 'constant':
			case 'future_day':
			case 'past_day':
			case 'future_month':
			case 'past_month':
			case 'future_year':
			case 'past_year':
			case 'seconds_from_now':
			case 'months_from_now':
			case 'years_from_now':
				value_div
					.find( '.js-wpv-' + self.meta + '-field-value-combo-input' )
						.show();
				break;
			case 'url':
				value_div
					.find( '.js-wpv-' + self.meta + '-field-value-combo-input' )
						.show();
				value_input
					.addClass( 'js-wpv-filter-validate' )
					.data('type', 'url');
				break;
			case 'attribute':
				value_div
					.find( '.js-wpv-' + self.meta + '-field-value-combo-input' )
						.show();
				value_input
					.addClass( 'js-wpv-filter-validate' )
					.data('type', 'shortcode');
				break;
			case 'date':
				value_div
					.find( '.js-wpv-' + self.meta + '-field-value-combo-date' )
						.show();
				break;
			case 'framework':
				value_div
					.find( '.js-wpv-' + self.meta + '-field-value-combo-framework' )
						.show();
				break;
			default:
				
				break;
		}
	};
	
	self.meta_field_initialize_relationship = function() {
		if ( $( '.js-wpv-' + self.meta + '-field-compare-select' ).length > 1 ) {
			$( '.js-wpv-filter-' + self.meta + '-field-relationship-container' ).show();
		} else if ( $( '.js-wpv-' + self.meta + '-field-compare-select' ).length == 0 ) {
			$( '.js-filter-' + self.meta + '-field' ).remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		} else {
			$( '.js-wpv-filter-' + self.meta + '-field-relationship-container' ).hide();
		}
	};
	
	self.resolve_meta_field_value = function() {
		$( '.js-wpv-' + self.meta + '-field-values' ).each( function( index ) {
			var text_box = $( this ).find( '.js-wpv-' + self.meta + '-field-values-real' ),
			resolved_value = '';
			$( this ).find( '.js-wpv-' + self.meta + '-field-value-div' ).each( function( index ) {
				if ( resolved_value != '' ) {
					resolved_value += ',';
				}
				var value = $( this ).find( '.js-wpv-' + self.meta + '-field-value-text' ).val(),
				framework_value = $( this ).find( '.js-wpv-' + self.meta + '-field-framework-value' ).val(),
				mode = $( this ).find( '.js-wpv-' + self.meta + '-field-compare-mode' ).val();
				switch ( mode ) {
					case 'url':
						value = 'URL_PARAM(' + value + ')';
						break;
					case 'attribute':
						value = 'VIEW_PARAM(' + value + ')';
						break;
					case 'framework':
						value = 'FRAME_KEY(' + framework_value + ')';
						break;
					case 'now':
						value = 'NOW()';
						break;
					case 'today':
						value = 'TODAY()';
						break;
					case 'future_day':
						value = 'FUTURE_DAY(' + value + ')';
						break;
					case 'past_day':
						value = 'PAST_DAY(' + value + ')';
						break;
					case 'this_month':
						value = 'THIS_MONTH()';
						break;
					case 'future_month':
						value = 'FUTURE_MONTH(' + value + ')';
						break;
					case 'past_month':
						value = 'PAST_MONTH(' + value + ')';
						break;
					case 'this_year':
						value = 'THIS_YEAR()';
						break;
					case 'future_year':
						value = 'FUTURE_YEAR(' + value + ')';
						break;
					case 'past_year':
						value = 'PAST_YEAR(' + value + ')';
						break;
					case 'seconds_from_now':
						value = 'SECONDS_FROM_NOW(' + value + ')';
						break;
					case 'months_from_now':
						value = 'MONTHS_FROM_NOW(' + value + ')';
						break;
					case 'years_from_now':
						value = 'YEARS_FROM_NOW(' + value + ')';
						break;
					case 'date':
						var month = $( this ).find( '.js-wpv-' + self.meta + '-field-date select' ),
						mm = month.val(),
						jj = month.next().val(),
						aa = month.next().next().val();
						value = 'DATE(' + jj + ',' + mm + ',' + aa + ')';
						break;
				}
				resolved_value += value;
			})
			text_box.val( resolved_value );
		});
	};
	
	self.remove_meta_field_filters = function() {
		$( self.filter_close_save_selector ).removeClass( 'js-wpv-section-unsaved' );
		var nonce = $( '.js-wpv-filter-remove-' + self.meta + '-field' ).data( 'nonce' ),
		meta_field = [],
		spinnerContainer = $( self.spinner ).insertBefore( $( '.js-wpv-filter-remove-' + self.meta + '-field' ) ).show(),
		error_container = $( self.filter_row ).find( '.js-wpv-filter-multiple-toolset-messages' );
		$('.js-wpv-filter-' + self.meta + '-field-multiple-element .js-filter-remove').each( function() {
			meta_field.push( $( this ).data( 'field' ) );
		});
		var data = {
			action: 'wpv_filter_' + self.meta + '_field_delete',
			id: self.view_id,
			field: meta_field,
			wpnonce: nonce,
		};
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$( self.filter_row )
						.addClass( 'wpv-filter-deleted' )
						.animate({
						  height: "toggle",
						  opacity: "toggle"
						}, 400, function() {
							$( this ).remove();
							self.filter_current_options = $( self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select' ).serialize();
							$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ self.meta + '-field' ] );
							self.meta_field_initialize_relationship();
						});
				} else {
					WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
				}
			 },
			 error: function ( ajaxContext ) {
				 console.log( "Error: ", ajaxContext.responseText );
			 },
			 complete: function() {
				 spinnerContainer.remove();
			 }
		});
		$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
	};
	
	//--------------------
	// Events
	//--------------------
	
	$( document ).on( 'change', '.js-wpv-' + self.meta + '-field-compare-select', function() {
		self.meta_field_initialize_compare();
	});
	
	$( document ).on( 'change', '.js-wpv-' + self.meta + '-field-compare-mode', function() {
		self.meta_field_adjust_value_controls( this )
	});
	
	// Add another value

	$( document ).on( 'click', '.js-wpv-' + self.meta + '-field-add-value', function() {
		var thiz_parent_item = $( this ).parents( '.js-wpv-filter-multiple-element' ),
		clone = thiz_parent_item
			.find( '.js-wpv-' + self.meta + '-field-value-div:last' )
				.clone();
		clone
			.find( '.js-wpv-' + self.meta + '-field-value-text' )
				.val('');
		clone
			.find( '.js-wpv-' + self.meta + '-field-compare-mode' )
				.val( 'constant' );
		clone
			.find( '.js-wpv-' + self.meta + '-field-remove-value' )
				.show();
		clone
			.insertAfter(
				thiz_parent_item
					.find( '.js-wpv-' + self.meta + '-field-value-div:last' )
			);
		self.meta_field_initialize_compare();
		self.meta_field_initialize_compare_mode();
		self.manage_filter_changes();
	});

	// Remove value

	$( document ).on( 'click', '.js-wpv-' + self.meta + '-field-remove-value', function() {
		$( this )
			.parents( '.js-wpv-' + self.meta + '-field-value-div' )
				.remove();
		self.manage_filter_changes();
	});
	
	// Watch changes
	
	$( document ).on( 'change keyup input cut paste', self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select', function() {
		self.resolve_meta_field_value();
		self.manage_filter_changes();
	});
	
	$( document ).on( 'click', self.filter_close_save_selector, function() {
		WPViews.query_filters.clear_validate_messages( self.filter_row );
		if ( self.filter_current_options == $( self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row('.js-filter-' + self.meta + '-field');
		} else {
			var valid = true;
			valid = WPViews.query_filters.validate_filter_options( self.filter_row );
			if ( valid ) {
				self.resolve_meta_field_value();
				var nonce = $( this ).data('nonce'),				
				spinnerContainer = $( self.spinner ).insertBefore( $( this ) ).show(),
				error_container = $( self.filter_row ).find( '.js-wpv-filter-multiple-toolset-messages' ),
				data = {
					action: 'wpv_filter_' + self.meta + '_field_update',
					id: self.view_id,
					fields: $('.js-filter-' + self.meta + '-field input, .js-filter-' + self.meta + '-field select').not( '.js-wpv-element-not-serialize' ).serialize(),
					wpnonce: nonce
				};
				self.filter_current_options = $( self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select' ).serialize();
				$.post( ajaxurl, data, function( response ) {
					if ( response.success ) {
						$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
							$( document ).trigger( 'js_event_wpv_query_filter_saved', [ self.meta + '-field' ] );
						$( '.js-wpv-filter-' + self.meta + '-field-summary' ).html( response.data.summary );
						WPViews.query_filters.close_and_glow_filter_row( self.filter_row, 'wpv-filter-saved' );
					} else {
						WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
					}
				}, 'json' )
				.fail(function(jqXHR, textStatus, errorThrown) {
					console.log( "Error: %s %s", textStatus, errorThrown );
				})
				.always(function() {
					spinnerContainer.remove();
				});
			}
		}
	});
	
	$( document ).on( 'click', '.js-wpv-filter-' + self.meta + '-field-multiple-element .js-filter-remove', function() {
		var thiz = $( this ),
		row = thiz.parents('.js-wpv-filter-' + self.meta + '-field-multiple-element'),
		li_item = thiz.parents( self.filter_row ),
		field = thiz.data('field'),
		nonce = thiz.data('nonce'),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).hide(),
		error_container = row.find( '.js-wpv-filter-toolset-messages' ),
		data = {
			action: 'wpv_filter_' + self.meta + '_field_delete',
			id: self.view_id,
			field: field,
			wpnonce: nonce,
		};
		if ( li_item.find( '.js-wpv-filter-' + self.meta + '-field-multiple-element' ).length == 1 ) {
			self.remove_meta_field_filters();
		} else {
			spinnerContainer.show();
			$.post( ajaxurl, data, function( response ) {
				if ( response.success ) {
					row
						.addClass( 'wpv-filter-multiple-element-removed' )
						.fadeOut( 500, function() {
							$( this ).remove();
							$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ self.meta + '-field' ] );
							self.meta_field_initialize_relationship();
						});
					$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
				} else {
					WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
				}
			}, 'json' )
			.fail( function( jqXHR, textStatus, errorThrown ) {
				console.log( "Error: ", textStatus, errorThrown );
			})
			.always( function() {
				
			});
		}
	});
	
	$( document ).on('click', '.js-filter-' + self.meta + '-field .js-wpv-filter-remove-' + self.meta + '-field', function(e) {
		if ( $( self.filter_row ).find( '.js-wpv-filter-' + self.meta + '-field-multiple-element' ).length > 1 ) {
			var dialog_height = $(window).height() - 100;
			self.filter_dialog.dialog('open').dialog({
				maxHeight: dialog_height,
				draggable: false,
				resizable: false,
				position: { my: "center top+50", at: "center top", of: window }
			});
		} else {
			self.remove_meta_field_filters();
		}
	});
	
	// Created, saved and deleted
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		if ( 
			filter_type == self.meta + '-field' 
			|| filter_type.substr( 0, self.meta_field.length ) == self.meta_field 
			|| filter_type == 'all' 
		) {
			self.manage_filter_changes();
			self.meta_field_initialize_compare();
			self.meta_field_initialize_compare_mode();// Might not be needed here
			self.meta_field_initialize_relationship();
		}
		if ( filter_type == 'parametric-all' ) {
			self.filter_current_options = $( self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select' ).serialize();
			self.manage_filter_changes();
			self.meta_field_initialize_compare();
			self.meta_field_initialize_compare_mode();// Might not be needed here
			self.meta_field_initialize_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	$( document ).on( 'js_event_wpv_query_filter_saved', function( event, filter_type ) {
		if ( 
			filter_type == self.meta + '-field' 
			|| filter_type.substr( 0, self.meta_field.length ) == self.meta_field 
			|| filter_type == 'all' 
		) {
			self.filter_current_options = $( self.filter_options_container_selector + ' input, ' + self.filter_options_container_selector + ' select' ).serialize();
			self.manage_filter_changes();
			self.meta_field_initialize_compare();
			self.meta_field_initialize_compare_mode();// Might not be needed here
			self.meta_field_initialize_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	$( document ).on( 'js_event_wpv_query_filter_deleted', function( event, filter_type ) {
		if ( 
			filter_type == self.meta + '-field' 
			|| filter_type.substr( 0, self.meta_field.length ) == self.meta_field 
			|| filter_type == 'all' 
		) {
			self.manage_filter_changes();
			self.meta_field_initialize_compare();
			self.meta_field_initialize_compare_mode();// Might not be needed here
			self.meta_field_initialize_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	/**
	* init_dialogs
	*
	* Initialize the dialogs
	*
	* @since 1.10
	*/
	
	self.init_dialogs = function() {
		self.filter_dialog = $( '#js-wpv-filter-' + self.meta + '-field-delete-filter-row-dialog' ).dialog({
			autoOpen: false,
			modal: true,
			title: wpv_meta_field_filter_texts[ self.meta ].dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpv_meta_field_filter_texts[ self.meta ].cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-secondary',
					text: wpv_meta_field_filter_texts[ self.meta ].edit_filters,
					click: function() {
						$( this ).dialog( "close" );
						WPViews.query_filters.open_filter_row( $( self.filter_row ) );
					}
				},
				{
					class: 'button-primary js-wpv-filters-meta-fields-delete-filter-row',
					text: wpv_meta_field_filter_texts[ self.meta ].delete_filters,
					click: function() {
						self.remove_meta_field_filters();
						$( this ).dialog( "close" );
					}
				}
			]
		});
	};
	
	self.init = function() {
		self.meta_field_initialize_compare();
		self.meta_field_initialize_relationship();
		self.init_dialogs();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
	WPViews.custom_field_filter_gui = new WPViews.MetaFieldFilterGUI( $, 'custom' );
	WPViews.usermeta_field_filter_gui = new WPViews.MetaFieldFilterGUI( $, 'usermeta' );
    WPViews.termmeta_field_filter_gui = new WPViews.MetaFieldFilterGUI( $, 'termmeta' );
});