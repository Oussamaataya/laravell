import L from 'leaflet';
import { OpenStreetMapProvider } from 'leaflet-geosearch';

// Fix for default marker icon issue with Webpack/Vite
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

/**
 * Initialise une carte interactive pour sélectionner une localisation
 * @param {Object} options - Options de configuration
 * @param {string} options.containerId - ID du conteneur de la carte
 * @param {number} options.defaultLat - Latitude par défaut
 * @param {number} options.defaultLng - Longitude par défaut
 * @param {number} options.zoom - Niveau de zoom par défaut
 * @param {string} options.latInputId - ID du champ input pour la latitude
 * @param {string} options.lngInputId - ID du champ input pour la longitude
 * @param {string} options.addressInputId - ID du champ input pour l'adresse
 * @param {string} options.cityInputId - ID du champ input pour la ville
 * @returns {Object} - Objet contenant la carte et le marker
 */
export function initMapSelector(options = {}) {
    const {
        containerId = 'map',
        defaultLat = 36.8065,
        defaultLng = 10.1815,
        zoom = 13,
        latInputId = 'latitude',
        lngInputId = 'longitude',
        addressInputId = 'address',
        cityInputId = 'city'
    } = options;

    // Créer la carte
    const map = L.map(containerId).setView([defaultLat, defaultLng], zoom);

    // Ajouter le layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    // Créer un marker draggable
    const marker = L.marker([defaultLat, defaultLng], { 
        draggable: true 
    }).addTo(map);

    // Provider pour la recherche d'adresse
    const provider = new OpenStreetMapProvider();

    // Fonction pour mettre à jour les champs du formulaire
    function updateFormFields(lat, lng, address = '', city = '') {
        const latInput = document.getElementById(latInputId);
        const lngInput = document.getElementById(lngInputId);
        const addressInput = document.getElementById(addressInputId);
        const cityInput = document.getElementById(cityInputId);

        if (latInput) latInput.value = lat.toFixed(6);
        if (lngInput) lngInput.value = lng.toFixed(6);
        if (addressInput && address) addressInput.value = address;
        if (cityInput && city) cityInput.value = city;
    }

    // Fonction pour obtenir l'adresse à partir des coordonnées (reverse geocoding)
    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`
            );
            const data = await response.json();
            
            if (data && data.address) {
                const address = data.display_name;
                const city = data.address.city || 
                            data.address.town || 
                            data.address.village || 
                            data.address.county || 
                            '';
                
                updateFormFields(lat, lng, address, city);
                
                // Afficher un popup avec l'adresse
                marker.bindPopup(`<b>${address}</b>`).openPopup();
            }
        } catch (error) {
            console.error('Erreur lors du reverse geocoding:', error);
            updateFormFields(lat, lng);
        }
    }

    // Gérer le clic sur la carte
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        marker.setLatLng([lat, lng]);
        reverseGeocode(lat, lng);
    });

    // Gérer le drag du marker
    marker.on('dragend', function(e) {
        const { lat, lng } = e.target.getLatLng();
        reverseGeocode(lat, lng);
    });

    // Initialiser avec les coordonnées par défaut
    reverseGeocode(defaultLat, defaultLng);

    // Retourner l'objet avec les méthodes utiles
    return {
        map,
        marker,
        
        /**
         * Définir la position du marker et centrer la carte
         */
        setPosition(lat, lng) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], zoom);
            reverseGeocode(lat, lng);
        },
        
        /**
         * Rechercher une adresse et placer le marker
         */
        async searchAddress(query) {
            try {
                const results = await provider.search({ query });
                if (results.length > 0) {
                    const { x, y, label } = results[0];
                    this.setPosition(y, x);
                    return results;
                }
                return [];
            } catch (error) {
                console.error('Erreur lors de la recherche:', error);
                return [];
            }
        },

        /**
         * Détruire la carte
         */
        destroy() {
            map.remove();
        }
    };
}

/**
 * Initialise une carte en lecture seule pour afficher une localisation
 * @param {Object} options - Options de configuration
 * @param {string} options.containerId - ID du conteneur de la carte
 * @param {number} options.lat - Latitude
 * @param {number} options.lng - Longitude
 * @param {number} options.zoom - Niveau de zoom
 * @param {string} options.popupText - Texte du popup
 * @returns {Object} - Objet contenant la carte
 */
export function initMapViewer(options = {}) {
    const {
        containerId = 'map-viewer',
        lat = 36.8065,
        lng = 10.1815,
        zoom = 15,
        popupText = 'Localisation de l\'événement'
    } = options;

    // Créer la carte
    const map = L.map(containerId).setView([lat, lng], zoom);

    // Ajouter le layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    // Ajouter un marker
    const marker = L.marker([lat, lng]).addTo(map);
    
    if (popupText) {
        marker.bindPopup(popupText).openPopup();
    }

    return {
        map,
        marker,
        
        /**
         * Mettre à jour la position
         */
        updatePosition(newLat, newLng, newPopupText = null) {
            marker.setLatLng([newLat, newLng]);
            map.setView([newLat, newLng], zoom);
            if (newPopupText) {
                marker.bindPopup(newPopupText).openPopup();
            }
        },
        
        /**
         * Détruire la carte
         */
        destroy() {
            map.remove();
        }
    };
}

// Export global pour utilisation dans les blades
window.initMapSelector = initMapSelector;
window.initMapViewer = initMapViewer;
