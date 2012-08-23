=== Game Schedules ===
Contributors: MarkODonnell
Donate link: http://Coming-Soon
Tags: sports,games,schedule,sports teams,team schedule,countdown timer  
Requires at least: 3.3.1
Tested up to: 3.3.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Provides team schedules of games, with results, and a countdown timer from now to gametime. Includes shortcodes and widgets.

== Description ==

This plugin to manages and displays sports team schedules. It also provides a countdown timer to the next game on the schedule.

It creates a custom post type, sched_games, installs an editor for this post type, and provides a shortcode and a widget to display schedules as simple html tables. It also creates a shortcode and a widget to display a countdown timer to the next scheduled game.

The plugin supports multiple team schedules as you need based on schedule IDs and years. For example, 1=Varsity Schedule, 2=JV Schedule, and 3=Frosh Schedule. It also supports multiple years, so you can keep an archive of past results, as well as future schedules. Therefore each game entered is attached to a schedule and a year. Countdown timers can be attached to any schedule and year.

Use the Edit Game Schedule screen (screenshot-2) to enter the Scheduled Games. The available fields are:

* Title: The post title is for internal admin use only. The title is not displayed anywhere else. I suggest using the title to simplify game organization and sorting. For example, titling a game "2012-02-06 Oceanside" makes it easy to find sixth (06) JV (schedule 02) game of year 2012 in the editor's list of games. Games will sort in a convenient order.
* Schedule ID: An integer is recommended and tested. Other strings, maybe "G-V" for "Girls Varsity" should work but are as yet untested. Defaults to 1.
* Schedule Year: Four digit year. E.g., "2012", defaults to current year. [Make sure it's a legit year!]
* Game Day: Select a date from the dropdown.
* Game Month: Select a month from the dropdown.
* Opponent: In any format you choose, e.g., "Cal", "California", "Cal Bears", "California Golden Bears", "Cal*" (* for a league game)
* Home Game? : Check this box if it is a home game. Home games can be highlighted (by color, font, etc.) on the schedules. The default is bold. More importantly, by default away games are shown as "@Opponent" in the countdown timer.
* Location: In free format text. (This field may be tied to the game locations plugin someday.)
* Game Time: Game times should be formated as "HH:MMpm". For example, "07:30pm". If the time is not recognizable, it will be displayed as typed, but the countdown timer won't work correctly. It may be entered as T.B.D.
* Game Result: The game result in free form. For example, "27-14", or "27-14 W", or "27-14 L" or "14-27".
* Media Links: Three media links are provided. Initially, this field will be empty. After the game, you may enter up to 3 titles and URLs. E.g., you might enter "ESPN Sports" and "http://espn.go.com/" and the plugin will create the link in the table.

**Game Schedule Widget & Shortcode**

Game schedules may be displayed via a shortcode or as a widget.

The schedule SHORTCODE [mstw_gs_table] accepts two arguments:

1. year="nnnn" tells the shortcode what schedule year to display. Defaults to current year. 
2. sched="nn" tells the shortcode what schedule (id) to display. Defaults to 1.

For example, [mstw_gs_table year="2012" sched="4"] will display schedule id 4 for the year 2012.

The schedule WIDGET supports of the same parameters.

**Countdown Timer Widget & Shortcode**

The countdown timer SHORTCODE [mstw_gs_countdown] accepts four arguments:

1. year="nnnn" tells the shortcode what schedule year to display. Defaults to current year.
2. sched="nn" tells the shortcode what schedule (id) to display. Defaults to 1.
3. intro="text string" defines what string to display before the countdown. Defaults to "Time to kickoff:"
4. current="yyyy Mon dd hh:mmpm" sets the time to countdown from, primarily used for testing purposes. Sample format: "2012 May 05 7:00pm". Defaults to the current time. [It's not clear how this would be used other than testing, but it's available if needed.]

The countdown timer WIDGET supports the same parameters.

**Notes:**

* There is no singular post template. Right now, there is no reason to view a single game because all the information is available in the schedules themselves and the edit table. But I could be convinced otherwise; it's simple enough to create a single game template.
* DON'T USE THE QUICK EDIT feature in the table editor. It will clear a number of fields. Most annoying. I'll fix it or remove it someday.
* The Game Schedule plugin is the second in a set of plugins supporting a framework for sports team websites. Others will include Game Locations (available on WordPress.org), Team Rosters, Coaching Staffs, Sponsors, Frequently Asked Questions, Users Guide, and more. If you are a developer and there is one you would really like to have, please let me know.

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
Sure. The schedule works great The references are to "game schedule" and "sched_games", only because that was original purpose of the plugin. Note however that it has no 'calendar' features, it just provides a simple list of events as a table.  

= Can I set up separate schedules for different teams? =
Yes. The schedule id and year define each schedule. They are arguments for shortcodes and options for the widgets. For all practical purposes, you can set up many as you want. You are limited only by computer memory and database (disk) space. 

= Can I have schedules for more than one season? =
Yes. See "How many separate schedules can I set up?"

= How do I change the look of the schedule or the countdown timer? =
In this version, you have to edit the plugin's stylesheet, mstw-gs-styles.css. It is located in game-schedule/css. It is short, simple, and well documented. The schedule plugin and the schedule widget have separate sets of styles. The countdown plugin and countdown widget share one set of styles. In the future, I plan to provide options on the admin page to control the schedule table and countdown timer styles. 

= What can I do if I have more than three media links? =
If you are that popular, why not create one media link on the schedule that goes to a page of all your links? Or, you can hack the plugin code. I've considered a setting for "number of media links" (the JV and Frosh teams typically have none, at least in San Diego), but that's low on my list right now.

== Screenshots ==

1. Sample Game Schedule Table (via Shortcode)
2. Editor - all games (table)
3. Editor - single game
4. Sample Countdown Timer & Schedule widgets
5. Countdown Timer & Schedule widget menus

== Changelog ==

= 0.1 =
* Initial release.

== Upgrade Notice ==

The current version of Game Schedules requires WordPress 3.2.1 or higher. If you use older version of WordPress, it may work or it may not. Good luck.