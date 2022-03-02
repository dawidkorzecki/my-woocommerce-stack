=== Multi-Step Checkout for WooCommerce ===
Created: 30/10/2017
Contributors: diana_burduja
Email: diana@burduja.eu
Tags: multistep checkout, multi-step-checkout, woocommerce, checkout, shop checkout, checkout steps, checkout wizard, checkout style, checkout page
Requires at least: 3.0.1
Tested up to: 5.9 
Stable tag: 2.19
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
* In the browser input box, type "Multi-Step Checkout for WooCommerce".
* Select the "Multi-Step Checkout for WooCommerce" plugin and click "Install".
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
* Open the WP admin panel.. click the "Plugins" page.. and click "Activate" under the newly added "Multi-Step Checkout for WooCommerce" plugin.

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

= 2.19 =
* 01/04/2022
* Feature: add the "data-current-title" attribute to the steps
* Fix: the Shipping section was missing on the Neve theme
* Fix: the Order section was missing when the Elementor Pro Checkout widget was used on the checkout page

= 2.18 =
* 10/16/2021
* Fix: compatibility with the OceanWP theme
* Fix: the multi-steps weren't loading if the "Fallback Modus" option from the Germanized plugin is enabled.
* Fix: compatibility with the Local Pickup Plus plugin by SkyVerge

= 2.17 =
* 07/29/2021
* Compatibility with the themes by `fuelthemes`
* Fix: move the "woocommerce-notices-wrapper" before the step tabs
* Fix: move the script.min.js file in the footer. This avoids a JS error when the jQuery library is loaded twice. See the support topic: https://wordpress.org/support/topic/javascript-error-197/
* Fix: the select2 was not initialized on the shipping country and shipping state fields

= 2.16 =
* 04/27/2021
* Fix: check if window.location.hash is defined before using it
* Fix: missing steps content when the Avada Builder plugin is active

= 2.15 =
* 03/10/2021
* Tweak: add "wpmc-no-back-to-cart" CSS class
* Fix: correct the "Show the Login step" message
* Fix: increase the priority for the "woocommerce_locate_template" filter
* Tweak: add "btn-color-primary" CSS class to the buttons under the WoodMart theme

= 2.14 =
* 01/14/2021
* Modify the plugin's name from WooCommerce Multi-Step Checkout to Multi-Step Checkout for WooCommerce

= 2.13 =
* 12/07/2020
* Add the "woocommerce_checkout_logged_in_message" filter
* Fix: the Login section was misplaced in the Neve theme
* Fix: replace the $text_domain with a string 
* Test with PHP 8.0, WordPress 5.6, WooCommerce 4.8, jQuery 3.5.1

= 2.12 =
* 10/12/2020
* Fix: use "flex-end" instead of "right" for the navigation buttons
* Test with WooCommerce 4.5

= 2.11 =
* 08/16/2020
* Fix: "Your Order" section under the Electro theme
* Test with WooCommerce 4.4

= 2.10 =
* 07/08/2020
* Fix: sale badges were missing on the Astra theme
* Fix: the "prev" and "next" buttons were present on the first and the last step if the theme was declaring a "display: inline-block !important" rule on the buttons
* Fix: add function to load the default WooCommerce template files. Useful for conflicts with some themes. 
* Compatibility with the Porto theme

= 2.9.1 =
* 05/05/2020
* Declare compatibility WooCommerce 4.1

= 2.9 =
* 03/12/2020
* Declare compatibility WooCommerce 4.0 
* Declare compatibility WordPress 5.4 
* Tweak: add `wmsc_buttons_class` filter for the buttons class

= 2.8 =
* 01/30/2020
* Declare compatibility WooCommerce 3.9
* Fix: "prev" or "next" button wouldn't hide when necessary on theRetailer theme

= 2.7 =
* 12/31/2019
* Tweak: write navigation buttons with "flex" for a better responsible design

= 2.6 =
* 12/11/2019
* Fix: add CSS rule for a one-step checkout wizard
* Fix: use "self" instead of "this" in the "wpmc_switch_tab" JS hook 
* Fix: the coupon was showing on the Payment step instead of the Order step on the Bridge theme

