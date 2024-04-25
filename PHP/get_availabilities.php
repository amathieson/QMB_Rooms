<?php
include_once "api_header.php";
$events = json_decode(file_get_contents("cache/week.json"));
$translations = json_decode(file_get_contents("config.json"))->room_translations;
$rooms = [];
foreach ($events->rooms as $room) {
    $roomName = isset($translations->$room) ? $translations->$room : $room;
    $rooms[$roomName] = json_decode(json_encode(["start"=>PHP_INT_MAX,"end"=>0, "room"=>$roomName, "roomID"=>md5($room)]));
}
foreach ($events->events as $event) {
    $room = $event->room;
    $roomName = isset($translations->$room) ? $translations->$room : $room;
    if (!isset($rooms[$roomName])) {
        $rooms[$roomName] = json_decode(json_encode(["start"=>PHP_INT_MAX,"end"=>0, "room"=>$roomName, "roomID"=>md5($event->room)]));
    }
    if (time() <= $event->end && $rooms[$roomName]->start > $event->start) {
        $event->roomID = md5($event->room);
        $event->room = $roomName;
        $rooms[$roomName] = $event;
    }
}
echo json_encode(["rooms"=>$rooms,"pull_time"=>$events->pull_time], JSON_PRETTY_PRINT);
ini_restore("date.timezone");