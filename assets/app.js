import './bootstrap.js';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
// Importer vue js
import './js/app.js';
// Importer les styles Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';

// Importer les scripts Bootstrap
import 'bootstrap';

import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
