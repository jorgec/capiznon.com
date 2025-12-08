# Taxonomies

The theme uses four taxonomies to organize locations.

## Location Type

**Slug**: `location_type`  
**Hierarchical**: Yes (like categories)

Used for primary classification of locations. Supports parent-child relationships.

### Default Types

```
Food & Dining
├── Restaurant
├── Cafe
├── Bar
├── Eatery
├── Fast Food
├── Bakery
└── Food Stall

Shopping
├── Mall
├── Boutique
├── Store
├── Market
└── Souvenir Shop

Accommodation
├── Hotel
├── Resort
├── Inn
├── Pension House
└── Hostel

Attractions
├── Beach
├── Park
├── Museum
├── Church
├── Historical Site
└── Nature Spot

Services
├── Bank
├── Hospital
├── Pharmacy
├── Gas Station
└── Transportation Hub

Entertainment
├── Cinema
├── KTV
├── Sports Facility
└── Events Venue
```

### Adding Custom Types

1. Go to **Locations → Types**
2. Enter name and slug
3. Select parent (optional)
4. Click "Add New Type"

## Location Tags

**Slug**: `location_tag`  
**Hierarchical**: No (like tags)

Used for features, amenities, and attributes.

### Default Tags

- wifi
- parking
- pet-friendly
- outdoor-seating
- air-conditioned
- accepts-credit-cards
- wheelchair-accessible
- family-friendly
- romantic
- live-music
- delivery
- takeout
- reservations
- 24-hours
- halal
- vegetarian-options
- seafood
- local-cuisine

## Areas

**Slug**: `location_area`  
**Hierarchical**: Yes

Geographic areas and neighborhoods.

### Default Areas (Roxas City)

- Downtown / Poblacion
- Baybay Beach
- Pueblo de Panay
- Culasi
- Libas
- Banica
- Cagay
- Dayao

### Adding Custom Areas

1. Go to **Locations → Areas**
2. Enter area name
3. Select parent area (optional)
4. Add description and coordinates if needed

## Price Range

**Slug**: `location_price`  
**Hierarchical**: Yes

Budget indicators for visitors.

### Default Price Ranges

| Symbol | Name | Description |
|--------|------|-------------|
| ₱ | Budget | Under ₱200 |
| ₱₱ | Moderate | ₱200-500 |
| ₱₱₱ | Upscale | ₱500-1000 |
| ₱₱₱₱ | Fine Dining | ₱1000+ |

## Using Taxonomies in Filters

All taxonomies are available in the map sidebar filters:

- **Type dropdown**: Filter by location type
- **Area dropdown**: Filter by geographic area
- **Tags dropdown**: Filter by amenities
- **Price dropdown**: Filter by budget

Multiple filters can be combined (AND logic).

## REST API

All taxonomies are exposed via the REST API:

```
GET /wp-json/capiznon-geo/v1/filters
```

Returns all taxonomy terms with counts.
