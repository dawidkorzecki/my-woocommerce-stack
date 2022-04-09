=== Multi-Step Checkout Pro for WooCommerce ===
Created: 30/10/2017
Contributors: diana_burduja
Email: diana@burduja.eu
Tags: multistep checkout, multi-step-checkout, woocommerce, checkout, shop checkout, checkout steps, checkout wizard, checkout style, checkout page
Requires at least: 3.0.1
Tested up to: 6.1
Stable tag: 2.27
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 5.2.4

Change your WooCommerce checkout page with a multi-step checkout page. This will let your customers have a faster and easier checkout process, therefore a better conversion rate for you.


== Description ==

Create a better user experience by splitting the checkout process in several steps. This will also improve your conversion rate.

The plugin was made with the use of the WooCommerce standard templates. This ensure that it should work with most the themes out there. Nevertheless, if you find that something isn't properly working, let us know in the Support forum.

= Features =

* Sleak design
* Mobile friendly
* Responsive layout
* Adjust the main color to your theme
* Inherit the form and buttons design from your theme
* Keyboard navigation

= Available translations = 

* German
* French

Tags: multistep checkout, multi-step-checkout, woocommerce, checkout, shop checkout, checkout steps, checkout wizard, checkout style, checkout page

== Installation ==

* From the WP admin panel, click "Plugins" -> "Add new".
* In the browser input box, type "Multi-Step Checkout Pro for WooCommerce".
* Select the "Multi-Step Checkout Pro for WooCommerce" plugin and click "Install".
* Activate the plugin.

OR...

* Download the plugin from this page.
* Save the .zip file to a location on your computer.
* Open the WP admin panel, and click "Plugins" -> "Add new".
* Click "upload".. then browse to the .zip file downloaded from this page.
* Click "Install".. and then "Activate plugin".

OR...

* Download the plugin from this page.
* Extract the .zip file to a location on your computer.
* Use either FTP or your hosts cPanel to gain access to your website file directories.
* Browse to the `wp-content/plugins` directory.
* Upload the extracted `wp-image-zoooom` folder to this directory location.
* Open the WP admin panel.. click the "Plugins" page.. and click "Activate" under the newly added "Multi-Step Checkout Pro for WooCommerce" plugin.

== Frequently Asked Questions ==

= The login form isn't showing in the wizard =
Please check the 'Display returning customer login reminder on the "Checkout" page' option found on the WP Admin -> WooCommerce -> Settings -> Accounts page

= Is the plugin GDPR compatible? =
The plugin doesn't add any cookies and it doesn't modify/add/delete any of the form fields. It simply reorganizes the checkout form into steps.

== Screenshots ==

1. Login form
2. Billing
3. Review Order
4. Choose Payment
5. Settings page
6. On mobile devices

== Changelog ==

= 2.27 =
* 01/12/2022
* Fix: the Shipping section was missing on the Neve theme
* Fix: alternative way of adding the thumbnails to the Order section, in case the use of the "woocommerce_cart_item_name" hook conflicts with other plugins
* Fix: the Order section was missing when the Elementor Pro Checkout widget was used on the checkout page

= 2.26 =
* 11/16/2021
* Fix: compatibility with the Local Pickup Plus plugin by SkyVerge
* Fix: validation error for custom fields with conditional logic added by the Flexible Checkout Fields Pro plugin.
* Fix: compatibility with the OceanWP theme
* Fix: the multi-steps weren't loading if the "Fallback Modus" option from the Germanized plugin is enabled.
* Fix: compatibility between the "Double Opt-In Customer Registration" option from German Market plugin for the registration form on the checkout page.

= 2.25 =
* 07/29/2021
* Fix: don't jump to steps which hold hidden invalid fields
* Fix: required radio fields were not validated per step
* Fix: Register with the "woocommerce_checkout_fields" filter the optional fields set with the Checkout Manager for WooCommerce plugin by QuadLayers
* Fix: move the "woocommerce-notices-wrapper" before the step tabs
* Compatibility with the themes by `fuelthemes`
* Fix: the select2 was not initialized on the shipping country and shipping state fields

