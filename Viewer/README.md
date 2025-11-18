# GreenView 3D Viewer - Magento 2 Extension

A Magento 2 extension for integrating GreenView 3D viewers and AR functionality into your store.

**Version:** 3.1.1
**Author:** Angga Pixa
**License:** GPL-2.0+

## Features

- Display 3D Splat viewers using widgets or layout XML
- AR integration with QR code support for desktop and direct links for mobile
- Local file caching for optimal performance
- Admin interface for managing splats and viewing API logs
- API sync functionality to keep splat data up-to-date
- Secure API token management with encryption
- Support for both production and sandbox environments
- Comprehensive error logging and monitoring

## Requirements

- Magento 2.3.x or higher
- PHP 7.4 or higher
- Valid GreenView API token from https://green-view.nl

## Installation

### Method 1: Manual Installation

1. **Upload Module Files:**
   ```bash
   # Navigate to your Magento root directory
   cd /path/to/magento

   # Create vendor directory structure
   mkdir -p app/code/GreenView/Viewer

   # Copy module files to app/code/GreenView/Viewer/
   ```

2. **Copy JavaScript Library:**
   ```bash
   # Copy cds-splat.iife.js from WordPress plugin
   cp /path/to/wordpress/plugin/public/js/cds-splat.iife.js \
      app/code/GreenView/Viewer/view/frontend/web/js/
   ```

3. **Enable Module:**
   ```bash
   php bin/magento module:enable GreenView_Viewer
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento setup:static-content:deploy -f
   php bin/magento cache:flush
   ```

### Method 2: Composer Installation

```bash
# Add repository to composer.json (if hosting in private repo)
composer config repositories.greenview-viewer vcs https://github.com/yourrepo/greenview-viewer

# Install module
composer require greenview/viewer:^3.1

# Enable and deploy
php bin/magento module:enable GreenView_Viewer
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:flush
```

## Configuration

### 1. Basic Setup

Navigate to **Stores > Configuration > GreenView > 3D Viewer Settings**

- **Enable Module:** Yes/No toggle
- **Company API Token:** Your GreenView API token
- **Sandbox Mode:** Use staging API for testing
- **Cache Lifetime:** Duration to cache splat files (default: 86400 seconds / 24 hours)

### 2. Validate API Token

Click the "Validate Token" button to verify your API connection and view your company information.

### 3. Sync Splats

Navigate to **GreenView > Sync Splats** to fetch all available splats from the API and store them locally.

## Usage

### Method 1: Using Widgets (Recommended)

The easiest way to add 3D viewers to your store is through widgets.

#### Adding a 3D Viewer Widget

1. Navigate to **Content > Widgets > Add Widget**
2. Select **Type:** "GreenView 3D Viewer"
3. Choose **Design Theme** and click "Continue"
4. Configure widget:
   - **Splat Slug:** Enter the slug of your splat
   - **Width:** e.g., "100%" or "500px" (default: 100%)
   - **Height:** e.g., "400px" or "50vh" (default: 400px)
   - **Enable Animation:** Yes/No
5. Assign to store views and layout
6. Save widget

#### Adding an AR Button Widget

