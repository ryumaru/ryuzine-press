=== Ryuzine Press ===
Author URI: http://www.ryumaru.com
Plugin URI: http://www.ryumaru.com/downloads/packages/
Contributors: offworld
Tags: Issue, Issue Manager, Editions, Magazine, Webcomics
Requires at least: 3.5
Tested up to: 4.2.3
Stable Tag: 1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Bridges curated blog content into the Ryuzine webapp.

== Description ==

"Ryuzine Press™" is a plugin which bridges content from your WordPress blog to a Ryuzine webapp (web application) installation. The plugin allows you to easily invoke the Ryuzine webApp and assign select WordPress posts and other media to the webapp's pages to create unlimited, curated "editions" of your blog content as Ryuzine publications. It is not a theme itself, and can be used in conjunction with any WordPress theme without altering the front-end layout of your blog. Your Ryuzine Press Editions will exist alongside your blog, not replace it.

"Ryuzine™" is a digital publishing webapp targeting both desktop and mobile users. It features a responsive HTML5+CSS3+Javascript interface. The original stand-alone version was released in October 2011 along with the "Ryuzine Writer" authoring web app. These were later joined by a newsstand webapp called "Ryuzine Rack."

= License =

Ryuzine Press is released under the GNU General Public License version 3 (GPLv3). The Ryuzine webapps are released under the Mozilla Public License (MPL) 2.0.  The Ryuzine webapps only supply some scripts, images, and stylesheets to the Ryuzine Press plugin template.  You may have received the Ryuzine webapps bundled with the plugin (allowable under MPLv2 Section 3.3) and the combined "Larger Work" is released under a GPL/MPL dual-license.  

