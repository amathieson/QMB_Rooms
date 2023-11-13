<?php
include_once "api_header.php";
$events = json_decode(file_get_contents("cache/week.json"));
$translations = json_decode(file_get_contents("room_translations.json"));
$timetable = [];
foreach ($events->events as $event) {
    if (strtolower(md5($event->room)) === strtolower($_GET['room'])) {
        $room = $event->room;
        $event->roomID = md5($event->room);
        $event->room = isset($translations->$room) ? $translations->$room : $room;
        $timetable[] = $event;
    }
}

function timetableSort($a, $b)
{
    if ($a->start == $b->start) {
        return 0;
    }
    return ($a->start < $b->start) ? -1 : 1;
}
uasort($timetable, "timetableSort");
$days = [];
foreach ($timetable as $event) {
    if (!isset($days[$event->day]))
        $days[$event->day] = [];

    $days[$event->day][] = $event;
}

$rm_ID = ($_GET['room']);
echo json_encode(["days"=>$days,"pull_time"=>$events->pull_time, "week"=>$events->week, "room"=>$translations->$rm_ID], JSON_PRETTY_PRINT);
ini_restore("date.timezone");