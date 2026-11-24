<?php
// Construit le site de démo « Maison Lumière » (salon de coiffure & beauté fictif)
// dans un WordPress fraîchement installé avec le thème Twenty Twenty-Five.
require_once '/wordpress/wp-load.php';
// Agir en tant qu'administrateur : sinon WordPress filtre le contenu (styles globaux, HTML).
wp_set_current_user( 1 );

$U = untrailingslashit( home_url() );

// ---------- Photos (Unsplash, licence gratuite — voir CREDITS-PHOTOS.md) ----------
// Chaque photo est importée dans la médiathèque ; en cas d'échec, elle est liée en direct.
$PHOTOS = array(
	'salon'     => array( '1521590832167-7bcbfaa6381f', 'Intérieur lumineux du salon avec fauteuils et grand miroir' ),
	'salon2'    => array( '1633681926022-84c23e8cb2d6', 'Espace de coiffure moderne avec miroirs ronds' ),
	'brushing'  => array( '1562322140-8baeececf3df', 'Coiffeuse réalisant un brushing' ),
	'couleur'   => array( '1560869713-bf165a9cfac1', 'Cheveux blonds lumineux après une couleur' ),
	'beaute'    => array( '1570172619644-dfd03ed5d881', 'Soin du visage avec masque en institut' ),
	'boucles'   => array( '1629397685944-7073f5589754', 'Coiffeuse réalisant des boucles au fer' ),
	'sechage'   => array( '1580618672591-eb180b1a973f', 'Séchage à la brosse ronde' ),
	'ongles'    => array( '1610992015762-45dca7fa3a85', 'Manucure rose poudré' ),
	'manucure'  => array( '1632345031435-8727f6897d53', 'Pose de vernis en institut' ),
	'produits'  => array( '1776105609708-3d8908ab4dc2', 'Étagères de produits capillaires' ),
);
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$IMG_URLS = array();
foreach ( $PHOTOS as $key => $p ) {
	$remote = 'https://images.unsplash.com/photo-' . $p[0] . '?w=1600&q=80&fm=jpg';
	$IMG_URLS[ $key ] = $remote;
	$tmp = download_url( $remote, 60 );
	if ( is_wp_error( $tmp ) ) { continue; }
	$att = media_handle_sideload( array( 'name' => 'maison-lumiere-' . $key . '.jpg', 'tmp_name' => $tmp ), 0, $p[1] );
	if ( is_wp_error( $att ) ) { @unlink( $tmp ); continue; }
	update_post_meta( $att, '_wp_attachment_image_alt', $p[1] );
	$IMG_URLS[ $key ] = wp_get_attachment_image_url( $att, 'large' );
}

$T = function ( $s ) use ( $U, $PHOTOS, $IMG_URLS ) {
	$s = preg_replace_callback( '/\{\{IMG:([a-z0-9]+)(?::([a-z0-9 -]+))?\}\}/', function ( $m ) use ( $PHOTOS, $IMG_URLS ) {
		$class = 'ml-photo' . ( empty( $m[2] ) ? '' : ' ' . $m[2] );
		return '<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"' . $class . '"} -->' . "\n"
			. '<figure class="wp-block-image size-large ' . $class . '"><img src="' . esc_url( $IMG_URLS[ $m[1] ] ) . '" alt="' . esc_attr( $PHOTOS[ $m[1] ][1] ) . '"/></figure>' . "\n"
			. '<!-- /wp:image -->';
	}, $s );
	return str_replace( '{{URL}}', $U, $s );
};

// ---------- Nettoyage du contenu d'exemple ----------
foreach ( get_posts( array( 'post_type' => array( 'post', 'page' ), 'post_status' => 'any', 'numberposts' => -1 ) ) as $p ) {
	wp_delete_post( $p->ID, true );
}

