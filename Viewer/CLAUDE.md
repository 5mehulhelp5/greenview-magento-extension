# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

GreenView 3D Viewer is a Magento 2 extension for integrating 3D Splat viewers and AR functionality into Magento stores. This is a port of the WordPress plugin with the same functionality, adapted to Magento 2's architecture.

Version: 3.1.1
Module Name: GreenView_Viewer
Requirements: Magento 2.3+, PHP 7.4+

## Architecture

### Magento 2 Structure

This extension follows Magento 2's module architecture with proper separation of concerns:

- **registration.php**: Registers the module with Magento's ComponentRegistrar
- **etc/module.xml**: Defines module dependencies and version
- **etc/db_schema.xml**: Declarative schema for database tables
- **etc/di.xml**: Dependency injection configuration
- **etc/config.xml**: Default configuration values
- **etc/acl.xml**: Admin resource permissions
- **etc/widget.xml**: Widget definitions for Page Builder

### Key Components

#### 1. Data Layer (Model & ResourceModel)

**Models:**
- `Model/Splat.php`: Entity model for splat data
- `Model/ApiLog.php`: Entity model for API error logs
- `Model/ResourceModel/Splat.php`: Database operations for splats
- `Model/ResourceModel/ApiLog.php`: Database operations for logs
- `Model/ResourceModel/*/Collection.php`: Collection classes for grid data

**Database Tables:**
- `greenview_splats`: Stores synced splat data (id, name, slug, file_url, plugin_config, etc.)
- `greenview_api_logs`: Stores API error logs for monitoring

#### 2. Service Layer

**Service/ApiClient.php:**
- Wraps all API communication using Magento's Curl client
- Handles authentication with x-company-token header
- Supports dual environments (production/sandbox via Helper/Data)
- Automatic error logging to database
- Methods: `validateToken()`, `getCompanyInfo()`, `getSplats()`, `getSplatBySlug()`, `getSplatById()`

**Service/SplatManager.php:**
- Business logic for splat management
- `syncSplats()`: Paginated API fetch (50 per page) → local storage
- `getSplatData()`: Local database first, API fallback
- `clearAllSplats()`: Truncates table for fresh sync

**Helper/FileCache.php:**
- File caching in `pub/media/greenview-splats/`
- Configurable cache lifetime (default: 24 hours)
- Automatic file extension detection (.splat, .ply, .gltf, .glb)
- `getCachedFileUrl()`: Returns cached file or downloads from API
- `clearCache()`: Removes cached files for specific splat or all

**Helper/Data.php:**
- Configuration helper for accessing system config values
- Methods: `isEnabled()`, `getCompanyToken()`, `isSandboxMode()`, `getApiBaseUrl()`, `getCacheLifetime()`
- Token encryption/decryption via EncryptorInterface

#### 3. Admin Layer

**Controllers (Adminhtml):**
- `Splats/Index`: Displays splat grid
- `Sync/Index`: Triggers manual sync, redirects to splat grid
- `Logs/Index`: Displays API error log grid
- `Logs/Clear`: Clears all API logs
- `System/Config/Validate`: AJAX endpoint for token validation

**UI Components:**
- `view/adminhtml/ui_component/greenview_splats_listing.xml`: Splat grid configuration
- `view/adminhtml/ui_component/greenview_logs_listing.xml`: Log grid configuration
- Data providers configured in `etc/di.xml` as virtual types

**System Configuration:**
- `etc/adminhtml/system.xml`: Admin settings form
- Path: Stores > Configuration > GreenView > 3D Viewer Settings
- Fields: enabled, company_token (encrypted), sandbox_mode, cache_lifetime
- Custom field: Validate Button (Block/Adminhtml/System/Config/ValidateButton.php)

#### 4. Frontend Layer

**Blocks:**
- `Block/Viewer.php`: Main viewer rendering logic
- `Block/ArButton.php`: AR button rendering with mobile detection
- `Block/Widget/Viewer.php`: Widget wrapper for Viewer
- `Block/Widget/ArButton.php`: Widget wrapper for AR Button

**Templates:**
- `view/frontend/templates/viewer.phtml`: Renders `<cds-splat>` web component
- `view/frontend/templates/ar-button.phtml`: Renders AR button with QR modal for desktop

**Widgets:**
- Defined in `etc/widget.xml`
- GreenView 3D Viewer: Parameters (slug, width, height, animate)
- GreenView AR Button: Parameters (slug, text, bg_color, border_color, text_color, width)
- Accessible via Content > Widgets in admin

**Layout XML:**
- `view/frontend/layout/default.xml`: Loads CSS and JS on all pages
- Static assets: `view/frontend/web/css/greenview-viewer.css`, `view/frontend/web/js/cds-splat.iife.js`

### Data Flow

1. **Initial Setup**: Admin configures API token in system config (encrypted storage)
2. **Sync Process**: Admin triggers sync → SplatManager fetches paginated data → truncates table → stores all splats
3. **Widget Rendering**: Widget block created → gets splat data via SplatManager → FileCache downloads/caches file → template renders viewer
4. **AR Flow**: AR button rendered → mobile detection → mobile: direct link, desktop: QR modal with generated QR code URL
5. **Error Handling**: API errors logged to database → viewable in admin grid → clearable via controller

### Important Implementation Notes

#### Magento-Specific Patterns

**Dependency Injection:**
- All class dependencies injected via constructor
- Configured in `etc/di.xml` for virtual types
- Use ObjectManager only in unavoidable cases (not in this module)

**Configuration Access:**
- Use ScopeInterface::SCOPE_STORE for store-specific values
- Encrypted fields use Magento\Framework\Encryption\EncryptorInterface
- Path format: `section/group/field` (e.g., `greenview_viewer/general/enabled`)

