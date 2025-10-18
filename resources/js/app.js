import './bootstrap';

import Alpine from 'alpinejs';

// Import Leaflet CSS
import 'leaflet/dist/leaflet.css';
import 'leaflet-geosearch/dist/geosearch.css';

// Import custom Leaflet styles
import '../css/leaflet-custom.css';

// Import map selector module
import './map-selector';

window.Alpine = Alpine;

Alpine.start();
