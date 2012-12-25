=== Game Schedules ===
Contributors: Mark O'Donnell
Donate link: http://shoalsummitsolutions.com
Tags: sports,games,schedule,sports teams,team schedule,countdown timer  
Requires at least: 3.3.1
Tested up to: 3.4.2
Stable tag: 2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Manages multiple sports team schedules. Includes shortcodes and widgets to display schedules and a countdown timer to the next game.

== Description ==

Manages sports team schedules including: game times, dates, opponents, locations, results, and links to media (stories on games). Includes shortcodes and widgets to display schedules and a countdown timer from the current time to then next game or next home game. 

The Game Schedules plugin creates a custom post type (sched_games), installs an editor for this post type, and provides a shortcode and a widget to display schedules as simple html tables. It also creates a shortcode and a widget to display a countdown timer to the next scheduled game. The plugin user interface, not the admin page, is internationalized (as of v 2.0) and a Croatian translation is included with the distribution.

The plugin supports as many team schedules as you need based on unique schedule IDs. For example: 1=Varsity Schedule (for 2012), 2=JV Schedule (for 2012), 3=Frosh Schedule (for 2012), and 4=Varsity Schedule (for 2013). It supports multiple years, so you can keep an archive of past results, current schedules and results, and/or future schedules. Countdown timers can be attached to any schedule ID.

Use the Edit Game Schedule screen (screenshot-2) to enter the Scheduled Games. The available fields are:

