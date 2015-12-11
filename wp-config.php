<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'prono_soupe');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'root');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', '94.23.200.181');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '`/`L)<@Hk#(J|_qz!XM2Qd93]L?zc23jyI/h:-|-*yCs;lsQJEGswSaXvUQk1ilJ');
define('SECURE_AUTH_KEY',  'lH{AhF^f+f||fT6[gl0fcQlpM,u:I<+:nP=@-:~0=OK+FG:(*sYYq1B-^RdWNPU>');
define('LOGGED_IN_KEY',    'qx9,%[!:Zrl#HKXQCwtuD5B}4@_s_vs$eD+7T^8 0E]|:{/& Iv9K_yM5V/uL<oX');
define('NONCE_KEY',        'yJ&~!o*JblM#:xjzS!>1Io;dLO@K7T^-<}v-A>E!{f7!+@)lt42]Ik(1^j+o*MhR');
define('AUTH_SALT',        '@FhEzJU>*hRa|)Qh:h~4|GVLZMZ<>?wo-)?s/QcGzM+Q[!>UU_]yy OJIx$Tm)G:');
define('SECURE_AUTH_SALT', 'tUdRnd0-X05~!yGXdjJ-kiHLTAnu7K*|<?+4ky+7,+<N~YB!A}!|I.67pkp$Y?3F');
define('LOGGED_IN_SALT',   '?rW>P-!_g;[z:1J.bLO;aAK~$p~{Pp*Zmwe8x/t[o~Ux1vQiE|.Jj*K@3!xuGt8+');
define('NONCE_SALT',       '(h_]H#v|T=z6Y2`,P;V7J?k>nDcV[Dh.24pae9bOG{.tb&hWPbHl J =,A?oPC$r');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');