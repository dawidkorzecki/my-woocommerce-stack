=== WP Grid Builder ===
Author URI: https://wpgridbuilder.com
Plugin URI: https://wpgridbuilder.com
Contributors: WP Grid Builder, Loïc Blascos
Tags: ecommerce, facet, filter, grid, justified, masonry, metro, post filter, post grid, taxonomy, user, search
Requires at least: 4.7.0
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 1.6.4
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Build advanced grid layouts with real time faceted search for your eCommerce, blog, portfolio, and more...

== Description ==

= WP Grid Builder WordPress Plugin =

[Live demo](https://demos.wpgridbuilder.com)

WP Grid Builder is a modular and flexible WordPress Grid plugin, which allows you to create advanced and faceted grids.
Show off your post types, taxonomy terms or users in Masonry, Metro, Justified or carousel layout.
Filter your grids from any (custom) taxonomy terms, WordPress fields and custom fields.
Possibilities are endless and do not require coding knowledge.

WP Grid Builder will fit to any project which displays posts, users, or taxonomy terms.
The plugin is perfect to create eCommerces, blogs, portfolios, galleries and so more...
The plugin can also be used to layout grids/carousels from your WordPress media library.

WP Grid Builder was built with performance in mind.
The plugin is able to handle large amout of posts without impacting loading speed of your website.
The faceted search system can handle thousands of posts with an appropriate server (VPS or dedicated server)

WP Grid Builder also includes advanced PHP and JavaScript APIs for developers.
You can use the facet system as standalone without the grid and card system.

**WordPress Features**

WP Grid Builder is certainly the most advanced Grid plugin.
It comes with plenty of options and possibilities easily configurable thanks to powerful admin interface.

**Main Features:**

* Fully Responsive
* Mobile Friendly
* Lazy load support
* RTL layout support
* HTML5 Browser History support
* Google Fonts integration
* 250 SVG icons included
* HTML5 videos support (.mp4, .webm, .ogv)
* Youtube, Vimeo, Wistia support from video post format
* Post formats support (standard, audio, video)
* Index based faceted search
* Accessibility support (WCAG standards)
* W3C standard valid
* SEO Friendly
* Import/Export settings
* PHP and JavaScript APIs
* Developer Friendly
* Multisite Support
* Automatic Updates
* Compatible with Gutenberg or any page builder using shortcodes
* Compatible with WooCommerce plugin
* Compatible with Easy Digital Downloads plugin
* Compatible with Advanced Custom Fields plugin
* Compatible with Relevanssi plugin
* Compatible with SearchWP plugin

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-grid-builder` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Use the Gridbuilder ᵂᴾ screen to configure the plugin.


== Frequently Asked Questions ==

= What does WP Grid Builder do, exactly? =

WP Grid Builder allows to create grids of (custom) post type(s), users, and taxonomy terms.
Grids can be filtered, thanks to an advanced facet system, by taxonomy terms, WordPress fields and custom fields.

= Is WP Grid Builder compatible with multisites installation? =

Yes, WP Grid Builder can be activated for all your sub-sites, just activate it from your main network site.

= Is WP Grid Builder compatible with all web browsers? =

Yes, WP Grid Builder is compatible with all modern web browsers. WP Grid Builder is compatible with Google Chrome, Safari, FireFox, Opera, Edge and IE11.

== Changelog ==

= 1.6.4 - February 11, 2022 =

* Updated  PHP filter wp_grid_builder/indexer/term_query_args to better translate facets.
* Fixed    Issue with term query arguments when indexing taxonomy facets.
* Fixed    Issue with the indexing progression in the facet settings.
* Fixed    Issue with duplicates in the WooCommerce sale price card block.
* Fixed    Issue with WooCommerce prices without tax in facets.
* Fixed    Issue with shortcode editor button not available in some contexts.
* Fixed    Display issue with select field (multiple) in the card builder.

= 1.6.3 - January 14, 2022 =

* Updated  JavaScript libraries and facets assets.
* Fixed    Prevent throwing exceptions with history state when origin is not the same.
* Fixed    Issue with Relevanssi and custom search result snippets.

= 1.6.2 - December 6, 2021 =

* Fixed    Conflict with the terms highlighted by Relevanssi in the cards.
* Fixed    Conflict with SearchWP and custom WP_Query.
* Fixed    Restore default post status of grids.

= 1.6.1 - November 24, 2021 =

* Fixed    Issue with carousel buttons not being correctly sized if too small.
* Fixed	   Carousel dragging issue with Safari on IOS 15.
* Fixed    Missing term order option for the sort facet.
* Fixed    Wrong slug for Meta Box add-on.

= 1.6.0 - October 5, 2021 =

* Added    Support for upper source with Range Slider and Date Picker facets.
* Added    Support for compare type with Range Slider and Date Picker facets.
* Fixed    PHP notices from WordPress 5.8 when rendering grids/facets in Gutenberg.
* Fixed    Issue with WP All Import and WooCommerce products when indexing.
* Fixed    Prevent items from being added to the cart when filtering.
* Fixed    Issue with search template and WooCommerce ordering.

= 1.5.9 - July 27, 2021 =

* Added    Support for WooCommerce taxonomy term order for facet choices.
* Changed  Support for inline CSS styles for preview and fallback mode.
* Fixed    Issue with ACF time picker field format used in facet choices.
* Fixed    Issue with First Media Content option and mp3 format.

= 1.5.8 - May 24, 2021 =

* Improved Support for Relevanssi search based on users.
* Fixed    Issue with query orderby clause on WooCommerce archive pages.
* Fixed    Issue with onchange event and range slider facet.
* Fixed    Issue with user email block converting email address to url.

= 1.5.7 - April 28, 2021 =

* Improved Default date in facet date picker on open (selected/current/max date).
* Improved Active/full price blocks display the price suffix set in WooCommerce.
* Fixed    Issue with media player and flexible card media with Masonry layout.
* Fixed    Issue with SearchWP and the media library in grid view when searching.
* Fixed    Issue with output buffering when filtering custom queries or archive results.
* Fixed    Issue with Selection facet not taking into accord all selected choices.
* Fixed    Issue with hierarchical facets and included/excluded taxonomy terms.
* Fixed    Issue with Relevanssi when filtering custom queries.
* Fixed    Issue with WooCommerce order on archive pages.
* Fixed    Issue with excluded facets from Apply facet.
* Fixed    Conflict with Elementor lightbox script.

= 1.5.6 - March 15, 2021 =

* Fixed    Conflict with search query when no post type is defined.
* Fixed    Issue with choice count always displayed with autocomplete and asynchrounous combobox.
* Fixed    Issue with Geolocation and Map facets showing selected values in selection facet.

= 1.5.5 - March 10, 2021 =

* Added    Oxygen add-on in add-ons list (plugin dashboard).
* Fixed    Issue with WooCommerce price order and the sort facet.
* Fixed    Issue with the main query on index blog page.

= 1.5.4 - March 01, 2021 =

* Improved Allow to cache empty facets when not filtered.
* Improved Behaviour of facet choices in disabled state.
* Improved JSON detection in response when filtering custom content.
* Improved WhatsApp social sharing button now contains the post link.
* Fixed    Issue with SearchWP and facet choices in search template.
* Fixed    Issue with Google Map marker icons not clickable in some cases.
* Fixed    Issue with Query Monitor plugin when filtering custom queries.
* Fixed    Regression where facet IDs were incorrectly typed.

= 1.5.3 - February 9, 2021 =

* Improved Error handling (JSON response) when filtering custom or archive content.
* Improved Automatically expand children in checkbox treeview from query string.
* Improved Preload dynamic assets (CSS/JS) of facets when necessary.
* Fixed    Conflict with color picker input and AMP for WP plugin.
* Fixed    Non-composited animations of the range facet skeleton loader.
* Fixed    Issue with Range Slider facet when min/max values are equal.
* Fixed    Issue with WooCommerce variable prices and Range Slider facet.
* Fixed    Issue with querySelector and HTML attribute value from buttons.
* Fixed    Issue with WP filesystem when using non direct method.
* Fixed    Issue with Selection facet and integers used as facet value.

= 1.5.2 - January 11, 2021 =

* Added    Scroll to top option (with offset) for pagination facet.
* Improved Keywords highlight when using SearchWP as facet search engine.
* Improved Display a JS error when facets are placed inside custom content.
* Updated  Flatpickr library used for date picker facet.
* Fixed    Issue when sorting WooCommerce products by average rating.
* Fixed    Issue with plugin/add-ons and WordPress auto-update feature.
* Fixed    Issue with Per Page facet allowing to pass excessive values.
* Fixed    Issue with Date Facet when single date selection is cleared on close.
* Fixed    Issue with Date Facet when selected dates are outside min/max date range.

= 1.5.1 - December 15, 2020 =

* added    Option to easily filter archive pages and custom queries with facets having as grid name "wpgb-content".
* Improved HTML caching of facets when they are present on different archive templates.
* Fixed    Issue with asynchronous combobox when passing lang parameter from Polylang or WPML.
* Fixed    Issue when importing .json file generated by the plugin (text/plain mime type issue).
* Fixed    Issue with French translation and the number of activated licenses (plugin dashboard).
* Fixed    Conflict with CSSTidy library and PHP constants.

= 1.5.0 - November 23, 2020 =

* added    Color Picker facet to visually filter by colors/images.
* added    A-Z Index facet to filter by starting letter or number.
* added    Rest API routes (for developers) to fetch and search facet choices.
* added    Compatibility with PHP 8.0.0 and WordPress 5.6.
* Fixed    Issue with instant Search facet and trailing whitespaces.
* Fixed    Issue when grid images have missing width and/or height.
* Fixed    Issue with default combobox label not correctly translated.
* Fixed    Issue when rounding image aspect ratio in grids.
* Fixed    Issue with range slider and right-to-left layout.
* Fixed    Issue with several selection facets with different slugs.
* Fixed    Issue with rating facet and PHP 5.6.

= 1.4.3 - October 19, 2020 =

* Fixed    Issue when installing add-ons from plugin dashboard.
* Fixed    Wrong French translation when installing add-ons.

= 1.4.2 - October 19, 2020 =

* Improved Added taxonomy key for duplicated names in taxonomies list (props Marie Comet).
* Improved Range slider behaviour when hidden from view (tab, toggle, accordion, etc.).
* Improved Query optimization when rendering facets and searching for facet choices (asynchronous facets).
* added    wpgb-dots-page class for pagination facet.
* changed  HTML markup of range slider thumbs (thumbs are now wrapped).
* changed  CSS rules of several facets (checkbox, radio, dropdown, buttons, inputs, range).
* changed  Hook priority to enqueue grid and facets assets on the frontend.
* Updated  Google Fonts available in the card builder.
* Fixed    CSS transition issue when updating facet content.
* Fixed    z-index issue with Dropdown and Autocomplete facets.
* Fixed    Missing aria-hidden attribute on rating stars SVG icon.
* Fixed    Issue with multiple select field control in the card builder.
* Fixed    Issue with grid JS instance and unique identifier (on destroy).
* Fixed    Issue with fixed height set on card with Masonry layout when hidden from view.
* Fixed    Issue with 0 numeric value in card custom field.
* Fixed    Race conditions with instant Search facet and asynchronous requests.

= 1.4.1 - September 11, 2020 =

* Fixed    Issue with reset facet demo content.
* Fixed    Issue with included facets to reset.
* Fixed    Issue with main query and offset parameter.

= 1.4.0 - August 31, 2020 =

* Added    Shadow grid principle to render facets without grids or templates in a page.
* Added    Apply facet action to filter a grid on click or to redirect to a filtered page.
* Added    Shortcode, widget and Gutenberg block to render templates from an ID.
* Added    PHP filter wp_grid_builder/templates to register templates from an ID.
* Added    PHP filter wp_grid_builder/facet/title_tag to globally change facet title tag name.
* Improved Performance for asynchronous requests of dropdown (async) and autocomplete facets.
* Improved Disabled state for reset facet button and range slider clear button.
* Improved Automatically expand children in treeview of selected items.
* Improved Automatically uncheck parent when a child is selected in hierarchical checkboxes.
* Improved Remove non existing facet choices (query string) from selection facet.
* Improved Indexing of post metadata keys when a post is not directly updated.
* Updated  Flatpickr library used for date picker facet.
* Fixed    Missing aria labels (min and max values) in thumbs of the range slider facet.
* Fixed    Missing style of autocomplete and treeview navigation in facet stylesheet (if no grid in page).
* Fixed    Issue with Child Terms option not correctly including children (grid settings).
* Fixed    Issue when several templates (JavaScript instances) are present in a page.
* Fixed    Issue with responsive fonts in cards with several occurrences of the same grid in a page.
* Fixed    Issue with indexer when "Adjust IDs for multilingual functionality" option is enabled in WPML.
* Fixed    Issue when programmatically focusing on load more button (JS preventScroll).
* Fixed    Conflict with Elementor lightbox when a lightbox is set in WP Grid Builder settings.

= 1.3.1 - July 15, 2020 =

* Improved Lazy load support for gravatar in cards.
* Improved Integration with Jetpack lazy load module.
* Improved Integration with WP Rocket lazy load feature.
* Fixed    Issue with WP_Query and Relevanssi PHP filter fallback.
* Fixed    Issue with Safari browser and the card builder.
* Fixed    Issue with CSS non valid background shorthands.
* Fixed    Issue with CSS minification and properties in upper case.
* Fixed    Issue with badly-formed markup of search facet button.
* Fixed    Issue with localization of color picker strings (WP 5.5).

= 1.3.0 - June 16, 2020 =

* Added    New autocomplete facet to show suggestions asynchronously while typing.
* Added    Clear X button for search facet to easily clear field.
* Added    Accessible navigation treeview for checkboxes facet.
* Added    Automatically expand lists on refresh if there are selected choices.
* Added    Keep facet (checkboxes, radio, buttons) toggle state while filtering.
* Added    Indeterminate (partially checked) state for hierarchical checkboxes.
* Updated  CSSTidy PHP library to compress and minify stylesheets.
* Fixed    Deprecated PHP warnings with PHP 7.4.x.
* Fixed    Ajax issue with Relevanssi when using facets with search template.
* Fixed    Issue when prefiltering (PHP) with several grids/templates in a page.
* Fixed    Issue when getting facet by slug with wpgb.facets.getFacet() JS method.
* Fixed    JavaScript warning when deleting or cloning a block in the card builder.
* Fixed    Issue with single date facet appearance on mobile devices.
* Fixed    Missing french translation for clear button label of combobox.

= 1.2.3 - May 14, 2020 =

* Added    New orderby option in grid settings to order by term taxonomy count.
* Improved Added version number in query string of SVG sprite (admin).
* Changed  Wrong french translation in rating facet (& up => & plus).
* Changed  Dragger JS helper logic (carousel) to detect dragging from angle and vector thresholds.
* Fixed    Issue with WooCommerce grouped product prices.
* Fixed    Missing taxonomy term settings (term colors) on certain taxonomy (e.g.: WooCommerce attributes).
* Fixed    CSS issue in card builder with equal absolute positions (top, right, bottom, left) in Firefox.
* Fixed    Issue with do_shortcode in Raw Content block of the card builder.
* Fixed    Issue with wp_grid_builder/grid/query_args filter arguments for term and user queries.
* Fixed    Issue with home SVG icons set not rendered on frontend (missing id attribute in SVG tag).
* Fixed    Prevent to pre-filter main query in admin if no grids/templates are specified (wp_grid_builder/facet/query_string).
* Fixed    Prevent password fields in admin settings to be autofilled by browser (Chrome).
* Fixed    Minor markup issue in admin setting panels of the plugin.
* Fixed    CSS animation issue on Ball Spin Fade loader type.

= 1.2.2 - April 14, 2020 =

* Added    French translation of backend and frontend.
* Changed  Minor changes to admin settings panels.
* Changed  Minor changes to admin labels and descriptions.
* Updated  Flatpickr library used for date picker facet.
* Fixed    Prevent issue with multiple inlined custom JS codes.
* Fixed    Issue with WPML Media plugin and attachment queries.
* Fixed    Issue with Visual Composer column shortcodes in excerpt.

= 1.2.1 - March 25, 2020 =

* Improved Split styles and scripts to only load necessary assets on the frontend.
* Improved Facets scripts (date, range, select) are now loaded asynchronously on the frontend.
* Improved Render facets endpoint (onload) only queries content and fetches facet arguments.
* Improved Date and Range facet options are now handled asynchronously instead of being localized.
* Improved Range facet displays a skeleton placeholder while loading before initialization.
* Improved Use of font-variant-numeric for fluid content change in range facet.
* Improved Custom blocks are only rendered if they hold content.
* Added    Option to load/unload polyfills to support Internet Explorer 11 and older browsers.
* Added    Support filtering and sorting by WooCommerce featured products (available in facet custom fields).
* Updated  SVG calendar icon of the date facet input.
* Fixed    Issue with Gutenberg Fullscreen mode in WordPress 5.4 when resizing a grid.
* Fixed    Issue with Gutenberg align class name when editing a grid rendered on load in the editor.
* Fixed    Issue with read more link in card post content and excerpt.
* Fixed    Issue with Gutenberg and Google Fonts loaded from cards.
* Fixed    Issue with formatting input numbers in plugin settings.
* Fixed    Issue with select, date, range and search facets JS instantiation when conditionally hidden (with PHP filter).
* Fixed    Issue with "wp-" prefix in plugin assets folder name (to prevent issue on some servers).

= 1.2.0 - February 10, 2020 =

* Improved Accessibility with carousel keyboard navigation.
* Improved Exclude language taxonomy from taxonomy terms block of the card builder.
* Added    Support for strings translation with Polylang and WPML thanks to Multilingual add-on.
* Added    Support for [number] shortcode in toggle button label to display the number of hidden items (checkbox, radio, button, and hierarchy).
* Added    Support for [number] shortcode in load more button to display number of remaining items.
* Fixed    CSS issue with Gutenberg blocks and select/button components.
* Fixed    Issue with do_shortcode in card post content.
* Fixed    Issue with query string in asynchronous endpoint.
* Fixed    Issue with included terms in facets.
* Fixed    Issue with carousel keyboard navigation.
* Fixed    Issue when indexing taxonomy terms with WPML.

= 1.1.9 - January 20, 2020 =

* Improved Dynamic stylesheets principle to decrease numbers of generated files.
* Improved Support date and number formats for ACF repeater fields and array values in card builder.
* Improved Prevent to scroll to carousel viewport when buttons or pagination dots are focused.
* Fixed    Missing dependency from main plugin stylesheet in wp_enqueue_style() used by wpgb_render_template().
* Fixed    Issue with non numeric attachment ID when changing object attachment with wp_grid_builder/grid/the_object PHP filter.
* Fixed    Issue with missing CSS transitions in card builder from preview mode.
* Fixed    Issue with default accent color in facets if unset.
* Fixed    Issue with search facet and post status.

= 1.1.8 - January 8, 2020 =

* Improved Render blocks and shortcodes in card post content.
* Improved Preserve scrollRestoration on first load to scroll to anchor.
* Improved Preserve hash location in query string when filtering with histroy.
* Added    Draggable option to enable/disable dragging and flicking feature on carousel.
* Fixed    Issue when indexing taxonomy terms from attachment post type.
* Fixed    Issue with encoding facet values and special characters.
* Fixed    Issue with attachment post type and custom post formats from plguin settings.
* Fixed    Issue when assigning card to custom post formats.
* Fixed    Added fallback to default post ID in grid settings if missing ID from pll_get_post() function.
* Fixed    Missing datetime attribute in time HTML tag.
* Fixed    Width issue with select combobox search holder.
* Fixed    Corrected unvalid CSS property values (W3C non-compliant).
* Fixed    CSS transition flicker issue while loading cards stylesheet.

= 1.1.7 - December 2, 2019 =

* Fixed    Unset default touch action on range slider to prevent dragging issue on touch devices.
* Fixed    Missing carousel dots and navigation buttons (prev/next) in Grid Builder.
* Fixed    Missing icons for 3rd party add-ons in dashboard importer of the plugin.
* Fixed    CSS conflicts with facet unordered/ordered list style.

= 1.1.6 - November 18, 2019 =

* Improved WP Media modal keep selected media when adding new ones (does not require to hold ctrl/cmd key).
* Added    New set of SVG icons (home/buildings) for the card builder.
* Added    New hook 'wp_grid_builder/facet/orderby' to change facet query ORDER BY clause.
* Fixed    Rare query issue with term taxonomy ids used in meta_query.
* Fixed    PHP warnings when missing custom fields in facet settings and card builder.
* Fixed    JS issue when destroying range slider instance if facet is empty.
* Fixed    CSS conflict with admin notices if post options if enabled.

= 1.1.5 - November 4, 2019 =

* Improved Plugin license and updater refactor to easily register add-ons.
* Improved Preserve search relevance if no order is set.
* Improved 'noresults_callback' of wpgb_render_template() set to false prevents showing no results message.
* Added    New admin submenu to download and activate add-ons.
* Added    Support for the defer and async script attributes.
* Added    Option to reveal WooCommerce first gallery image when hovering thumbnail.
* Added    Support to sort by ACF meta key (repeaters are not supported).
* Updated  Flatpickr.js library to v4.6.3.
* Fixed    Facets not rendered in preview mode if grid not saved.

= 1.1.1 - September 12, 2019 =

* Improved Allow multiple facets selection in settings to reset facet(s).
* Improved Automatically translate custom field date format in cards.
* Added    Gutenberg block preview examples in block inserter.
* Fixed    PHP warning if missing user data when indexing.
* Fixed    PHP error when saving custom field attachment.
* Fixed    PHP issue with post permalink date structures.

= 1.1.0 - September 4, 2019 =

* Improved Settings API to allow plugins/add-ons to extend settings.
* Improved Increase limit for card spacings up to 999 in grid settings.
* Improved Allow multiple names (whitespace separated) in class attribute of wpgb_render_template() argument.
* Changed  PHP filter name for hierarchy facet.
* Fixed    Missing default Google Fonts weight (variant 400).
* Fixed    Facet not been centered when placed alone in grid builder area.
* Fixed    Issue with include parameter of WP_Term_Query set to [ 0 ] (WP Core bug: https://core.trac.wordpress.org/ticket/47719).
* Fixed    JS conflict with card preview iframe in overview page.
* Fixed    JS conflict with WordPress iris script from color picker.
* Fixed    JS issue with Internet Explorer 11.
* Fixed    CSS issue with post per page select facet.
* Fixed    PHP issue when splitting string by whitespaces for CSS classes.
* Fixed    PHP typo with orderby field name for term and user sources.

= 1.0.3 - June 17, 2019 =

* Added    wp_grid_builder/card/id PHP hook to change the card ID used for a post.
* Added    Possibility to include or exclude term(s) for queried posts (grid settings).
* Added    Possibility to set is_main_query in shortcode attribute.
* Added    Notice message in card builder for blocks that natively have an action (media button, social share, etc.).
* Fixed    JS issue with load more on scroll on facet refresh.
* Fixed    Card media thumbnail action which happens on click.
* Fixed    Card layer link issue when there isn't any overlay/content.
* Fixed    Rendering raw content in card overview panel.
* Fixed    Wrong default SVG play icon in cards.

= 1.0.2 - May 30, 2019 =

* Improved Grid layout performance by changing CSS stacking context.
* Added    Plugin update from subsites for multisite.
* Fixed    Force refreshing plugin info to view latest plugin details on plugins page.
* Fixed    JS load more issue on scroll with carousel.
* Fixed    CSS flickers on grid items with Safari.
* Fixed    Select dropdown position after refreshing facets.
* Fixed    JS error when highlighting select item in dropdown list on facet refresh.
* Fixed    PHP warning when deleting taxonomy terms if missing facets.

= 1.0.1 - May 23, 2019 =

* Improved Check ACF link field url key for custom field action link (card builder).
* Changed  Warning notice for asynchronous hierarchical list for select facet.
* Fixed    Prevent hierarchical list for asynchronous select facet. (Props Marie Comet)
* Fixed    Missing jQuery dependancy (in some cases) in preview mode and in cards overview iframes.
* Fixed    Autoplay issue with embedded iframes in grid.
* Fixed    Issue with upload media button and WP Media iframe.
* Fixed    Issue with post type attachment and videos not correctly fetched.

= 1.0.0 - May 14, 2019 =

* Initial release \o/
