<?php
/*
Plugin Name: iCal for Events Manager
Description: Creates an iCal feed for Events Manager at http://your-web-address/?ical. 
Version: 1.0.5
Author: benjo4u
Author URI: http://benjaminfleischer.com/code/ical-for-events-manager
*/

function iCalFeed()
{
    global $wpdb;

    if (isset($_GET["debug"]))
    {
        define("DEBUG", true);
    }
$getstring = $_GET['ical'];
if(isset($_GET['forceoffset'])) {
$forceoffset = get_option("gmt_offset");
} else {
$forceoffset = "";
}
 if($getstring == 'ics') {
        if(file_exists('icalendar.ics')) {
        header("Content-Type: text/Calendar");
        header("Content-Disposition: inline; filename=icalendar.ics");
        } else { echo 'no icalendar.ics file found'; }
}
    $queryEvents = "SELECT event_id AS id, event_name AS eventTitle, event_notes AS eventDescription, ";
    $queryEvent .= "location_name AS eventLocation, ";
    $queryEvents .= "event_start_date AS eventStartDate, event_start_time AS eventStartTime,  ";
    $queryEvents .= "event_end_date AS eventEndDate, event_end_time AS eventEndTime ";
    $queryEvents .= "FROM ".$wpdb->prefix."dbem_events e, ".$wpdb->prefix."dbem_locations l ";
    $queryEvents .= "WHERE event_id > 0 AND e.location_id = l.location_id ";
    $queryEvents .= "ORDER BY event_start_date DESC";

    $posts = $wpdb->get_results($queryEvents);
#settings
if(isset($_GET['tzlocation'])) { $tzlocation = $_GET['tzlocation']; }
else { $tzlocation = "America/Chicago"; }

if(isset($_GET['tzoffset_standard'])) { $tzoffset_standard = $_GET['tzoffset_standard'];}
else { $tzoffset_standard = "-0600"; }

if(isset($_GET['tzname'])) { $tzname = $_GET['tzname']; }
else { $tzname = "CST"; }

if(isset($_GET['tzname_daylight'])) { $tzname_daylight = $_GET['tzname_daylight']; }
else { $tzname_daylight="CDT"; }

if(isset($_GET['tzoffset_daylight'])) { $tzoffset_daylight = $_GET['tzoffset_daylight']; }
else { $tzoffset_daylight = "-0500"; }


    $events = "";
    $space = "    ";
    foreach ($posts as $post)
    {
        $convertDateStart = strtotime($post->eventStartDate);
        $convertDateEnd = strtotime($post->eventEndDate);
    if ($convertDateEnd < $convertDateStart ) {
     $convertDateEnd = $convertDateStart;
    }
    
        if (NULL != $post->eventStartTime)
        {
            $convertHoursStart = explode(":", $post->eventStartTime);
        }
        else
        {
            $convertHoursStart = explode(":", "20:00:00");
        }
        
        if (NULL != $post->eventEndTime)
        {
            $convertHoursEnd = explode(":", $post->eventEndTime);
        }
        else
        {
            $convertHoursEnd = explode(":", "20:00:00");
        }

        $convertedStart = mktime(
            $convertHoursStart[0] - $forceoffset,   //hours
            $convertHoursStart[1],                              //minutes
            $convertHoursStart[2],                              //seconds
            date("m" ,$convertDateStart),                               //month
            date("d", $convertDateStart),                               //day
            date("Y", $convertDateStart)                              //year
        );

        $convertedEnd = mktime(
           $convertHoursEnd[0] - $forceoffset,     //hours
#            $convertHoursEnd[0],     //hours
            $convertHoursEnd[1],                                //minutes
            $convertHoursEnd[2],                                //seconds
            date("m" ,$convertDateEnd),                               //month
            date("d", $convertDateEnd),                               //day
            date("Y", $convertDateEnd)                              //year

        );
$printableline = '\\n';
        $eventStart = date("Ymd\THis", $convertedStart) . "Z";
        $eventEnd = date("Ymd\THis", $convertedEnd) . "Z";
$timestamp = date("Ymd\THis", time()) . "Z";
        $summary = $post->eventTitle;
        $description = $post->eventDescription;
       # $description = str_replace(",", "\,", $description);
       # $description = str_replace("\\", "\\\\", $description);
        $description = str_replace("\n", $printableline, strip_tags($description));
       # $description = str_replace("\r", $space, strip_tags($description));
       # $description = str_replace("\t", $space, strip_tags($description));

        $uid = $post->id . "@" . get_bloginfo('home');
        $events .= "BEGIN:VEVENT\n";
        $events .= "DTSTART:" . $eventStart . "\n";
        $events .= "DTEND:" . $eventEnd . "\n";
        $events .= "DTSTAMP:".$timestamp."\n";
        $events .= "CREATED:".$timestamp."\n";
        $events .= "LAST-MODIFIED:".$timestamp."\n";
        $events .= "UID:" . $uid . "\n";
        $events .= "SUMMARY:" . $summary . "\n";
        $events .= "DESCRIPTION:" .  preg_replace("/[\n\t\r]/", $printableline, $description) . "\n";
        $events .= "END:VEVENT\n";
    }

    $blogName = get_bloginfo('name');
    $blogURL = get_bloginfo('home');

    if (!defined('DEBUG'))
    {
        header('Content-type: text/calendar');
        header('Content-Disposition: attachment; filename="iCal-EC.ics"');
    }

    $content = "BEGIN:VCALENDAR\n";
    $content .= "PRODID:-//" . $blogName . "//NONSGML v1.0//EN\n";
    $content .= "VERSION:2.0\n";
    $content .= "CALSCALE:GREGORIAN\n";
    $content .= "METHOD:PUBLISH\n";
    $content .= "X-WR-CALNAME:" . $blogName . "\n";
    $content .= "X-ORIGINAL-URL:" . $blogURL . "\n";
    $content .= "X-WR-CALDESC:Events for " . $blogName . "\n";
    $content .= "X-WR-TIMEZONE:".$tzlocation."\n";
    $content .= "BEGIN:VTIMEZONE\n";
    $content .= "TZID:".$tzlocation."\n";
    $content .= "X-LIC-LOCATION:".$tzlocation."\n";
    $content .= "BEGIN:STANDARD\n";
    $content .= "DTSTART:19700308T020000\n";
    $content .= "TZOFFSETFROM:".$tzoffset_standard."\n";
    $content .= "TZOFFSETTO:".$tzoffset_daylight."\n";
    $content .= "RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU\n";
    $content .= "TZNAME:".$tzname."\n";
    $content .= "END:STANDARD\n";
    $content .= "BEGIN:DAYLIGHT\n";
    $content .= "TZOFFSETFROM:".$tzoffset_daylight."\n";
    $content .= "TZOFFSETTO:".$tzoffset_standard."\n";
    $content .= "TZNAME:".$tzname_daylight."\n";
$content .="DTSTART:19701101T020000\n";
$content .="RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU\n";
    $content .= "END:DAYLIGHT\n";
    $content .= "END:VTIMEZONE\n";

    $content .= $events;
    $content .= "END:VCALENDAR";

if($getstring == 'cron' || $getstring == 'rss') {
$myFile = "icalendar.ics";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $content);
fclose($fh);
if ($getstring == 'cron') {
echo "icalendar.ics created";
} else {
$rsscron = '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">

<channel>
<title>'.$blogName.' cronless icalendar update</title>
<description>iCalendar for Events Manager Cronless Update</description>
<link>'.$blogURL.'</link>
<lastBuildDate>Mon, 28 Aug 2006 11:12:55 -0400 </lastBuildDate>
<pubDate>Tue, 29 Aug 2006 09:00:00 -0400</pubDate>
<item>
<title>You updated your icalendar feed with rss!</title>
<description>icalendar file updated</description>
<link>'.$blogURL.'/?ical</link>
<guid isPermaLink="false"> 1102345</guid>
<pubDate>Tue, 29 Aug 2006 09:00:00 -0400</pubDate>
</item>

</channel>
</rss>';
echo $rsscron;
}
exit;
} else {
    echo $content;
}
    if 
(defined('DEBUG'))
    {
        #echo "\n" . $queryEvents . "\n";    
        #echo $eventStart . "\n";
    }

    exit;
}

if (isset($_GET['ical']))
{
    add_action('init', 'iCalFeed');
}

?>
