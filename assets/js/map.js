/**
 * Capiznon Geo - Map Module
 * Interactive map using Leaflet.js
 */

(function() {
    'use strict';

    // Map instance
    let map = null;
    let markersLayer = null;
    let currentFilters = {};
    let allLocations = [];
    let selectedLocationId = null;

    // Marker icons by type
    const markerIcons = {
        default: '📍',
        restaurant: '🍽️',
        cafe: '☕',
        bar: '🍺',
        hotel: '🏨',
        shop: '🛍️',
        attraction: '⭐',
        beach: '🏖️',
        church: '⛪'
    };

    // Compute nearby search radius (in km) based on current zoom level
    function getNearbyRadiusForZoom() {
        if (!map) return 10;
        const z = map.getZoom() || 14;

        if (z >= 17) return 1;      // very close in
        if (z >= 15) return 2;
        if (z >= 13) return 4;
        if (z >= 11) return 7;
        return 10;                  // zoomed out
    }

    /**
     * Initialize the map
     */
    function initMap(containerId, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) return null;

        const settings = window.capiznonGeo || {};
        const lat = options.lat || settings.defaultLat || 11.5853;
        const lng = options.lng || settings.defaultLng || 122.7511;
        const zoom = options.zoom || settings.defaultZoom || 14;

        // Create map
        map = L.map(containerId, {
            center: [lat, lng],
            zoom: zoom,
            zoomControl: false,
            scrollWheelZoom: true
        });

        // Add zoom control to top-right
        L.control.zoom({ position: 'topright' }).addTo(map);

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        // Initialize marker cluster group
        markersLayer = L.markerClusterGroup({
            showCoverageOnHover: false,
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            iconCreateFunction: function(cluster) {
                const count = cluster.getChildCount();
                let size = 'small';
                if (count > 10) size = 'medium';
                if (count > 50) size = 'large';
                
                return L.divIcon({
                    html: `<div>${count}</div>`,
                    className: `marker-cluster marker-cluster-${size}`,
                    iconSize: L.point(40, 40)
                });
            }
        });

        map.addLayer(markersLayer);

        // Track when the user manually drags the map – this should switch
        // nearby context back to the map center instead of a selected place.
        map.on('dragstart', function() {
            selectedLocationId = null;
        });

        // When the map stops moving and no specific place is selected,
        // load nearby locations around the current map center with a
        // radius that depends on zoom level.
        map.on('moveend', async function() {
            if (selectedLocationId !== null) return;

            const center = map.getCenter();
            const radius = getNearbyRadiusForZoom();
            const data = await loadNearby(center.lat, center.lng, radius, { limit: 50 });
            if (!data || !Array.isArray(data.locations)) return;

            document.dispatchEvent(new CustomEvent('cg:locationsLoaded', {
                detail: {
                    locations: data.locations,
                    total: data.locations.length
                }
            }));
        });

        // Attach popup open handler once, for loading personal visit stats
        if (settings.isLoggedIn && !map._cgVisitsPopupListenerAttached) {
            map.on('popupopen', handlePopupOpenForVisits);
            map._cgVisitsPopupListenerAttached = true;
        }

        return map;
    }

    /**
     * Create custom marker icon
     */
    function createMarkerIcon(location) {
        const icon = markerIcons[location.marker_icon] || markerIcons.default;
        const color = location.marker_color || '#e74c3c';
        const isFeatured = location.featured;

        return L.divIcon({
            className: 'cg-marker-wrapper',
            html: `
                <div class="cg-marker ${isFeatured ? 'cg-marker-featured' : ''}" style="background-color: ${color}">
                    <span class="cg-marker-inner">${icon}</span>
                </div>
            `,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -36]
        });
    }

    /**
     * Create popup content
     */
    function createPopupContent(location) {
        const types = (location.types || []).map(t => t.name).join(', ');
        const cuisines = (location.cuisines || []).map(c => c.name);
        const thumbnail = location.thumbnail || '';
        const settings = window.capiznonGeo || {};
        const strings = settings.strings || {};
        
        let html = '<div class="cg-popup">';
        
        if (thumbnail) {
            html += `<img src="${thumbnail}" alt="${location.title}" class="cg-popup-image">`;
        }
        
        html += '<div class="cg-popup-content">';
        
        if (types) {
            html += `<div class="cg-popup-type">${types}</div>`;
        }
        
        html += `<h3 class="cg-popup-title">${location.title}</h3>`;

        if (cuisines.length) {
            html += `
                <div class="cg-popup-cuisines">
                    ${cuisines.map(name => `<span class="cg-badge-cuisine">${name}</span>`).join('')}
                </div>
            `;
        }
        
        if (location.excerpt) {
            html += `<p class="cg-popup-excerpt">${location.excerpt}</p>`;
        }

        // Personal visit stats container (only meaningful for logged-in users)
        if (settings.isLoggedIn) {
            html += `<div class="cg-popup-my-visits" data-location-id="${location.id}">
                <div class="cg-popup-my-visits-loading">${strings.loadingYourVisits || 'Loading your visits…'}</div>
            </div>`;
        }
        
        html += '<div class="cg-popup-footer">';
        html += `<a href="${location.url}" class="cg-popup-btn cg-popup-btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Details
        </a>`;
        
        if (location.lat && location.lng) {
            const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${location.lat},${location.lng}`;
            html += `<a href="${directionsUrl}" target="_blank" rel="noopener" class="cg-popup-btn cg-popup-btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Directions
            </a>`;
        }
        
        html += '</div>';
        
        // Visit button
        if (settings.isLoggedIn) {
            html += `<button type="button" class="cg-popup-visit-btn" data-location-id="${location.id}" data-location-title="${location.title}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                ${strings.recordVisit || 'Record Visit'}
            </button>`;
        } else {
            html += `<a href="${settings.loginUrl}" class="cg-popup-visit-btn cg-popup-visit-btn-login">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                ${strings.loginToVisit || 'Login to record visits'}
            </a>`;
        }
        
        html += '</div></div>';
        
        return html;
    }

    /**
     * Handle popup open to load personal visit stats
     */
    async function handlePopupOpenForVisits(e) {
        const settings = window.capiznonGeo || {};
        if (!settings.isLoggedIn) return;

        const popupContent = e.popup && e.popup.getElement();
        if (!popupContent) return;

        const statsContainer = popupContent.querySelector('.cg-popup-my-visits');
        if (!statsContainer) return;

        const locationId = parseInt(statsContainer.dataset.locationId, 10);
        if (!locationId) return;

        const strings = settings.strings || {};
        statsContainer.innerHTML = `<div class="cg-popup-my-visits-loading">${strings.loadingYourVisits || 'Loading your visits…'}</div>`;

        let baseUrl = settings.restUrl || '/wp-json/capiznon-geo/v1/';
        if (!baseUrl.endsWith('/')) baseUrl += '/';

        try {
            const response = await fetch(`${baseUrl}visits/location/${locationId}`, {
                headers: {
                    'X-WP-Nonce': settings.nonce || ''
                }
            });

            if (!response.ok) throw new Error('Failed to load visits');

            const data = await response.json();
            const visits = Array.isArray(data.user_visits) ? data.user_visits : [];

            if (visits.length === 0) {
                statsContainer.innerHTML = `<div class="cg-popup-my-visits-empty">${strings.noVisitsYetMy || 'You have no visits recorded here yet.'}</div>`;
                return;
            }

            const visitsCount = visits.length;

            // Compute averages from non-null ratings
            const foodRatings = visits.map(v => v.food_rating).filter(v => typeof v === 'number' && !isNaN(v));
            const serviceRatings = visits.map(v => v.service_rating).filter(v => typeof v === 'number' && !isNaN(v));

            const avg = arr => arr.length ? (arr.reduce((sum, v) => sum + v, 0) / arr.length) : null;
            const avgFood = avg(foodRatings);
            const avgService = avg(serviceRatings);

            const lastVisit = visits[0];

            const formatDate = iso => {
                if (!iso) return '';
                const d = new Date(iso);
                if (Number.isNaN(d.getTime())) return iso;
                return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
            };

            const renderStars = value => {
                if (!value) return '';
                const full = '★'.repeat(value);
                const empty = '☆'.repeat(5 - value);
                return `<span class="cg-popup-my-visits-stars" aria-label="${value} / 5">${full}${empty}</span>`;
            };

            statsContainer.innerHTML = `
                <div class="cg-popup-my-visits-inner">
                    <div class="cg-popup-my-visits-header">
                        <span class="cg-popup-my-visits-label">${strings.myVisits || 'My visits here'}</span>
                        <span class="cg-popup-my-visits-count">${visitsCount} ${strings.visitsCountLabel || 'visits'}</span>
                    </div>
                    <div class="cg-popup-my-visits-averages">
                        <div class="cg-popup-my-visits-metric">
                            <span class="cg-popup-my-visits-metric-label">${strings.foodShort || 'Food'}</span>
                            <span class="cg-popup-my-visits-metric-value">
                                ${avgFood ? avgFood.toFixed(1) : '–'}
                            </span>
                            ${avgFood ? renderStars(Math.round(avgFood)) : ''}
                        </div>
                        <div class="cg-popup-my-visits-metric">
                            <span class="cg-popup-my-visits-metric-label">${strings.serviceShort || 'Service'}</span>
                            <span class="cg-popup-my-visits-metric-value">
                                ${avgService ? avgService.toFixed(1) : '–'}
                            </span>
                            ${avgService ? renderStars(Math.round(avgService)) : ''}
                        </div>
                    </div>
                    <div class="cg-popup-my-visits-last">
                        <div class="cg-popup-my-visits-last-label">${strings.lastVisit || 'Last visit'}</div>
                        <div class="cg-popup-my-visits-last-date">${formatDate(lastVisit.visit_date)}</div>
                        <div class="cg-popup-my-visits-last-ratings">
                            ${lastVisit.food_rating ? `<span>${strings.foodShort || 'Food'}: ${renderStars(lastVisit.food_rating)}</span>` : ''}
                            ${lastVisit.service_rating ? `<span>${strings.serviceShort || 'Service'}: ${renderStars(lastVisit.service_rating)}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Error loading personal visits:', error);
            statsContainer.innerHTML = `<div class="cg-popup-my-visits-error">${strings.error || 'Error loading locations'}</div>`;
        }
    }

    /**
     * Add markers to map
     */
    function addMarkers(locations) {
        markersLayer.clearLayers();
        allLocations = locations;

        locations.forEach(location => {
            if (!location.lat || !location.lng) return;

            const marker = L.marker([location.lat, location.lng], {
                icon: createMarkerIcon(location)
            });

            marker.bindPopup(createPopupContent(location), {
                maxWidth: 320,
                minWidth: 280
            });

            marker.locationData = location;
            markersLayer.addLayer(marker);
        });
    }

    /**
     * Load locations from API
     */
    async function loadLocations(filters = {}) {
        const settings = window.capiznonGeo || {};
        let baseUrl = settings.restUrl || '/wp-json/capiznon-geo/v1/';
        
        // Ensure proper URL format
        if (!baseUrl.endsWith('/')) baseUrl += '/';
        
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value) params.append(key, value);
        });

        const url = `${baseUrl}locations${params.toString() ? '?' + params.toString() : ''}`;
        
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Failed to load locations');
            
            const data = await response.json();
            addMarkers(data.locations);
            
            // Dispatch event for other components
            document.dispatchEvent(new CustomEvent('cg:locationsLoaded', {
                detail: { locations: data.locations, total: data.total }
            }));
            
            return data;
        } catch (error) {
            console.error('Error loading locations:', error);
            document.dispatchEvent(new CustomEvent('cg:locationsError', {
                detail: { error }
            }));
            return null;
        }
    }

    /**
     * Load nearby locations
     */
    async function loadNearby(lat, lng, radius = 1, options = {}) {
        const settings = window.capiznonGeo || {};
        let baseUrl = settings.restUrl || '/wp-json/capiznon-geo/v1/';
        
        if (!baseUrl.endsWith('/')) baseUrl += '/';
        
        const params = new URLSearchParams({
            lat: lat,
            lng: lng,
            radius: radius,
            ...options
        });

        try {
            const response = await fetch(`${baseUrl}locations/nearby?${params}`);
            if (!response.ok) throw new Error('Failed to load nearby locations');
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error loading nearby locations:', error);
            return null;
        }
    }

    /**
     * Filter locations
     */
    function filterLocations(filters) {
        currentFilters = { ...currentFilters, ...filters };
        loadLocations(currentFilters);
    }

    /**
     * Clear all filters
     */
    function clearFilters() {
        currentFilters = {};
        loadLocations();
    }

    /**
     * Focus on a specific location
     *
     * Also loads nearby locations (within 1km) around that place and
     * updates the sidebar list via the cg:locationsLoaded event.
     */
    async function focusLocation(locationId) {
        let targetMarker = null;

        markersLayer.eachLayer(marker => {
            if (marker.locationData && marker.locationData.id === locationId) {
                targetMarker = marker;
            }
        });

        if (!targetMarker) return;

        selectedLocationId = locationId;
        map.setView(targetMarker.getLatLng(), 17);
        targetMarker.openPopup();

        const loc = targetMarker.locationData;
        if (!loc || !loc.lat || !loc.lng) return;

        const radius = getNearbyRadiusForZoom();
        const data = await loadNearby(loc.lat, loc.lng, radius, { exclude: locationId, limit: 50 });
        if (!data || !Array.isArray(data.locations)) return;

        document.dispatchEvent(new CustomEvent('cg:locationsLoaded', {
            detail: {
                locations: data.locations,
                total: data.locations.length
            }
        }));
    }

    /**
     * Fit map to show all markers
     */
    function fitToMarkers() {
        if (markersLayer.getLayers().length > 0) {
            map.fitBounds(markersLayer.getBounds(), { padding: [50, 50] });
        }
    }

    /**
     * Get current map bounds
     */
    function getBounds() {
        return map ? map.getBounds() : null;
    }

    /**
     * Search locations
     */
    function searchLocations(query) {
        filterLocations({ search: query });
    }

    // Expose public API
    window.CapiznonGeoMap = {
        init: initMap,
        load: loadLocations,
        loadNearby: loadNearby,
        filter: filterLocations,
        clearFilters: clearFilters,
        focus: focusLocation,
        fitToMarkers: fitToMarkers,
        getBounds: getBounds,
        search: searchLocations,
        getMap: () => map,
        getMarkers: () => markersLayer,
        getAllLocations: () => allLocations
    };

})();
