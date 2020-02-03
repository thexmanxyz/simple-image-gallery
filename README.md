# Simple Image Gallery Fork
Simple Image Gallery Fork is an extended Version of the [Simple Image Gallery](https://github.com/joomlaworks/simple-image-gallery) plugin provided by [JoomlaWorks](https://www.joomlaworks.net). It contains additional features and advanced configuration options which are beyond the scope of the original project.

Hence this fork was created to cover additional user requirements by maintaining the original functionality of the base project. Out of the box the configuration to add image galleries to your Joomla content is still simple and effective but it gives users more flexibility to customize the plugin. 

## How this plugin works
Adding image galleries inside your Joomla articles, K2 items, Virtuemart/HikaShop products or any other component that supports "content" plugins is dead-simple. The plugin can turn any folder of images located within your Joomla website installation into a grid-style image gallery with lightbox/modal previews. The plugin can be integrated by adding a simple plugin tag like `{gallery}myphotos{/gallery}` or `{gallery}some/folder/myphotos{/gallery}`.

So for example, if we have a folder of images called "my_trip_to_Paris" and located in images/my_trip_to_Paris, then we can create our gallery by simply entering the tag `{gallery}my_trip_to_Paris{/gallery}` into some Joomla article.

The galleries created are presented in a grid of thumbnails. When your site visitors click on a thumbnail, they see the original (source) image in a lightbox/modal popup. The thumbnails are generated and cached using PHP for better results.

The plugin is ideal for any type of website: from personal ones (where you'd post a photo gallery from your last summer vacation), to e-shops (for product presentation) to large news portals. With Simple Image Gallery Fork, you can have as many galleries as you want inside your content.

## Features
- You can place one or more image galleries anywhere within your content this gives you total layout freedom.
- The gallery layout is fluid by default which means it'll fit both responsive and adaptive website layouts.
- You can set a [root folder](https://github.com/thexmanxyz/simple-image-gallery-fork#root-folder) in the plugin backend configuration if you all your gallery folders are located under a single common path.
- You can use [MVC overrides](https://github.com/thexmanxyz/simple-image-gallery-fork#frontend-styling) to change the appearance of the thumbnail grid on your site.
- Uses the core Joomla updater.
- Supports JPEG, PNG, GIF and WEBP as source images.
- Allows printing the image gallery grid when using the print preview feature available in most Joomla components (including the default article system and K2).
- German and English translation supported out of the box.
- Disabling of MooTools supported.
- Uses fancyBox 3 for the lightbox/modal previews.
- Option to load fancyBox locally or via CDN.
- Configurable fancyBox version.
- Customizable labeling of fancyBox GUI elements and caption.
- Options to disable fancyBox GUI elements.
- Support for different animation and transition effects.
- Option for animation and transition duration.
- Simple image protection.
- Loop navigation supported.
- Keyboard control setting.
- Auto hide idle timer.
- Auto fullscreen mode.
- Auto slideshow and transition speed.
- Auto thumbnail grid and behavior settings.
- Touch settings (enable/disable, momentum, vertical touch)
- Styling with CSS classes.

## Migration
Migrating from [Simple Image Gallery](https://github.com/joomlaworks/simple-image-gallery) is very simple. Either uninstall the plugin or just disable it. No necessity to change existing `{gallery}{/gallery}` tags. The existing configuration of the original plugin will not be migrated and the thumbnail cache requires to be rebuild.

## Customization and Configuration
In the following paragraphs we will discuss important configuration options of the plugin and how the frontend appearance of galleries can be customized.

### Root Folder
When you maintain multiple image galleries across your site it makes sense to define a root folder within the backend configuration of the plugin. This way you won't have to declare the full path to each gallery folder inside the `{gallery}...{/gallery}` plugin tags but rather only the gallery sub folder name or path (e.g. `{gallery}folder{/gallery}`). By default the "root folder" points to the `images` folder because that's the default folder for uploading media files in Joomla as well. 

To get a feeling on how this works let's take a look on an example. If your "root folder" is located under `images/galleries` and you want to maintain three galleries, just create for each one a sub folder (e.g. `images/galleries/gallery1`, `images/galleries/gallery2` and `images/galleries/gallery3`). When you want to embed them within your content just use the following gallery tags `{gallery}gallery1{/gallery}`, `{gallery}gallery2{/gallery}` and `{gallery}gallery3{/gallery}`.

### Frontend Styling
1. Copy the folder content of `/plugin/sigf/tmpl/Classic` to `/templates/TEMPLATE/html/sigf/Classic`. If the folders `sigf` and `Classic` do not exist under `/templates/TEMPLATE/html`, create them.
2. Modify the HTML and CSS files in the `/templates/TEMPLATE/html/sigf/Classic` folder until the appearance matches your needs.

**Info:** The name of the actual `TEMPLATE` folder depends on the actual template you want the styling to apply to.

## Demo
You can see a demo of the plugin here: *coming soon*

## Learn More
Visit the Simple Image Gallery Fork product page at: *coming soon*

## Compatibility & License
Simple Image Gallery Fork is PHP5 and PHP7 compatible and fully compatible with Joomla versions 1.5, 2.5, 3.x and the upcoming 4.x.

Joomla 1.5 must have the "MooTools Upgrade" system plugin enabled to avoid JavaScript conflicts between MooTools and newer jQuery releases used by the plugin.

Simple Image Gallery Fork is a Joomla plugin released under the GNU General Public License.

## Credits
Thanks to [JoomlaWorks](https://www.joomlaworks.net) for creating the original [Simple Image Gallery](https://github.com/joomlaworks/simple-image-gallery) plugin this fork is based on. 