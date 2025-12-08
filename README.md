# Capiznon Geo - WordPress Theme

A beautiful, bohemian beach-themed WordPress theme for discovering locations in Capiz province, Philippines. Features interactive maps, advanced filtering, and a modern PWA experience.

## 🏝️ Overview

Capiznon Geo is a location-based directory theme designed specifically for showcasing places, businesses, and attractions in Capiz province. With its warm, bohemian beach aesthetic and powerful mapping capabilities, it provides an engaging way for users to explore and discover locations.

### ✨ Key Features

- **Interactive Maps** - Powered by Leaflet with clustering and custom markers
- **Advanced Filtering** - Multi-criteria search with real-time results
- **PWA Ready** - Progressive Web App with offline capabilities
- **Mobile Optimized** - Fully responsive design with touch interactions
- **Frontend Submissions** - Allow users to add locations from the frontend
- **Visit Tracking** - Users can record and track their visits
- **SEO Optimized** - Structured data and semantic markup
- **Performance Focused** - Local dependencies, no external CDNs

## 🛠️ Tech Stack

### **Frontend**
- **CSS Framework**: Tailwind CSS v3.4.0 (built/production)
- **JavaScript**: Vanilla JS (ES6+)
- **Mapping**: Leaflet v1.9.4 + MarkerCluster v1.4.1
- **Fonts**: Google Fonts (Outfit) - Local copy
- **Icons**: Emoji + Custom SVG icons
- **PWA**: Service Worker + Web App Manifest

### **Backend**
- **Platform**: WordPress 6.0+
- **PHP**: 7.4+ (modern PHP practices)
- **Architecture**: Custom post types and taxonomies
- **API**: WordPress REST API (custom endpoints)
- **Database**: MySQL/MariaDB (WordPress standard)

### **Build Tools**
- **CSS Build**: Tailwind CLI (PostCSS + Autoprefixer)
- **Package Manager**: npm
- **Version Control**: Git

### **Dependencies (All Pinned Locally)**
```
├── Tailwind CSS v3.4.0
├── Leaflet v1.9.4
├── Leaflet MarkerCluster v1.4.1
└── Google Fonts (Outfit)
```

## 📁 Theme Structure

```
capiznon-geo/
├── 📄 style.css                 # Theme stylesheet
├── 📄 index.php                  # Main template
├── 📄 functions.php              # Theme functions
├── 📄 header.php                 # Site header
├── 📄 footer.php                 # Site footer
├── 📄 front-page.php             # Homepage with map
├── 📄 archive-cg_location.php    # Locations archive
├── 📄 single-cg_location.php     # Single location page
├── 📄 sidebar.php                # Sidebar
├── 📄 manifest.webmanifest       # PWA manifest
├── 📄 sw.js                      # Service worker
├── 📁 assets/
│   ├── 📁 css/                   # Stylesheets
│   │   ├── main.css              # Custom styles
│   │   ├── single-location.css   # Single page styles
│   │   ├── admin.css             # Admin styles
│   │   ├── input.css             # Tailwind input
│   │   └── tailwind-built.css    # Built Tailwind CSS
│   ├── 📁 js/                    # JavaScript
│   │   ├── main.js               # Frontend functionality
│   │   ├── map.js                # Map functionality
│   │   └── admin.js              # Admin functionality
│   ├── 📁 vendor/                # Pinned dependencies
│   │   ├── tailwindcss.js        # Tailwind CSS
│   │   ├── leaflet.css/js        # Leaflet maps
│   │   ├── markercluster.*       # Marker clustering
│   │   └── outfit.css            # Google Fonts
│   └── 📁 icons/                 # PWA icons
├── 📁 inc/                       # Core functionality
│   ├── setup.php                 # Theme setup
│   ├── post-types.php            # Custom post types
│   ├── taxonomies.php            # Taxonomies
│   ├── meta-boxes.php            # Admin meta boxes
│   ├── rest-api.php              # REST API endpoints
│   ├── template-functions.php    # Helper functions
│   ├── assets.php                # Asset management
│   ├── visits.php                # Visit tracking
│   └── scaffold.php              # Data seeder
├── 📁 docs/                      # Documentation
├── 📄 package.json               # npm configuration
├── 📄 tailwind.config.js         # Tailwind configuration
└── 📄 README.md                  # This file
```

## 🚀 Installation & Setup

### **Requirements**
- WordPress 6.0+
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.2+
- Memory limit: 128MB+

### **Quick Install**

1. **Upload Theme**
   ```bash
   # Copy entire capiznon-geo folder to:
   wp-content/themes/capiznon-geo/
   ```

2. **Activate Theme**
   - Go to WordPress Admin → Appearance → Themes
   - Activate "Capiznon Geo"

3. **Populate Data**
   - Go to Locations → Data Scaffold
   - Click "Create All Data" to populate:
     - Location types (Food, Accommodation, etc.)
     - Areas (Capiz municipalities)
     - Tags, price ranges, cuisines
     - Sample locations

4. **Configure Settings**
   - Set default map center (Roxas City: 11.5853, 122.7511)
   - Configure Mapbox token (optional, for custom tiles)

### **Manual Setup (Advanced)**

If you prefer to set up data manually:

```bash
# Import sample data (optional)
wp capiznon import-sample-data

# Or create via admin interface
# Locations → Data Scaffold → Individual actions
```

## 🔧 Development

### **Local Development**

