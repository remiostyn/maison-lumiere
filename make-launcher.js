// Génère launcher.html : une page avec un bouton qui ouvre la démo dans WordPress Playground.
const fs = require('fs');
const path = require('path');
const url = fs.readFileSync(path.join(__dirname, 'playground-url.txt'), 'utf8').trim();

const html = `<title>Maison Lumière, démo</title>
<meta name="claude:viewport-fit-sides" content="cover">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Manrope:wght@400;600&display=swap">
<style>
/* Layout : une seule carte centrée, comme une carte de visite de salon. */
:root{--bg:#FBF8F5;--fg:#2B2320;--muted:#6f625b;--accent:#7A5442;--soft:#F4ECE6;--line:#E3D3C9;--on-accent:#FBF8F5;
--display:"Cormorant Garamond",Georgia,serif;--body:Manrope,system-ui,sans-serif}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){--bg:#1d1816;--fg:#f1e9e3;--muted:#b7a89f;--accent:#d8a88e;--soft:#28211e;--line:#3a302b;--on-accent:#1d1816;color-scheme:dark}}
:root[data-theme="dark"]{--bg:#1d1816;--fg:#f1e9e3;--muted:#b7a89f;--accent:#d8a88e;--soft:#28211e;--line:#3a302b;--on-accent:#1d1816;color-scheme:dark}
body{background:var(--bg);color:var(--fg);font-family:var(--body);font-size:16px;line-height:1.6;
padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px));padding-block:48px}
main{max-width:620px;margin:0 auto;display:grid;gap:28px}
.eyebrow{font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--accent);margin:0}
h1{font-family:var(--display);font-weight:600;font-size:clamp(40px,8vw,60px);line-height:1.05;margin:0;text-wrap:balance}
p{margin:0}
.lead{color:var(--muted);font-size:17px}
.cta{display:inline-block;justify-self:start;background:var(--accent);color:var(--on-accent);text-decoration:none;font-weight:600;padding:14px 26px;border-radius:999px}
.cta:focus-visible{outline:3px solid var(--fg);outline-offset:3px}
.card{background:var(--soft);border:1px solid var(--line);border-radius:14px;padding:22px 24px;display:grid;gap:10px}
.card h2{font-family:var(--display);font-size:26px;font-weight:600;margin:0}
ul{margin:0;padding-left:20px;display:grid;gap:4px}
.note{font-size:13px;color:var(--muted)}
</style>
<main>
  <p class="eyebrow">Site vitrine de démonstration · WordPress</p>
  <h1>Maison Lumière, coiffure &amp; beauté</h1>
  <p class="lead">Un site complet pour salon de coiffure, prêt à personnaliser pour un client. Le bouton ouvre une copie neuve du site dans WordPress Playground, sans installation ni compte.</p>
  <a class="cta" id="open-demo" href="${url}" target="_blank" rel="noopener">Ouvrir la démo en direct</a>
  <section class="card">
    <h2>Ce que contient le site</h2>
    <ul>
      <li>Accueil avec savoir-faire, avantages, avis et appel à réserver</li>
      <li>Prestations &amp; tarifs en tableaux</li>
      <li>Le salon : histoire, équipe, engagements</li>
      <li>Contact : adresse, horaires et formulaire de rendez-vous</li>
      <li>Mentions légales et politique de confidentialité à compléter</li>
    </ul>
  </section>
  <p class="note">Le chargement prend environ une minute. Chaque ouverture repart du site d'origine : les modifications faites dans Playground ne sont pas gardées. Entreprise, adresse et avis sont fictifs.</p>
</main>
`;
fs.writeFileSync(path.join(__dirname, 'launcher.html'), html);
console.log('launcher.html écrit');