// ---------- Palette de couleurs (styles globaux) ----------
$gs = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true );
if ( ! empty( $gs['ID'] ) ) {
	$palette = array(
		array( 'slug' => 'base',     'name' => 'Ivoire',        'color' => '#FBF8F5' ),
		array( 'slug' => 'contrast', 'name' => 'Expresso',      'color' => '#2B2320' ),
		array( 'slug' => 'accent-1', 'name' => 'Caramel',       'color' => '#C08A6E' ),
		array( 'slug' => 'accent-2', 'name' => 'Rose poudré',   'color' => '#EBDCD3' ),
		array( 'slug' => 'accent-3', 'name' => 'Cacao',         'color' => '#7A5442' ),
		array( 'slug' => 'accent-4', 'name' => 'Taupe',         'color' => '#8C817B' ),
		array( 'slug' => 'accent-5', 'name' => 'Lin',           'color' => '#F4ECE6' ),
		array( 'slug' => 'accent-6', 'name' => 'Sable',         'color' => '#E3D3C9' ),
	);
	$data = array(
		'version'                     => 3,
		'isGlobalStylesUserThemeJSON' => true,
		'settings'                    => array(
			'color'  => array( 'palette' => array( 'theme' => $palette ) ),
			'layout' => array( 'contentSize' => '860px', 'wideSize' => '1200px' ),
		),
		'styles'                      => array(
			'elements' => array(
				'button'  => array(
					'color'  => array( 'background' => 'var:preset|color|accent-3', 'text' => 'var:preset|color|base' ),
					'border' => array( 'radius' => '999px' ),
				),
				'heading' => array( 'typography' => array( 'fontWeight' => '500', 'letterSpacing' => '-0.02em' ) ),
				'link'    => array( 'color' => array( 'text' => 'var:preset|color|accent-3' ) ),
			),
			'css'      => <<<'CSS'
{{EXTRA_CSS}}
CSS
			,
		),
	);
	wp_update_post( array( 'ID' => $gs['ID'], 'post_content' => wp_slash( wp_json_encode( $data ) ) ) );
}

// ---------- Formulaire de contact (Contact Form 7) ----------
$form_block = '<!-- wp:paragraph --><p><em>Formulaire de contact indisponible.</em></p><!-- /wp:paragraph -->';
if ( class_exists( 'WPCF7_ContactForm' ) ) {
	$cf = WPCF7_ContactForm::get_template( array( 'locale' => 'fr_FR', 'title' => 'Demande de rendez-vous' ) );
	$cf->set_properties( array(
		'form' => "<label> Votre nom\n    [text* your-name autocomplete:name] </label>\n\n<label> Votre e-mail\n    [email* your-email autocomplete:email] </label>\n\n<label> Votre téléphone\n    [tel your-tel autocomplete:tel] </label>\n\n<label> Prestation souhaitée\n    [select your-service \"Coupe femme\" \"Coupe homme\" \"Couleur / balayage\" \"Soin du visage\" \"Manucure / beauté des mains\" \"Autre\"] </label>\n\n<label> Votre message (date et heure préférées)\n    [textarea your-message] </label>\n\n[acceptance rgpd] J'accepte que mes données soient utilisées pour traiter ma demande. [/acceptance]\n\n[submit \"Envoyer ma demande\"]",
	) );
	$cf->save();
	$form_block = '<!-- wp:shortcode -->' . "\n" . '[contact-form-7 id="' . $cf->id() . '" title="Demande de rendez-vous"]' . "\n" . '<!-- /wp:shortcode -->';
}

