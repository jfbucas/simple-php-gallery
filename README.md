Simple PHP Gallery
==================

Simple PHP Gallery by Paul Griffin.
http://relativelyabsolute.com/

Version: 1.1

Licensing Info
--------------

This work is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike License.
To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/1.0/
or send a letter to Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.

What this means:

You are free:

* to copy, distribute, display, and perform the work
* to make derivative works

Under the following conditions:

* Attribution. You must give the original author (Paul Griffin) credit.
* Noncommercial. You may not use this work for commercial purposes.
* Share Alike. If you alter, transform, or build upon this work, you may distribute the
resulting work only under a license identical to this one.
* For any reuse or distribution, you must make clear to others the license terms of this work.
* Any of these conditions can be waived if you get permission from the author.

Installation
------------

### Requirements

#### Minimum requirements

- PHP 4.0 or better
- GD Library, 1.0 or better

#### Recommended requirements

- PHP 4.0 or better
- GD Library, 2.0 or better
- File write permission for PHP
- mod_rewrite

The GD Library is strongly recommended. Simple PHP Gallery will automatically detect (and attempt to
append onto `sp_config.php`) what version of GD is installed on your server. If you would like to see what
version of GD you have, upload the included `gd_detect.php` file to your server and load it in
your browser. This script will display your version of GD and output code to be added to `sp_config.php`,
should you want to add this information manually.

Note: The resizing feature will be disabled if your version of GD is older than 2.0. This is due to the
poor quality of images produced by GD 1.x. Quality is generally passable for thumbnails, but decidedly
not for full-size images.

In order to support the `mod_rewrite` links, your site must run on an Apache web server installation with
`mod_rewrite` enabled. Contact your hosting provider if you are unsure of your server's configuration.

Configuration
-------------

### Upload & Go

Note that Simple PHP Gallery is designed to be as easy to use and hassle-free as possible. With this in
mind, default settings that should have the best compatibility have already been set in the included
`sp_config.php` file. If you are so inclined, you should be able to just upload files without having to
manually configure anything.

### Importing Settings from Version 1.0

If you are upgrading from version 1.0, you can import your settings and descriptions from your previous
installation. Upload the included file named `sp_config_parse.php` into the same directory as `sp_config.dsc`.
Load `sp_config_parse.php` in your browser.

This script will attempt to read your current `sp_config.dsc` file, parse out your options and descriptions,
then write two files, `sp_config.php` & `sp_descriptions.ini`.

You will be notified of the status of your import attempt. If your import fails, the most probable cause
is inability to write new files due to PHP's security restrictions. These are usually controlled by your
hosting provider. In this case, you will probably have to manually configure Simple PHP Gallery.

### Gallery Settings

First, open the file named `sp_config.php` This file controls all of the configuration options for Simple
PHP Gallery Here is what the various options do:

#### Parameter `$title`

The title of your gallery. This will be on every page of the gallery, both in the `<title>` tag and the
`<h1>` tag.

Default Value:

    $title = 'My Gallery';

#### Parameter `$maxthumbwidth`

If the GD extension is installed, this variable controls the maximum width of generated thumbnails, in
pixels.

Default Value:

    $maxthumbwidth = 120;

#### Parameter `$maxthumbheight`

If the GD extension is installed, this variable controls the maximum height of generated thumbnails, in
pixels.

Default Value:

    $maxthumbheight = 120;

#### Parameter `$resize`

Why bother resizing all of your pictures by hand, when you can let Simple PHP Gallery do it for you
automatically ? Setting this option to true will enable image resizing.

Note that this option will not work if either `$maxwidth` or `$maxheight` is less than or equal to 0, or if
your version of GD is older than 2.0.

Default Value:

    $resize = false;

#### Parameter `$maxwidth`

If `$resize` is true, this variable controls the maximum width of resized images, in pixels.

Default Value:

    $maxwidth = 640;

#### Parameter `$maxheight`

If `$resize` is true, this variable controls the maximum height of resized images, in pixels.

Default Value:

    $maxheight = 640;

#### Parameter `$modrewrite`

