The Embedded version lets you display Content Templates and Views in your site, without requiring any extra plugin.

= Instructions =

1. Install like any other plugin into WordPress

2. Export your configuration from your development site. 
Go to the Views->Import/Export menu and click on the 'Export' button. 
You will receive a ZIP file with the XML and PHP configuration files (both are required).
Unzip that file and place both settings.xml and the setting.php into the root directory of this plugin.

You're done!

= Changelog =

v. 2.0
	- Added Views to the new shared Toolset admin menu and Export / Import page.
	
	- Added the functionality to sort a View listing terms by termmeta.
	
	- Added a "format" attribute to the [wpv-post-excerpt] shortcode to control whether its output should be wrapped in paragraph tags.
	
	- Added an information tab to the [wpv-post-body] shortcode GUI.
	
	- Added a missing info="login" attribute value to the [wpv-current-user] shortcode.
	
	- Added a global setting to disable history management related to parametric search, and a specific setting to control this on each View.
	
	- Added a tolerance setting for the infinite scrolling AJAX pagination effect.
	
	- Added two new URL attributes, wpv_sort_orderby and wpv_sort_order, to control a View ordering.
	
	- Removed the Help page.
	
	- Improved the View output by removing some hidden inputs and reviewing ID attributes.
	
	- Improved the query filters that depend on the current page, current post and objects set by a parent View.
	
	- Improved the management of cached data, resulting in better performance.
	
	- Improved the parametric search by checking that all taxonomies involved do exist.
	
	- Improved the parametric search by avoiding counters to display double values when the form and the results are rendered using different shortcodes.
	
	- Improved the compatibility with WPML by disabling the Content Template selection when creating or editing a translation.
	
	- Fixed the combination of query filters using the post__in query argument.
	
	- Fixed an issue on the frontend Views API: it was not being loaded.
	
	- Fixed an issue on frontend pagination controls stopping events delegation.
	
	- Fixed an issue regarding AJAX pagination and parametric search, resulting in wrong pagination outcome.
	
	- Fixed an issue regarding WPML and query filters by specific post IDs, when setting values that belong to translated posts.

v. 1.12.1
	- Fixed an issue regarding parametric search, related to taxonomies no longer existing.
	
	- Fixed an issue regarding parametric search, related to results counters returning a wrong number.
	
	- Fixed a problem in AJAX pagination returning no results in the second and other pages.
	
	- Fixed a PHP notice on an undefined variable.
	
	- Fixed a PHP error on a file not loaded when needed.
		
	- Fixed a problem with the Divi page builder related to the Tolset shortcodes generator.
	
	- Fixed a compatibility issue between WPML and the parametric search, that returned results only in the default language.

v. 1.12, AKA Peter Pan
	- Added termmeta query filters to Views used to list terms.
	
	- Added support for Types termmeta integration to Fields and Views dialogs and Loop Wizards.
	
	- Added a Fields and Views button to a new shortcode generator in the admin bar.
	
	- Added an option to display the last modified date on the [wpv-post-date] shortcode by using a new "type" attribute.
	
	- Improved post relationship filter for a parametric search, so that only ancestors with actual descendants are shown as options, and counters are accurate.
	
	- Improved the output of Views by removing hidden inputs that are no longer needed.
	
	- Improved compatibility of Fields and Views dialogs with Layouts and Visual Composer.
	
	- Fixed a compatibility issue between Views parametric searches and server object caching.
	
	- Fixed an issue regarding the frontend cache of Views, related to Content Templates with custom CSS used inside a View.
	
	- Fixed an issue regarding AJAX pagination history events, and slider Views with numeric signatures.
	
	- Fixed an issue regarding AJAX pagination history events, and nested Views structures.
	
	- Fixed an issue regarding AJAX pagination on Views loaded through AJAX events, such as those in nested structures.
	
	- Fixed an issue regarding responsive Views output, and its debounce tolerance.
	
	- Fixed a problem in Views conditionals when one of the compared values is a number and the other is a string.
	
	- Fixed a compatibility issue with third party plugins that register 404 events.
	
	- Fixed a compatibility issue with Masonry-based themes, related to a forced Views output width set on a resize event.
	
	- Performed a security review on generic POSTed data for several AJAX calls.

v. 1.11.1
	- Improved the media management in preparation of the upcoming Toolset Maps plugin.
	
	- Fixed a problem when using AJAX pagination on the results of a parametric search.

v. 1.11, AKA Lady Jessica

	- Added infinite scrolling as an AJAX effect for the Views pagination.

	- Added proper target attributes to links used as pagination controls.
	
	- Added proper history management when using AJAX pagination and/or a parametric search with AJAX results.
	
	- Improved the dialogs to insert a View or a conditional shortcode into an editor.
	
	- Improved compatibility with Layouts related to Content Template cells.

	- Improved compatibility with WordPress related to admin layout structures.

	- Fixed an issue related to archive loops where some information was missing, resulting in some shortcodes not working.

	- Fixed an issue on wpv-post-body shortcodes not being parsed if they referred to non-existing Content Templates.

v. 1.10.1

	- Improved compatibility with WordPress 4.3.1.