// ---------- Contenu des pages ----------
$home = <<<'HTML'
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"backgroundColor":"accent-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-accent-5-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:paragraph {"textColor":"accent-3","fontSize":"small"} -->
<p class="has-accent-3-color has-text-color has-small-font-size">SALON DE COIFFURE &amp; INSTITUT DE BEAUTÉ · LYON 6e</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size">Révélez votre beauté naturelle</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Coupes sur mesure, couleurs végétales et soins visage dans un lieu calme et lumineux, à deux pas du parc de la Tête d'Or.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{{URL}}/contact/">Prendre rendez-vous</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="{{URL}}/prestations/">Voir les prestations</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%">{{IMG:salon:ml-tall}}</div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Nos savoir-faire</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Trois univers, une même exigence : prendre le temps de vous écouter.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"accent-2"} -->
<div class="wp-block-column has-accent-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">{{IMG:brushing}}

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Coiffure</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Coupes femme, homme et enfant, brushings et chignons. Un diagnostic offert à chaque visite pour une coupe adaptée à votre visage.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="{{URL}}/prestations/">À partir de 25 € →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"accent-2"} -->
<div class="wp-block-column has-accent-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">{{IMG:couleur}}

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Couleur</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Colorations végétales, balayages et mèches lumineuses. Des teintes naturelles qui respectent la fibre de vos cheveux.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="{{URL}}/prestations/">À partir de 55 € →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"accent-2"} -->
<div class="wp-block-column has-accent-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">{{IMG:beaute}}

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Beauté</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Soins du visage, manucure, épilations et maquillage. Un moment de détente avec des produits français et bio.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="{{URL}}/prestations/">À partir de 15 € →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">{{IMG:boucles:ml-tall}}</div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading -->
<h2 class="wp-block-heading">Pourquoi nous choisir ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Depuis 2012, Maison Lumière accueille ses clientes et clients comme à la maison. Notre équipe se forme chaque année aux dernières techniques pour vous offrir le meilleur.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><strong>Diagnostic offert</strong> avant chaque prestation</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Produits bio et fabriqués en France</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Ouvert jusqu'à 20 h</strong> le jeudi</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Accessible</strong> aux personnes à mobilité réduite</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Paiement</strong> CB, espèces et chèques cadeaux</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="{{URL}}/le-salon/">Découvrir le salon</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"backgroundColor":"accent-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-accent-5-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Ils nous font confiance</h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>« Enfin un salon qui écoute vraiment ! Mon balayage est exactement ce que je voulais. »</p>
<!-- /wp:paragraph --><cite>Camille, cliente depuis 2019</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>« Accueil chaleureux, conseils précieux et un soin du visage parfait. Je recommande les yeux fermés. »</p>
<!-- /wp:paragraph --><cite>Sophie, cliente depuis 2021</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>« Coupe homme rapide et soignée, ambiance détendue. Mon nouveau rendez-vous du mois. »</p>
<!-- /wp:paragraph --><cite>Karim, client depuis 2023</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph {"align":"center","textColor":"accent-4","fontSize":"small"} -->
<p class="has-text-align-center has-accent-4-color has-text-color has-small-font-size">Avis d'exemple pour la démonstration — à remplacer par de vrais avis clients.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"textAlign":"center","textColor":"base"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color">Envie de changement ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Réservez votre moment en quelques clics ou appelez-nous au 04&nbsp;00&nbsp;00&nbsp;00&nbsp;00.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent-1","textColor":"contrast"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-accent-1-background-color has-text-color has-background wp-element-button" href="{{URL}}/contact/">Prendre rendez-vous</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
HTML;

$prestations = <<<'HTML'
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Tous nos tarifs sont TTC et incluent un diagnostic personnalisé. Les prix peuvent varier selon la longueur des cheveux.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Coiffure femme</h2>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":false,"className":"is-style-stripes"} -->
<figure class="wp-block-table is-style-stripes"><table><thead><tr><th>Prestation</th><th>Tarif</th></tr></thead><tbody><tr><td>Shampoing, coupe, brushing</td><td>45 €</td></tr><tr><td>Brushing seul</td><td>28 €</td></tr><tr><td>Coupe enfant (– 12 ans)</td><td>20 €</td></tr><tr><td>Chignon / coiffure de mariée</td><td>sur devis</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Coiffure homme</h2>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":false,"className":"is-style-stripes"} -->
<figure class="wp-block-table is-style-stripes"><table><thead><tr><th>Prestation</th><th>Tarif</th></tr></thead><tbody><tr><td>Coupe homme</td><td>25 €</td></tr><tr><td>Coupe + taille de barbe</td><td>35 €</td></tr><tr><td>Taille de barbe seule</td><td>15 €</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Couleur</h2>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":false,"className":"is-style-stripes"} -->
<figure class="wp-block-table is-style-stripes"><table><thead><tr><th>Prestation</th><th>Tarif</th></tr></thead><tbody><tr><td>Coloration végétale</td><td>55 €</td></tr><tr><td>Balayage / mèches</td><td>à partir de 75 €</td></tr><tr><td>Patine / gloss</td><td>30 €</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Beauté &amp; bien-être</h2>
<!-- /wp:heading -->

{{IMG:manucure:ml-wide}}

