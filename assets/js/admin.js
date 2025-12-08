/**
 * Capiznon Geo - Admin JavaScript
 */

(function($) {
    'use strict';

    const settings = window.capiznonGeoAdmin || {};

    $(document).ready(function() {
        initMapPicker();
        initGalleryUploader();
        initCuisineMetaBoxVisibility();
    });

    /**
     * Show Cuisines taxonomy box only for Food & Dining locations
     */
    function initCuisineMetaBoxVisibility() {
        // Only on cg_location edit screens
        if (!$('body').hasClass('post-type-cg_location')) return;

        const typeBox = document.getElementById('location_typediv');
        const cuisineBox = document.getElementById('location_cuisinediv');

        if (!typeBox || !cuisineBox) return;

        function isFoodSelected() {
            // Checkboxes UI
            const checked = typeBox.querySelectorAll('input[type="checkbox"]:checked');
            for (const input of checked) {
                if (input.value === 'food-dining') return true;
            }

            // Fallback: select dropdown UI
            const select = typeBox.querySelector('select');
            if (select && select.value === 'food-dining') return true;

            return false;
        }

        function updateVisibility() {
            if (isFoodSelected()) {
                cuisineBox.style.display = '';
            } else {
                cuisineBox.style.display = 'none';
            }
        }

        // Initial state
        updateVisibility();

        // Listen for changes in the type box
        typeBox.addEventListener('change', updateVisibility);
    }

    /**
     * Initialize map picker for coordinates
     */
    function initMapPicker() {
        const mapContainer = document.getElementById('cg-map-picker');
        if (!mapContainer) return;

        const latInput = document.getElementById('cg_latitude');
        const lngInput = document.getElementById('cg_longitude');

        const lat = parseFloat(latInput?.value) || settings.defaultLat || 11.5853;
        const lng = parseFloat(lngInput?.value) || settings.defaultLng || 122.7511;
        const zoom = settings.defaultZoom || 14;

        // Initialize map
        const map = L.map('cg-map-picker').setView([lat, lng], zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Add marker
        let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        // Update inputs when marker is dragged
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            latInput.value = pos.lat.toFixed(8);
            lngInput.value = pos.lng.toFixed(8);
        });

        // Update marker when map is clicked
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            latInput.value = e.latlng.lat.toFixed(8);
            lngInput.value = e.latlng.lng.toFixed(8);
        });

        // Update marker when inputs change
        function updateMarkerFromInputs() {
            const newLat = parseFloat(latInput.value);
            const newLng = parseFloat(lngInput.value);
            if (!isNaN(newLat) && !isNaN(newLng)) {
                marker.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng]);
            }
        }

        latInput?.addEventListener('change', updateMarkerFromInputs);
        lngInput?.addEventListener('change', updateMarkerFromInputs);

        // Fix map display issues in meta boxes
        setTimeout(function() {
            map.invalidateSize();
        }, 100);

        // Re-invalidate when meta box is toggled
        $(document).on('postbox-toggled', function() {
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        });
    }

    /**
     * Initialize gallery uploader
     */
    function initGalleryUploader() {
        const container = document.getElementById('cg-gallery-container');
        if (!container) return;

        const addButton = document.getElementById('cg-add-gallery-images');
        const imagesList = document.getElementById('cg-gallery-images');

        // Media uploader
        let mediaUploader;

        addButton?.addEventListener('click', function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: settings.strings?.selectImages || 'Select Images',
                button: {
                    text: settings.strings?.useImages || 'Add to Gallery'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function() {
                const attachments = mediaUploader.state().get('selection').toJSON();
                
                attachments.forEach(function(attachment) {
                    const thumbnail = attachment.sizes?.thumbnail?.url || attachment.url;
                    
                    const li = document.createElement('li');
                    li.className = 'cg-gallery-item';
                    li.dataset.id = attachment.id;
                    li.innerHTML = `
                        <img src="${thumbnail}" alt="">
                        <input type="hidden" name="cg_gallery[]" value="${attachment.id}">
                        <button type="button" class="cg-remove-image">&times;</button>
                    `;
                    
                    imagesList.appendChild(li);
                });
            });

            mediaUploader.open();
        });

        // Remove image
        imagesList?.addEventListener('click', function(e) {
            if (e.target.classList.contains('cg-remove-image')) {
                e.target.closest('.cg-gallery-item').remove();
            }
        });

        // Make sortable if jQuery UI is available
        if ($.fn.sortable) {
            $(imagesList).sortable({
                placeholder: 'cg-gallery-placeholder',
                cursor: 'move'
            });
        }
    }

})(jQuery);