= 2.24 =
* 04/27/2021
* Fix: check if window.location.hash is defined before using it
* Fix: missing steps content when the Avada Builder plugin is active

= 2.23 =
* 03/29/2021
* Fix: allow checkbox values through the AJAX validation process
* Tweak: show hand pointer on the step tabs for the Breadcrumbs design

= 2.22  =
* 02/25/2021
* Fix: remove button from "Billing address review" when the shop sells in more than one country
* Tweak: add ids for the Address Review lines
* Fix: "Ship to a different address" checkbox was always unchecked on the Astra theme

= 2.21 =
* 01/14/2021
* Fix: on Order Review show the value and not the option id of the select inputs
* Modify the plugin's name from "WooCommerce Multi-Step Checkout Pro" to "Multi-Step Checkout Pro for WooCommerce"

= 2.20 =
* 12/24/2020
* Fix: compatibility with the WooCommerce Amazon Pay plugin on the Astra theme
* Fix: when validating fields, don't assume the name attribute is required
* Small adjustments for compatibility with PHP 8.0 and jQuery 3.5.1

= 2.19 =
* 11/27/2020
* Feature: add "Billing address review" and "Shipping address review" to the Order section
* Fix: add the #login hash to the url only if there isn't one already present
* Fix: the Login section was misplaced in the Neve theme
* Fix: allow array field names for the AJAX per-step validation

= 2.18 =
* 10/01/2020
* Fix: small validation error translation issue
* Fix: the Payment section was showing double on the Urna theme
* Fix: sometimes the validation message for the terms and conditions checkbox would move the steps to the Billing section
* Fix: invert the Breadcrumb steps for the right-to-left languages

= 2.17 =
* 08/16/2020
* Fix: "Your Order" section under the Electro theme
* Test with WooCommerce 4.4

= 2.16 =
* 07/08/2020
* Fix: the "prev" and "next" buttons were present on the first and the last step if the theme was declaring a "display: inline-block !important" rule on the buttons
* Fix: add function to load the default WooCommerce template files. Useful for conflicts with some themes. 
* Feature: add the `wmsc_delete_step_by_category` filter, which will delete a step if one/all products is part or not of a category 
* Fix: compatibility with the "Email Verification / SMS Verification / OTP Verification" plugin by miniOrange

= 2.15  =
* 05/25/2020
* Fix: the sale badges were missing on the Astra theme
* Fix: the editing of the checkout page with the Elementor Pro plugin was crashing
* Compatibility with the Porto theme
* Tweak: add plain JS validation for non-hidden required fields

= 2.14.1 =
* 05/05/2020
* Declare compatibility with WC4.1

= 2.14 =
* 04/18/2020
* Fix: with lazy load functionality from SG Optimizer there are two thumbnails shown instead of one
* Tweak: show error in browser console if there is an error during the AJAX validation
* Fix: two columns for Login and Registration, even if the theme removes the default WooCommerce CSS
* Fix: Registration form under the Puca theme

= 2.13 =
* 03/17/2020
* Declare compatibility with WC4.0
* Declare compatibility with WP5.4
* Tweak: add `wmsc_buttons_class` filter for the buttons class

= 2.12 =
* 02/11/2020
* Compatibility with the tet30 theme
* Feature: change the URL for each step

= 2.11 =
* 02/03/2020
* Tweak: add "woocommerce-validated" and "woocommerce-invalid" CSS classes to inputs after validation
* Fix: on theRetailer theme the "next", "previous" and "skip login" buttons were shown on all steps
* Fix: the loading icon wasn't showing if the Billing step wasn't the first step

= 2.10 =
* 12/31/2019
* Tweak: write navigation buttons with "flex" for a better responsible design
* Other small fixes 

= 2.9 =
* 12/05/2019
* Fix: the step-by-step validation rules for the checkout fields added by the Brazilian Market for WooCommerce plugin
* Fix: add CSS rule for a one-step checkout wizard
* Fix: show default template if no template is selected
* Tweak: add loading icon when switching the steps