<!-- wp:table {"hasFixedLayout":false,"className":"is-style-stripes"} -->
<figure class="wp-block-table is-style-stripes"><table><thead><tr><th>Prestation</th><th>Tarif</th></tr></thead><tbody><tr><td>Soin du visage éclat (45 min)</td><td>60 €</td></tr><tr><td>Manucure classique</td><td>25 €</td></tr><tr><td>Pose de vernis semi-permanent</td><td>35 €</td></tr><tr><td>Épilation sourcils</td><td>15 €</td></tr><tr><td>Maquillage événement</td><td>45 €</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"backgroundColor":"accent-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-accent-5-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Offrez un moment Maison Lumière</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nos chèques cadeaux sont valables un an sur toutes les prestations. Disponibles au salon ou sur simple demande.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{{URL}}/contact/">Réserver ou commander</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
HTML;

$salon = <<<'HTML'
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Maison Lumière est né d'une envie simple : créer un salon où l'on se sent bien, où l'on prend le temps, et où chaque prestation est pensée pour vous.</p>
<!-- /wp:paragraph -->

{{IMG:salon2:ml-wide}}

<!-- wp:heading -->
<h2 class="wp-block-heading">Notre histoire</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ouvert en 2012 par Léa, coiffeuse passionnée formée à Paris, le salon a grandi avec ses clientes et clients. Aujourd'hui, une équipe de quatre personnes vous accueille dans un espace de 120 m² baigné de lumière naturelle.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">L'équipe</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"accent-5"} -->
<div class="wp-block-column has-accent-5-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Léa</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>Fondatrice, coiffeuse coloriste</strong><br>Spécialiste du balayage et des couleurs végétales.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"accent-5"} -->
<div class="wp-block-column has-accent-5-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Hugo</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>Coiffeur barbier</strong><br>Coupes homme, dégradés et soins de la barbe.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"accent-5"} -->
<div class="wp-block-column has-accent-5-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Inès</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>Esthéticienne</strong><br>Soins du visage, manucure et maquillage.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Nos engagements</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Des produits professionnels <strong>bio, vegan et fabriqués en France</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Un <strong>diagnostic personnalisé</strong> et des conseils pour l'entretien à la maison</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Le <strong>tri et le recyclage</strong> des cheveux coupés et des emballages</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Le salon en images</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column">{{IMG:sechage:ml-tall}}</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">{{IMG:ongles:ml-tall}}</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">{{IMG:produits:ml-tall}}</div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{{URL}}/contact/">Venir nous rencontrer</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
HTML;

$contact = <<<'HTML'
<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Adresse</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>12 rue des Lilas<br>69006 Lyon</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Nous joindre</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Tél. : 04&nbsp;00&nbsp;00&nbsp;00&nbsp;00<br>E-mail : contact@exemple.fr</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Horaires</h3>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":false} -->
<figure class="wp-block-table"><table><tbody><tr><td>Lundi</td><td>Fermé</td></tr><tr><td>Mardi – Mercredi</td><td>9 h – 19 h</td></tr><tr><td>Jeudi</td><td>9 h – 20 h</td></tr><tr><td>Vendredi</td><td>9 h – 19 h</td></tr><tr><td>Samedi</td><td>8 h 30 – 18 h</td></tr><tr><td>Dimanche</td><td>Fermé</td></tr></tbody></table></figure>
<!-- /wp:table --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Demande de rendez-vous</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Indiquez la prestation et vos disponibilités : nous vous rappelons sous 24 h pour confirmer.</p>
<!-- /wp:paragraph -->

{{FORM}}</div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
HTML;

$mentions = <<<'HTML'
<!-- wp:paragraph -->
<p><mark>Modèle à compléter avec les informations réelles de l'entreprise avant la mise en ligne.</mark></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Éditeur du site</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Raison sociale] – [Forme juridique] au capital de [montant] €<br>Siège social : [adresse]<br>SIRET : [numéro] – RCS [ville]<br>N° TVA intracommunautaire : [numéro]<br>Téléphone : [numéro] – E-mail : [adresse e-mail]<br>Directeur·rice de la publication : [nom]</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Hébergement</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Nom de l'hébergeur] – [adresse] – [téléphone]</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Conception du site</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Votre nom ou celui de votre agence] – [site ou e-mail]</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Propriété intellectuelle</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>L'ensemble des contenus de ce site (textes, images, logo) est la propriété de [Raison sociale], sauf mention contraire. Toute reproduction sans autorisation est interdite.</p>
<!-- /wp:paragraph -->
HTML;