This variable controls whether the script outputs query string links or `mod_rewrite` links. If your server
supports `mod_rewrite`, and you have uploaded or appended the included `.htaccess file`, this should be set
to `true`. If your server does not support `mod_rewrite`, if you don't want to use `mod_rewrite`, or if you
just have no idea what this is about and would prefer not to fool with it, set it to `false`.

The difference in this script output looks like this:

When `$modrewrite = true;` links look like this:

    <a href="galleryroot/folder/dir/subdir/subsubdir/">

When `$modrewrite = false;` links look like this:

    <a href="galleryroot/sp_index.php?dir=./dir/subdir/subsubdir/">

Using `mod_rewrite` links is nice because it hides the technology and overlays a virtual directory structure
over your physical directories. The user never leaves the index file, but it looks to them as if they
were navigating a directory tree. `mod_rewrite` links are also nice because they are easier to remember and
link directly.

Default Value:

    $modrewrite = false;

#### Parameter `$cachethumbs`

This variable controls whether the Simple PHP Gallery should cache the thumbnails it generates. If
caching is on, each thumbnail will only be generated once, then saved to the server. When a thumnail is
requested, the script will automatically detect whether it has previously generated the thumbnail. If it
has, it will serve the file on disk, rather than generate a new thumbnail. Setting this to `true` takes a
large burden off of the server, so you probably want to leave it set to the default. If caching fails
for any reason (usually inability to read/write the cache folder), caching will be disabled, regardless
of this setting. If you change this variable to `false` after thumbnails have already been cached, the
cache folder (specified below) and all contents will be deleted automatically.

Also note that enabling thumbnail caching will also cause Simple PHP Gallery to generate a file in the
cache folder named `cache.ini`. This file is meant to be used in conjunction with Dan Benjamin's "Better
Image Rotator" (http://alistapart.com/articles/betterrotator/).

Default Value:

    $cachethumbs = true;

#### Parameter `$cachefolder`

If `$cachethumbs` is `true`, this variable sets the folder that the thumbnails should be saved into. This
folder is created in the same directory as `sp_index.php` and will not show up as a folder in your gallery.
Note that if caching is enabled, but the cache folder does not exist, the script will attempt to create
it and `chmod` it to 755.

Default Value:

    $cachefolder = 'cache';

#### Parameter `$cacheresized`

If `$resize` is true, this variable controls whether Simple PHP Gallery should cache the resized images it
generates. If caching is on, each image will only be resized once, then saved to the server. When an
image is requested, the script will automatically detect whether it has previously resized the image. If
it has, it will serve the file on disk, rather than generate a new resized image. Setting this to `true`
takes a large burden off of the server, so you probably want to leave it set to the default. If caching
fails for any reason (usually inability to read/write the cache folder), caching will be disabled,
regardless of this setting. If you change this variable to `false` after thumbnails have already been
cached, the cache folder (specified below) and all contents will be deleted automatically.

Default Value:

    $cacheresized = true;

#### Parameter `$cacheresizedfolder`

If `$cacheresized` is true, this variable sets the folder that the resized images should be saved into.
This folder is created in the same directory as `sp_index.php` and will not show up as a folder in your
gallery. Note that if caching is enabled, but the cache folder does not exist, the script will attempt
to create it and `chmod` it to 755.

Default Value:

    $cacheresizedfolder = 'rcache';

#### Parameter `$precache`

When viewing single images, enabling this option will cause the previous and next images to load into the
background, so that they will already be in the browser's cache when visitors click the "Previous" or
"Next" buttons. This gives the appearance of near-instantaneous image loading for your visitors. Note
that this feature will not work if `$resize` is `true` and `$cacheresized` is `false`.

Default Value:

    $precache = true;

#### Parameter `$hidefolders[]`

This is a special setting, an array of folder names that should not show up in your gallery. Just add a
line for each folder you wish to hide. Follow this format:

    $hidefolders[] = 'hiddenfolder1';
    $hidefolders[] = 'hiddenfolder2';

Folders named `cgi-bin` and `.git` are hidden by default.

