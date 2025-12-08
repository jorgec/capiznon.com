# REST API

The theme provides a custom REST API for accessing location data.

## Base URL

```
/wp-json/capiznon-geo/v1/
```

## Endpoints

### Get All Locations

```
GET /wp-json/capiznon-geo/v1/locations
```

Returns all published locations with coordinates.

#### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `type` | string | Filter by location type slug(s), comma-separated |
| `area` | string | Filter by area slug(s), comma-separated |
| `tag` | string | Filter by tag slug(s), comma-separated |
| `price` | string | Filter by price range slug(s), comma-separated |
| `search` | string | Search query |
| `featured` | boolean | Only return featured locations |

#### Example

```
GET /wp-json/capiznon-geo/v1/locations?type=restaurant&area=downtown
```

#### Response

```json
{
  "locations": [
    {
      "id": 123,
      "title": "Sample Restaurant",
      "slug": "sample-restaurant",
      "url": "https://example.com/location/sample-restaurant/",
      "lat": 11.5853,
      "lng": 122.7511,
      "types": [
        { "id": 5, "name": "Restaurant", "slug": "restaurant" }
      ],
      "featured": true,
      "marker_color": "#e74c3c",
      "marker_icon": "restaurant",
      "thumbnail": "https://example.com/wp-content/uploads/image.jpg",
      "excerpt": "A great place to eat..."
    }
  ],
  "total": 1
}
```

### Get Single Location

```
GET /wp-json/capiznon-geo/v1/locations/{id}
```

Returns full details for a single location.

#### Response

Includes all fields from the list endpoint, plus:

- `content`: Full HTML content
- `address`: Full address object
- `contact`: Phone, email, website, social links
- `hours`: Operating hours by day
- `gallery`: Array of gallery images
- `areas`: Area terms
- `tags`: Tag terms
- `price`: Price range term

### Get Nearby Locations

```
GET /wp-json/capiznon-geo/v1/locations/nearby
```

Find locations within a radius of a point.

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `lat` | number | Yes | Center latitude |
| `lng` | number | Yes | Center longitude |
| `radius` | number | No | Radius in km (default: 1) |
| `exclude` | integer | No | Location ID to exclude |
| `type` | string | No | Filter by type slug |
| `limit` | integer | No | Max results (default: 10, max: 50) |

#### Example

```
GET /wp-json/capiznon-geo/v1/locations/nearby?lat=11.5853&lng=122.7511&radius=1
```

#### Response

```json
{
  "locations": [
    {
      "id": 124,
      "title": "Nearby Cafe",
      "distance": 0.35,
      ...
    }
  ],
  "center": { "lat": 11.5853, "lng": 122.7511 },
  "radius": 1
}
```

### Get Filter Options

```
GET /wp-json/capiznon-geo/v1/filters
```

Returns all taxonomy terms for building filter UI.

#### Response

```json
{
  "types": [
    { "id": 1, "name": "Food & Dining", "slug": "food-dining", "count": 15, "parent": 0 },
    { "id": 5, "name": "Restaurant", "slug": "restaurant", "count": 8, "parent": 1 }
  ],
  "areas": [...],
  "tags": [...],
  "prices": [...]
}
```

## JavaScript API

The theme provides a JavaScript API for map interactions:

```javascript
// Initialize map
CapiznonGeoMap.init('container-id');

// Load locations
CapiznonGeoMap.load({ type: 'restaurant' });

// Load nearby
CapiznonGeoMap.loadNearby(11.5853, 122.7511, 1);

// Filter
CapiznonGeoMap.filter({ type: 'cafe', area: 'downtown' });

// Clear filters
CapiznonGeoMap.clearFilters();

// Focus on location
CapiznonGeoMap.focus(123);

// Fit all markers
CapiznonGeoMap.fitToMarkers();

// Get map instance
const map = CapiznonGeoMap.getMap();
```

## Events

The theme dispatches custom events:

```javascript
// Locations loaded
document.addEventListener('cg:locationsLoaded', (e) => {
  console.log(e.detail.locations);
  console.log(e.detail.total);
});

// Error loading locations
document.addEventListener('cg:locationsError', (e) => {
  console.error(e.detail.error);
});
```