= 2.8.3 =
* 11/15/2019
* Fix: the coupon was showing on the Payment step instead of the Order step on the Bridge theme
* Fix: use "self" instead of "this" in the "wpmc_switch_tab" JS hook 
* Fix: don't show validation errors for hidden fields

= 2.8.2 =
* 11/05/2019
* Fix: product titles disappeared from the product category pages on the Astra theme

= 2.8 =
* 11/01/2019
* Tweak: add an element with "clear:both" after the buttons, so they don't get covered with the next element
* Fix: error notice overlaps the tabs on the "Login" step on Divi theme
* Fix: the "Shipping" section on the Astra theme was missing
* Fix: the steps don't scroll up to the top on the Flatsome theme because of the sticky menu

= 2.7 =
* 10/12/2019
* Fix: the "Your Order" section on Avada theme was hidden
* Perform an AJAX validation when moving to the next step instead of only a JS validation

= 2.6 =
* 09/24/2019
* Fix: the 'CPF' and 'CPFJ' hidden fields added by the Brazilian Market on WooCommerce plugin aren't properly validated
* Add explanations for modifying the Registration form

= 2.5 =
* 09/18/2019
* Fix: don't show server-side errors on the login step
* Fix: if "Create account?" checkbox not enabled, then don't validate the #account_username_field

= 2.4 =
* 08/19/2019
* Fix: misalignment of the steps in breadcrumbs style on the Bridge theme
* Feature: option to change the color for the visited steps
* Feature: option to add a "check" sign for the visited steps

= 2.3 =
* 07/14/2019
* Add Dutch translation
* Add Italian translation
* Change steps order for RTL language websites
* Fix: compatibility with the SendCloud plugin
* Fix: add the `woocommerce_checkout_after_customer_details` hook also when the Shipping step is removed

= 2.2 =
* 06/06/2019
* Fix: the legal terms were showing twice with WooCommerce Germanized
* Change steps order for RTL language websites

= 2.1 =
* 05/30/2019
* Fix: the coupon form was not showing up
* Show warning about an option in the German Market plugin

= 2.0 =
* 05/26/2019
* Code refactory so to allow programatically to add/remove/modify steps
* New: validate "number" fields with min and max attributes
* Fix: don't show the buttons if the customers must register
* Show warning for incompatibility with the Suki theme

= 1.26 =
* 05/08/2019
* Fix small issues with the WooCommerce Germanized plugin
* Fix: double thumbnails in the Review section with Avada theme 

= 1.25 =
* 04/21/2019
* Fix: don't toggle the coupon form on the Avada theme
* Compatibility with the Shopper theme
* Feature: compatibility with the WooCommerce Points and Rewards plugin 

= 1.24 =
* 03/27/2019
* Feature: when opening the /checkout/ page, open a specific tab with the #step-1 URL hash
* Fix: don't validate email fields, if they are in an hidden section

= 1.23 =
* 03/06/2019
* Fix: validation for radio fields on Safari on iPhone devices
* Fix: the "Your Order" section is squished in half a column on the Storefront theme

= 1.22 =
* 02/26/2019
* Fix: when "create an account?" is unchecked, remove the "woocommerce-invalid-required-field" class for the password  
* Fix: small design fixes for the Avada theme
* Admin notice for "WooCommerce One Page Checkout" option for Avada theme
* Fix: design error with WooCommerce Germanized and "Order & Payment" steps together
* Feature: validate the required checkbox fields

= 1.21 =
* 02/20/2019
* Fix: input fields for the Square payment gateway were too small
* Fix: the steps were shown over the header if the header was transparent
* Fix: translate appropriately the "Please fix the errors ..." message 
* Fix: adjust the checkout form template for the Avada theme
* Fix: with Visual Composer the "next" and "previous" buttons weren't clickable on iPhone 
* Compatibility with the WooCommerce Germanized plugin
* Fix: add the default WooCommerce div ids to the sections as they can be used by other plugins
* Feature: add the "wpmc_before_switching_tab" and "wpmc_after_switching_tab" JavaScript triggers to the ".woocommerce-checkout" element

