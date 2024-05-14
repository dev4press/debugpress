# DebugPress

## Changelog

### Version: 3.5.1 (2023.11.18)

* **fix** Internal debug to error log remain in the source code

### Version: 3.5 (2023.11.06)

* **new** Tracker logs the trace for each HTTP API request made
* **new** Tracker executes action for every completed HTTP API request
* **new** AJAX Tracker executes action for every completed Admin AJAX request
* **new** HTTP API log shows the trace and timestamps for each request
* **new** Popup tools links to the coreActivity events and logs
* **new** Popup header shows icons for tabs with labels with improved sizing
* **new** Popup tabs show label or icon only, depending on the screen size
* **fix** Trace information for tracker HTTP API request was lost

### Version: 3.4.1 (2023.10.15)

* **fix** Function `apache_get_version` not working on every server

### Version: 3.4 (2023.10.06)

* **new** Updated some plugin system requirements
* **edit** KINT now loads own helper d() and s() functions
* **edit** Various styling improvements and tweaks
* **edit** Improved organization of the print libraries now moved to vendor directory
* **fix** MySQL tools panel showing error if the server information can't be retrieved
* **fix** Problem with the Info method for getting server IP in some cases
* **fix** Few more issues with Info method for getting database information

### Version: 3.3 (2023.07.03)

* **new** Support for more versions of the Dev4Press Library
* **new** System tab shows additional information about Apache
* **new** System tab shows WordPress database overview information
* **new** Remember open/close sections on some panels
* **edit** Some changes to the displayed System and Basic tabs
* **edit** Various small tweaks to the plugin PHP code
* **edit** Kint Pretty Print Library 5.0.7
* **fix** Few issues with Info method for getting database information

### Version: 3.2 (2023.06.22)

* **new** Support for the deprecated hook run handling
* **new** Execute actions when each error has been logged
* **new** Deprecated tracking now logs the caller information
* **edit** Improved caller trace cleanup for error calls
* **edit** Display more relevant error source file and log for errors
* **edit** Errors tab moved to the end of the display order
* **edit** Changed tab order for several other debugger tabs
* **edit** Various small tweaks to the plugin PHP code
* **fix** Fatal error tracking calling default handler directly
* **fix** Sometimes errors not getting displayed in the Error tab

### Version: 3.1 (2023.06.14)

* **new** Identify SQL queries sources for Dev4Press plugins
* **new** Hooks panel can filter by the WordPress Admin callbacks
* **edit** Improved method for displaying button activation flags
* **edit** Many improvements to escaping variables for display
* **edit** Better optimized included images for logos
* **edit** Various small tweaks to the plugin PHP code
* **edit** Various small tweaks to the main JavaScript file
* **fix** Hooks panel not filtering MU Plugins

### Version: 3.0.1 (2023.05.05)

* **edit** Minor updates to the plugin readme file
* **edit** Various improvements to the PHP core code
* **fix** Warnings related to OPCache for some PHP configurations

### Version: 3.0 (2023.04.03)

* **new** Modify debugger popup layout and size
* **new** Modify debugger popup modal state
* **new** Modify debugger popup opening state (auto, manual, remember state)
* **new** Save active tab and show it first on next page load
* **new** Trigger debugger popup display via keyboard shortcut
* **new** Access Key option to enable loading on demand via URL
* **new** Settings block and information for the On Demand access
* **new** Settings block and information for shortcut key activation
* **new** Admin bar button has basic stats dropdown menu
* **new** Plugin settings Help tab with On Demand information
* **new** Content tab split into Content and Rewrite tabs
* **new** Basic tab shows currently active theme information
* **new** Admin tab content moved to the Request tab
* **new** Refreshed the debugger look with new icons
* **new** Function to write log entry into custom info/log file
* **new** `IP` class mostly rewritten and expanded
* **new** Mousetrap Javascript v1.6.5 library
* **edit** Few improvements to the plugin init and load process
* **edit** Various improvements to the PHP core code
* **edit** Changes to some plugin default settings
* **edit** `IP` class expanded Cloudflare IP range
* **edit** Smart Animated Popup v2.0 library
* **del** removed the dedicated Admin tab
* **fix** Few issues with the `IP` class range methods

### Version: 2.2 (2023.02.03)

* **new** Updated some plugin system requirements
* **new** Server panel shows PHP Include Path value
* **new** Server panel shows Memcache/Memcached status
* **new** Server panel shows all loaded extensions
* **new** Server panel shows expanded MySQL information
* **edit** Various improvements to some panels display conditions
* **fix** Several more issues related to changes in PHP 8.1 and 8.2
* **fix** Issue with detection of the PEAR System.php file

### Version: 2.1 (2022.12.30)

* **new** Tested with PHP 8.2 and WordPress 6.1
* **new** Query panel shows current post and metadata for that post
* **edit** Improvements to the Query panel and organization of displayed data
* **edit** Various language syntax improvements and changes
* **edit** Kint Pretty Print Library 5.0.1
* **fix** Several issues related to changes in PHP 8.2
* **fix** Few minor issues with the layouts in the debugger panels
* **fix** Missing semicolon in one instance in JavaScript

### Version: 2.0 (2022.08.31)

