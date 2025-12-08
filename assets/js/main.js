/**
 * Capiznon Geo - Main JavaScript
 */

(function() {
    'use strict';

    const settings = window.capiznonGeo || {};

    // Register service worker for PWA support
    if ('serviceWorker' in navigator && settings.themeUrl) {
        window.addEventListener('load', function() {
            try {
                const base = settings.themeUrl.replace(/\/+$/, '');
                const swUrl = `${base}/sw.js`;
                
                navigator.serviceWorker.register(swUrl, {
                    scope: base + '/'
                }).then(registration => {
                    console.log('Service worker registered:', registration.scope);
                    
                    // Handle updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New version available
                                console.log('New service worker available');
                            }
                        });
                    });
                    
                }).catch(err => {
                    console.warn('Service worker registration failed:', err);
                });
                
                // Handle service worker messages
                navigator.serviceWorker.addEventListener('message', event => {
                    // Handle any messages from service worker
                    if (event.data && event.data.type) {
                        console.log('Service worker message:', event.data);
                    }
                });
                
            } catch (err) {
                console.warn('Service worker registration error:', err);
            }
        });
    }

    /**
     * Ensure Add Location modal exists in the DOM
     */
    function ensureAddLocationModal() {
        if (document.getElementById('cg-add-location-modal')) return;

        const settings = window.capiznonGeo || {};

        const modal = document.createElement('div');
        modal.id = 'cg-add-location-modal';
        modal.className = 'cg-visit-modal';
        modal.innerHTML = `
            <div class="cg-visit-modal-backdrop"></div>
            <div class="cg-visit-modal-content">
                <div class="cg-visit-modal-header">
                    <h3 class="cg-visit-modal-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>${settings.strings?.addLocationTitle || 'Add a place'}</span>
                    </h3>
                    <button type="button" class="cg-visit-modal-close" aria-label="Close">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="cg-visit-modal-body">
                    <div class="cg-visit-field">
                        <label>Coordinates</label>
                        <div class="text-xs text-ocean-mid" id="cg-add-location-coords"></div>
                    </div>

                    <div class="cg-visit-field">
                        <label for="cg-add-location-title">Place name</label>
                        <input type="text" id="cg-add-location-title" class="cg-visit-input" placeholder="e.g. Best Cafe" />
                    </div>

                    <div class="cg-visit-field">
                        <label for="cg-add-location-type">Type</label>
                        <select id="cg-add-location-type" class="cg-visit-input"></select>
                    </div>

                    <div class="cg-visit-field" id="cg-add-location-cuisines-wrap">
                        <label for="cg-add-location-cuisines">Cuisines (optional)</label>
                        <select id="cg-add-location-cuisines" class="cg-visit-input" multiple></select>
                        <div class="mt-2 flex gap-2">
                            <input type="text" id="cg-add-location-cuisine-new" class="cg-visit-input" placeholder="Add a cuisine…" />
                            <button type="button" id="cg-add-location-cuisine-add" class="cg-cuisine-add-btn" style="flex:0 0 auto;min-width:3rem;padding:0.5rem 0.75rem;background:var(--cg-primary);color:white;border:none;border-radius:0.5rem;font-weight:bold;cursor:pointer;">
                                +
                            </button>
                        </div>
                    </div>

                    <div class="cg-visit-field">
                        <label for="cg-add-location-excerpt">Short description (optional)</label>
                        <textarea id="cg-add-location-excerpt" class="cg-visit-input" rows="2"></textarea>
                    </div>

                    <div class="cg-visit-field">
                        <label for="cg-add-location-address">Address (optional)</label>
                        <input type="text" id="cg-add-location-address" class="cg-visit-input" />
                    </div>

                    <div class="cg-visit-field">
                        <label for="cg-add-location-photos">Photos (optional)</label>
                        <input type="file" id="cg-add-location-photos" class="cg-visit-input" accept="image/*" multiple />
                    </div>

                    <p class="text-xs text-ocean-mid mt-2">
                        ${settings.strings?.addLocationHelper || 'Depending on your role, new places may need to be reviewed before they appear on the map.'}
                    </p>
                </div>
                <div class="cg-visit-modal-footer">
                    <button type="button" class="cg-visit-btn-cancel">
                        ${settings.strings?.cancel || 'Cancel'}
                    </button>
                    <button type="button" class="cg-visit-btn-save" id="cg-add-location-save">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>${settings.strings?.save || 'Save'}</span>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        const backdrop = modal.querySelector('.cg-visit-modal-backdrop');
        const closeBtn = modal.querySelector('.cg-visit-modal-close');
        const cancelBtn = modal.querySelector('.cg-visit-btn-cancel');
        const saveBtn = modal.querySelector('#cg-add-location-save');

        backdrop.addEventListener('click', closeAddLocationModal);
        closeBtn.addEventListener('click', closeAddLocationModal);
        cancelBtn.addEventListener('click', closeAddLocationModal);
        saveBtn.addEventListener('click', saveAddLocation);
    }

    function openAddLocationModal(lat, lng) {
        const modal = document.getElementById('cg-add-location-modal');
        if (!modal) return;

        modal.dataset.lat = String(lat);
        modal.dataset.lng = String(lng);

        const coordsEl = modal.querySelector('#cg-add-location-coords');
        if (coordsEl) {
            coordsEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }

        // Reset fields
        modal.querySelector('#cg-add-location-title').value = '';
        modal.querySelector('#cg-add-location-excerpt').value = '';
        modal.querySelector('#cg-add-location-address').value = '';
        modal.querySelector('#cg-add-location-photos').value = '';

        // Populate selects from filter options if available
        const filters = window.cgFilterOptions || {};
        const typeSelect = modal.querySelector('#cg-add-location-type');
        const cuisinesSelect = modal.querySelector('#cg-add-location-cuisines');
        const cuisinesWrap = modal.querySelector('#cg-add-location-cuisines-wrap');
        const addCuisineInput = modal.querySelector('#cg-add-location-cuisine-new');
        const addCuisineBtn = modal.querySelector('#cg-add-location-cuisine-add');

        if (typeSelect && Array.isArray(filters.types)) {
            typeSelect.innerHTML = '<option value="">Select type...</option>';

            const types = filters.types;
            const parents = types.filter(t => !t.parent);
            const children = types.filter(t => t.parent);

            parents.forEach(parent => {
                const parentOpt = document.createElement('option');
                parentOpt.value = parent.slug;
                parentOpt.textContent = parent.name;
                typeSelect.appendChild(parentOpt);

                children
                    .filter(c => c.parent === parent.id)
                    .forEach(child => {
                        const childOpt = document.createElement('option');
                        childOpt.value = child.slug;
                        childOpt.textContent = '↳ ' + child.name;
                        typeSelect.appendChild(childOpt);
                    });
            });
        }

        if (cuisinesSelect && Array.isArray(filters.cuisines)) {
            cuisinesSelect.innerHTML = '';
            filters.cuisines.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.slug;
                opt.textContent = c.name;
                cuisinesSelect.appendChild(opt);
            });
        }

        // Build list of food-related type slugs (Food & Dining + its children)
        let foodTypeSlugs = window.cgFoodTypeSlugs || null;
        if (!foodTypeSlugs && Array.isArray(filters.types)) {
            const types = filters.types;
            const foodParent = types.find(t => t.slug === 'food-dining');
            const set = new Set();
            if (foodParent) {
                set.add(foodParent.slug);
                types
                    .filter(t => t.parent === foodParent.id)
                    .forEach(t => set.add(t.slug));
            }
            foodTypeSlugs = Array.from(set);
            window.cgFoodTypeSlugs = foodTypeSlugs;
        }

        const isFoodType = slug => {
            if (!slug || !Array.isArray(foodTypeSlugs)) return false;
            return foodTypeSlugs.includes(slug);
        };

        // Reset cuisines visibility based on type
        if (cuisinesWrap) {
            if (typeSelect && isFoodType(typeSelect.value)) {
                cuisinesWrap.style.display = '';
            } else {
                cuisinesWrap.style.display = 'none';
            }
        }

        // Attach change handler once to toggle cuisines field
        if (typeSelect && !typeSelect.dataset.cgCuisineToggleBound) {
            typeSelect.dataset.cgCuisineToggleBound = '1';
            typeSelect.addEventListener('change', function() {
                if (!cuisinesWrap) return;
                if (isFoodType(this.value)) {
                    cuisinesWrap.style.display = '';
                } else {
                    cuisinesWrap.style.display = 'none';
                    if (cuisinesSelect) {
                        Array.from(cuisinesSelect.options).forEach(o => {
                            o.selected = false;
                        });
                    }
                }
            });
        }

        // Allow adding a new cuisine tag inline (client-side only; term is created when saving)
        if (addCuisineBtn && addCuisineInput && cuisinesSelect && !addCuisineBtn.dataset.cgCuisineAddBound) {
            addCuisineBtn.dataset.cgCuisineAddBound = '1';
            addCuisineBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const name = addCuisineInput.value.trim();
                if (!name) return;

                // Simple slugify
                const slug = name
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[^\w\s-]/g, '')
                    .trim()
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');

                if (!slug) return;

                // Check if already exists
                let existing = Array.from(cuisinesSelect.options).find(o => o.value === slug || o.textContent.toLowerCase() === name.toLowerCase());
                if (!existing) {
                    const opt = document.createElement('option');
                    opt.value = slug;
                    opt.textContent = name;
                    cuisinesSelect.appendChild(opt);
                    existing = opt;
                }

                existing.selected = true;
                addCuisineInput.value = '';
            });
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAddLocationModal() {
        const modal = document.getElementById('cg-add-location-modal');
        if (!modal) return;
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    async function saveAddLocation() {
        const settings = window.capiznonGeo || {};
        const modal = document.getElementById('cg-add-location-modal');
        if (!modal) return;

        const lat = parseFloat(modal.dataset.lat || '0');
        const lng = parseFloat(modal.dataset.lng || '0');
        const titleInput = modal.querySelector('#cg-add-location-title');
        const typeSelect = modal.querySelector('#cg-add-location-type');
        const cuisinesSelect = modal.querySelector('#cg-add-location-cuisines');
        const excerptInput = modal.querySelector('#cg-add-location-excerpt');
        const addressInput = modal.querySelector('#cg-add-location-address');
        const photosInput = modal.querySelector('#cg-add-location-photos');

        const title = titleInput.value.trim();
        const type = typeSelect.value.trim();

        if (!title) {
            alert(settings.strings?.addLocationTitleRequired || 'Please enter a place name.');
            return;
        }
        if (!type) {
            alert(settings.strings?.addLocationTypeRequired || 'Please choose a location type.');
            return;
        }

        const saveBtn = modal.querySelector('#cg-add-location-save');
        const originalHtml = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="cg-spinner-small" width="16" height="16" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="31.4" stroke-dashoffset="10"/>
            </svg>
            ${settings.strings?.saving || 'Saving...'}
        `;

        try {
            const formData = new FormData();
            formData.append('title', title);
            formData.append('type', type);
            formData.append('lat', String(lat));
            formData.append('lng', String(lng));
            if (excerptInput.value.trim()) formData.append('excerpt', excerptInput.value.trim());
            if (addressInput.value.trim()) formData.append('address', addressInput.value.trim());

            const selectedCuisines = Array.from(cuisinesSelect.selectedOptions || []).map(o => o.value).filter(Boolean);
            selectedCuisines.forEach(slug => {
                formData.append('cuisines[]', slug);
            });

            if (photosInput.files && photosInput.files.length) {
                Array.from(photosInput.files).forEach(file => {
                    formData.append('photos[]', file);
                });
            }

            const response = await fetch(`${settings.restUrl}locations`, {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': settings.nonce,
                },
                body: formData,
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || settings.strings?.error || 'Error saving location');
            }

            closeAddLocationModal();
            const status = data && data.status;
            if (status === 'pending' && settings.strings?.addLocationSavedPending) {
                showVisitToast(settings.strings.addLocationSavedPending);
            } else {
                showVisitToast(settings.strings?.addLocationSaved || 'Place added successfully.');
                // Reload map data to show the new location
                if (window.CapiznonGeoMap && typeof window.CapiznonGeoMap.load === 'function') {
                    window.CapiznonGeoMap.load();
                }
            }
        } catch (error) {
            console.error('Error saving location:', error);
            alert(error.message || settings.strings?.error || 'Error saving location');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalHtml;
        }
    }

    function showGeoErrorModal() {
        const settings = window.capiznonGeo || {};
        let modal = document.getElementById('cg-geo-error-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'cg-geo-error-modal';
            modal.className = 'cg-visit-modal';
            modal.innerHTML = `
                <div class="cg-visit-modal-backdrop"></div>
                <div class="cg-visit-modal-content">
                    <div class="cg-visit-modal-header">
                        <h3 class="cg-visit-modal-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/>
                            </svg>
                            <span>${settings.strings?.addLocationGeoErrorTitle || 'Location access required'}</span>
                        </h3>
                        <button type="button" class="cg-visit-modal-close" aria-label="Close">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="cg-visit-modal-body">
                        <p class="text-sm text-ocean-deep">
                            ${settings.strings?.addLocationGeoErrorBody || 'We could not access your current location. Please enable location permissions in your browser settings, or add a place by tapping on the map instead.'}
                        </p>
                    </div>
                    <div class="cg-visit-modal-footer">
                        <button type="button" class="cg-visit-btn-cancel">
                            ${settings.strings?.ok || 'OK'}
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            const backdrop = modal.querySelector('.cg-visit-modal-backdrop');
            const closeBtn = modal.querySelector('.cg-visit-modal-close');
            const cancelBtn = modal.querySelector('.cg-visit-btn-cancel');

            const close = () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            };

            backdrop.addEventListener('click', close);
            closeBtn.addEventListener('click', close);
            cancelBtn.addEventListener('click', close);
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Initialize when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        initializeMap();
        initializeFilters();
        initializeSearch();
        initializeSidebar();
        initializeLightbox();
        initializeVisitModal();
        initializeAddLocation();
    });

    /**
     * Initialize the main map
     */
    function initializeMap() {
        const mapContainer = document.getElementById('cg-map');
        if (!mapContainer) return;

        // Initialize map
        CapiznonGeoMap.init('cg-map');
        
        // Load all locations
        CapiznonGeoMap.load();

        // Note: Geolocation is only requested when user clicks "Add My Location" button
        // This complies with browser requirements for user gesture initiation

        // Listen for location updates
        document.addEventListener('cg:locationsLoaded', function(e) {
            updateLocationCount(e.detail.total);
            updateLocationList(e.detail.locations);
        });
    }

    /**
     * Initialize Add Location flow (buttons + modal)
     */
    function initializeAddLocation() {
        const settings = window.capiznonGeo || {};
        if (!settings.isLoggedIn) {
            // Hide buttons if present
            document.getElementById('cg-add-place-here')?.classList.add('hidden');
            document.getElementById('cg-add-my-location')?.classList.add('hidden');
            return;
        }

        const map = window.CapiznonGeoMap && window.CapiznonGeoMap.getMap ? window.CapiznonGeoMap.getMap() : null;
        if (!map) {
            // Map not ready yet; try again shortly
            setTimeout(initializeAddLocation, 500);
            return;
        }

        const addHereBtn = document.getElementById('cg-add-place-here');
        const addMyLocBtn = document.getElementById('cg-add-my-location');
        if (!addHereBtn && !addMyLocBtn) return;

        let addLocationMode = false;

        // Ensure modal exists
        ensureAddLocationModal();

        // Add place here: enable map click mode
        addHereBtn?.addEventListener('click', function() {
            addLocationMode = true;
        });

        map.on('click', function(e) {
            if (!addLocationMode) return;
            addLocationMode = false;
            openAddLocationModal(e.latlng.lat, e.latlng.lng);
        });

        // Add my location: require geolocation
        addMyLocBtn?.addEventListener('click', function() {
            if (!navigator.geolocation) {
                showGeoErrorModal();
                return;
            }

            navigator.geolocation.getCurrentPosition(
                position => {
                    const { latitude, longitude } = position.coords;
                    openAddLocationModal(latitude, longitude);
                },
                () => {
                    showGeoErrorModal();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 60000,
                }
            );
        });
    }

    /**
     * Initialize filter controls
     */
    function initializeFilters() {
        // Type filter
        const typeFilter = document.getElementById('cg-filter-type');
        if (typeFilter) {
            typeFilter.addEventListener('change', function() {
                CapiznonGeoMap.filter({ type: this.value });
            });
        }

        // Area filter
        const areaFilter = document.getElementById('cg-filter-area');
        if (areaFilter) {
            areaFilter.addEventListener('change', function() {
                CapiznonGeoMap.filter({ area: this.value });
            });
        }

        // Tag filter
        const tagFilter = document.getElementById('cg-filter-tag');
        if (tagFilter) {
            tagFilter.addEventListener('change', function() {
                CapiznonGeoMap.filter({ tag: this.value });
            });
        }

        // Price filter
        const priceFilter = document.getElementById('cg-filter-price');
        if (priceFilter) {
            priceFilter.addEventListener('change', function() {
                CapiznonGeoMap.filter({ price: this.value });
            });
        }

        // Cuisine filter (only meaningful for food places)
        const cuisineFilter = document.getElementById('cg-filter-cuisine');
        const cuisineWrapper = document.getElementById('cg-filter-cuisine-wrapper');
        if (cuisineFilter) {
            cuisineFilter.addEventListener('change', function() {
                CapiznonGeoMap.filter({ cuisine: this.value });
            });
        }

        // Featured toggle
        const featuredToggle = document.getElementById('cg-filter-featured');
        if (featuredToggle) {
            featuredToggle.addEventListener('change', function() {
                CapiznonGeoMap.filter({ featured: this.checked ? '1' : '' });
            });
        }

        // Clear filters button
        const clearBtn = document.getElementById('cg-clear-filters');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                // Reset all filter inputs
                document.querySelectorAll('.cg-filter-control').forEach(el => {
                    if (el.type === 'checkbox') {
                        el.checked = false;
                    } else {
                        el.value = '';
                    }
                });
                CapiznonGeoMap.clearFilters();
            });
        }

        // Load filter options from API
        loadFilterOptions();
    }

    /**
     * Load filter options from API
     */
    async function loadFilterOptions() {
        try {
            const response = await fetch(`${settings.restUrl}filters`);
            if (!response.ok) return;
            
            const data = await response.json();

            // Cache for reuse (e.g., add-location modal)
            window.cgFilterOptions = data;

            populateFilterSelect('cg-filter-type', data.types, true);
            populateFilterSelect('cg-filter-area', data.areas);
            populateFilterSelect('cg-filter-tag', data.tags);
            populateFilterSelect('cg-filter-price', data.prices);
            populateFilterSelect('cg-filter-cuisine', data.cuisines);
        } catch (error) {
            console.error('Error loading filters:', error);
        }
    }

    /**
     * Populate a filter select element
     */
    function populateFilterSelect(elementId, items, hierarchical = false) {
        const select = document.getElementById(elementId);
        if (!select || !items || !Array.isArray(items) || items.length === 0) return;

        // Keep the first option (All)
        const firstOption = select.querySelector('option');
        select.innerHTML = '';
        if (firstOption) select.appendChild(firstOption);

        if (hierarchical) {
            // Group by parent
            const parents = items.filter(item => !item.parent);
            const children = items.filter(item => item.parent);

            parents.forEach(parent => {
                const option = document.createElement('option');
                option.value = parent.slug;
                option.textContent = parent.name;
                select.appendChild(option);

                // Add children
                children.filter(c => c.parent === parent.id).forEach(child => {
                    const childOption = document.createElement('option');
                    childOption.value = child.slug;
                    childOption.textContent = `  └ ${child.name}`;
                    select.appendChild(childOption);
                });
            });
        } else {
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                select.appendChild(option);
            });
        }
    }

    /**
     * Initialize search functionality
     */
    function initializeSearch() {
        const searchInput = document.getElementById('cg-search');
        if (!searchInput) return;

        let debounceTimer;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                CapiznonGeoMap.search(this.value);
            }, 300);
        });

        // Clear search on escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                CapiznonGeoMap.search('');
            }
        });
    }

    /**
     * Initialize sidebar
     */
    function initializeSidebar() {
        const sidebar = document.getElementById('cg-sidebar');
        const toggleBtn = document.getElementById('cg-sidebar-toggle');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('translate-x-full');
                sidebar.classList.toggle('translate-x-0');
            });
        }
    }

    /**
     * Update location count display
     */
    function updateLocationCount(count) {
        const countEl = document.getElementById('cg-location-count');
        if (countEl) {
            countEl.textContent = count;
        }
    }

    /**
     * Update location list in sidebar
     */
    function updateLocationList(locations) {
        const listContainer = document.getElementById('cg-location-list');
        if (!listContainer) return;

        if (locations.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-12 px-4">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-accent-light to-ocean-foam rounded-2xl flex items-center justify-center">
                        <span class="text-4xl">🏝️</span>
                    </div>
                    <p class="font-semibold text-ocean-deep mb-1">${settings.strings?.noResults || 'No places found'}</p>
                    <p class="text-sm text-ocean-shallow">Try adjusting your filters</p>
                </div>
            `;
            return;
        }

        listContainer.innerHTML = locations.map(location => createLocationCard(location)).join('');

        // Add click handlers
        listContainer.querySelectorAll('.cg-location-card').forEach(card => {
            card.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                CapiznonGeoMap.focus(id);
                
                // Highlight active card
                listContainer.querySelectorAll('.cg-location-card').forEach(c => {
                    c.classList.remove('active');
                });
                this.classList.add('active');

                // Close list panel (especially useful on mobile)
                const listPanel = document.getElementById('cg-list-panel');
                if (listPanel && listPanel.classList.contains('cg-list-panel-open')) {
                    listPanel.classList.remove('cg-list-panel-open');
                }
            });
        });
    }

    /**
     * Create location card HTML
     */
    function createLocationCard(location) {
        const types = (location.types || []).map(t => t.name).join(', ');
        const cuisines = (location.cuisines || []).map(c => c.name);
        const thumbnail = location.thumbnail || '';
        const isFeatured = location.featured;
        
        return `
            <div class="cg-location-card cursor-pointer" data-id="${location.id}">
                ${thumbnail ? `
                    <div class="aspect-[4/3] bg-gradient-to-br from-accent-light to-ocean-foam overflow-hidden">
                        <img src="${thumbnail}" alt="${location.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                ` : `
                    <div class="aspect-[4/3] bg-gradient-to-br from-accent-light to-ocean-foam flex items-center justify-center">
                        <span class="text-4xl">${location.marker_icon === 'restaurant' ? '🍽️' : location.marker_icon === 'cafe' ? '☕' : location.marker_icon === 'hotel' ? '🏨' : location.marker_icon === 'beach' ? '🏖️' : '📍'}</span>
                    </div>
                `}
                <div class="p-4 relative z-10">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        ${types ? `<span class="inline-block text-[10px] font-bold text-ocean-deep uppercase tracking-wider bg-sand-light px-2 py-1 rounded-full">${types}</span>` : ''}
                        ${isFeatured ? `<span class="text-sm" title="Featured">⭐</span>` : ''}
                    </div>
                    <h3 class="font-bold text-base-black leading-tight mb-1">${location.title}</h3>
                    ${cuisines.length ? `
                        <div class="cg-location-cuisines mt-1 flex flex-wrap gap-1">
                            ${cuisines.map(name => `
                                <span class="cg-badge-cuisine">${name}</span>
                            `).join('')}
                        </div>
                    ` : ''}
                    ${location.excerpt ? `<p class="text-sm text-ocean-mid line-clamp-2">${location.excerpt}</p>` : ''}
                </div>
            </div>
        `;
    }

    /**
     * Initialize lightbox for galleries
     */
    function initializeLightbox() {
        // Create lightbox element if it doesn't exist
        if (!document.getElementById('cg-lightbox')) {
            const lightbox = document.createElement('div');
            lightbox.id = 'cg-lightbox';
            lightbox.className = 'cg-lightbox';
            lightbox.innerHTML = `
                <button class="cg-lightbox-close" aria-label="Close">&times;</button>
                <img src="" alt="">
            `;
            document.body.appendChild(lightbox);

            // Close on click
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox || e.target.classList.contains('cg-lightbox-close')) {
                    lightbox.classList.remove('active');
                }
            });

            // Close on escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    lightbox.classList.remove('active');
                }
            });
        }

        // Add click handlers to gallery images
        document.querySelectorAll('.cg-gallery-item').forEach(item => {
            item.addEventListener('click', function() {
                const fullUrl = this.dataset.full || this.querySelector('img')?.src;
                if (fullUrl) {
                    const lightbox = document.getElementById('cg-lightbox');
                    lightbox.querySelector('img').src = fullUrl;
                    lightbox.classList.add('active');
                }
            });
        });
    }

    /**
     * Load nearby locations for a specific point
     */
    window.loadNearbyLocations = async function(lat, lng, containerId, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '<div class="cg-loading"><div class="cg-spinner"></div></div>';

        const data = await CapiznonGeoMap.loadNearby(lat, lng, options.radius || 1, options);
        
        if (!data || data.locations.length === 0) {
            container.innerHTML = `
                <p class="text-gray-500 text-center py-4">
                    ${settings.strings?.noResults || 'No nearby locations found'}
                </p>
            `;
            return;
        }

        container.innerHTML = data.locations.map(location => `
            <a href="${location.url}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                ${location.thumbnail ? `
                    <img src="${location.thumbnail}" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                ` : `
                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                `}
                <div class="flex-1 min-w-0">
                    <h4 class="font-medium text-gray-900 truncate">${location.title}</h4>
                    <p class="text-sm text-gray-500">${location.distance} km away</p>
                </div>
            </a>
        `).join('');
    };

    /**
     * Initialize visit modal
     */
    function initializeVisitModal() {
        // Create modal if it doesn't exist
        if (!document.getElementById('cg-visit-modal')) {
            const modal = document.createElement('div');
            modal.id = 'cg-visit-modal';
            modal.className = 'cg-visit-modal';
            modal.innerHTML = `
                <div class="cg-visit-modal-backdrop"></div>
                <div class="cg-visit-modal-content">
                    <div class="cg-visit-modal-header">
                        <h3 class="cg-visit-modal-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>${settings.strings?.recordVisit || 'Record Visit'}</span>
                        </h3>
                        <button type="button" class="cg-visit-modal-close" aria-label="Close">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="cg-visit-modal-body">
                        <div class="cg-visit-location-name"></div>
                        
                        <div class="cg-visit-field">
                            <label for="cg-visit-date">${settings.strings?.visitDate || 'Visit Date'}</label>
                            <div class="cg-visit-date-wrapper">
                                <input type="date" id="cg-visit-date" class="cg-visit-input">
                                <button type="button" id="cg-visit-today" class="cg-visit-today-btn">
                                    ${settings.strings?.today || 'Today'}
                                </button>
                            </div>
                        </div>
                        
                        <div class="cg-visit-ratings">
                            <div class="cg-visit-rating-group">
                                <label>
                                    <span class="cg-rating-icon">🍽️</span>
                                    ${settings.strings?.food || 'Food'}
                                </label>
                                <div class="cg-star-rating" data-rating="food">
                                    ${[1,2,3,4,5].map(i => `
                                        <button type="button" class="cg-star" data-value="${i}" aria-label="${i} stars">
                                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </button>
                                    `).join('')}
                                </div>
                            </div>
                            
                            <div class="cg-visit-rating-group">
                                <label>
                                    <span class="cg-rating-icon">👋</span>
                                    ${settings.strings?.service || 'Service'}
                                </label>
                                <div class="cg-star-rating" data-rating="service">
                                    ${[1,2,3,4,5].map(i => `
                                        <button type="button" class="cg-star" data-value="${i}" aria-label="${i} stars">
                                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </button>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                        
                        <div class="cg-visit-field">
                            <label for="cg-visit-notes">${settings.strings?.notes || 'Notes'} <span class="cg-optional">(optional)</span></label>
                            <textarea id="cg-visit-notes" class="cg-visit-input" rows="2" placeholder="${settings.strings?.notesPlaceholder || 'Any thoughts about your visit...'}"></textarea>
                        </div>
                    </div>
                    <div class="cg-visit-modal-footer">
                        <button type="button" class="cg-visit-btn-cancel">
                            ${settings.strings?.cancel || 'Cancel'}
                        </button>
                        <button type="button" class="cg-visit-btn-save">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            ${settings.strings?.save || 'Save Visit'}
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Event handlers
            const backdrop = modal.querySelector('.cg-visit-modal-backdrop');
            const closeBtn = modal.querySelector('.cg-visit-modal-close');
            const cancelBtn = modal.querySelector('.cg-visit-btn-cancel');
            const saveBtn = modal.querySelector('.cg-visit-btn-save');
            const todayBtn = modal.querySelector('#cg-visit-today');
            const dateInput = modal.querySelector('#cg-visit-date');

            // Close modal handlers
            backdrop.addEventListener('click', closeVisitModal);
            closeBtn.addEventListener('click', closeVisitModal);
            cancelBtn.addEventListener('click', closeVisitModal);

            // Today button
            todayBtn.addEventListener('click', function() {
                dateInput.value = new Date().toISOString().split('T')[0];
            });

            // Star rating handlers
            modal.querySelectorAll('.cg-star-rating').forEach(ratingGroup => {
                ratingGroup.querySelectorAll('.cg-star').forEach(star => {
                    star.addEventListener('click', function() {
                        const value = parseInt(this.dataset.value);
                        const group = this.closest('.cg-star-rating');
                        group.dataset.selectedValue = value;
                        
                        group.querySelectorAll('.cg-star').forEach((s, idx) => {
                            s.classList.toggle('active', idx < value);
                        });
                    });

                    // Hover effect
                    star.addEventListener('mouseenter', function() {
                        const value = parseInt(this.dataset.value);
                        const group = this.closest('.cg-star-rating');
                        
                        group.querySelectorAll('.cg-star').forEach((s, idx) => {
                            s.classList.toggle('hover', idx < value);
                        });
                    });

                    star.addEventListener('mouseleave', function() {
                        const group = this.closest('.cg-star-rating');
                        group.querySelectorAll('.cg-star').forEach(s => {
                            s.classList.remove('hover');
                        });
                    });
                });
            });

            // Save visit
            saveBtn.addEventListener('click', saveVisit);

            // Close on escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeVisitModal();
                }
            });
        }

        // Listen for visit button clicks in popups
        document.addEventListener('click', function(e) {
            const visitBtn = e.target.closest('.cg-popup-visit-btn');
            if (visitBtn && !visitBtn.classList.contains('cg-popup-visit-btn-login')) {
                e.preventDefault();
                openVisitModal(
                    visitBtn.dataset.locationId,
                    visitBtn.dataset.locationTitle
                );
            }
        });
    }

    /**
     * Open visit modal
     */
    function openVisitModal(locationId, locationTitle) {
        const modal = document.getElementById('cg-visit-modal');
        if (!modal) return;

        // Set location data
        modal.dataset.locationId = locationId;
        modal.querySelector('.cg-visit-location-name').textContent = locationTitle;

        // Reset form
        modal.querySelector('#cg-visit-date').value = new Date().toISOString().split('T')[0];
        modal.querySelector('#cg-visit-notes').value = '';
        
        // Reset ratings
        modal.querySelectorAll('.cg-star-rating').forEach(group => {
            group.dataset.selectedValue = '';
            group.querySelectorAll('.cg-star').forEach(s => s.classList.remove('active'));
        });

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close visit modal
     */
    function closeVisitModal() {
        const modal = document.getElementById('cg-visit-modal');
        if (!modal) return;

        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    /**
     * Save visit
     */
    async function saveVisit() {
        const modal = document.getElementById('cg-visit-modal');
        if (!modal) return;

        const locationId = modal.dataset.locationId;
        const visitDate = modal.querySelector('#cg-visit-date').value;
        const notes = modal.querySelector('#cg-visit-notes').value;
        const foodRating = modal.querySelector('.cg-star-rating[data-rating="food"]').dataset.selectedValue;
        const serviceRating = modal.querySelector('.cg-star-rating[data-rating="service"]').dataset.selectedValue;

        if (!visitDate) {
            alert(settings.strings?.selectDate || 'Please select a date');
            return;
        }

        const saveBtn = modal.querySelector('.cg-visit-btn-save');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="cg-spinner-small" width="16" height="16" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="31.4" stroke-dashoffset="10"/>
            </svg>
            ${settings.strings?.saving || 'Saving...'}
        `;

        try {
            const response = await fetch(`${settings.restUrl}visits`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': settings.nonce
                },
                body: JSON.stringify({
                    location_id: parseInt(locationId),
                    visit_date: visitDate,
                    food_rating: foodRating ? parseInt(foodRating) : null,
                    service_rating: serviceRating ? parseInt(serviceRating) : null,
                    notes: notes || null
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to save visit');
            }

            // Success
            closeVisitModal();
            showVisitToast(settings.strings?.visitRecorded || 'Visit recorded!');

        } catch (error) {
            console.error('Error saving visit:', error);
            alert(error.message || settings.strings?.error || 'Error saving visit');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }

    /**
     * Show success toast
     */
    function showVisitToast(message) {
        // Remove existing toast
        const existing = document.querySelector('.cg-visit-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'cg-visit-toast';
        toast.innerHTML = `
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);

        // Trigger animation
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Remove after delay
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

})();
