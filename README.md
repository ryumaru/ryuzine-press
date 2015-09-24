# Ryuzine Press Plugin

Ryuzine Press is a plugin for WordPress that “bridges” curated content from your blog into the Ryuzine webapp.  It also allows you to replace the *ryuzine* custom post-type archive with the Ryuzine Rack newsstand.

## Installation

### For Non-Developers

1. Publishers (people who just want to USE the plugin and not contribute code to help develop it) should download the Ryuzine Press plugin from the [Ryu Maru website](http://www.ryumaru.com/downloads/ryuzinepress/ryuzine-press.zip).
2. Un-zip the folder and upload it to your WordPress */wp-content/plugins*/ folder.
3. From your WordPress Dashboard go to *Plugins > Installed Plugins* and find the "Ryuzine Press" plugin in the list and "activate" it.
4. In your Dashboard you may want to go to *Ryuzine Press > Tools > Update Ryuzine* and check if there is an update for Ryuzine webapp (there is no automatic notice for webapp updates, you just have to periodically click the button to initiate a check).
5. In your Dashboard go to *Ryuzine Press > Options* and set any default configuration options for your Ryuzine Press Editions (note: when you create a new Edition you also have the option of over-riding many settings on a per-edition basis).

### For Developers

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

## License

Ryuzine Press is released under the GNU General Public License version 3 (GPLv3). The Ryuzine webapps are released under the Mozilla Public License (MPL) 2.0.  The Ryuzine webapps only supply some scripts, images, and stylesheets to the Ryuzine Press plugin template.  You may have received the Ryuzine webapps bundled with the plugin (allowable under MPLv2 Section 3.3) and the combined "Larger Work" is released under a GPL/MPL dual-license.  

Distribution of publications in Ryuzine format does not require you to also provide source code if the webapp or plugin code has not been modified. Source code for both the Ryuzine Press plugin and Ryuzine webapps is available on [GitHub](https://github.com/ryumaru).

“Ryuzine” and the Ryuzine logos are trademarks of K.M. Hansen & Ryu Maru.  If you are distributing unaltered software, downloaded directly from Ryu Maru, to anyone in any way or for any purpose, no further permission is required.  Any other use of our trademarks requires prior authorization.