= 2.5.1 =
* 11/05/2019
* Fix: product titles disappeared from the product category pages on the Astra theme

= 2.5 =
* 10/31/2019
* Fix: the "Your Order" section on Avada theme was hidden
* Fix: the "Shipping" section on the Astra theme was missing
* Fix: The "Your Order" section on the Shopper theme was not full width
* Tweak: add an element with "clear:both" after the buttons, so they don't get covered with the next element
* Fix: the steps don't scroll up to the top on the Flatsome theme because of the sticky menu

= 2.4 =
* 10/01/2019
* Feature: when opening the /checkout/ page, open a specific tab with the #step-1 URL hash
* Fix: if "Ship to a different address?" not selected, don't switch tab to Shipping when an error is found
* Fix: don't show server-side errors on the login step

= 2.3 =
* 07/14/2019
* Change steps order for RTL language websites
* Fix: compatibility with the SendCloud plugin
* Fix: add the `woocommerce_checkout_after_customer_details` hook also when the Shipping step is removed

= 2.2 =
* 06/06/2019
* Fix: the legal terms were showing twice with WooCommerce Germanized

= 2.1 =
* 05/30/2019
* Fix: the coupon form was not showing up
* Show warning about an option in the German Market plugin

= 2.0 =
* 05/24/2019
* Warning: plugin incompatible with the Suki theme
* Code refactory so to allow programatically to add/remove/modify steps

= 1.20 =
* 05/08/2019
* Fix small issues with the WooCommerce Germanized plugin
* Declare compatibility with WordPress 5.2

= 1.19 =
* 04/27/2019
* Feature: compatibility with the WooCommerce Points and Rewards plugin 
* Declare compatibility with WooCommerce 3.6
* Tweak: update the Bootstrap library used in the admin side to 3.4.1 version

= 1.18 =
* 04/12/2019
* Fix: the "Your Order" section is squished in half a column on the Storefront theme
* Fix: don't toggle the coupon form on the Avada theme
* Fix: remove constantly loading icon from the Zass theme

= 1.17 =
* 02/24/2019
* Feature: add the "wpmc_before_switching_tab" and "wpmc_after_switching_tab" JavaScript triggers to the ".woocommerce-checkout" element
* Fix: design error with WooCommerce Germanized and "Order & Payment" steps together
* Fix: small design fixes for the Avada theme
* Admin notice for "WooCommerce One Page Checkout" option for Avada theme 

= 1.16.2 =
* 02/18/2019
* Fix: PHP warnings when WooCommerce Germanized isn't installed

= 1.16.1 =
* 02/17/2019
* Fix: use the available strings from WooCommerce Germanized so the translation doesn't break

= 1.16 =
* 02/14/2019
* Fix: input fields for the Square payment gateway were too small
* Fix: "load_text_domain" is loaded now in the "init" hook 
* Fix: the steps were shown over the header if the header was transparent
* Fix: adjust the checkout form template for the Avada theme
* Fix: with Visual Composer the "next" and "previous" buttons weren't clickable on iPhone 
* Fix: spelling errors in the nl_NL translation
* Compatibility with the WooCommerce Germanized plugin

= 1.15 =
* 12/27/2018
* Tweak: show a warning about the "Multi-Step Checkout" option for the OceanWP theme
* Compatibility with the WooCommerce Social Login plugin from SkyVerge
* Add nl_NL, nl_BE, fr_CA, fr_BE, de_CH languages
* Feature: option for the sign between two united steps. For example "Billing & Shipping"

= 1.14 =
* 12/04/2018
* Fix: set "padding:0" to the steps in order to normalize to all the themes
* Fix: the "WooCommerce not installed" message was showing up even if WooCommerce was installed
* Fix: small design changes for the Flatsome, Enfold and Bridge themes  
* Fix: load the CSS and JS assets only on the checkout page

= 1.13 =
* 10/03/2018
* remove PHP notice when WPML option isn't enabled

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
