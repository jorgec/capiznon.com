# Configuration

## Theme Settings

Access theme settings at **Appearance → Geo Settings**.

### Default Map Center

Set the default coordinates for the map:

- **Default Latitude**: The latitude of the map center (e.g., 11.5853 for Roxas City)
- **Default Longitude**: The longitude of the map center (e.g., 122.7511 for Roxas City)
- **Default Zoom Level**: Initial zoom level (1-20, default: 14)

### Mapbox Token (Optional)

For custom map styles, you can use Mapbox:

1. Create a free account at [mapbox.com](https://mapbox.com)
2. Generate an access token
3. Paste the token in the settings

Without a Mapbox token, the theme uses OpenStreetMap tiles (free, no account required).

## Navigation Menus

The theme supports three menu locations:

1. **Primary Menu** - Main navigation in the header
2. **Footer Menu** - Links in the footer
3. **Mobile Menu** - Separate menu for mobile devices (falls back to Primary if not set)

Configure menus at **Appearance → Menus**.

## Widget Areas

Three widget areas are available:

1. **Sidebar** - Standard blog sidebar
2. **Map Sidebar** - Widgets alongside the map (future use)
3. **Footer** - Footer widget area

## Image Sizes

The theme registers custom image sizes:

| Name | Dimensions | Usage |
|------|------------|-------|
| `location-card` | 400×300 | Location cards in lists |
| `location-gallery` | 800×600 | Gallery images |
| `location-hero` | 1200×600 | Single location hero |

After activation, regenerate thumbnails using a plugin like "Regenerate Thumbnails" to create these sizes for existing images.

## Performance Tips

### Caching

Enable a caching plugin for better performance:
- WP Super Cache
- W3 Total Cache
- LiteSpeed Cache

### Image Optimization

Optimize images before upload:
- Use WebP format when possible
- Compress images to reduce file size
- Use appropriate dimensions

### CDN

Consider using a CDN for static assets:
- Cloudflare (free tier available)
- BunnyCDN
- KeyCDN
