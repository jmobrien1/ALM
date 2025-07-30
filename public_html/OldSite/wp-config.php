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



// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define('DB_NAME', 'wpforalm');



/** MySQL database username */

define('DB_USER', 'wpforalm');



/** MySQL database password */

define('DB_PASSWORD', 'oTg9tHa4!');



/** MySQL hostname */

define('DB_HOST', 'localhost');



/** Database Charset to use in creating database tables. */

define('DB_CHARSET', 'utf8');



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

define('AUTH_KEY',         ';qy[ZVSUGO>7}7hy7a2d+-gDVD~!_T!1dHg^`z<I4F*-}@qvGX2`:S?`?#N4(a2=');
define('SECURE_AUTH_KEY',  'eI|+t0&YDwZy-p{9<~zM tSh>@-qlYuDm1|r1{3jWC#W|+fn{f{=i2TP<p-YoY3 ');
define('LOGGED_IN_KEY',    '=DhSo{gT&H@si+xlE-F|Bw0LGSd[.Ac~ZXL|EySap*@h/k+7to?>7il3V[36~3(?');
define('NONCE_KEY',        'biHpXm~t+G~{9T~@Q`j-_,,E9_a!V^s` Vu4:j_N^++=+qaN:@b<r#q[_3#N:PqR');
define('AUTH_SALT',        '2-L~|;+,9Fg)5@u!IT+rl1z{LMNe`rF-6#?zpTP,G6-Lpf1VH{?FW+^xX~k*qs~f');
define('SECURE_AUTH_SALT', 'V:|b=ox2A]U3KI(=S]w+uixs_a/tQr[FNm.dE xI[y(PAWaSi Z~$|V~.}C.]I3j');
define('LOGGED_IN_SALT',   '|(4>tFX}VlbaX>q.-#C+$-F%x:D9ig]n}S=MET-V SBp3om5:b-n|?AcCv@<%rli');
define('NONCE_SALT',       'h4FW$;SR|ryl6rxb!2T|2-4~L-8+j} K )]R-l;{^F#n7UH+*vx,nQNsncQ^YkC<');



/**#@-*/



/**

 * WordPress Database Table prefix.

 *

 * You can have multiple installations in one database if you give each a unique

 * prefix. Only numbers, letters, and underscores please!

 */

$table_prefix  = 'wp_';



/**

 * WordPress Localized Language, defaults to English.

 *

 * Change this to localize WordPress. A corresponding MO file for the chosen

 * language must be installed to wp-content/languages. For example, install

 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German

 * language support.

 */

define('WPLANG', '');



/**

 * For developers: WordPress debugging mode.

 *

 * Change this to true to enable the display of notices during development.

 * It is strongly recommended that plugin and theme developers use WP_DEBUG

 * in their development environments.

 */

define('WP_DEBUG', false);



/* That's all, stop editing! Happy blogging. */



/** Absolute path to the WordPress directory. */

if ( !defined('ABSPATH') )

	define('ABSPATH', dirname(__FILE__) . '/');



/** Sets up WordPress vars and included files. */

require_once(ABSPATH . 'wp-settings.php');