**Database Schema:**
- Declarative schema in `etc/db_schema.xml` (Magento 2.3+)
- No install/upgrade scripts needed
- Run `setup:upgrade` to apply schema changes
- Primary keys can be non-integer (UUID string in this case)

**UI Components:**
- Grid data providers configured as virtual types in di.xml
- Collection factories extend `Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult`
- Custom columns extend `Magento\Ui\Component\Listing\Columns\Column`

**Widgets:**
- Widget blocks must implement `Magento\Widget\Block\BlockInterface`
- Extend base block classes (Viewer/ArButton) for code reuse
- Template path format: `Vendor_Module::path/to/template.phtml`

#### File Caching Strategy

Unlike WordPress which uses `wp_remote_get()` with streaming, Magento uses:
```php
$this->curl->get($url);
$fileContent = $this->curl->getBody();
$mediaDir->writeFile($path, $fileContent);
```

Cache location: `pub/media/greenview-splats/` (publicly accessible)
Lifetime check: Compare `filemtime($file)` with configured lifetime

#### API Integration

**Headers:**
- `x-company-token`: API authentication
- `User-Agent`: 'Magento2-GreenView-Viewer/3.1.1'

**Endpoints:**
- Production: `https://api.green-view.nl/integration`
- Sandbox: `https://api-stg.green-view.nl/integration`
- Switched via sandbox_mode config

**Error Logging:**
- Creates ApiLog model instance
- Saves with: operation, url, error_message, response_code, response_body_preview, user_ip, user_agent
- Viewable in admin grid, clearable via controller

## Development Commands

### Module Operations

**Enable module:**
```bash
php bin/magento module:enable GreenView_Viewer
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:flush
```

**Disable module:**
```bash
php bin/magento module:disable GreenView_Viewer
php bin/magento setup:upgrade
php bin/magento cache:flush
```

**Upgrade module:**
```bash
# After code changes
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush

# If schema changed
php bin/magento setup:db-schema:upgrade

# If static files changed
php bin/magento setup:static-content:deploy -f
```

### Testing

**Check module status:**
```bash
php bin/magento module:status GreenView_Viewer
```

**View database schema:**
```bash
php bin/magento setup:db:status
```

**Clear specific caches:**
```bash
php bin/magento cache:clean config
php bin/magento cache:clean layout
php bin/magento cache:clean block_html
```

**Developer mode:**
```bash
php bin/magento deploy:mode:set developer
```

### Logs

**Check system log:**
```bash
tail -f var/log/system.log
```

**Check exception log:**
```bash
tail -f var/log/exception.log
```

## Common Development Tasks

### Adding New Configuration Field

1. Add field to `etc/adminhtml/system.xml` under appropriate group
2. Add default value to `etc/config.xml`
3. Add getter method to `Helper/Data.php`
4. Clear config cache: `php bin/magento cache:clean config`

### Adding New Widget Parameter

1. Add parameter to `etc/widget.xml` under appropriate widget
2. Add getter method to corresponding Block class (Block/Widget/Viewer.php or Block/Widget/ArButton.php)
3. Use in template via `$block->getParameterName()`
4. Clear layout cache: `php bin/magento cache:clean layout`

### Modifying Database Schema

1. Update `etc/db_schema.xml`
2. Run: `php bin/magento setup:db-schema:upgrade`
3. Update Model class if adding/removing fields
4. Clear cache: `php bin/magento cache:flush`

### Adding New API Endpoint

1. Add method to `Service/ApiClient.php`
2. Use `makeRequest()` helper for consistent error handling
3. Optionally add wrapper in `Service/SplatManager.php` for business logic
4. Test with both production and sandbox modes

### Creating New Admin Grid

1. Create UI component XML in `view/adminhtml/ui_component/`
2. Configure data provider in `etc/di.xml` as virtual type
3. Create controller in `Controller/Adminhtml/`
4. Create layout XML in `view/adminhtml/layout/`
5. Add menu item in `etc/adminhtml/menu.xml`
6. Add ACL resource in `etc/acl.xml`

### Adding Custom Column to Grid

1. Create column class extending `Magento\Ui\Component\Listing\Columns\Column`
2. Implement `prepareDataSource()` method
3. Reference in UI component XML with `class` attribute
4. Clear layout cache

## Differences from WordPress Plugin

| WordPress | Magento 2 | Notes |
|-----------|-----------|-------|
| Shortcodes | Widgets / Layout XML | Widgets provide GUI for non-technical users |
| Options API | System Configuration | Encrypted storage, multi-store support |
| $wpdb | ResourceModel/Collection | ORM-like pattern with type safety |
| wp_remote_get() | Curl client | Similar functionality, different API |
| wp_upload_dir() | Filesystem DirectoryList | Magento abstraction layer |
| add_action/add_filter | Events/Observers | Not used in this module (direct rendering) |
| wp_enqueue_script() | Layout XML | Automatic dependency management |

## Security Considerations

- API token stored encrypted using Magento's EncryptorInterface
- ACL permissions required for all admin operations
- CSRF protection via form_key in AJAX requests
- SQL injection prevented by parameterized queries in ResourceModel
- XSS prevented by template escaping (`$block->escapeHtml()`, `$block->escapeUrl()`)
- File uploads to media directory protected (no direct PHP execution)
- Admin-only access to sync, logs, and configuration

## Performance Optimization

- File caching reduces API calls (configurable lifetime)
- Local database storage for splat metadata (fast lookups)
- Indexed database columns (slug, name) for grid performance
- Pagination in API sync (50 per page)
- Static content deployment for production
- Flat collection loading in grids (no lazy loading overhead)
