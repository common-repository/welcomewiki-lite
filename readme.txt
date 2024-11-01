=== WelcomeWiki Lite ===
Contributors: vtrung, marktmattson
Tags: content framework, embed, mediawiki, travel, wiki, wiki inc, wiki-embed, wikipedia, wikivoyage, world tourism, tourism marketing, destinations

Requires at least: 3.5
Tested up to: 3.71
Stable tag: 1.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WelcomeWiki Lite lets users embed an entire MediaWiki page or a section from a MediaWiki page into their Wordpress or non-WordPress site.

== Description ==

<br /><strong>What is WelcomeWiki Lite</strong><br />
WelcomeWiki Lite lets users embed an entire MediaWiki Page or a section from a MediaWiki page into their Wordpress or non-WordPress site. WelcomeWiki Lite embeds content from any or all pages that follow MediaWiki protocols including Wikipedia and WikiVoyage.

WelcomeWiki Lite is implemented using shortcodes [welcomewikilite] or [welcomewikiliteform]. [welcomewikilite] derives from either the dashboard or from [[]] in page or post editors.

[welcomewikiliteform] may also be used in a post or page to build a web form-based manifestation of internal operations. [welcomewikiliteform] was built for use by WordPress and non-WordPress developers alike. The web form generates a generic js script which may be embedded into any webpage regardless of framework.

<br /><strong>How Did WelcomeWiki Lite Come About?</strong><br />
WelcomeWiki Lite resulted from a fusion of three independent projects, which until 2013 were unaware of each others’ existence until a serendipitous moment in 2013 when they all came together. 

Mark Mattson and Trung Van Huu of Cartonova had been experimenting with copying Wikipedia content into websites built for clients; Terry Jackson had completed a feasibility study entitled ‘Wales Settlements Project & Wiki Wales’, and Robin Owain, Wikimedia UK’s Wikipedia Manager for Wales, invited Jackson to conduct a similar study for the ‘Llwybrau Byw! Living Paths!’ pilot project.

By this time, Mattson had become acquainted with Jackson on a LinkedIn group entitled ‘A New Model for Destination Marketing’ and thus the circle was complete! It was a unanimous decision then to make the code available to the WordPress and Wikipedia communities.

== Installation ==

	1.	Upload WelcomeWiki_Lite folder to the /wp-content/plugins/ directory
	2.	Activate the plugin through the 'Plugins' menu in WordPress
	3.	Change the wiki embed settings to your liking
	4.	Change css to your liking


== Screenshots ==

1. "Shortcode editor pop-up in page Editor" This description corresponds to screenshot Page_shortcode.png in the assets directory.
2. "Shortcode in Page Editor reflects wikiurl, sections and settings" This description corresponds to screenshot shortcode.png in the assets directory.
3. "WelcomeWiki Lite as a web form reflecting wikiurl, sections, settings, and script generator" This description corresponds to screenshot Webform.png in the assets directory.
4. "Embedded wiki content in webpage contracted using purchased WordPress theme" This description corresponds to screenshot page_embed.png in the assets directory.

== Changelog ==

= 1.7 =
* Fix wrong path of image on admin-overlay.php* Clean and advance js call on welcomewiki_script.js* Remove according js on welcomewikilite.php

= 1.6 =
* Update Description
* Version management

= 1.5 =
* Update Readme
* Version management

= 1.4 =
* Update Readme
* Version management

= 1.3 =
* Update Readme
* Version management

= 1.2 =
* Update Readme
* Version management

= 1.1 =
* Validated Readme
* Version management

= 1.0 =
* Initial commit

== Frequently Asked Questions ==
<strong>Is there a detailed reference manual with the plugin?</strong><br />
No. Everything you need to know can be found on these two videos or in the Usage section under the Other Notes tab. <a href="http://welcomewiki.walesmaps.net/?page_id=2428">WelcomeWiki Lite Plugin Version to Build Content Within WordPress</a><br><a href="http://welcomewiki.walesmaps.net/?page_id=2351">WelcomeWiki Lite Web Version to Build Content for Use on Any Website Within or Outside WordPress</a><br />
<br /><strong>Can I install the widget more than one time?</strong><br />
That&acute;s very easy! Just upload it to as many themes as you wish.<br />
<br /><strong>How can I use Shortcodes?</strong><br />
Refer to the videos above and refer to the Usage section under the Other Notes tab.<br />
<br /><strong>What content will Welcomewiki Lite gather?</strong><br />
Welcomewiki Lite will gather any page or one section of any page that uses MediaWiki protocols.<br />
<br /><strong>Can Welcomewiki Lite be used outside WordPress?</strong>
Yes, but not as a plugin. WordPress can employ content within pages or posts using WordPress editors or they can build a page or post for use outside a WordPress site. This outside use is explained in the Usage section of the plugin and by watching <a href="http://welcomewiki.walesmaps.net/?page_id=2351">WelcomeWiki Lite Web Version to Build Content for Use on Any Website Within or Outside WordPress</a>


== Usage ==

WelcomeWiki Lite accepts the following arguments: * url: (required) the web address of the wiki article that you want to embed on this page. * Remove links and [1]: Hide "edit" and references from the wiki. * Remove content index: Hide the page's contents box. * Remove right column index box: Hide right column from wiki pages. * Remove images and captions: Hide images and captions.

Settings for the plugin can be found in 'WelcomeWiki Lite' -> 'Settings' on the dashboard.

Using the plugin on the dashboard generates as shortcode [welcomewikilite] in the right column upon Save. This shortcode is copied and pasted into a post or page.

The plugin may also be used directly within the post or page editor by clicking the link "WelcomeWiki Lite" to the right of the "Add Media" button above the content editor. When using the plugin in this fashion, a popup window opens in the post or page editor that offers the same settings and embed options as those offered in the dashboard. A button inside the pop-up window named "Insert into Post/Page" places the shortcode and associated settings directly into the page or post for display.

A second display option that can be used from a Page or Post editor page is the shortcode [welcomewikiliteform].

By placing [welcomewikiliteform] directly into a post or page and saving, a web form page is created that is a graphical front end view of the dashboard and [welcomewikilite] editor functions. This graphical web form does not generate a shortcode for embedding. Rather, it generates a js script which may be used in any web framework. This graphical view and its js processor may be seen as a screen shot Webform.png.

Setting options are available at the dashboard, on the post/page editor popup [welcomewikilite] or on the [welcomewikiliteform] graphical web form. Settings enable users to select an entire wiki page or a section for embedding. 

Example: [welcomewikilite wikiurl="http://en.wikipedia.org/wiki/Wales" sections="Etymology of Wales" settings="1,2,3"]
