// Assemble blueprint.json (WordPress Playground) à partir de build.php
// et écrit le lien Playground prêt à ouvrir dans playground-url.txt.
const fs = require('fs');
const path = require('path');
const dir = __dirname;
const css = fs.readFileSync(path.join(dir, 'extra.css'), 'utf8').trim();
const php = fs.readFileSync(path.join(dir, 'build.php'), 'utf8').replace('{{EXTRA_CSS}}', css);

const blueprint = {
  $schema: 'https://playground.wordpress.net/blueprint-schema.json',
  meta: { title: 'Maison Lumière — démo salon de coiffure', author: 'demo' },
  landingPage: '/',
  preferredVersions: { php: '8.3', wp: 'latest' },
  features: { networking: true },
  login: true,
  steps: [
    { step: 'setSiteLanguage', language: 'fr_FR' },
    { step: 'installTheme', themeData: { resource: 'wordpress.org/themes', slug: 'twentytwentyfive' }, options: { activate: true } },
    { step: 'installPlugin', pluginData: { resource: 'wordpress.org/plugins', slug: 'contact-form-7' }, options: { activate: true } },
    { step: 'setSiteOptions', options: {
      blogname: 'Maison Lumière',
      blogdescription: 'Coiffure & beauté à Lyon',
      permalink_structure: '/%postname%/',
      timezone_string: 'Europe/Paris',
      date_format: 'j F Y',
      time_format: 'G\\hi',
    } },
    { step: 'runPHP', code: php },
  ],
};

const json = JSON.stringify(blueprint, null, 2);
fs.writeFileSync(path.join(dir, 'blueprint.json'), json);
// JSON 100 % ASCII (accents en \uXXXX) pour éviter tout souci d'encodage dans l'URL.
const ascii = JSON.stringify(blueprint).replace(/[\u007f-￿]/g, c => '\\u' + c.charCodeAt(0).toString(16).padStart(4, '0'));
const url = 'https://playground.wordpress.net/#' + Buffer.from(ascii, 'ascii').toString('base64');
fs.writeFileSync(path.join(dir, 'playground-url.txt'), url);
console.log('blueprint.json écrit, URL de', url.length, 'caractères');
