=== Game Schedules ===
Contributors: MarkODonnell
Donate link: http://shoalsummitsolutions.com
Tags: sports,games,schedule,sports teams,team schedule,countdown timer  
Requires at least: 3.3.1
Tested up to: 3.7.1
Stable tag: 4.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manages multiple sports team schedules. Includes shortcodes and widgets to display schedules and a countdown timer to the next game.

== Description ==

Welcome to the MSTW Game Schedules Plugin from [Shoal Summit Solutions](http://shoalsummitsolutions.com/).

**Note that version 4.0.1 is a maintenance build that corrects a number of bugs. Please review the change log.**

The MSTW Game Schedules plugin manages multiple sports team schedules including: game dates and times, opponents, locations, results, and links to media (print stories or video on games). It features a countdown timer from the current time to then next game or next home game. Shortcodes and widgets are available to display schedule tables, sliders, and countdown timers.

= NEW IN VERSION 4.0 =
There are numerous NEW FEATURES in the front and back ends of version 4.0:

* New [mstw_gs_slider] shortcode providing a schedule slider [screenshot-8] 
* The ability to display team logos on schedule tables and schedule sliders [screenshot-8]
* Re-purpose data fields via show/hide and re-label display settings. (Similar to what can be done in the other MSTW Plugins.)
* Supports a complete suite of custom time and date formats via native PHP date format strings
* Color settings admin screen so that display colors can be controlled without editing CSS files
* Display next N games from current date in schedule tables (the [mstw_gs_table] shortcode and widget)
* Filter allowing developers to control user access to the Game Schedules admin menu items

= Front-end features for Website visitors =
The following features enhance the site user experience:

* Schedules Slider -displays scheduled games in a slider format
* Schedules Table - displays full schedules in tabular format
* Countdown Timer - displays countdown timer to the next game
* Team Logos - can now be shown on all of the above displays

= Back-end features for Website Admins =
The following plugin features enhance the website admin experience:

* Sort the All Games table by schedule ID. This frequently requested functionality makes life much nicer for admins with many schedules.
* Integration with [Game Locations Plugin](http://wordpress.org/extend/plugins/game-locations/) - makes it easy to enter game locations and link them to maps and enue websites.
* Unlimited Number of Schedules - may be created, so historical schedules and results can be saved and future schedules can be advertised.
* Import Schedule from CSV Files - allows an administrator to upload schedules from CSV format files.
* Plugin Stylesheet - allows an administrator to style schedule and countdown timers shortcode and widget displays via one simple, well-documented CSS stylesheet (css/mstw-gs-style.css). In version 4.0, CSS tags are available to style each display individually by team. See the examples on [the MSTW development website](http://shoalsummitsolutions.com/dev/)
* Internationalization (I18n) - the plugin is fully internationalized (as of v 3.0) and Croatian, Spanish, Swiss German, and Finnish translations are included with the distribution. (Many thanks to Juraj, Roberto, Chris, and Lauri!)

= Notes =

* The Game Schedule plugin is the second in a set of plugins supporting the My Sports Team Website (MSTW) framework for sports team websites. Others include Game Locations, Team Rosters, Coaching Staffs, and League Standings, which are all available on [WordPress.org](http://wordpress.org/extend/plugins). [Learn more about MSTW](http://shoalsummitsolutions.com/my-sports-team-website/).


= Helpful Links =

* [**Read the complete user's manual at shoalsummitsolutions.com»**](http://shoalsummitsolutions.com/category/gs-plugin)

== Installation ==

**NOTES**

1. *If you are upgrading, please read the upgrade notes. You won't lose schedule data but you COULD lose and changes you've made to the plugin stylesheet.*

2. *The first thing you want to do upon installation is make sure the WP default timezone is set correctly in the Wordpress Settings->General screen.*

The **AUTOMATED** way:

1. Go to the Plugins->Installed plugins page in Wordpress Admin.
2. Click on Add New.
3. Search for Game Schedules.
4. Click Install Now.
5. Activate the plugin.
6. Use the new Games menu to create and manage your schedules.

The **MANUAL** way:

1. Download the plugin from the wordpress site.
2. Copy the entire /mstw-game-schedule/ directory into your /wp-content/plugins/ directory.
3. Go to the Wordpress Admin Plugins page and activate the plugin.
4. Use the new Games menu to create and manage your schedules.

== Frequently Asked Questions ==

= How do I get the team logos to work in my schedule tables and sliders? =
Team logos require use of the new Teams and Schedules Custom Data Types. While there is quite a bit of setup required to enable these new cool features, the setup work then makes it very easy to enter games. Please read the full documentation on [shoalsummitsolutions.com](http://shoalsummitsolutions.com/category/users-manuals/gs-plugin/).

= Why is the plugin called "Game Schedules"? Couldn't I use it for other event schedules? =
Sure. The software doesn't know or care that the entries are "games". The references are to "game schedule" and "sched_games", only because that was original purpose of the plugin. Note however that it has no 'calendar' features, it just provides a simple list of events as a table.  

= Can I set up separate schedules for different teams and/or different seasons? =
Yes. A unique schedule ID defines each schedule. It is the primary argument for shortcodes and the primary option for the widgets. For all practical purposes, you can set up many schedules as you want. So for example schedule '2012-varsity' could be the varsity schedule 2012, schedule '2' the JV in 2012, schedule 'frosh-basketball' the frosh in 2012, and schedules 4-6 could be the same teams in 2013. (Schedule ID's must be in WordPress 'slug format'. I suggest using descriptive slugs, but numbers would work just fine.)

= I live in Split, Croatia (or wherever). Does the plugin support other languages? =
The plugin supports localization as of version 2.0. If you happen to live in Split, you're in luck. A Croatian translation is contained in the /lang directory. (Thanks Juraj!) A Spanish translation was added in version 2.4. (Thanks Roberto!) A Swiss German version is new to version 3.0. (Thanks Chris!) These translation files may need to be updated for version 3.0 and all its new features. A Finnish version will be added to the next release after 3.0, it is in the trunk now. (Thanks Lauri!) =NOTE:= Many more strings have been added in version 4.0 on both the front and back ends, so these translations need to be updated.

= How do I change the look (text colors, background colors, etc.) of the schedule or the countdown timer? =
Version 4.0 features a new set of color controls in the Display Settings admin screen. As in previous versions you can still edit the plugin's stylesheet, mstw-gs-styles.css. It is located in the game-schedule/css directory. It is short, simple, and well documented. The schedule plugin and the schedule widget have separate sets of styles. The countdown plugin and countdown widget share one set of styles. Note that team specific styling tags have been added so if you have multiple teams, each schedule and slider can be customized using the stylesheet. For example, see [the examples on my dev site](http://shoalsummitsolutions.com/dev/).

= How do I change the date-time group formats? Day [Month Year is more convenient in Europe.] =
As of version 3.0, seven time and date formats can be customized via the Game Schedules Plugin Display Settings Admin page. Those seven formats were not enough to provide worldwide support, so new in version 4.0 is the ability to set any custom date format using the php date() function format strings.

= The formats are right, but my dates and times or the countdown time are not still not correct. What's wrong? =
The date and time displays on both the user and the admin pages as well as the countdown timer are driven by the default WordPress time zone, which is set on the WordPress Settings->General screen. *It is important that you set the correct timezone in the WordPress Settings -> General Settings BEFORE entering any schedule data.*

= What can I do if I have more than three media links? =
If you are that popular, why not create one media link on the schedule that goes to a page of all your links? Or, you can hack the plugin code. I've considered a setting for "number of media links" (the JV and Frosh teams typically have none, at least in San Diego), but that's low on my list right now.

= Can I display more than one schedule on a single page by using multiple shortcodes? =
Yes. As of version 1.1 you can display multiple schedule tables and countdown timers on a single page using the [shortcodes].

= All my data for a game got "zero'ed out", what happened? =
Either: 

* You used the "Quick Edit" link in the list of all games (prior to version 2.0). Install version 2.0 or don't do that!
* You edited a game, updated it, and when you exited the game editor, it asked if you really wanted to leave the page because there were unsaved changes. Knowing that you already saved the changes, you clicked on "leave this page". Wrong! In this case you may not know best, just stay on the page and save the game again.
* You entered some really bad data for a game. Prior to version 3.0, the plugin was particularly sensitive about the time format. (This is fixed! Install the latest version.)

= I keep getting weird error messages on the page with the sort code. Any idea what's up?  =
These messages were caused by bad date-time data, and should be fixed in version 3.0. If you’re seeing such messages, please upgrade. If everything seems to be working okay except for these annoying error messages, you might want to turn them off on your WordPress site. To do so, edit wp-config.php and add the following lines:
  
> `error_reporting(0);`

> `@ini_set(‘display_errors’, 0);`

otherwise Wordpress overwrites the ALERTS set by PHP.INI`


== Screenshots ==

1. Sample Game Schedule Table (via [mstw_gs_table] shortcode)
2. Editor - all games (table)
3. Editor - single game
4. Sample Countdown Timer & Schedule widgets
5. Countdown Timer & Schedule widget menus
6. Theme settings screen (admin)
7. CSV Import screen (admin)
8. Sample schedule slider (via [mstw_gs_slider] shortcode)

== Changelog ==

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

== Upgrade Notice ==

The current version of Game Schedules was developed and tested one WordPress 3.7.1. If you use older version of WordPress, good luck! Much of the plugin was originally developed on 3.4.x so that might work okay. (No guarantees.) If you are using a newer version of WP, please let me know how the plugin works, especially if you encounter problems.

Upgrading to this version of Game Schedules should not impact any existing schedules. (But backup your DB before you upgrade, just in case. :) **NOTE that it will overwrite the css folder and the plugin's stylesheet - mstw-gs-styles.css.** So if you've made modifications to the stylesheet, you may want to move them to a safe location before installing the new version of the plugin. Also, you should test your display settings; some may need to be reset. An effort was made to change as little as possible, but the display settings may not be 100% backward compatible.