Distribution of publications in Ryuzine format does not require you to also provide source code if the webapp or plugin code has not been modified. Source code for both the Ryuzine Press plugin and Ryuzine webapps is available on [GitHub](https://github.com/ryumaru).

“Ryuzine” and the Ryuzine logos are trademarks of K.M. Hansen & Ryu Maru.  If you are distributing unaltered software, downloaded directly from Ryu Maru, to anyone in any way or for any purpose, no further permission is required.  Any other use of our trademarks requires prior authorization.

== Installation ==

= For Non-Developers =

1. Publishers (people who just want to USE the plugin and not contribute code to help develop it) should download the Ryuzine Press plugin from the [Ryu Maru website](http://www.ryumaru.com/downloads/ryuzinepress/ryuzine-press.zip).
2. Un-zip the folder and upload it to your WordPress */wp-content/plugins*/ folder.
3. From your WordPress Dashboard go to *Plugins > Installed Plugins* and find the "Ryuzine Press" plugin in the list and "activate" it.
4. In your Dashboard you may want to go to *Ryuzine Press > Tools > Update Ryuzine* and check if there is an update for Ryuzine webapp (there is no automatic notice for webapp updates, you just have to periodically click the button to initiate a check).
5. In your Dashboard go to *Ryuzine Press > Options* and set any default configuration options for your Ryuzine Press Editions (note: when you create a new Edition you also have the option of over-riding many settings on a per-edition basis).

= For Developers =

1. Developers who want to help improve the plugin can download the source and contribute code through the [GitHub repository](https://github.com/ryumaru/ryuzine-press).
2. Un-Zip into your WordPress */wp-content/plugins/* folder
3. From your WordPress Dashboard go to *Plugins > Installed Plugins* and find “Ryuzine Press” in the list and “activate” it.
4. You will see a warning that the “Ryuzine webapp” is not installed.  In your Dashboard go to * Ryuzine Press > Tools* and select the “Update Ryuzine” tab.
5. You should see a button inviting you to “Install” the webapp.  Click that and it will try to download, unzip, and install the webapp into the Ryuzine Press plugin.
	a. If file, folder, or server permissions disallow this from working you will need to **manually** install the webapp by downloadin, unzipping, and uploading the "ryuzine" folder via FTP into the "ryuzine-press" plugin folder:
		i. Download the [BUNDLED RELEASE](http://www.ryumaru.com/downloads/ryuzine/1.0/ryuzine.zip), which includes a number of Add-Ons and Themes or
		ii. Download the [SOURCE RELEASE](https://github.com/ryumaru/ryuzine), which has only the minimal Add-Ons and Themes needed to function (you can also manually download and install more Add-Ons and Themes if you wish).  If you download the Source Release make sure you change the folder name from "ryuzine-master" to just "ryuzine" or it won't work.
6. You may need to refresh the Tools page before the warning goes away.  There is no automatic update check for the webapp, you will need to periodically press the button to initiate a check, or follow @RyuMaruCo on Twitter or check the [Ryu Maru](http://www.ryumaru.com) website for announcements of new releases.
7. In your Dashboard go to *Ryuzine Press > Options* and set any default configuration options for your Ryuzine Press Editions (note: when you create a new Edition you also have the option of over-riding many of these setting on a per-edition basis).

== Frequently Asked Questions ==

= Q. I just installed the new version and all my existing Editions seem to be messed up.  Help! How do I fix them? =

A. Editions created with the older versions of the Ryuzine Press plugin used the standard "Categories" taxonomy.  Since version 0.9.6.6 the plugin uses a custom "Ryuzine Press Issues" taxonomy.  Manually reassigning all your Editions and the posts within to the new taxonomy is a lot of work, which is why we have an additional [Migration Assistant](http://www.ryumaru.com/downloads/ryuzinepress/ryuzinepress-migrator.zip) plugin to help you bulk process existing Editions into the new format.

= Q. I see a warning that "Ryuzine" is not installed, but didn't I just install the plugin? =

A. The Ryuzine webapp is a separate program that must be installed INTO the Ryuzine Press Plugin.  If you go to *Ryuzine Press > Tools > Update Ryuzine* you should see a button to "Install" the webapp to the plugin.  However, if it says "Download" the plugin folder is not writable and you either need to change the permissions on it or manually FTP the downloaded and unzipped file into the Ryuzine Press plugin folder.

= Q. I thought I had the "Ryuzine" webapp installed, but after updating it says that it is not installed.  What happened to it? =

A. The Ryuzine Press plugin now has the ability to auto-update like any other WordPress plugin.  This process, however, trashes any existing installation of the webapp (because it is inside the plugin's folder).  After updating the plugin you will also need to update the webapp.  This is actually not a bad idea since both the webapp and the plugin are usually updated at the same time and ensures you will be able to take advantage of new fixes or features.

= Q. Why do I have to install the webapp separately? =

A. Because the Ryuzine webapps are released under a different license than the Ryuzine Press Plugin you must acquire them separately.

= Q. I created a new Edition, but when I press "View Ryuzine" why do I get a white page with "Add, Bookmarks, Done, Fonts, Done...etc." instead of a magazine? =

A. You need to assign the Edition to at least one entry in the "Ryuzine Press Issues" meta-box, and the Edition cannot be the ONLY thing assigned to that Issue.

= Q. I created a new Edition and assigned it to a new "Ryuzine Press Issue" I just created.  But when I try to "view" it just sits there on the "Loading app..." splash screen.  What am I doing wrong? =

A. At least one post has to be assigned to that same Issue or there won't be any content for the Ryuzine webapp to show, which causes it to stop at the splash screen.  Make sure you assign posts to the Issue and try viewing it again.

= Q. Can you embed other Editions into an Edition? =

A. Technically yes, if any of the posts included in the Edition have another Edition embedded into them (and in case you are wondering, no embedding an Edition into itself does not work).  If you really want to include the content of another Edition, however, you should be using the "Collection" capability - which means, in addition to this Edition's Issue, you simply assign the current Edition to the same Ryuzine Issue as the other one.  You should avoid assigning posts with Ryuzine embeds in them to your Editions - it creates a confusing end-user experience!


== Changelog ==

= 1.0 =
* App Icons are now automatically generated from one image (you no longer need multiple images already at the icon dimensions)
* Added support for the new App Logo image
* Add-Ons list is now auto-generated and they can be enabled/disabled is a list similar to  how you activate/deactivate WordPress plugins.
* Integrated banner ad settings now consistent with other integrated advertising.
* Lightbox shortcodes updated to HTML <figure> code.
* If using “ryulightbox” add-on Lightbox Links no longer require using shortcode to set parameters (and if using no lightbox script they are simply regular <a> links)
* Templates updated to new Ryuzine Document Format specification.
* Edition-specific styles are now written into a “generated_css” folder instead of into the “ryuzine/css” folder, allowing updates of the Ryuzine webapp without having to regenerate the Edition external stylesheets.  The only time you’d need to regenerate them now would be after an update of Ryuzine Press itself.
* Added ability to apply WordPress theme to Ryuzine Press Editions (though this can potentially break the Ryuzine layout and/or require over-rides in an Issue-specific Stylesheet to make it work).
* Fixed numerous debug notices, mostly due to variables values being compared before checking if the variable has been set.
* Fixed WordPress comment form inclusion in Ryuzine pages


== Upgrade Notice ==

= 1.0 =
Will not work with webapp beta versions!  Upgrade both Ryuzine Press *and* the Ryuzine webapp.
