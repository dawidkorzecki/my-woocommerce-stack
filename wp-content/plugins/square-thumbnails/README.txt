=== Square Thumbnails ===
Contributors: ilmdesigns
Donate link: http://ilmdesigns.com/portfolio_page/square-thumbnails-plugin/
Tags: crop, resize, thumbnails, square thumbnails, square no cropping, woocommerce, product image, creating all sizes
Requires at least: 3.5
Tested up to: 4.8
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin for creating square thumbnails from images, without cropping them. It is very useful for for products images in woocommerce.

== Description ==

Square Thumbails is a plugin for creating square thumbnails from images, without cropping them. It is like when you set background image in CSS to contain.

A plugin for creating square thumbnails from images, without cropping them. If the plugin is installed and activated, when you upload an image it will automatically create square thumbnails, for all sizes.
If you use a plugin to regenerate the thumbnails, the plugin will create square thumbanails for all existing images.
We needed this plugin to have square images not cropped for products in Woocommerce.


== Installation ==

1. Go to your admin area and select Plugins -> Add new from the menu.
1. Activate the plugin through the 'Plugins' menu in WordPress

We tested it on wordpress versions starting with 3.4 and we found that is compatible starting with wordpress 3.5. It is also compatible with PHP 7.

== Frequently Asked Questions ==

No questions yet.

== Screenshots ==

Here is a screenshot with the same image uploaded, but without plugin activated and with crop checked in media settings, so you can see that the image is cut.
1. screenshot-1.png

Here is a screen shot with an uploaded image when the plugin is activated. You can see that the image is visible without being cut and also the thumbnails are square.
2. screenshot-2.png



== Changelog ==



= 1.0.4 =
Major update for the entire code and fixes for all bugs.
Some new options were added:
- option to choose if original image should be also put in a square
- option to choose if you want to be created all existing sizes, even if the original image is smaller than some of them. WP by default is not creating thumbnails larger than the original image is.
= 1.0.3 =
Fixed the bug reported by @chuchilade and by @peer_012. The bug was caused by hex to rgb color conversion.
= 1.0.2 = 
Fix for eregi deprecation. thanks to mjassen (https://wordpress.org/support/users/mjjojo/) for reporting this error and for helping to fix.
= 1.0.1 =
* Horizontal align of the image in the frame
* Vertical align of the image in the frame
* Set background color of the frame or check to automatically read the color from the image, point (0,0)
= 1.0 =
* Initial release.