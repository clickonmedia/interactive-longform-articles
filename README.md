# Interactive Longform Articles

Interactive multimedia articles, with a background scroll effect. Wordpress plugin for for longform journalism.

![Screencast](https://github.com/clickonmedia/screenshots/raw/master/interactive-480-15s-10fps.gif)

## Examples

https://www.theversed.com/88874/wavemaker-the-road-to-recovery

https://www.theversed.com/91272/ascender-one-sailors-journey-reach-new-heights

https://www.theversed.com/88688/krav-maga-the-peacekeeper

https://www.theversed.com/90435/life-preserver-meet-the-navy-airr-who-is-dedicating-his-life-to-helping-others/


## Usage

1. Upload the unzipped plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin in Plugins section in Wordpress
3. Go to Settings > Interactive Longform Articles and adjust plugin settings
4. Go to Settings > Permalinks and click "Save"
5. Add a new post or page, and select "Interactive article" as the page template
6. Add sections for the article. For each section, choose:
  * Section type
  * Background
  * Text content
7. Publish article


## Instructions

https://github.com/clickonmedia/interactive-longform-articles/wiki/Instructions

This plugin does not yet support the new Gutenberg editor in WP 5.0 and up. To disable Gutenberg, please take a look at:
https://kinsta.com/blog/disable-gutenberg-wordpress-editor

### Settings

* In Settings > Interactive Longform Articles, you can:
  * Enable Interactive Articles as a separate post type
  * Enable optional sections
  * Add Google Analytics Event tracking

### Optional features

#### Custom logo

* A custom logo can be added by enabling Custom Logo for the theme: https://developer.wordpress.org/themes/functionality/custom-logo

#### Header and footer

* Default header and footer can be customized by including a header.php or footer.php template in  "interactive" directory in the theme

#### Shortcode

* A carousel list of interactive articles can be added by using shortcode [interactive-list max="3"]. "Max" parameter indicates the maximum amount of articles listed. Requires that featured images are set for all articles.

## Requirements

* Wordpress 3.0 - 4.*

## Wordpress Plugin page

https://wordpress.org/plugins/interactive-longform-articles/

## Development

```
npm install
npm install gulp-cli -g
gulp default
gulp watch
npx babel --watch src --out-dir js --presets react-app/prod
```

The plugin uses SASS for styling. The files can be found at the /sass directory.

https://sass-lang.com/guide

## License

Licensed under GNU General Public License v3.0. See LICENSE for details

## Acknowledgements

Authored by the tech team at CLICKON Media Ltd https://www.clickon.co