v. 1.10, AKA Marty McFly
	
	- Added a new shortcode, wpv-conditional, for conditional output, along with a GUI for inserting it.
	
	- Added a new caching system for eligible Views in the frontend.
	
	- Added extra options when inserting a View to override limit, offset, order, and orderby settings.
	
	- Added extra options when inserting a View to set values for filters using a shortcode attribute.
	
	- Added a new shortcode wpv-theme-option to obtain the values of registered options when integrating a framework into Views.
	
	- Added a new method for detecting and registering the most used frameworks into the Views Integration.
	
	- Added a new shortcode, wpv-autop, to force formatting on pieces of content.
	
	- Improved the read-only page for a View when using the embedded mode, so that it shows the Content Templates assigned to it.
	
	- Improved the wpv-user shortcode so that it can be used outside the loop of a View that lists users.
	
	- Improved compatibility with WordPress 4.3 by removing PHP4 class constructors.
	
	- Improved compatibility with WordPress 4.3 by adjusting the admin pages to the new structures.
	
	- Improved the internal APIs with several new actions and filters.
	
	- Migrated almost all dialogs from Colorbox to jQueryUI Dialogs.
	
	- Fixed the query filter by post date when the selected date can have an ambiguous meaning.
	
	- Fixed several typos and updated old texts.

v. 1.9.1
	
	- Restored the functionality affected by the WordPress 4.2.3 update.

v. 1.9, AKA Meina Gladstone

	- Added a GUI for inserting Views shortcodes.
		
	- Added class and style attributes to several shortcodes that output HTML tags.
	
	- Added a new shortcode wpv-noautop to display pieces of content without paragraph formatting - included a Quicktag button for easy insertion.
		
	- Added a new debug output to the wpv-if conditional shortcode.
				
	- Improved the combination of limit, offset and pagination settings on a View to avoid expensive auxiliar queries.
	
	- Improved the output of custom CSS and JS for Views and Content Templates - HTML comments should make it easier to identify their source.
	
	- Improved the frontend javascript that controls the pagination, the parametric search interaction and the table sorting.
	
	- Improved the Views AJAX pagination when using a custom spinner - avoided enforcing fixed dimensions and improved the positioning of the spinner.
	
	- Fixed the WordPress media shortcodes (audio, video, playlist) when used on Views with AJAX pagination or with parametric search with automatic results.
	
	- Fixed lower-than comparison functions for date, custom field and usermeta field query filters - a previous security review broke them.
	
	- Fixed the Views pagination spinner “No spinner” setting.
		
	- Fixed edit View links on Views widgets when using the Views Embedded plugin.
		
	- Fixed the query filter by specific users on a View listing users - the URL parameter mode was not being applied.
	
	- Fixed the “Don’t include current page” setting on a View when it is used on a post displayed on an archive page.
	
	- Fixed the API functions to display a View or return its results - avoided errors by checking that the requested item is a published View.
	
	- Improved some shortcodes attributes, like the ones for wpv-post-taxonomy, wpv-post-featured-image and wpv-post-edit-link.
	
	- Improved the compatibility with WPML by setting better translation settings to some private custom fields used to store Views settings.
	
	- Improved the compatibility with WPML by adjusting AJAX pagination when adding language settings as URL parameters.
	
	- Improved the compatibility with 4.2 related to loading spinners.
	
	- Improved the compatibility with 4.2 related to the link Quicktag dialog.
	
	- Improved the compatibility with RTL languages.

v. 1.8.1

	- Fixed an inconsistency on query filters getting values from shortcode attributes - empty values should apply no filter.
	
	- Fixed a bug on Views listing users and filtering by specific users set on a URL parameter.
	
	- Fixed a bug about "lower than" options on the query filters by custom field, usermeta field and post date.
	  https://wp-types.com/forums/topic/custom-date-filter-stopped-working/
	
	- Fixed the frameworks integration - frameworks using an option to store values were not registered correctly.
		
	- Improved the compatibility with Layouts related to archives pagination.

v. 1.8, AKA R2-D2

	- Added a complete GUI for the Views Embedded plugin.
	
	- Added a new API function is_wpv_content_template_assigned.
	
	- Added a new API function is_wpv_wp_archive_assigned.
	
	- Improved the import workflow.
	
	- Improved the compatibility with WordPress 4.1 related to meta_query entries for custom fields and sorting by meta values.
	
	- Improved the compatibility with WordPress 4.2 related to term splitting.
	
	- Improved the compatibility with WordPress 4.2 related to cache objects for taxonomies.
	
	- Improved the compatibility with WordPress 4.2 related to accessing the global $wp_filter.
	
	- Changed the Google Maps script register handler for better third-party compatibility.
	
	- Fixed an issue about filtering by custom field values containing a plus sign.
	
	- Fixed an issue about filtering by Types checkboxes fields - extended support for complex queries in WordPress 4.1+.
	
	- Fixed an issue about multiselect items on a parametric search - avoid force selecting the first option by default.
	
	- Cleaned some deprecated code.

v. 1.7, AKA Zaphod Beeblebrox

v. 1.6.2

	- First release as standalone plugin.