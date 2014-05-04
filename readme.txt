=== Game Schedules ===
Contributors: MarkODonnell
Donate link: http://shoalsummitsolutions.com
Tags: sports,games,schedule,sports teams,team schedule,countdown timer  
Requires at least: 3.3.1
Tested up to: 3.9
Stable tag: 4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manages multiple sports team schedules. Includes shortcodes and widgets to display schedules and a countdown timer to the next game.

== Description ==

The MSTW Game Schedules plugin manages multiple sports team schedules including: game dates and times, opponents, locations, results, and links to media (print stories or video on games). It features a countdown timer from the current time to then next game or next home game. Shortcodes and widgets are available to display schedule tables, sliders, and countdown timers.

= NEW IN VERSION 4.1 =

* Schedule tables can now display the last N results from the current time.
* New CSS tags (by team) allow COLUMNS in schedule tables to be customized.
* Added contextual help to Display Settings admin screen.

= Notes =

* The Game Schedule plugin is part of the My Sports Team Website (MSTW) framework for sports team and league websites. Others include Game Locations, Team Rosters, Coaching Staffs, and League Standings, which are all available on [WordPress.org](http://wordpress.org/extend/plugins). [Learn more about MSTW](http://shoalsummitsolutions.com/my-sports-team-website/).


= Helpful Links =
* [**See what the plugin and do for your website on the MSTW Dev Site -»**](http://dev.shoalsummitsolutions.com/)
* [**Read the user's manual at shoalsummitsolutions.com -»**](http://shoalsummitsolutions.com/category/gs-plugin)

== Installation ==

**IMPORTANT!**

1. *If you are upgrading, please read the upgrade notes. You shouldn't lose schedule data but you COULD easily lose and changes you've made to the plugin stylesheet.*
2. *Upon installation make sure the WP default timezone is set correctly in the Wordpress Settings->General screen.*

All the normal installation methods for WordPress plugins work:

1. Go to the Plugins->Installed plugins screen in Wordpress Admin. Click on Add New. Search for Game Schedules. Install it.
2. Download the plugin (.zip file) from WordPress.org. Go to the Plugins->Installed plugins screen in Wordpress Admin. Click on Add New. Click on the Upload link. Find the downloaded .zip file on your computer. Install it.
3. Download the plugin (.zip file) from WordPress.org. Unzip the file. Upload the extracted plugin folder to your website's wp-content/plugins directory using an FTP client or your hosting provider's file manager app. Activate it on the Plugins->Installed plugins screen in WordPress Admin.

== Frequently Asked Questions ==

[The FAQs may be found here.](http://shoalsummitsolutions.com/gs-faq/)

== Screenshots ==

1. Sample Game Schedule Table (via [mstw_gs_table] shortcode)
2. Editor - all games (table)
3. Editor - single game
4. Sample Countdown Timer & Schedule widgets
5. Countdown Timer & Schedule widget menus
6. Theme settings screen (admin)
7. CSV Import screen (admin)
8. Sample schedule slider (via [mstw_gs_slider] shortcode)

== Upgrade Notice ==

The current version of Game Schedules has been tested on WP 3.9 with the Twentyeleven theme. If you use older version of WordPress, good luck! If you are using a newer version, please let me know how the plugin works, especially if you encounter problems.

Upgrading to this version of Game Schedules should not impact any existing schedules. (But backup your DB before you upgrade, just in case. :) **NOTE that it will overwrite the css folder and the plugin's stylesheet - mstw-gs-styles.css.** So if you've made modifications to the stylesheet, you may want to move them to a safe location before installing the new version of the plugin. Also, you should re-test your display settings; some may need to be reset. An effort was made to change as little as possible, but the display settings may not be 100% backward compatible.

== Changelog ==

= 4.1 =
Bug fixes, clean-up, and some new features.

* Cleaned up Schedules, Teams, and Games admin screens to properly handle missing required data elements.
* Added CSS tags to allow COLUMNS in schedule tables to be customized.
* Added last_dtg=now, which provides the ability to display the last N results.
* Added contextual help to the Display Settings admin screen.
* Added previously missing and new strings to lang/default.pot
* Continued work on removing all non-fatal PHP notices and warnings.

= 4.0.1 =
Lots of clean-up intended to suppress PHP Notices and Warnings plus bug fixes:

* Problem with Schedule slider at the end of a season
* Problem with Schedule DB entries, Team selection from Teams DB being ignored
* Problem with Game entries, Location selection from Locations Plugin DB being ignored
* Problem with table and slider opponent name formats being mixed up (cut and paste error)

= 4.0 =
Another MAJOR UPGRADE with significant new functionality requested by users including:

* Added a new shortcode, [mstw_gs_slider], which displays a schedule slider
* Added the ability to show team logos on schedule sliders and tables. Created two new CDT's - Teams and Schedules - to support this capability
* Added the ability to display multiple team schedules on one schedule table or slider.
* Added the ability to display the next N games in a schedule to the [mstw_gs_table] shortcode and the widget (by setting the first date-time to 'now'
* Integrated Game Locations into the new Teams CPT
* Re-organized (expanded) Display Settings into a tabbed admin screen
* Added the ability to show/hide data fields and custom data field labels so they can be re-purposed
* Admin screens now display any customized data field labels, rather than the defaults
* Added the ability to specify custom formats for dates and times (based on standard php date() format strings).
* Added javascript colorpicker controls to display settings admin screens
* Added the ability to sort the "All Games" admin table by Schedule ID
* Removed the Edit option from the Bulk Edit actions. Bulk delete remains available
* Added a javascript datepicker control to simplify game date entry
* Added a filter (mstw_gs_user_capability) that allows developers to better control admin access to the plugin's admin menu items
* Completed internationalization of admin screens
* Completely re-factored the code for both the front and back ends

= 3.0 =
A MAJOR upgrade adding significant functionality requested by users:

* Several admin settings pages were added.
* The number of games displayed in a schedule ([shortcode] or widget can now be set.
* A date range can now be set for the games displayed in a schdule [shortcode] or widget can now be set.
* The plugin is now integrated with the Game Locations plugin. Game locations can be entered to game schedules from a pulldown list of locations in the Game Locations plugin. A link to the location in Google Maps is automatically added.
* Changed the current time to get the WORDPRESS time instead of the SERVER time as was the case previously.
* Changed the actual countdown time (time to next game) construction due to some 'anomalies' with the previous version.

= 2.4 = 
* Fixed a bug prevented "TBD dates" from displaying properly (and producing php warnings in some cases).
* Added a Spanish translation. Thanks to Roberto in Madrid.

= 2.3 = 
Fixed a bug (related to translation) that was causing dates to drift a month off in the shortcode table display.

= 2.2 =
* Changed date() to mstw_date_loc() - forgot a column in the shortcode.
* Added $mstw_gs_time_format to support changing the date format on the schedule table [shortcode].
* Updated the Croatian translation. 

= 2.1 =
* Added the option for a link from a game's location field (displayed in the shortcode table and the countdown widget) to a specified URL.
* Corrected a typo that make the new links on opponent entries not work in every scenario.
* Fixed several bugs in the stylesheet and how it loads (enqueues).

= 2.0 =
* Schedules can now go across multiple years. Schedules are now identified only by the ID, instead of ID and year (as in previous versions). The year field is now simply a part of the game date.
* Added internationalization for the user interface only, not the admin pages. Provided a default .po file in the /lang directory for any would be translators out there, and a Croatian translation (in the mstw-loc-domain-hr_HR.po file).
* Added a setting to countdown timer widget and an argument to the countdown timer shortcode that tells the countdown timer to use home games only.
* Added team links. You can specify a URL for each game. The schedule table shortcode (not widget) will add a link to the specified URL on the opponent name.
* Removed the "current time" field in the widget and argument in the shortcode. This was used only for testing and is not really needed. (There are other ways to test.)
* Removed Quick Edit link from the editor. (It broke things and was unnecessary.) 

= 1.1 =
* Expanded the media URL input fields to maxlength 255. (Some media outlets have long URLs.)
* Corrected bug so that multiple schedules (shortcodes) can be now displayed on one page.
* Added a custom header for wordpress.org

= 1.0 =
* Initial release.