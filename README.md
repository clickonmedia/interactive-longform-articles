# Interactive Longform Articles

Interactive multimedia articles, with a background scroll effect. Wordpress plugin for for longform journalism.

## Examples

https://www.theversed.com/88510/meet-the-extraordinary-racing-driver-who-went-from-navy-to-nascar

https://www.theversed.com/90093/vague-a-lame-waves-of-the-soul

https://www.theversed.com/88688/krav-maga-the-peacekeeper

![Screencast](https://github.com/clickonmedia/interactive-longform-articles/blob/v2-dev/img/screen-480-15s-10fps.gif?raw=true)

## Usage

1. Upload the unzipped plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Add a new post or page and select "Interactive article" as the template on the right sidebar
4. Add sections for the article. For each section, choose:
... * Section type
... * Background
... * Text content
5. Publish article

### Settings

* In Settings > Interactive Longform Articles, you can:
... * Enable Interactive Articles as a separate post type
... * Enable optional sections
... * Add Google Analytics Event tracking

### Text formatting

* Click 'Toolbar Toggle' icon to reveal more text formatting options under the 'Formats' option.

### Custom logo

* A custom logo can be added by enabling Custom Logo for the theme: https://developer.wordpress.org/themes/functionality/custom-logo

### Header and footer

* Default header and footer can be customized by including a header.php or footer.php template in  "interactive" directory in the theme

### Shortcode

* A carousel list of interactive articles can be added by using shortcode [interactive-list max="3"]. "Max" parameter indicates the maximum amount of articles listed. Requires that featured images are set for all articles.

## Requirements

* Wordpress 3.0+

## Development

```
npm install
gulp default
gulp watch
```

The plugin uses SASS for styling. The files can be found at the /sass directory.

https://sass-lang.com/guide

## License

Licensed under GNU General Public License v3.0. See LICENSE for details

## Acknowledgements

Authored by the tech team at CLICKON Media Ltd https://www.clickon.co