1. **Clone Repository**
   ```bash
   git clone <repository-url> capiznon-geo
   cd capiznon-geo
   ```

2. **Install Dependencies**
   ```bash
   npm install
   ```

3. **Build CSS**
   ```bash
   # Development build (with watch)
   npm run build-css

   # Production build (minified)
   npm run build-css-prod
   ```

4. **Configure WordPress**
   - Set up local WordPress environment
   - Activate theme
   - Run scaffold for initial data

### **CSS Customization**

1. **Edit Tailwind Config**
   ```javascript
   // tailwind.config.js
   module.exports = {
     content: ['./**/*.php', './assets/js/*.js'],
     theme: {
       extend: {
         colors: {
           // Custom bohemian palette
         }
       }
     }
   }
   ```

2. **Rebuild CSS**
   ```bash
   npm run build-css-prod
   ```

### **Adding Features**

- **Custom Post Types**: Edit `inc/post-types.php`
- **Taxonomies**: Edit `inc/taxonomies.php`
- **REST Endpoints**: Edit `inc/rest-api.php`
- **Templates**: Modify PHP template files
- **Styles**: Edit `assets/css/main.css`

## 📦 Deployment

### **Production Deployment**

1. **Prepare Files**
   ```bash
   # Remove development files (optional)
   rm -rf node_modules/
   rm package-lock.json
   rm tailwind.config.js
   ```

2. **Upload to Server**
   - Upload entire `capiznon-geo` folder
   - Ensure permissions: 755 for folders, 644 for files

3. **Configure WordPress**
   ```bash
   # Set proper file permissions
   find wp-content/themes/capiznon-geo -type d -exec chmod 755 {} \;
   find wp-content/themes/capiznon-geo -type f -exec chmod 644 {} \;
   ```

4. **Activate & Configure**
   - Activate theme in WordPress admin
   - Run data scaffold
   - Test all functionality

### **Server Requirements**

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 7.4+ with extensions:
  - `php-curl`
  - `php-gd`
  - `php-json`
  - `php-mbstring`
  - `php-xml`
- **Database**: MySQL 5.7+ or MariaDB 10.2+

### **Performance Optimization**

1. **Caching**
   - Enable WordPress caching plugins
   - Configure server-level caching

2. **Images**
   - Optimize images before upload
   - Use WordPress image optimization

3. **CDN** (Optional)
   - Theme is self-contained, but CDN can be used for:
     - WordPress media uploads
     - Custom map tiles

## 🎨 Customization

### **Colors & Styling**

Edit the bohemian beach palette in `tailwind.config.js`:

```javascript
colors: {
  primary: { DEFAULT: '#f59e0b', light: '#fbbf24', dark: '#d97706' },
  accent: { DEFAULT: '#fbbf24', dark: '#92400e' },
  sand: { DEFAULT: '#fef3c7', light: '#fef9c3', dark: '#fde68a' },
  ocean: { deep: '#92400e', mid: '#b45309', shallow: '#fbbf24', foam: '#fef3c7' }
}
```

### **Map Configuration**

Default settings in `functions.php`:

```php
define('CAPIZNON_GEO_DEFAULT_LAT', 11.5853);  // Roxas City
define('CAPIZNON_GEO_DEFAULT_LNG', 122.7511);
define('CAPIZNON_GEO_DEFAULT_ZOOM', 14);
```

### **Custom Fields**

Add new meta fields in `inc/meta-boxes.php`:

```php
// Add custom field for locations
add_meta_box('cg_custom_field', 'Custom Info', 'render_custom_field', 'cg_location');
```

## 🔍 Features in Detail

### **Mapping System**
- Interactive Leaflet maps with custom styling
- Marker clustering for performance
- Custom icons and popups
- Geolocation support
- Search by location

### **Filtering System**
- Real-time search with debouncing
- Multi-criteria filtering
- Hierarchical categories
- AJAX-powered results
- Mobile-optimized filter panel

### **User Features**
- Frontend location submission
- Visit tracking system
- User-generated content
- Social sharing
- PWA installation

### **Admin Features**
- Data scaffold system
- Bulk location management
- Custom meta fields
- Visit analytics
- Export/import capabilities

## 🐛 Troubleshooting

### **Common Issues**

1. **Map Not Loading**
   - Check JavaScript console for errors
   - Verify Leaflet dependencies in `assets/vendor/`
   - Ensure REST API endpoints are accessible

2. **Filters Not Working**
   - Check REST API permissions
   - Verify taxonomy data exists
   - Clear browser cache

3. **PWA Issues**
   - Verify service worker registration
   - Check manifest.webmanifest paths
   - Ensure HTTPS for PWA features

### **Debug Mode**

Enable WordPress debug:

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check debug log: `wp-content/debug.log`

## 📝 Changelog

### **v1.0.0** (Current)
- Initial release
- Interactive mapping system
- Advanced filtering
- PWA capabilities
- Admin scaffold system
- Bohemian beach design

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Make changes and test thoroughly
4. Commit changes: `git commit -m 'Add feature'`
5. Push to branch: `git push origin feature-name`
6. Submit pull request

## 📄 License

This theme is licensed under GPL v2.0 or later.

## 🙋‍♂️ Support

For support and documentation:
- Check the `/docs/` folder for detailed guides
- Review the admin scaffold system for data setup
- Test with sample data before customizing

---

**Built with ❤️ for Capiz province**  
🏝️ Bohemian beach aesthetic meets modern web technology
