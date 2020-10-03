=== Flexo Archives ===
Contributors: mattyrob, heathharrelson
Tags: sidebar, archive, archives, collapsible archive, collapsible, collapse, widget
Requires at least: 4.9
Tested up to: 4.9
Stable tag: 3.0

Displays your archives as a compact list of years that expands when clicked.

== Description ==

This widget is designed to be a more compact alternative to the default archives widget supplied with WordPress. If you've been blogging regularly for several years, the archive list produced by the default widget grows to be quite long. If you use Flexo Archives instead, the list will be displayed as a much smaller list of years. When you click a year, it expands to show the months of that year when you posted. By default the expansion is animated.

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
