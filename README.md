# Ryuzine Press Plugin

Ryuzine Press is a plugin for WordPress that “bridges” curated content from your blog into the Ryuzine webapp.  It also allows you to replace the *ryuzine* custom post-type archive with the Ryuzine Rack newsstand.

## Installation

1. Download the Ryuzine Press plugin from the [GitHub Repository](https://github.com/ryumaru/ryuzine-press).

### Using Admin Upload

2. Go to your *Dashboard > Plugins > Add New* and press the "Upload Plugin" button at the top of the page.
3. Browse to the ZIP file you downloaded from GitHub and select it and press the "Install Now" button.

### Using FTP

2. Unzip the file you downloaded into a folder.
3. Using your favorite FTP software upload that folder to your *~/wp-content/plugins/* folder.

4. Go to your *Dashboard > Plugins > Installed Plugins* and activate the "Ryuzine Press" plugin.
5. You will also need to install the Ryuzine Webapp as well.  Go to *Dashboard > Ryuzine Press > Tools > Update Ryuzine* to install it (it can't be bundled with Ryuzine Press for licensing reasons).
6. Go to your *Dashboard > Ryuzine Press > Options* to set any default configuration for your Ryuzine publications.

### Installation Issues

* If Wordpress cannot download the Ryuzine Webapp you will need to go get it [directly](https://github.com/ryumaru/ryuzine), unzip it, make sure the folder is named "ryuzine" and FTP upload it to your site by placing it INSIDE of the Ryuzine Press Plugin folder.  If you cannot FTP to your site and it won't download you won't be able to use Ryuzine Press.


## License

Ryuzine Press is released under the GNU General Public License version 3 (GPLv3). The Ryuzine webapps are released under the Mozilla Public License (MPL) 2.0.  The Ryuzine webapps only supply some scripts, images, and stylesheets to the Ryuzine Press plugin template.  You may have received the Ryuzine webapps bundled with the plugin (allowable under MPLv2 Section 3.3) and the combined "Larger Work" is released under a GPL/MPL dual-license.  

Distribution of publications in Ryuzine format does not require you to also provide source code if the webapp or plugin code has not been modified. Source code for both the Ryuzine Press plugin and Ryuzine webapps is available on [GitHub](https://github.com/ryumaru).

“Ryuzine” and the Ryuzine logos are trademarks of K.M. Hansen & Ryu Maru.  If you are distributing unaltered software, downloaded directly from Ryu Maru, to anyone in any way or for any purpose, no further permission is required.  Any other use of our trademarks requires prior authorization.