=== iCal for Events Manager ===
Plugin Name: iCalendar feed for Events Manager
Author URI: http://benjaminfleischer.com/code/ical-for-events-manager/
Contributors: benjo4u
Donate link: http://benjaminfleischer.com/code/donate
Tags: ical, events manager, ical feed, feed, icalendar, calendar, events, event, subscribe
Requires at least: 2.6
Tested up to: 2.7.1
Version: 1.0.4b
Stable tag: 1.0.4b

Add an iCal feed to your site for the Events Manager plugin

== Description ==

Creates an iCal feed for [Events Manager](http://davidebenini.it/wordpress-plugins/events-manager/) based on [Events Calendar plugin](http://wordpress.org/extend/plugins/ical-for-events-calendar/) by YukataNinja.

Based on [Gary King's iCal Posts](http://www.kinggary.com/archives/build-an-ical-feed-from-your-wordpress-posts-plugin) and modifications by [Jerome](http://capacity.electronest.com/ical-for-ec-event-calendar).

Currently, the timezone information is hardcoded as Chicago in the ical-ec.php file.  The next version will allow editing this from the admin panel

= 1.0.4b =
*  Somehow I didn't push the actual revision to 1.0.4 like I thought. 
*  Also corrected example, that forceoffset should be uses with ical, ical=rss, or ical=cron

= 1.0.4 =
*  To force the use of the Wordpress GMT offset include the get variable forceoffset in your url. This makes it work in Google Calendar for me
forceoffset, uses the gmt_offset in Wordpress
*  Example: http://your-domain/?ical=cron&forceoffset

*  Also fixed the line breaks

= 1.0.3 =
*  Changed description output to enconded quotable to preserve line breaks

*  Added these configuration get parameters for time zones:
    tzlocation, e.g. America/Chicago
    tzoffset_standard, e.g. -0600
    tzname, e.g. CST
    tzname_daylight, e.g. CDT
    tzoffset_daylight, e.g. -0500
*  I haven't robustly tested this. I think you can find these values [here](http://www.w3.org/2002/12/cal/tzd/)


*  Added ability to cron output to a file with these get parameters
    ical   outputs to the screen
    ical=cron  outputs to an ics file and displays success message
    ical=rss outputs to an ics file and should be subscribable by an rss reader for cronless update
    ical=ics get the ics file if available
*  This seems to be working right now

*  Example with output to screen:
    Feed will be at http://your-web-address/?ical
*  Example with output to screen and custom timezones
    Feed will be at http://your-web-address/?ical&tzlocation=America/Chicago etc.
*  Example with cron or rss file creation and custom timezones
    Feed will be at http://your-web-address/?ical=rss&tzlocation=America/Chicago etc.
    Feed will be at http://your-web-address/?ical=cron&tzlocation=America/Chicago etc.
*  Example to get ics file
    Feed will be at http://your-web-address/?ical=ics


== Installation ==

1. Unzip in your plugins directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Where is the feed located? =

At http://your-web-address/?ical
