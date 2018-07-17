# Interactive Longform Articles

Interactive multimedia articles, with a background scroll effect. Wordpress plugin for for longform journalism.

## Examples

https://www.theversed.com/88510/meet-the-extraordinary-racing-driver-who-went-from-navy-to-nascar

https://www.theversed.com/90093/vague-a-lame-waves-of-the-soul

https://www.theversed.com/88688/krav-maga-the-peacekeeper

## Usage

1. Upload the unzipped plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Settings > Interactive Longform Articles
⋅⋅⋅ * Enable interative articles for the relevant post types
⋅⋅⋅ * Configure other settings (optional)
4. Add a new post and select "Enable interactive article"
5. Add sections for the article. For each section, choose:
... * Section type
... * Background
... * Text content
6. Publish article

## Requirements

* Wordpress 3.0+
* Advanced Custom Fields Pro: https://www.advancedcustomfields.com/pro

## Development

```
npm install
gulp watch
```

The plugin uses SASS for styling. The files can be found at the /sass directory.

https://sass-lang.com/guide

## Miscellaneous

add_theme_support( 'custom-logo' );

## License

Licensed under GNU General Public License v3.0. See LICENSE for details

## Acknowledgements

Authored by the tech team at CLICKON Media Ltd https://www.clickon.co
