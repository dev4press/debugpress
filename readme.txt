=== DebugPress: Debugger in a Popup ===
Contributors: GDragoN
Donate link: https://buymeacoffee.com/millan
Tags: dev4press, query monitor, debugging, development, ajax monitor
Stable tag: 3.9.2
Requires at least: 5.5
Tested up to: 6.6
Requires PHP: 7.3
Requires CP: 2.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

DebugPress is an easy-to-use plugin that implements popups for debugging and profiling website pages with support for intercepting AJAX requests.

== Description ==

DebugPress is an easy-to-use plugin implementing popup for debugging and profiling currently loaded WordPress powered website page with support for intercepting AJAX requests. The main debugger window is displayed as a popup, activated through the button with the Bug integrated into WordPress Toolbar, or floating on the page.

The plugin currently has a total of 22 tabs in the popup debugger window, showing all kinds of information relevant to the current page, WordPress setup, background AJAX calls, and much more.

The plugin doesn't modify or replace any WordPress files or functions.

= Home and GitHub =
* Learn more about the plugin: [DebugPress Website](https://debug.press/)
* Contribute to plugin development: [DebugPress on GitHub](https://github.com/dev4press/debugpress)

= Quick Overview Video =
https://www.youtube.com/watch?v=-eFnBRLhy-s

= Pretty Print for objects =
To display the content of objects or arrays, plugin has pretty print functionality through use of simpler PrettyPrint library, or more robust third-party library Kint. You can choose one or the other from the plugin Settings, Advanced tab.

= Debugger Panels =
Currently, the plugin has the following panels:

* Basic
* Request (optional)
* Query (for frontend only)
* Content (optional)
* Roles (optional)
* Constants (optional)
* Hooks (optional)
* PHP (optional)
* Server (optional)
* Enqueue (optional)
* SQL Queries (if SQL queries logging is enabled)
* User (optional, if user is logged in only)
* HTTP (optional, if HTTP API calls are captured)
* bbPress (optional, on bbPress forum pages only)
* Plugins (optional, if plugins store data)
* Errors (for all captured PHP errors)
* Doing It Wrong (for captured WordPress Doing It Wrong warnings)
* Deprecated (for captured PHP deprecated warnings)
* AJAX (for captured AJAX calls while page is active)
* Store (for any user stored objects during the page loading)
* Tools (internal and external tools links)
* Debug Log (load content on demand from WordPress 'debug.log')
* Layout (control the size, position and activation of the popup)

= SQL Queries =
This panel lists all the queries WordPress has run. It allows you to order the queries by execution order or length of execution, and all queries can be filtered by the query type, database table it targets or the WordPress function that called it. Every query displays the execution time, order, caller functions stack and fully formatted SQL query that is easy to read. For some Dev4Press created plugins (and that list will grow), DebugPress can detect the source of the query and allow you to filter by the plugin calling the query.

= PHP and WordPress Errors =
Plugin has 3 panels dedicated to showing PHP and WordPress errors and warnings. The Plugin captures this information during the page load, and it shows full debug trace as returned by the PHP debug tracing function.

= AJAX =
The plugin tracks every AJAX call coming through WordPress `admin-ajax.php` handler, and with every response, it returns HTTP headers with AJAX request basic execution information. Right now, plugin is not returning a list of logged errors or SQL queries, because both can produce huge output that goes over the HTTP header limits. Plan is to introduce these in the future plugin versions.

= Info Panels =
In the administration Tools menu, plugin adds DebugPress Info page showing several panels

* PHP Info: full formatted results from `phpinfo()` function
* OPCache Info: full settings and statistics for the OPCache PHP extension
* MySQL Variables: full MySQL settings retrieved from the database

= Plugin Settings =
The plugin has various options controlling the plugin activation, button integration position, user roles that can see the debugger window, options to attempt overriding WordPress debug flags and options controlling the visibility of optional debugger panels.

= Log into Database =
Debugger popup is visible for the request running in the browser. But there are many requests that are happening in the background (AJAX calls, REST API calls...), and for them, you can't see errors, call information and other stuff in the debugger. Because of that, DebugPress supports logging of various events into database with the use of 'coreActivity' plugin, and it is highly recommended to install and use coreActivity.

Log various debug events into a database with the free plugin: [coreActivity Plugin](https://wordpress.org/plugins/coreactivity/), supporting over 120 events and 10 popular WordPress plugins. DebugPress related events will be logged and available for later analysis, and this includes errors, AJAX calls, and HTTP API calls.

= Documentation and Support =
To get help with the plugin, you can use WordPress.org support forums, or you can use Dev4Press.com support forums.

* Plugin Documentation: [DebugPress Website](https://debug.press/documentation/)
* Support Forum: [Dev4Press Support](https://support.dev4press.com/forums/forum/plugins-free/debugpress/)

== Installation ==
= General Requirements =
* PHP: 7.3 or newer
* Tested with the latest PHP, version 8.3

= WordPress Requirements =
* WordPress: 5.5 or newer
* Tested with the latest WordPress, version 6.5

= Basic Installation =
* Upload folder `debugpress` to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress
* Plugin settings are available under WordPress 'Settings' panel

== Frequently Asked Questions ==
= How I can install this plugin? =
If you are not sure about WordPress plugins installation, here is the quick information: [Plugin Installation](https://debug.press/documentation/plugin-installation/).

= How can I enable WordPress Debug Mode? =
To enable WordPress debug mode via `wp-config.php`, check out the article here: [WordPress Setup](https://debug.press/documentation/wordpress-setup/).

= How can I open Debugger popup? =
If you have enabled debugger (for admin side and/or frontend), Debugger is activated via Bug button placed in the WordPress Toolbar or as a float button (depending on the settings). Since version 3.0, you can activate the button via keyboard shortcut, default combination is `ctrl+shift+u`, but it can be changed in the plugin settings.

= Can I change the size and position of the popup? =
Yes. Once the popup is open, you will find the Layout icon next to the button to close the popup. On the layout panel, you can change the location for the popup, size, modal status and auto activation on page load.

= Where can I configure the plugin? =
Open the WordPress 'Settings' menu, there you will find 'DebugPress' panel.

== Changelog ==
= 3.9.2 (2024.08.23) =
* Fix: one more issue with the access to OPCache status information

= 3.9.1 (2024.08.19) =
* Fix: problem with the Tracker when closure makes a call [#08](https://github.com/dev4press/debugpress/issues/8)

= 3.9 (2024.05.14) =
* Edit: few more updates and improvements
* Edit: replacement of some native with WordPress functions
* Edit: various small updates and tweaks
* Edit: Kint Pretty Print Library 5.1.1
* Fix: various PHP notices related to PHP 8.1 and newer

= 3.8 (2024.04.24) =
* Edit: few minor tweaks and changes
* Edit: updates to the plugin readme file
* Edit: small changes related to the PHP 8.3 compatibility
* Fix: various PHP notices related to PHP 8.1 and newer

= 3.7 (2024.01.23) =
* Edit: changes related to WordPress and PHP code standards
* Edit: updated Query object for page query conditionals
* Edit: updated Dev4Press Library Query detection versions
* Fix: few compare statements written as assignment

= 3.6 (2023.12.15) =
* Edit: Increase Kint Library depth levels to 12
* Edit: Kint Pretty Print Library 5.1.0
* Fix: Small issue with getting the OPCache version when not allowed by the server

= 3.5.1 (2023.11.18) =
* Fix: Internal debug to error log remain in the source code

= 3.5 (2023.11.06) =
* New: Tracker logs the trace for each HTTP API request made
* New: Tracker executes action for every completed HTTP API request
* New: AJAX Tracker executes action for every completed Admin AJAX request
* New: HTTP API log shows the trace and timestamps for each request
* New: Popup tools links to the coreActivity events and logs
* New: Popup header shows icons for tabs with labels with improved sizing
* New: Popup tabs show label or icon only, depending on the screen size
* Fix: Trace information for tracker HTTP API request was lost

= 3.4.1 (2023.10.15) =
* Fix: Function `apache_get_version` not working on every server

= 3.4 (2023.10.06) =
* New: Updated some plugin system requirements
* Edit: KINT now loads own helper d() and s() functions
* Edit: Various styling improvements and tweaks
* Edit: Improved organization of the print libraries now moved to vendor directory
* Fix: MySQL tools panel showing error if the server information can't be retrieved
* Fix: Problem with the Info method for getting server IP in some cases
* Fix: Few more issues with Info method for getting database information

= 3.3 (2023.07.03) =
* New: Support for more versions of the Dev4Press Library
* New: System tab shows additional information about Apache
* New: System tab shows WordPress database overview information
* New: Remember open/close sections on some panels
* Edit: Some changes to the displayed System and Basic tabs
* Edit: Various small tweaks to the plugin PHP code
* Edit: Kint Pretty Print Library 5.0.7
* Fix: Few issues with Info method for getting database information

= 3.2 (2023.06.22) =
* New: Support for the deprecated hook run handling
* New: Execute actions when each error has been logged
* New: Deprecated tracking now logs the caller information
* Edit: Improved caller trace cleanup for error calls
* Edit: Display more relevant error source file and log for errors
* Edit: Errors tab moved to the end of the display order
* Edit: Changed tab order for several other debugger tabs
* Edit: Various small tweaks to the plugin PHP code
* Fix: Fatal error tracking calling default handler directly
* Fix: Sometimes errors not getting displayed in the Error tab

= 3.1 (2023.06.14) =
* New: Identify SQL queries sources for Dev4Press plugins
* New: Hooks panel can filter by the WordPress Admin callbacks
* Edit: Improved method for displaying button activation flags
* Edit: Many improvements to escaping variables for display
* Edit: Better optimized included images for logos
* Edit: Various small tweaks to the plugin PHP code
* Edit: Various small tweaks to the main JavaScript file
* Fix: Hooks panel not filtering MU Plugins

= 3.0.1 (2023.05.05) =
* Edit: Minor updates to the plugin readme file
* Edit: Various improvements to the PHP core code
* Fix: Warnings related to OPCache for some PHP configurations

= 3.0 (2023.04.03) =
* New: Modify debugger popup layout and size
* New: Modify debugger popup modal state
* New: Modify debugger popup opening state (auto, manual, remember state)
* New: Save active tab and show it first on next page load
* New: Trigger debugger popup display via keyboard shortcut
* New: Access Key option to enable loading on demand via URL
* New: Settings block and information for the On Demand access
* New: Settings block and information for shortcut key activation
* New: Admin bar button has basic stats dropdown menu
* New: Plugin settings Help tab with On Demand information
* New: Content tab split into Content and Rewrite tabs
* New: Basic tab shows currently active theme information
* New: Admin tab content moved to the Request tab
* New: Refreshed the debugger look with new icons
* New: Function to write log entry into custom info/log file
* New: `IP` class mostly rewritten and expanded
* New: Mousetrap Javascript v1.6.5 library
* Edit: Few improvements to the plugin init and load process
* Edit: Various improvements to the PHP core code
* Edit: Changes to some plugin default settings
* Edit: `IP` class expanded Cloudflare IP range
* Edit: Smart Animated Popup v2.0 library
* Del: Removed the dedicated Admin tab
* Fix: Few issues with the `IP` class range methods

= 2.2 (2023.02.03) =
* New: Updated some plugin system requirements
* New: Server panel shows PHP Include Path value
* New: Server panel shows Memcache/Memcached status
* New: Server panel shows all loaded extensions
* New: Server panel shows expanded MySQL information
* Edit: Various improvements to some panels display conditions
* Fix: Several more issues related to changes in PHP 8.1 and 8.2
* Fix: Issue with detection of the PEAR System.php file

= 2.1 (2022.12.30) =
* New: Tested with PHP 8.2 and WordPress 6.1
* New: Query panel shows current post and metadata for that post
* Edit: Improvements to the Query panel and organization of displayed data
* Edit: Various language syntax improvements and changes
* Edit: Kint Pretty Print Library 5.0.1
* Fix: Several issues related to changes in PHP 8.2
* Fix: Few minor issues with the layouts in the debugger panels
* Fix: Missing semicolon in one instance in JavaScript

= 2.0 (2022.08.31) =
* New: Prevent loading of the debugger panel for REST request
* New: Default PrettyPrint library has own CSS file
* New: Debugger now loads on the WordPress login pages
* New: Kint set as default pretty print library for new installations
* Edit: Improvements to the plugin loading process
* Edit: Various minor updates and tweaks
* Edit: Kint Pretty Print Library 4.2
* Fix: Plugin can break some REST request responses

= 1.9 (2022.05.15) =
* New: Tested with WordPress 6.0
* Edit: Several minor updates and improvements
* Edit: Kint Pretty Print Library 4.1.1
* Fix: Some layout issues with the tables

= 1.8 (2021.09.30) =
* New: requires WordPress 5.1 or newer
* New: activation button indicator for number of stored object
* Edit: few more compatibility changes to the JavaScript code
* Edit: OPCache status support for server restricted access
* Fix: OPCache warnings when access is restricted on server

= 1.7 (2021.04.03) =
* New: debugger panel - Plugins
* New: plugins panel - log specially formatted objects from plugins
* New: settings panel shows Tracking tab
* New: settings group to select Pretty Print engine to use
* New: third party pretty print Engine: Kint
* Edit: various improvements to the debugger popup styling
* Fix: few Info methods have wrong names
* Fix: few warnings thrown by the Tracker object

= 1.6 (2021.01.06) =
* New: requires PHP 7.0 or newer
* New: requires WordPress 5.0 or newer
* New: debugger panel - Roles
* New: roles panel - shows registered user roles
* New: changed the loading order and activation priority
* Edit: few improvements to the readme file
* Fix: tools panel - broken links to all the plugin panels
* Fix: tracker loading causes' problem with some plugins changing user roles

= 1.5 (2020.11.14) =
* New: debugger panel - Tools
* New: tools panel - links to the individual plugin info panels
* New: tools panel - links to the individual WordPress tools panels
* New: tools panel - links to test with Google PageSpeed Insights
* New: tools panel - link to test with GTMetrix website
* New: contextual help tab for the plugin settings
* New: contextual help includes information about debug mode activation
* Edit: various styling changes and tweaks
* Fix: few minor typos in various parts of the code
* Fix: few external links were missing REL noopener attribute
* Fix: minor issue with CMS version INFO method

= 1.4.1 (2020.11.06) =
* Edit: support for the edge case HTTP request missing valid URL
* Fix: HTTP panel could still fail in some edge case requests

= 1.4 (2020.11.03) =
* Edit: improvements to the way HTTP panel displays logged data
* Fix: HTTP panel shows duplicated content type entry
* Fix: HTTP panel attempts to display data that doesn't exist
* Fix: error log loading fails due to file offset calculation
* Fix: problem with returning error log via AJAX method

= 1.3.2 (2020.10.03) =
* New: link to plugin Tools (Info) page added to Debugger popup footer
* Edit: improvements to documentation for pretty print functions
* Edit: renamed existing System class to Server for potential file name conflict
* Fix: debuglog tab doesn't scroll to the end of the list after the list is loaded
* Fix: test for Pear presence can throw warnings on some hosts

= 1.3.1 (2020.10.01) =
* Edit: minor changes to the main plugin class
* Fix: one constant was not defined

= 1.3 (2020.10.01) =
* New: additional Info page added into WordPress admin Tools
* New: tools info page shows the content of PHP Info
* New: tools info page shows the content of OPCache Info
* New: tools info page shows the content of MySQL Variables
* New: plugin settings panel uses tabbed interface to organize settings
* New: option to control AJAX tracking data save into debug.log
* New: filters to control AJAX tracking activity and data returned
* New: filter to control every plugin settings returned value
* New: ability to print SQL formatted string for the user stored queries
* New: improved documentation for all the functions
* Edit: refactored functions and improved the functions' organization
* Edit: refactored pretty print function to use different name and classes
* Edit: uniform return of rendered results from ErrorFormat class
* Edit: expanded plugin readme.txt and added new screenshots
* Removed: several unused methods in the ErrorFormat class

= 1.2.1 (2020.09.27) =
* Edit: various improvements to errors and warnings tracking handlers
* Fix: missing argument for the deprecated tracking handler

= 1.2 (2020.09.25) =
* New: debugger activator: show number of captured HTTP API calls
* New: debugger Log panel: renamed to Store
* New: debugger Log panel: rewritten rendering to match other panels
* New: debugger HTTP tab: shows number of calls in the tab button
* New: settings to control AJAX calls tracking on active page
* New: settings to control errors and warnings tracking
* Edit: various minor improvements and changes
* Fix: wrong class name in the backtrace tracker processor
* Fix: few small issues with the deprecated tracker processor
* Fix: several issues with displaying AJAX calls results

= 1.1 (2020.09.23) =
* New: debugger panel - Debug Log
* New: using CSS variables for some debugger styling
* New: filters to modify CSS variables
* New: improved the look of the plugin settings page
* Edit: expanded some information for plugin settings
* Edit: changed plugins own actions and filters for uniformity
* Edit: many improvements to the debugger styling
* Edit: various improvements to the SCSS organization
* Edit: various tweaks and changes

= 1.0 (2020.09.15) =
* First official release

== Upgrade Notice ==
= 3.8 =
Various updates and improvements.

= 3.7 =
Various updates and improvements.

= 3.6 =
Various updates and improvements.

= 3.5 =
Tracking improvements. Popup layout improvements. Tweaks and fixes.

= 3.4 =
Various updates and improvements.

= 3.3 =
Few updates and improvements. Updated Kint Library.

= 3.2 =
Tracking improvements and bug fixes.

= 3.1 =
Various updates, improvements and fixes.

== Screenshots ==
* Debugger popup: Basic debugger panel
* Debugger popup: Current page Query
* Debugger popup: Captured AJAX calls
* Debugger popup: SQL Queries
* Debugger popup: WordPress debug log
* Debugger popup: Layouts and Size controls
* Debugger popup: Responsive layout
* Settings: Activation
* Settings: Panels
* Settings: Tracking
* Settings: Advanced
* Tools: PHP Info
* Tools: OPCache Info
* Tools: MySQL Variables
