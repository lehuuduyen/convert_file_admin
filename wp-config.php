<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sql_convert_admi' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '*>MG!(~]g#Y6tx#!k-}.lUWN^VpB{*Rfg^&z<7Yq0#C,<#^~e=%-#w-TWAyTg28r' );
define( 'SECURE_AUTH_KEY',  'YfqT%WjN^O_wyp-9:`80n<lk!~}S9#{q3/&v3RFfy5>#CQo)=~g:GJpJ; uVdKWL' );
define( 'LOGGED_IN_KEY',    'ncNoh/?|bXSZCdHQEq%oK!^iH4Sr}H>kw%wQ:g-Tl_1 l4}/L5tJ/|xdenkVGi8_' );
define( 'NONCE_KEY',        'Ad `cll(o4X!G~w&%O-{lc7o2{CZoHHQwM=py+o9b`v(m4Zm!(z1m.#l7s6pGq1m' );
define( 'AUTH_SALT',        'J&BoRn1q6t.n/%>or%A5tgOaWkrQ*a08vhk)&[qHyHVgp0^AL!I-[H[g`ejhh~op' );
define( 'SECURE_AUTH_SALT', '`&&y[S,a<zyM?j4A%VwO6T`i!3.j-5(;[-`Kl8K>7l^]]FSaM|lt(DgvW 1^uq6r' );
define( 'LOGGED_IN_SALT',   'w )a=Hv.;%V!bTJW_97KLH19r&CG>Lb%m^llOxxm^*+O[l3z!1&>]~{twkJ:MG^x' );
define( 'NONCE_SALT',       'mEs*lYwwX;m[^F6FdsQL-+N5l}T(eB(Uyf2s2*Xr!;^wSP0<&`4g;i(g02fj)&%9' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'lk_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