* Title: The post title is for internal admin use only. The title is displayed only on the admin pages. I suggest using the title to simplify game organization and sorting. For example, titling a game "2012-02-06 Oceanside" makes it easy to find sixth game (06) on the JV schedule (02) for the year 2012 in the editor's list of games. Properly titled games will sort in a convenient order.
* Schedule ID: An integer is recommended and tested. Other strings, maybe "G-V" for "Girls Varsity" should work but are as yet lightly tested. Defaults to 1.
* Game Year: Four digit year. E.g., "2012", defaults to current year. [Make sure it's a legit year!]
* Game Day: Select a date from the dropdown.
* Game Month: Select a month from the dropdown.
* Opponent: In any format you choose, e.g., "Cal", "California", "Cal Bears", "California Golden Bears", "Cal*" (* for a league game)
* Opponent Link: If you enter a URL in this field, the Opponent field will be linked to the URL provided; maybe the team's website or a story on the game. [Note that these links are available through the schedule shortcode and the countdown timer shortcode and widget. The schedule widget does not support these links.]
* Home Game? : Check this box if it is a home game. Home games can be highlighted (by color, font, etc.) on the schedules. The default is bold. More importantly, by default away games are shown as "@Opponent" in the countdown timer.
* Location: In free format text. (This field may be tied to the game locations plugin someday.)
* Location Link: If you enter a URL in this field, the Location field will be linked to the URL provided; maybe a map to the game. [Note that these links are available in the schedule shortcode and the countdown timer shortcode and widget. The schedule widget does not support these links because it doesn't support a location field.]
* Game Time: Game times should be formated as "HH:MMpm". For example, "07:30pm". If the time is not recognizable, it will be displayed as typed, but the countdown timer won't work correctly. It may be entered as "T.B.D." or "TBD" or "T.B.A." or "TBA".
* Game Result: The game result in free form. For example, "27-14", or "W 27-14", or "L 27-14" or simply "WIN".
* Media Links: Three media links are provided. Initially, these fields are empty. You may enter up to 3 titles and URLs. E.g., you might enter "ESPN Sports" and "http://espn.go.com/" and the plugin will create the link in the table.

**Game Schedule Widget & Shortcode**

Game schedules may be displayed via a shortcode or as a widget.

The schedule SHORTCODE [mstw_gs_table] accepts one argument:
 
1. sched="nn" tells the shortcode what schedule (ID) to display. Defaults to 1.

For example, [mstw_gs_table sched="4"] will display schedule ID 4 and [mstw_gs_table] will display schedule ID 1.

The schedule WIDGET supports the same parameter along with a widget title.

**Countdown Timer Widget & Shortcode**

The countdown timer SHORTCODE [mstw_gs_countdown] accepts three arguments:

1. sched="nn" tells the shortcode what schedule (ID) to display. Defaults to 1.
2. intro="text string" defines what string to display before the countdown. Defaults to "Time to kickoff:"
3. home_only="true/false" tells the shortcode to countdown to home games only. Defaults to false (all games).

The countdown timer WIDGET supports the same parameters along with a widget title.

**Notes:**

* There is no singular post template. Right now, there is no reason to view a single game because all the information is available in the schedules themselves and/or the edit table. But I could be convinced otherwise; it's simple enough to create a single game template (single-game.php) should you need one.
* The Game Schedule plugin is the second in a set of plugins supporting a framework for sports team websites. Others will include Game Locations (available at shoalsummitsolutions.com), Team Rosters, Coaching Staffs, Sponsors, Frequently Asked Questions, Users Guide, and more. If you are a developer and there is one you would really like to have, or if you would like to participate in the development of one, please let me know (mark@shoalsummitsolutions.com).

== Installation ==

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

= Why is the plugin called "Game Schedules"? Couldn't I use it for other event schedules? =
Sure. The software doesn't know or care that the entries are "games". The references are to "game schedule" and "sched_games", only because that was original purpose of the plugin. Note however that it has no 'calendar' features, it just provides a simple list of events as a table.  

= Can I set up separate schedules for different teams? =
Yes. The unique schedule id defines each schedule. It is the key argument for shortcodes and option for the widgets. For all practical purposes, you can set up many as you want. So for example schedule 1 could be the varsity, schedule 2 the JV, and schedule 3 the frosh.

= Can I have schedules for more than one season? =
Yes. Just use different schedule ID's for different seasons. (See "Can I set up separate schedules for different teams?")

= I live in Split, Croatia (or wherever). Does the plugin support other languages? =
The plugin supports localization as of version 2.0. If you happen to live in Split, you're in luck. A Croatian translation is contained in the /lang directory. (Thanks Juraj!) A Spanish translation was added in version 2.4. (Thanks Roberto!)

= How do I change the look (text colors, background colors, etc.) of the schedule or the countdown timer? =
In this version you have to edit the plugin's stylesheet, mstw-gs-styles.css. It is located in the game-schedule/css directory. It is short, simple, and well documented. The schedule plugin and the schedule widget have separate sets of styles. The countdown plugin and countdown widget share one set of styles. In the future, I plan to provide options for commonly changed styles on the admin page to control the schedule table and countdown timer styles. 

= How do I change the date-time group formats? Day [Month Year is more convenient in Europe.] =
Unfortunately, much like the styles described in the previous question, you must edit the code to do this. In this case, go in to the plugin's main file - mstw-game-schedule.php. At the start of the code, you will find the following lines:

> `//Date column of the widget's table`

> `$mstw_gs_dtg_format =  'j M y';`

> `// For the dashboard/metabox - don't need the year, it's already displayed.`

> `$mstw_dash_dtg_format =  'j M'; `

> `// For the countdown timer; game time with a time`

> `$mstw_gs_cdt_time_format = "l, j M g:i a";`

> `// For the countdown timer; game time with only a game date (no time)`

> `$mstw_gs_cdt_tbd_format = "l, j M";`

> `//Date column of the widget's table`

> `$mstw_gs_sw_dtg_format = 'd M y'; `

> `//Time column for the [shortcode]'s schedule table`

>	`$mstw_gs_time_format = "g:i a"; `

Refer to the php date() function's formatting options to decipher the codes. (I plan to make these options on the admin page ... someday.) 

**Note however** that the actual entry of the game's time is very sensitive to format and you MUST use the "Americanized" time format exactly as specified when adding or editing a game. 

= I changed the format, but my dates and times (still) are not correct. What's wrong? =
You may need to set the time zone. The very first excutable line in the file is:

> `date_default_timezone_set('America/Los_Angeles');`

You need to change that to the correct timezone. *Note* that there are two other places where that line needs to be changed, so search the file for "Los_Angeles". Google the function and you'll easily find the list of allowable PHP timezones. This is on the "make it an option in the admin page someday" list.

= What can I do if I have more than three media links? =
If you are that popular, why not create one media link on the schedule that goes to a page of all your links? Or, you can hack the plugin code. I've considered a setting for "number of media links" (the JV and Frosh teams typically have none, at least in San Diego), but that's low on my list right now.

= Can I display more than one schedule on a single page by using multiple shortcodes? =
Yes. (As of version 1.1.) 

= All my data for a game got "zero'ed out", what happened? =
Either: 

* You used the "Quick Edit" link in the list of all games (prior to version 2.0). Install version 2.0 or don't do that!
* You edited a game, updated it, and when you exited the game editor, it asked if you really wanted to leave the page because there were unsaved changes. Knowing that you already saved the changes, you clicked on "leave this page". Wrong! In this case you may not know best, just stay on the page and save the game again.
* You entered some really bad data for a game. It is particularly sensitive about the time format. (I know I should improve the error checking. It's on the list!)

= I keep getting weird error messages on the page with the sort code. Something like:  =
These are caused by bad date-time data. I have tried to prevent these with better error checking in the front end code (in version 2.4). If you see them, you may want to double check that all your game time entries are valid. And, if a time is unknown, you use one of the following strings: TBD, T.B.D., TBA, T.B.A. I will tighten things up in the admin side in the next major release (version 3.0). If everything seems to be working okay except for these annoying error messages, you might want to turn them off on your WordPress site. To do so, edit wp-config.php and add the following lines:
  
> `error_reporting(0);`

> `@ini_set(‘display_errors’, 0);`

otherwise Wordpress overwrites the ALERTS set by PHP.INI`


== Screenshots ==

1. Sample Game Schedule Table (via Shortcode)
2. Editor - all games (table)
3. Editor - single game
4. Sample Countdown Timer & Schedule widgets
5. Countdown Timer & Schedule widget menus

== Changelog ==

= 1.0 =
* Initial release.

= 1.1 =
* Expanded the media URL input fields to maxlength 255. (Some media outlets have long URLs.)
* Corrected bug so that multiple schedules (shortcodes) can be now displayed on one page.
* Added a custom header for wordpress.org

= 2.0 =
* Schedules can now go across multiple years. Schedules are now identified only by the ID, instead of ID and year (as in previous versions). The year field is now simply a part of the game date.
* Added internationalization for the user interface only, not the admin pages. Provided a default .po file in the /lang directory for any would be translators out there, and a Croatian translation (in the mstw-loc-domain-hr_HR.po file).
* Added a setting to countdown timer widget and an argument to the countdown timer shortcode that tells the countdown timer to use home games only.
* Added team links. You can specify a URL for each game. The schedule table shortcode (not widget) will add a link to the specified URL on the opponent name.
* Removed the "current time" field in the widget and argument in the shortcode. This was used only for testing and is not really needed. (There are other ways to test.)
* Removed Quick Edit link from the editor. (It broke things and was unnecessary.)

= 2.1 =
* Added the option for a link from a game's location field (displayed in the shortcode table and the countdown widget) to a specified URL.
* Corrected a typo that make the new links on opponent entries not work in every scenario.
* Fixed several bugs in the stylesheet and how it loads (enqueues).

= 2.2 =
* Changed date() to mstw_date_loc() - forgot a column in the shortcode.
* Added $mstw_gs_time_format to support changing the date format on the schedule table [shortcode].
* Updated the Croatian translation. 
 
= 2.3 = 
Fixed a bug (related to translation) that was causing dates to drift a month off in the shortcode table display.

= 2.4 = 
* Fixed a bug prevented "TBD dates" from displaying properly (and producing php warnings in some cases).
* Added a Spanish translation. Thanks to Roberto in Madrid.

== Upgrade Notice ==

The current version of Game Schedules has been tested up to 3.5. If you use older version of WordPress, good luck! (Much of it was developed on 3.4.x so that should be ok.) If you are using a newer version, please let me know how the plugin works, especially if you encounter problems.

Upgrading to this version of Game Schedules should not impact any existing schedules. (But backup your DB before you upgrade, just in case. :)