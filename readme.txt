=== Interactive Longform Articles ===

Contributors: CLICKON Media
Tags: interactive, multimedia, longform, journalism, article, scroll, image, video, presentation
Requires at least: 3.0.1
Tested up to: 5.4.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Interactive multimedia articles for longform journalism, with a background scroll effect.


== Description ==

Interactive longform articles, with a fading scroll effect.


== Examples ==

https://www.theversed.com/90435/life-preserver-meet-the-navy-airr-who-is-dedicating-his-life-to-helping-others/

https://www.theversed.com/91272/ascender-one-sailors-journey-reach-new-heights

https://www.theversed.com/88874/wavemaker-the-road-to-recovery


== Usage ==

1. Upload the unzipped plugin files to the `/wp-content/plugins/` directory, or install the plugin via Wordpress CMS
2. Activate the plugin in Plugins section in Wordpress CMS.
3. If using WP 5.0+, disable Gutenberg Editor (see instruction below)
4. Add a new post or page, and select "Interactive article" as the page template
5. Create an Interactive Article (see instruction below)
6. Publish article


== How to disable the Gutenberg editor ==

Interactive Longform Articles doesn't currently support the Gutenberg Editor. Please see your options on disabling the editor at: https://kinsta.com/blog/disable-gutenberg-wordpress-editor


== How to create Interactive Articles ==

https://github.com/clickonmedia/interactive-longform-articles/wiki/Instructions


== Settings ==

* There are a few optional settings you can adjust in Settings > Interactive Longform Articles:
  * Enable Interactive Articles as a separate post type
  * Enable the "downloads" section
  * Add Google Analytics Event tracking
  * Adjust the progress indicator color


== Optional features ==


=== Custom logo ===

* A custom logo can be added by enabling Custom Logo for the theme: https://developer.wordpress.org/themes/functionality/custom-logo


=== Header and footer ===

* Default header and footer can be customized by including a header.php or footer.php template in a directory named "interactive" inside the theme


=== Shortcode ===

* A carousel list of interactive articles can be added by using shortcode [interactive-list max="3"]. "Max" parameter indicates the maximum amount of articles listed. Requires that featured images are set for all articles.


== Requirements ==

* Wordpress 3.0 - 4.*
* Wordpress 5.* with Gutenberg disabled