* **new** Prevent loading of the debugger panel for REST request
* **new** Default PrettyPrint library has own CSS file
* **new** Debugger now loads on the WordPress login pages
* **new** Kint set as default pretty print library for new installations
* **edit** Improvements to the plugin loading process
* **edit** Various minor updates and tweaks
* **edit** Kint Pretty Print Library 4.2
* **fix** Plugin can break some REST request responses

### Version: 1.9 (2022.05.15)

* **new** Tested with WordPress 6.0
* **edit** Several minor updates and improvements
* **edit** Kint Pretty Print Library 4.1.1
* **fix** Some layout issues with the tables

### Version: 1.8 (2021.09.30)

* **new** requires WordPress 5.1 or newer
* **new** activation button indicator for number of stored object
* **edit** few more compatibility changes to the JavaScript code
* **edit** OPCache status support for server restricted access
* **fix** OPCache warnings when access is restricted on server

### Version: 1.7 (2021.04.03)

* **new** debugger panel - Plugins
* **new** plugins panel - log specially formatted objects from plugins
* **new** settings panel shows Tracking tab
* **new** settings group to select Pretty Print engine to use
* **new** third party pretty print Engine: Kint
* **edit** various improvements to the debugger popup styling
* **fix** few Info methods have wrong names
* **fix** few warnings thrown by the Tracker object

### Version: 1.6 (2021.01.06)

* **new** requires PHP 7.0 or newer
* **new** requires WordPress 5.0 or newer
* **new** debugger panel - Roles
* **new** roles panel - shows registered user roles
* **new** changed the loading order and activation priority
* **edit** few improvements to the readme file
* **fix** tools panel - broken links to all the plugin panels
* **fix** tracker loading causes' problem with some plugins changing user roles

### Version: 1.5 (2020.11.14)

* **new** debugger panel - Tools
* **new** tools panel - links to the individual plugin info panels
* **new** tools panel - links to the individual WordPress tools panels
* **new** tools panel - links to test with Google PageSpeed Insights
* **new** tools panel - link to test with GTMetrix website
* **new** contextual help tab for the plugin settings
* **new** contextual help includes information about debug mode activation
* **edit** various styling changes and tweaks
* **fix** few minor typos in various parts of the code
* **fix** few external links were missing REL noopener attribute
* **fix** minor issue with CMS version INFO method

### Version: 1.4.1 (2020.11.06)

* **edit** support for the edge case HTTP request missing valid URL
* **fix** HTTP panel could still fail in some edge case requests

### Version: 1.4 (2020.11.03)

* **edit** improvements to the way HTTP panel displays logged data
* **fix** HTTP panel shows duplicated content type entry
* **fix** HTTP panel attempts to display data that doesn't exist
* **fix** error log loading fails due to file offset calculation
* **fix** problem with returning error log via AJAX method

### Version: 1.3.2 (2020.10.03)

* **new** link to plugin Tools (Info) page added to Debugger popup footer
* **edit** improvements to documentation for pretty print functions
* **edit** renamed existing System class to Server for potential file name conflict
* **fix** debuglog tab doesn't scroll to the end of the list after the list is loaded
* **fix** test for Pear presence can throw warnings on some hosts

### Version: 1.3.1 (2020.10.01)

* **edit** minor changes to the main plugin class
* **fix** one constant was not defined

### Version: 1.3 (2020.10.01)

* **new** additional Info page added into WordPress admin Tools
* **new** tools info page shows the content of PHP Info
* **new** tools info page shows the content of OPCache Info
* **new** tools info page shows the content of MySQL Variables
* **new** plugin settings panel uses tabbed interface to organize settings
* **new** option to control AJAX tracking data save into debug.log
* **new** filters to control AJAX tracking activity and data returned
* **new** filter to control every plugin settings returned value
* **new** ability to print SQL formatted string for the user stored queries
* **new** improved documentation for all the functions
* **edit** refactored functions and improved the functions' organization
* **edit** refactored pretty print function to use different name and classes
* **edit** uniform return of rendered results from ErrorFormat class
* **edit** expanded plugin readme.txt and added new screenshots
* **removed** several unused methods in the ErrorFormat class

### Version: 1.2.1 (2020.09.27)

* **edit** various improvements to errors and warnings tracking handlers
* **fix** missing argument for the deprecated tracking handler

### Version: 1.2 (2020.09.25)

* **new** debugger activator: show number of captured HTTP API calls
* **new** debugger Log panel: renamed to Store
* **new** debugger Log panel: rewritten rendering to match other panels
* **new** debugger HTTP tab: shows number of calls in the tab button
* **new** settings to control AJAX calls tracking on active page
* **new** settings to control errors and warnings tracking
* **edit** various minor improvements and changes
* **fix** wrong class name in the backtrace tracker processor
* **fix** few small issues with the deprecated tracker processor
* **fix** several issues with displaying AJAX calls results

### Version: 1.1 (2020.09.23)

* **new** debugger panel - Debug Log
* **new** using CSS variables for some debugger styling
* **new** filters to modify CSS variables
* **new** improved the look of the plugin settings page
* **edit** expanded some information for plugin settings
* **edit** changed plugins own actions and filters for uniformity
* **edit** many improvements to the debugger styling
* **edit** various improvements to the SCSS organization
* **edit** various tweaks and changes

### Version: 1.0 (2020.09.15)

* First official release