1. Navigate to **Content > Widgets > Add Widget**
2. Select **Type:** "GreenView AR Button"
3. Configure widget:
   - **Splat Slug:** Enter the slug of your splat
   - **Button Text:** e.g., "View in AR" (default: "Discover AR")
   - **Background Color:** Hex color code (default: #38585a)
   - **Border Color:** Hex color code
   - **Text Color:** Hex color code
   - **Button Width:** e.g., "235px"
4. Save and assign to layout

### Method 2: Using Layout XML

Add to your custom layout XML file (e.g., `cms_page_view.xml`):

```xml
<referenceContainer name="content">
    <!-- 3D Viewer -->
    <block class="GreenView\Viewer\Block\Viewer" name="greenview.viewer" template="GreenView_Viewer::viewer.phtml">
        <arguments>
            <argument name="slug" xsi:type="string">your-splat-slug</argument>
            <argument name="width" xsi:type="string">100%</argument>
            <argument name="height" xsi:type="string">500px</argument>
            <argument name="animate" xsi:type="boolean">true</argument>
        </arguments>
    </block>

    <!-- AR Button -->
    <block class="GreenView\Viewer\Block\ArButton" name="greenview.ar_button" template="GreenView_Viewer::ar-button.phtml">
        <arguments>
            <argument name="slug" xsi:type="string">your-splat-slug</argument>
            <argument name="text" xsi:type="string">View in AR</argument>
            <argument name="bg_color" xsi:type="string">#38585a</argument>
        </arguments>
    </block>
</referenceContainer>
```

### Method 3: Using PHTML Templates

In your custom `.phtml` template file:

```php
<?php
// 3D Viewer
echo $this->getLayout()
    ->createBlock(\GreenView\Viewer\Block\Viewer::class)
    ->setTemplate('GreenView_Viewer::viewer.phtml')
    ->setData('slug', 'your-splat-slug')
    ->setData('width', '100%')
    ->setData('height', '400px')
    ->setData('animate', true)
    ->toHtml();

// AR Button
echo $this->getLayout()
    ->createBlock(\GreenView\Viewer\Block\ArButton::class)
    ->setTemplate('GreenView_Viewer::ar-button.phtml')
    ->setData('slug', 'your-splat-slug')
    ->setData('text', 'Discover AR')
    ->toHtml();
?>
```

## Admin Features

### Manage Splats

Navigate to **GreenView > Manage Splats** to:
- View all synced splats in a grid
- Search and filter splats
- View splat details (thumbnail, name, slug, file type, AR status)
- See creation and update dates

### Sync Splats

Navigate to **GreenView > Sync Splats** to manually synchronize splat data from the GreenView API. This:
- Fetches all pages of splat data (50 per page)
- Updates local database
- Clears file cache to ensure fresh downloads

### API Logs

Navigate to **GreenView > API Logs** to:
- View all API error logs
- Filter by timestamp, operation, or error message
- See detailed request/response information
- Clear all logs with one click

## Available Parameters

### 3D Viewer Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `slug` | string | *required* | Splat slug identifier |
| `id` | string | - | Splat ID (alternative to slug) |
| `width` | string | 100% | Viewer width |
| `height` | string | 400px | Viewer height |
| `animate` | boolean | false | Enable auto-rotation |
| `class` | string | - | Custom CSS class |
| `style` | string | - | Custom inline styles |
| `position` | string | - | 3D position (x,y,z) |
| `scale` | string | - | 3D scale (x,y,z) |
| `initial_camera_position` | string | - | Camera position (x,y,z) |
| `use_observer` | boolean | true | Enable intersection observer |

### AR Button Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `slug` | string | *required* | Splat slug identifier |
| `id` | string | - | Splat ID (alternative to slug) |
| `text` | string | Discover AR | Button text |
| `bg_color` | string | #38585a | Background color (hex) |
| `border_color` | string | #38585a | Border color (hex) |
| `text_color` | string | #38585a | Text color (hex) |
| `width` | string | 235px | Button width |
| `class` | string | - | Custom CSS class |
| `style` | string | - | Custom inline styles |

## Architecture

### Module Structure

```
GreenView/Viewer/
├── Block/
│   ├── Adminhtml/
│   │   └── System/Config/ValidateButton.php
│   ├── Widget/
│   │   ├── ArButton.php
│   │   └── Viewer.php
│   ├── ArButton.php
│   └── Viewer.php
├── Controller/
│   └── Adminhtml/
│       ├── Logs/
│       ├── Splats/
│       ├── Sync/
│       └── System/Config/
├── etc/
│   ├── adminhtml/
│   │   ├── menu.xml
│   │   └── system.xml
│   ├── acl.xml
│   ├── config.xml
│   ├── db_schema.xml
│   ├── di.xml
│   ├── module.xml
│   └── widget.xml
├── Helper/
│   ├── Data.php
│   └── FileCache.php
├── Model/
│   ├── ResourceModel/
│   │   ├── ApiLog/
│   │   └── Splat/
│   ├── ApiLog.php
│   └── Splat.php
├── Service/
│   ├── ApiClient.php
│   └── SplatManager.php
├── Ui/Component/Listing/Column/
│   ├── SplatActions.php
│   └── Thumbnail.php
├── view/
│   ├── adminhtml/
│   │   ├── layout/
│   │   ├── templates/
│   │   └── ui_component/
│   └── frontend/
│       ├── layout/
│       ├── templates/
│       └── web/
│           ├── css/
│           └── js/
└── registration.php
```

### Data Flow

1. **Configuration**: Admin enters API token in system configuration
2. **Sync**: Manual sync fetches paginated data from API → stores in database
3. **Display**: Widget/block retrieves splat data → caches file locally → renders viewer
4. **Caching**: Files cached in `pub/media/greenview-splats/` with configurable lifetime
5. **Error Logging**: All API errors logged to database for monitoring

### Database Tables

**greenview_splats**
- Stores splat metadata (id, name, slug, file_type, URLs, config, etc.)
- Primary key: `id` (UUID from API)
- Indexes on `slug` and `name` for fast lookup

**greenview_api_logs**
- Stores API error logs
- Auto-incrementing `log_id`
- Includes timestamp, operation, URL, error details, user info

## Troubleshooting

### Module Not Appearing in Admin

```bash
php bin/magento module:status
php bin/magento module:enable GreenView_Viewer
php bin/magento setup:upgrade
php bin/magento cache:flush
```

### Database Tables Not Created

```bash
php bin/magento setup:upgrade
php bin/magento setup:db-schema:upgrade
```

### Static Files Not Loading

```bash
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
```

### API Connection Issues

1. Check API token in **Stores > Configuration > GreenView**
2. Click "Validate Token" button
3. Check **GreenView > API Logs** for error details
4. Verify network connectivity to api.green-view.nl
5. Try enabling Sandbox Mode for testing

### File Caching Issues

- Check write permissions on `pub/media/` directory
- Verify cache lifetime configuration
- Clear cache: **GreenView > Sync Splats** (clears all cached files)
- Check `var/log/system.log` for file cache errors

## Development

### Enable Developer Mode

```bash
php bin/magento deploy:mode:set developer
```

### View Logs

```bash
tail -f var/log/system.log
tail -f var/log/exception.log
```

### Debugging

Add to your layout XML for testing:

```xml
<action method="setTemplate">
    <argument name="template" xsi:type="string">GreenView_Viewer::viewer.phtml</argument>
</action>
```

## Support

- Documentation: https://green-view.nl/documentation
- Support: https://green-view.nl/support
- GitHub Issues: [Your Repository URL]

## Changelog

See CHANGELOG.md for version history.

## License

This extension is licensed under GPL-2.0+. See LICENSE.txt for details.

## Credits

Developed by Angga Pixa for GreenView
Based on the GreenView WordPress Plugin v3.1.1
