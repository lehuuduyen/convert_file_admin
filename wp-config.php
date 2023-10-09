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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sql_convert_admi' );

/** Database username */
define( 'DB_USER', 'sql_convert_admi' );

/** Database password */
define( 'DB_PASSWORD', 'jiidkCfS7bEpiWy7' );

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
define( 'AUTH_KEY',         '-(4A)I$}n0)SkY|@wZfLQ!.b=sSQtVm$UPAP{+{e^x6{8r8pO# h)KX,gGJ:m,0y' );
define( 'SECURE_AUTH_KEY',  '4`8C6,(aX0:#ZN=>e&nZ2AQBsyT  rCq fF<$<IZQ3Sm4dUZ+}zQV}Et!?US#blF' );
define( 'LOGGED_IN_KEY',    ':dQ*4[mdV/#rsSeg}D#P?;cg~f5Zdqn^ZB7#h]IznnryT@qkgZJ6,hWt(r$u$ABW' );
define( 'NONCE_KEY',        '_jEopOg>TT=P,&0yl2*~pIYgfK -^}dQVKNv&bR*ErL-9!SU&aQ){)^1imkwmj!*' );
define( 'AUTH_SALT',        '-(MYM_pLV24H1.a5nToTl2Sk1QE3B_Y(3.K@L8O{CkMeeKA^]*`p3;B{]c`7g8sS' );
define( 'SECURE_AUTH_SALT', 'NQQhFu%P/b(jaKB,l1=Z5~pGYib4rP2B@D)a.I/j*-BK^s07`KEbJU[vL5gGSF~Y' );
define( 'LOGGED_IN_SALT',   'P9KL)]:*{q5;Mm6c6?x5LYrA(~)#F7[Q~owo@CE]Ej@<z(WuN$.?7ca9ph4DLM3H' );
define( 'NONCE_SALT',       'X#Lf9tvm.NbBG<dChw$0l_X,{7&.enj_Rx-<{<rn >,`v!vs}sB5]-P|LBs]xM3!' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
