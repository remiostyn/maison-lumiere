# Démo « Maison Lumière » : guide d'utilisation et de vente

## Contenu du dossier
| Fichier | Rôle |
|---|---|
| `build.php` | Le site lui-même : pages, couleurs, menu, pied de page, formulaire |
| `extra.css` | Styles supplémentaires (formulaire, tableaux, pied de page) |
| `make-blueprint.js` | Assemble `blueprint.json` et le lien Playground (`node make-blueprint.js`) |
| `blueprint.json` | Recette WordPress Playground qui reconstruit le site en une minute |
| `playground-url.txt` | Lien direct vers une démo neuve |
| `make-launcher.js` / `launcher.html` | Page de présentation avec le bouton « Ouvrir la démo » |

## Montrer la démo à un prospect
1. Ouvrir la page de lancement et cliquer sur **Ouvrir la démo en direct**.
   Si le lien ne s'ouvre pas, copier le contenu de `playground-url.txt` dans la barre d'adresse.
2. Attendre environ une minute : WordPress s'installe dans le navigateur.
3. Montrer les pages, puis le côté administration (barre noire en haut) pour prouver que le client pourra modifier ses textes seul.
4. Chaque ouverture repart de zéro : on peut tout modifier pendant le rendez-vous sans risque.

Si Playground affiche « Playground crashed / WordPress bridge did not start » : un bloqueur de publicité (uBlock Origin, par exemple) bloque Playground. Le désactiver pour playground.wordpress.net (icône de l'extension → bouton d'alimentation), ou ouvrir la démo dans Edge ou Chrome. Testez la démo sur votre ordinateur de rendez-vous avant de voir le prospect.

Astuce : sur mobile, la démo fonctionne aussi. Montrer le site sur le téléphone du prospect fait souvent effet.

## Adapter la démo à un vrai client
- Nom, adresse, téléphone, horaires, tarifs : dans `build.php` (rechercher « Maison Lumière », « 12 rue des Lilas », « 04&nbsp;00 »).
- Couleurs : tableau `$palette` en haut de `build.php`.
- Puis relancer `node make-blueprint.js` pour obtenir un nouveau lien.
- Remplacer les photos de démonstration (Unsplash, voir `CREDITS-PHOTOS.md`) par les **vraies photos** du salon : vitrine, équipe, réalisations. C'est ce qui fait la différence.
- Remplacer les avis d'exemple par de **vrais avis** (Google, par exemple). Ne jamais publier de faux avis.

## Mise en ligne pour le client
1. Le client (ou vous, à son nom) prend un hébergement WordPress et un nom de domaine.
   Exemples : o2switch, Hostinger, OVHcloud, Infomaniak.
2. Installer WordPress sur l'hébergement (installation en un clic chez la plupart des hébergeurs).
3. Transférer le site :
   - **Simple** : dans la démo Playground, installer l'extension *All-in-One WP Migration*, exporter le site, puis l'importer sur l'hébergement avec la même extension.
   - **Ou** reconstruire les pages à la main en copiant les blocs depuis la démo (Éditeur → menu ⋮ → Éditeur de code).
4. Vérifier ensuite :
   - le formulaire de contact : configurer l'envoi d'e-mails (extension *WP Mail SMTP*) et faire un test ;
   - les mentions légales et la politique de confidentialité, complétées avec les vraies informations ;
   - un bandeau cookies si le site utilise des statistiques (par exemple *Complianz*) ;
   - le HTTPS (certificat SSL, gratuit chez la plupart des hébergeurs) ;
   - une extension SEO (*Yoast SEO* ou *Rank Math*) et la fiche **Google Business Profile** du salon ;
   - les sauvegardes automatiques (*UpdraftPlus*) et les mises à jour.
5. Créer un compte **Éditeur** pour le client (pas Administrateur) pour éviter les fausses manipulations.

## Points juridiques à prévoir
- Un **devis signé** et des conditions de vente : prix, contenu, délais, nombre de retouches.
- Préciser qui possède le site, le domaine et l'hébergement après paiement (idéalement le client).
- Images : uniquement celles du client ou des banques libres de droits.
- Vous devez avoir un statut pour facturer (micro-entreprise, par exemple).

## Idées d'offres
- **Site vitrine** : création + mise en ligne + formation d'une heure.
- **Maintenance mensuelle** : mises à jour, sauvegardes, petites modifications de textes. C'est un revenu récurrent.
- **Options** : réservation en ligne, galerie photo, référencement local, traduction.

Les prix pratiqués varient beaucoup selon la région et l'expérience : comparez les offres de freelances près de chez vous avant de fixer les vôtres.
