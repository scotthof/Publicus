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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'publcccc_wordpress');

/** MySQL database username */
define('DB_USER', 'publcccc_23infia');

/** MySQL database password */
define('DB_PASSWORD', 'i2n3iofnia!!osndfioasj?FCohsd');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Bc>&~ZGxh*%;1kUZPBM=+ULD*)DsxBIHLDGB[vu.P86j(/%g7HtW_IYg=kJ3zu4s');
define('SECURE_AUTH_KEY',  'tvwT+*KGPw0dJ[Q )+-Y6tI>U+l~[z$jB-2mS`P70*9b*t~,)mD]TBk9mJr#Exx|');
define('LOGGED_IN_KEY',    'ffh)@efYES^JP7--dLz=JU>+qQ|DaT.vBeE>#Sv.Ac+N4=nY+Dj-ZP- c+N@z&xw');
define('NONCE_KEY',        'D={uoxGchB2oCGz(G#}RzuN5eO65eOkV|oOlMhJgl;yBxxNW--kc4T&Bf+Y:XH8#');
define('AUTH_SALT',        ')Fc$Woj2X|yp-igS3|a JVK+c@[:PuFy~2-=`-4hjIN1>G%eNSehFL21U`>FeeD3');
define('SECURE_AUTH_SALT', '.5n7nzMn{?Atr 8~;m#FRkG1+T#N Xz(/yHQbilneJ52a9Zcv&84dAy3icY!3E6$');
define('LOGGED_IN_SALT',   'i<rG|ZJO(+HT&&Awx|(a(RzaT+k@5$t7hUG+)4oT v|c-+Qg|1$r)qZj)(`#kf!s');
define('NONCE_SALT',       'a?S$FrMHQ5Df/Iv]od8R*!tzY}%a)1f1YxbW$vmPh<gqu@|8[N3Iu[`BZhTvl$Rc');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);