$privacy = <<<'HTML'
<!-- wp:paragraph -->
<p><mark>Modèle à adapter à la situation réelle de l'entreprise avant la mise en ligne.</mark></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Responsable du traitement</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Raison sociale], [adresse], joignable à [adresse e-mail].</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Données collectées</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Via le formulaire de contact : nom, adresse e-mail, téléphone et message. Ces données servent uniquement à répondre à votre demande de rendez-vous et ne sont jamais revendues.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Durée de conservation</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les demandes sont conservées [durée, par exemple 3 ans] à compter du dernier contact.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Lister ici les cookies utilisés, par exemple mesure d'audience, et la manière de les refuser.]</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Vos droits</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vous disposez d'un droit d'accès, de rectification, d'effacement et d'opposition sur vos données. Pour l'exercer, écrivez à [adresse e-mail]. Vous pouvez aussi saisir la CNIL (cnil.fr).</p>
<!-- /wp:paragraph -->
HTML;

$contact = str_replace( '{{FORM}}', $form_block, $contact );

$pages = array(
	'accueil'                    => array( 'Accueil', $home, 0 ),
	'prestations'                => array( 'Prestations & tarifs', $prestations, 1 ),
	'le-salon'                   => array( 'Le salon', $salon, 2 ),
	'contact'                    => array( 'Contact', $contact, 3 ),
	'mentions-legales'           => array( 'Mentions légales', $mentions, 10 ),
	'politique-de-confidentialite' => array( 'Politique de confidentialité', $privacy, 11 ),
);
$ids = array();
foreach ( $pages as $slug => $p ) {
	$ids[ $slug ] = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $p[0],
		'post_name'    => $slug,
		'post_content' => wp_slash( $T( $p[1] ) ),
		'menu_order'   => $p[2],
	) );
}
update_post_meta( $ids['accueil'], '_wp_page_template', 'page-no-title' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $ids['accueil'] );
update_option( 'wp_page_for_privacy_policy', $ids['politique-de-confidentialite'] );
update_option( 'default_comment_status', 'closed' );

// ---------- Menu principal ----------
$nav = '';
foreach ( array( 'accueil', 'prestations', 'le-salon', 'contact' ) as $slug ) {
	$nav .= '<!-- wp:navigation-link ' . wp_json_encode( array(
		'label' => $pages[ $slug ][0],
		'type'  => 'page',
		'id'    => $ids[ $slug ],
		'url'   => get_permalink( $ids[ $slug ] ),
		'kind'  => 'post-type',
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ' /-->';
}
wp_insert_post( array(
	'post_type'    => 'wp_navigation',
	'post_status'  => 'publish',
	'post_title'   => 'Menu principal',
	'post_content' => wp_slash( $nav ),
) );

// ---------- Pied de page personnalisé ----------
$footer = <<<'HTML'
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"textColor":"base"} -->
<h3 class="wp-block-heading has-base-color has-text-color">Maison Lumière</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Coiffure &amp; beauté à Lyon 6e depuis 2012.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":4,"textColor":"accent-1"} -->
<h4 class="wp-block-heading has-accent-1-color has-text-color">Horaires</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Mar – ven : 9 h – 19 h (jeu. 20 h)<br>Samedi : 8 h 30 – 18 h<br>Dimanche et lundi : fermé</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":4,"textColor":"accent-1"} -->
<h4 class="wp-block-heading has-accent-1-color has-text-color">Contact</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>12 rue des Lilas, 69006 Lyon<br>04&nbsp;00&nbsp;00&nbsp;00&nbsp;00<br>contact@exemple.fr</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:paragraph {"align":"center","textColor":"accent-6","fontSize":"small"} -->
<p class="has-text-align-center has-accent-6-color has-text-color has-small-font-size">© 2026 Maison Lumière · <a href="{{URL}}/mentions-legales/">Mentions légales</a> · <a href="{{URL}}/politique-de-confidentialite/">Confidentialité</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML;
$fid = wp_insert_post( array(
	'post_type'    => 'wp_template_part',
	'post_status'  => 'publish',
	'post_title'   => 'Pied de page',
	'post_name'    => 'footer',
	'post_content' => wp_slash( $T( $footer ) ),
) );
wp_set_object_terms( $fid, get_stylesheet(), 'wp_theme' );
wp_set_object_terms( $fid, 'footer', 'wp_template_part_area' );

flush_rewrite_rules();
echo "OK\n";
