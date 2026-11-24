# Maison Lumière : démo de site vitrine WordPress

Site vitrine de démonstration pour un salon de coiffure et institut de beauté **fictif**, construit avec WordPress (thème Twenty Twenty-Five) et lancé dans [WordPress Playground](https://playground.wordpress.net/), sans installation ni hébergement.

## Ouvrir la démo

Ce lien construit une copie neuve du site dans votre navigateur (environ une minute) :

https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/remiostyn/maison-lumiere/main/blueprint.json

Si la page affiche « WordPress bridge did not start », désactivez votre bloqueur de publicité (uBlock Origin, par exemple) pour playground.wordpress.net, ou ouvrez le lien dans Edge ou Chrome.

## Contenu du site

- **Accueil** : savoir-faire, avantages, avis d'exemple, appel à réserver
- **Prestations & tarifs**
- **Le salon** : histoire, équipe, engagements, galerie
- **Contact** : horaires et formulaire de rendez-vous (Contact Form 7)
- **Mentions légales** et **politique de confidentialité** : modèles à compléter

L'entreprise, l'adresse, l'équipe et les avis sont inventés. Les photos viennent d'Unsplash (licence gratuite, voir [CREDITS-PHOTOS.md](CREDITS-PHOTOS.md)).

## Fichiers

| Fichier | Rôle |
|---|---|
| `build.php` | Contenu du site : pages, couleurs, menu, pied de page, formulaire, photos |
| `extra.css` | Styles supplémentaires |
| `make-blueprint.js` | Assemble `blueprint.json` et `playground-url.txt` (`node make-blueprint.js`) |
| `blueprint.json` | Recette WordPress Playground |
| `make-launcher.js` / `launcher.html` | Page de présentation avec un bouton vers la démo |
| `GUIDE.md` | Adapter la démo à un client, la mettre en ligne, la vendre |

Après une modification de `build.php` ou `extra.css` : lancer `node make-blueprint.js`, puis envoyer `blueprint.json` sur GitHub. Le lien ci-dessus utilise toujours la dernière version.
