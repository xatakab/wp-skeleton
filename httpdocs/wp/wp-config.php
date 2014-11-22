<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// Include local configuration
if (file_exists(dirname(__FILE__) . '/local-config.php')) {
	include(dirname(__FILE__) . '/local-config.php');
}

// Global DB config
if (!defined('DB_NAME')) {
	define('DB_NAME', 'wordpress');
}
if (!defined('DB_USER')) {
	define('DB_USER', 'wordpress');
}
if (!defined('DB_PASSWORD')) {
	define('DB_PASSWORD', 'wordpress');
}
if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
}

/** Database Charset to use in creating database tables. */
if (!defined('DB_CHARSET')) {
	define('DB_CHARSET', 'utf8');
}

/** The Database Collate type. Don't change this if in doubt. */
if (!defined('DB_COLLATE')) {
	define('DB_COLLATE', '');
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
define('AUTH_KEY',         '{&YDBeKQ+;~!1d-Vdp<9Hxe4q6Duw*,dHpYk)Y|*D^B]e4ozU0byz>^X<cfj@xt~');
define('SECURE_AUTH_KEY',  '~@~>:vNoeMRFdG+,E10;*~@cvrl|KbtAwOG?=H&*Qsne)yi,|0{[DV-(LgkzJuEs');
define('LOGGED_IN_KEY',    'w>gf)!#MC^h`XmLzXQn&ViUst3<`))`{8;Y3<^MRDc&=N-R.mcNTwnU?s+DGA=5u');
define('NONCE_KEY',        'B!h]bFrb+~#sN5T=m-:]-TI>dy%nj{%InS})r^1d)J/DH1c5n+scLb0 @|}^WKzq');
define('AUTH_SALT',        '!4q):JhU+rqXw,@A[f=[Im0AQkL1 gJd;t.SBa|/-J4a+WbL&34-2,O;I#}:.!P`');
define('SECURE_AUTH_SALT', 'pBhO-Cy?;{+cya;)|2t}ng_z4gWgILK~swLC{bT,)xq^Mp@DbBvaU_wE-E#qJ d.');
define('LOGGED_IN_SALT',   'c,j9faeYk#ye5V<EQA/-:>#Ymc.J9|utQ6:Ij-u?/6*w!+((nF:Ylg@g-j:2HH<R');
define('NONCE_SALT',       'tDKT|_H(y:OgI7LqDj-}ug~~d-/6$e6kt6at<NU)+ba `-}^!Gd|59]ReXY>eC');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
//$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'ja');


/**
 * Set custom paths
 *
 * These are required because wordpress is installed in a subdirectory.
 */
if (!defined('WP_SITEURL')) {
	define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/wp');
}
if (!defined('WP_HOME')) {
	define('WP_HOME',    'http://' . $_SERVER['SERVER_NAME']);
}
if (!defined('WP_CONTENT_DIR')) {
	define('WP_CONTENT_DIR', dirname(__DIR__) . '/content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/content');
}


/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
if (!defined('WP_DEBUG')) {
	define('WP_DEBUG', false);
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