Default Value:

    $hidefolders[] = 'cgi-bin';
    $hidefolders[] = '.git';

#### Parameter `$showfolderdetails`

This variable controls whether the number of images and folders appears in parentheses next to links to
subfolders.

Default Value:

    $showfolderdetails = true;

#### Parameter `$showimgtitles`

This variable controls whether image titles appear below thumbnails. If an image title for a particular
thumbnail cannot be found in `sp_descriptions.ini`, the filename will be used instead.

Default Value:

    $showimgtitles = true;

#### Parameter `$alignimages`

This variable controls whether thumbnails should be aligned into columns, regardless of their width.
When set to `false`, thumbnails will float left and bunch up accordingly.

Default Value:

    $alignimages = true;

#### Parameter `$gd_version`

You will notice that this option is not initially found in `sp_config.php`. Simple PHP Gallery will detect
this setting automatically and attempt to append a line similar to the following to `sp_config.php`:

    $gd_version = '//your GD version here';

If Simple PHP Gallery is unable to write to `sp_config.php`, it will simply re-detect the version every
time the script is loaded. This is a minor additional burden on your server. If `sp_config.php` is not
writeable on your server, you may wish to add this setting manually.

To manually set `$gd_version`, upload the file named `gd_detect.php` to your server and load it into your
browser. The proper line of code to add to `sp_config.php` will be automatically generated for you.

Adding File & Folder Titles & Descriptions
------------------------------------------

Unlike Simple PHP Gallery 1.0, version 1.1 now uses an ini file to set file and folder titles and
descriptions. Also, unlike the previous version, 1.1 allows you to set titles and descriptions for both
individual images and folders.

Descriptions are now specified in the file named `sp_descriptions.ini`. This file will open in any text
editor. The format for an entry is very simple:

    [filename.jpg]
    title = "File Name"
    desc = "This is filename.jpg's description."


Folders work exactly the same:

    [foldername]
    title = "Folder Name"
    desc = "This is foldername's description."

Simply add as many entries as you wish. Both title and desc are optional, so you can specify none, one,
or both.

Special Note: To specify a description for the root folder of your gallery, use the following:

    [.]
    desc = "This is the root folder's description."

You can make changes to `sp_descriptions.ini` at any time, they will be applied immediately.

Copying files
-------------

Once you have finished configuring the script, copy the following files to the folder that you want to be
the root folder of your gallery:

- `sp_config.php`
- `sp_def_vars.php`
- `sp_descriptions.ini`
- `sp_getthumb.php`
- `sp_helper_functions.php`
- `sp_index.php`
- `sp_resize.php`
- `sp_styles.css`

If you have set `$modrewrite = true` in `sp_config.php`, you will also need to copy or append the contents of
the following file into a new or existing file called `.htaccess` in the root folder of your gallery:

- `htaccess.txt`

If you do not append the contents of the included `.htaccess` file, you should at least add a directive to
make `sp_index.php` the default document for the folder:

    DirectoryIndex sp_index.php

You can also just upload the `.htaccess` file included in the distribution archive (if your OS allows you to
see `.htaccess` files)

Adding new images and folders
-----------------------------

To add a new folder or image to your gallery, just upload it to the appropriate folder under the root
folder of your gallery. New folders and images will automatically added to the gallery. If caching is
enabled, new thumbnails will be generated and cached for the new images upon the first viewing of the
page.

Simple PHP Gallery currently supports JPEG (.jpg, .jpeg), GIF (.gif), and PNG (.png) image formats.

You can add additional folder aliases and image descriptions into your sp_descriptions.ini file and
re-upload it at any time. Changes will be immediate.

Notes
-----

### Special thanks

Erik Sagen (http://www.kartooner.com/)
Ryan Parman (http://www.skyzyx.com/)
Matt Burris (http://www.goodblimey.com/)

Erik, Ryan, and Matt took time out of their hectic schedules to help me test and recommend features for Simple PHP
Gallery. Their assistance was invaluable in getting this project released. Make sure you stop by their
sites and say hello!

Thanks for all the help, guys!
