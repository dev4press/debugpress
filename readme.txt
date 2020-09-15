=== DebugPress ===
Contributors: GDragoN
Donate link: https://debug.press/
Tags: dev4press, debugger, debug, debugging, development, profiler, queries, query monitor, ajax monitor
Stable tag: 1.0
Requires at least: 4.9
Tested up to: 5.5
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

DebugPress is an easy to use plugin implementing popup for debugging and profiling currently loaded WordPress powered website page with support for intercepting AJAX requests.

== Description ==

DebugPress is an easy to use plugin implementing popup for debugging and profiling currently loaded WordPress powered website page with support for intercepting AJAX requests. The main debugger window is displayed as a popup, activated through the button with the Bug integrated into WordPress Toolbar, or floating on the page.

The plugin currently has 18 tabs in the popup debugger window, showing all kinds of information relevant to current page, WordPress setup, background AJAX calls and much more.

The plugin doesn't modify or replaces any WordPress files or functions.

= Debugger Panels =
Currently, the plugin has following panels:

* Basic
* Request (optional)
* Admin (for admin side only)
* Query (for frontend only)
* Content (optional)
* Constants (optional)
* SQL Queries (if SQL queries logging is enabled)
* User (optional, if user is logged in only)
* PHP (optional)
* System (optional)
* HTTP (optional, if HTTP API calls are captured)
* bbPress (optional, on bbPress forum pages only)
* Enqueue (optional)
* Errors (if PHP errors are captured)
* Deprecated (if PHP deprecated warnings are captured)
* Doing It Wrong (if WordPress Doing It Wrong warnings are captured)
* AJAX (if AJAX calls are captured while page is active)
* Log (if there are user stored objects to show)

= Documentation and Support =
To get help with the plugin, you can use WordPress.org support forums, or you can use Dev4Press.com support forums.

* Plugin Documentation: [DebugPress Website](https://debug.press/documentation/)
* Support Forum: [Dev4Press Support](https://support.dev4press.com/forums/forum/plugins-free/debugpress/)

= SQL Queries =
This panel lists all the queries WordPress has run, and it allows you to order the queries by execution order or length of execution, and all queries can be filtered by the query type, database table it targets or the WordPress function that called it. Every query displays the the executio time, order, caller functions stack and fully formatted SQL query that is easy to read.

= PHP and WordPress Errors =
Plugin has 3 panels dedicated to showing PHP and WordPress errors and warnings. Plugin captures this information during the page load, and it shows full debug trace as returned by the PHP debug tracing function.

= AJAX =
The plugin tracks every AJAX call coming through WordPress `admin-ajax.php` handler, and with every response, it returns HTTP headers with AJAX request basic execution information. Right now, plugin is not returning list of logged errors or SQL queries, because both can produce huge ouptut that goes over the HTTP header limits. Plan is to introduce these in the future plugin versions.

= Plugin Settings =
The plugin has various options controlling the plugin activation, button integration position, user roles that can see the debugger window, options to attempt overriding WordPress debug flags and options controlling the visibility of optional debugger panels.

== Installation ==
= General Requirements =
* PHP: 5.6 or newer

= WordPress Requirements =
* WordPress: 4.9 or newer

= Basic Installation =
* Upload folder `debugpress` to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress
* Plugin settings are available under WordPress 'Settings' panel

== Frequently Asked Questions ==
= Where can I configure the plugin? =
Open the WordPress 'Settings' menu, there you will find 'DebugPress' panel.

= How can I open Debugger popup? =
If you have enabled debugger (for admin side and/or frontend), Debugger is activate via Bug button placed in the WordPress Toolbar or as a float button (depending on the settings).

== Changelog ==
= 1.0 (2020.09.15) =
* First official release

== Upgrade Notice ==
= 1.0 =
First official release.

== Screenshots ==
1. Debugger popup: basic debugger panel
2. Debugger popup: current page Query
3. Debugger popup: captured AJAX calls
4. Debugger popup: SQL Queries
5. Debugger popup: responsive layout
6. Settings: main plugin controls
7. Settings: additional controls and optional panels
