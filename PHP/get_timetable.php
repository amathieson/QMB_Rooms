<?php
// Including necessary files
include_once "api_header.php";

// Retrieving events and room translations from JSON files
$events = json_decode(file_get_contents("cache/week.json"));
$translations = json_decode(file_get_contents("config.json"))->room_translations;

// Initializing an empty array to store timetable data
$timetable = [];

// Looping through each event
foreach ($events->events as $event) {
    // Checking if the room of the event matches the requested room (provided in GET parameter)
    if (strtolower(md5($event->room)) === strtolower($_GET['room'])) {
        // Storing original room name
        $room = $event->room;
        // Generating room ID
        $event->roomID = md5($event->room);
        // Translating room name if translation exists, otherwise using the original room name
        $event->room = isset($translations->$room) ? $translations->$room : $room;
        // Adding the event to the timetable array
        $timetable[] = $event;
    }
}

// Function to sort the timetable by start time
function timetableSort($a, $b)
{
    if ($a->start == $b->start) {
        return 0;
    }
    return ($a->start < $b->start) ? -1 : 1;
}

// Sorting the timetable array using the custom sorting function
uasort($timetable, "timetableSort");

// Initializing an empty array to organize events by day
$days = [];
foreach ($timetable as $event) {
    // Checking if the day array for the event's day exists, if not, creating it
    if (!isset($days[$event->day]))
        $days[$event->day] = [];

    // Adding the event to the corresponding day array
    $days[$event->day][] = $event;
}

// Getting room ID from GET parameters
$rm_ID = ($_GET['room']);

// Encoding timetable data along with pull time, week, and room name into JSON format
echo json_encode(["days" => $days, "pull_time" => $events->pull_time, "week" => $events->week, "room" => $translations->$rm_ID], JSON_PRETTY_PRINT);

// Restoring the default timezone setting
ini_restore("date.timezone");