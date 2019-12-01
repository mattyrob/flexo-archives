=== Flexo Archives ===
Contributors: mattyrob, heathharrelson
Tags: sidebar, archive, archives, collapsible archive, collapsible, collapse, widget
Requires at least: 4.9
Tested up to: 4.9
Stable tag: 3.0

Displays your archives as a compact list of years that expands when clicked.

== Description ==

This widget is designed to be a more compact alternative to the default archives widget supplied with WordPress. If you've been blogging regularly for several years, the archive list produced by the default widget grows to be quite long. If you use Flexo Archives instead, the list will be displayed as a much smaller list of years. When you click a year, it expands to show the months of that year when you posted. By default the expansion is animated.

A standalone version that simply prints the HTML for the archive lists and attaches the JavaScript to normal pages is now provided for users who cannot use the widget.

I am currently seeking translations of the plugin. If you would like to help by translating the plugin into your language, [please post to the support forum](http://wordpress.org/tags/flexo-archives-widget).

Thanks to Dylan van der Heij for providing a Dutch translation.

== Installation ==

Flexo Archives requires at least WordPress 2.7.  For ancient versions of WordPress (back to 2.2 and earlier), you should be using the 1.X version.

You install the Flexo Archives widget in two steps. First you install the widget's code into WordPress, and then you add the widget to one of your theme's widget areas.

You can install the widget's code automatically or manually. To install automatically from your blog's plugin administration panel:

1. Log into your blog and click the 'Plugins' item in the dashboard menu.
1. Click the 'Add New' button at the top of the page.
1. Search for the term 'Flexo'.
1. Click the 'Install' link.

If the automatic install fails for some reason, you can install the plugin manually :

1. Download the zip file (`flexo-archives-widget.VERSION.zip`) from the WordPress plugins site.
1. Expand `flexo-archives-widget.VERSION.zip`
1. Upload the whole `flexo-archives-widget` directory to the `/wp-content/plugins/` directory. After the upload, you should have a directory named `/wp-content/plugins/flexo-archives-widget`.
1. Activate the Flexo Archives plugin through the 'Plugins' menu in the WordPress admin interface.

To add the widget to one of your theme's widget areas, log into your blog and go to the 'Appearance' panel. Click the 'Widgets' link and drag the widget to one of the widget areas. Configure the widget as desired.

== Frequently Asked Questions ==

= Why do the widget's colors or bullet shapes look funny? =

This is something I hear a lot about in connection with the plugin, but it isn't the widget's fault. While the widget creates and hides the lists used, the colors and bullet shapes of the lists are set by your theme's stylesheet. Your theme probably doesn't have rules in its stylesheet to match the nested lists generated.

To test whether the problem is your theme, temporarily configure your blog to use another WordPress theme, such as the Twenty Ten or Twenty Eleven themes provided with WordPress. Expand and contract a few year links in the sidebar. If things don't look odd, the problem is probably with your theme.

= Why would I use the standalone Flexo Archives function? =

If your WordPress theme supports widgets, you don't need to worry about the standalone function. You can stop reading now. Congratulations!

Unfortunately, some WordPress themes don't support widgets. If you are familiar with HTML though, the standalone Flexo Archives function exists to allow you to easily modify your theme to get the expanding archives list provided by the widget.

= How do I use the standalone Flexo Archives function? =

To use the standalone Flexo Archives function, install the plugin code as described in the first step of the installation instructions.

Next, enable the standalone function:

1. Go to the WordPress dashboard and click the 'Settings' menu. In recent versions of WordPress, this menu is near the bottom of the left column.
1. Click the 'Flexo Archives' option in the expanded menu.
1. Enable the standalone function using the checkbox and submit the form.

Finally, modify your theme to use the standalone function. Edit the PHP of your theme to add the following code where you want the archives list to appear:

`<?php if (function_exists('flexo_standalone_archives')){flexo_standalone_archives();} ?>`

The code will output the nested archive lists into the HTML at that point in the theme and automatically attach JavaScript to make the lists expand and contract.