= 1.20 =
* 01/07/2019
* Fix: input fields for the Square payment gateway were too small
* Fix: PHP error when activating the plugin and the free version is still active
* Feature: validation for radio inputs
* Fix: on certain themes (ex: Vineyard) the Shipping fields were still validated even if the "Ship to a different address?" is unchecked 

= 1.19 =
* 12/26/2018
* Feature: option for the sign between two united steps. For example "Billing & Shipping"
* Fix: add a weak CSS rule for ".woocommerce-invalid" fields
* Add regional translations for French and German (fr_CA, fr_BE, de_CH)
* Fix: the product title was overlapping the product image in the Order Review table
* Compatibility with the WooCommerce Social Login plugin from SkyVerge

= 1.18 =
* 12/13/2018
* Update the .pot file and the translations
* Tweak: when changing the color, change also the hex number next to the color
* Compatibility with the Hestia Pro theme. The login form was missing
* Fix: rename the language files to wp-multi-step-checkout-pro to fit the language domain
* Fix: don't show validation errors when "Ship to a different address?" is unchecked

= 1.17 =
* 12/10/2018
* Fix: when "Add product thumbnails to the Order Review section" is enabled, the product title was missing in the cart and minicart
* Fix: PHP notice

= 1.16 =
* 12/08/2018
* Fix: set "padding:0" to the steps in order to normalize to all the themes
* Fix: load the CSS and JS assets only on the checkout page
* Tweak: show a warning about the "Multi-Step Checkout" option for the OceanWP theme

= 1.15 =
* 11/21/2018
* New: Registration form along the Login form in the Login step
* New: add product thumbnails to the Order Review section

= 1.14 =
* 09/25/2018
* New: add clickable steps
* New: field validation for every step

= 1.13 =
* 09/12/2018
* New: add templating system
* New: add Material Design template
* New: add Breadcrumbs template

= 1.12 =
* 09/06/2018
* New: the plugin is multi-language ready

= 1.11 =
* 07/28/2018
* Fix: warning for sizeof() in PHP >= 7.2
* Fix: rename the CSS enqueue identifier
* Tweak: rename the "Cheating huh?" error message

= 1.10 =
* 06/25/2018
* Fix: PHP notice for WooCommerce older than 3.0
* Fix: message in login form wasn't translated

= 1.9 =
* 05/21/2018
* Change: add instructions on how to remove the login form
* Fix: add the `woocommerce_before_checkout_form` filter even when the login form is missing
* Compatibility with the Avada theme
* Tweak: for Divi theme add the left arrow for the "Back to cart" and "Previous" button

= 1.8 =
* 03/31/2018
* Tweak: add minified versions for CSS and JS files
* Fix: unblock the form after removing the .processing CSS class
* Fix: hide the next/previous buttons on the Retailer theme 

= 1.7 =
* 02/07/2018
* Fix: keyboard navigation on Safari/Chrome
* Fix: correct Settings link on the Plugins page
* Fix: option for enabling the keyboard navigation

= 1.6 =
* 01/19/2018
* Fix: center the tabs for wider screens
* Fix: show the "Have a coupon?" form from WooCommerce

= 1.5 =
* 01/18/2018
* Fix: for logged in users show the "Next" button and not the "Skip Login" button

= 1.4 =
* 12/18/2017
* Feature: allow to change the text on Steps and Buttons
* Tweak: change the settings page appearance
* Fix: change the "Back to Cart" tag from <a> to <button> in order to keep the theme's styling
* Add French translation

= 1.3 =
* 12/05/2017
* Add "language" folder and prepare the plugin for internationalization
* Add German translation

= 1.2 =
* 11/20/2017
* Fix: the steps were collapsing on mobile
* Fix: arrange the buttons in a row on mobile

= 1.1 =
* 11/09/2017
* Add a Settings page and screenshots
* Feature: scroll the page up when moving to another step and the tabs are out of the viewport

= 1.0 =
* 10/30/2017
* Initial commit

== Upgrade Notice ==

Nothing at the moment
