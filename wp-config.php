<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'woodmart' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'YC1FyiZH6vW1p4rsz88Xd4geiwmu8xqolU60m6zIdgDi5vyHK2R8SSzzPTZ2hxmK' );
define( 'SECURE_AUTH_KEY',  'MF8V5HWmGVaiyy8Rmnbb1FZYv0L0vxgKXgi9aVsVKNFgvtGHclAcJy1s6Jquepd6' );
define( 'LOGGED_IN_KEY',    'fztqdZCIG962fDepba0mbGgjSgzqTiK92gfrCqdNgpXWl3bomjb5yfQrDN4gDlOw' );
define( 'NONCE_KEY',        'IuPUmhi4Gl1KVJmX2X9wmGwEgonHh5ew7gdL5VYFL1gCcAG5viMVnXHqbSG43hpW' );
define( 'AUTH_SALT',        'hSdBGvv3Rw6hUtamcYLNZjyy9VxI3rJ5pJNCIfkUhG82hrN8tOkdWJCuteacvnag' );
define( 'SECURE_AUTH_SALT', 'f52ec0FLIeixU42TehATviezbePgGrT46v1bvNaIO1LQNejICoiFleu9XeHCTRXJ' );
define( 'LOGGED_IN_SALT',   'AdWGvyX1eOcWahGUGs3pMLYSebvKLS5KI7GW2BlYoCDvjckL204QH6C4opFxw3nK' );
define( 'NONCE_SALT',       'RHb12FyPC6kaR4RFdPpcMuysWRB6WVUYwJWFLw8ZXvu3ZDBzycv4ednfkIoRLL2w' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
// Enable WP_DEBUG mode
define( 'WP_DEBUG', true );

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
