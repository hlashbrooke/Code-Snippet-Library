=== Code Snippet Library ===
Contributors: hlashbrooke
Donate link: http://www.hughlashbrooke.com/donate
Tags: code, snippet, library, syntax highlighter, ace, github, editor
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Store a library of reusable code snippets that you can add to any post. Supports 61 programming languages.

== Description ==

"Code Snippet Library" is designed for use on code tutorial sites - it gives you a way to store code snippets that you can use numerous times in any post. You simply create the snippet and place the relevant shortcode where you want it to appear. The snippets are managed using WordPress' taxonomy UI, so they are quick and easy to add/edit. This means that if you need edit a snippet that appears in multiple posts you only need to edit it in one place and the changes will take effect everywhere it is displayed.

The primary features of Code Snippet Library include: Support for 61 programming languages, 29 different code editor themes for the admin and front-end displays, easy copy and paste for users without pop-ups, graceful fallback when snippets are viewed in feed readers and full i18n support.

Code Snippet Library uses the Ace code editor to edit and display your snippets (http://ace.ajax.org/) - this is the same editor used by GitHub, Cloud9 and many other services.

You can see the front-end side of the plugin in action on my website. Example: http://www.hughlashbrooke.com/two-methods-for-vertical-centering-in-css/ (displayed using the Monokai theme).

The idea for this plugin is based on WordPress Code Snippet by Allan Collins (http://wordpress.org/extend/plugins/wordpress-code-snippet/).

CONTRIBUTE ON GITHUB: https://github.com/hlashbrooke/Code-Snippet-Library

== Usage ==

Simply add your snippet, select its programming lanugage and then paste the supplied shortcode into any post where you want it to appear. You can also insert your shortcodes using the toolbar button in the editor.

Go to Code Snippets > Settings to modify the themes that your snippets will use - you can select different themes for the admin and front-end displays.

If you would like to execute the code in the snippet on the page (only works for PHP, HTML, CSS & Javascript) simply add 'execute="yes"' as a parameter to the shortcode. Be VERY careful with this as executing PHP script this way is not recommended.

== Installation ==

Installing "Code Snippet Library" can be done either by searching for "Code Snippet Library" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
1. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Customise the plugin from its settings page

== Frequently Asked Questions ==

= How do I add snippets to my posts/pages? =
See the usage notes here: http://wordpress.org/extend/plugins/code-snippet-library/other_notes/.

= Can I customise my snippet display? =
The 29 themes that are packaged with the Ace editor should cover all your needs, but you can always use the CSS selectors for the code display to modify how it appears.

= How will my snippets appear in feeds? =
The snippets are displayed inside HTML 'pre' tags, so they will simply be displayed as monospaced text in feeds.

= How will my snippets appear for users who have Javascript disabled? =
Because the snippets are displayed inside HTML 'pre' tags, they will simply be displayed as monospaced text if Javascript is not enabled in the user's browser.

= Can I add multiple snippets to the same page? =
Yes - this is possible in v1.1+.

== Screenshots ==

1. Snippet management screen using the built-in taxonomy UI.
2. Snippet edit screen using the built-in taxonomy UI.
3. Settings screen for the plugin.
4. Example code display using the 'Chrome' theme (default).
5. Example code display using the 'Solarized Light' theme.
6. Example code display using the 'Solarized Dark' theme.
7. Example code display using the 'Monokai' theme.

== Changelog ==

= 1.1 =
* 2013-03-28
* [FEATURE] Allowing multiple snippets to be displayed on the same page
* [FEATURE] Added editor toolbar button for adding shortcodes to content directly

= 1.0 =
* 2013-03-09
* Initial release

== Upgrade Notice ==

= 1.1 =
* [FEATURE] Allowing multiple snippets to be displayed on the same page
* [FEATURE] Added editor toolbar button for adding shortcodes to content directly

= 1.0 =
* Initial release