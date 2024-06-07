<?php
header("content-type: application/json");
header("Access-Control-Allow-Origin: *");
if (!file_exists("cache/week.json")) {
    echo json_encode([
        "error" => "Timetable cache missing!"
    ], JSON_PRETTY_PRINT);
    return;
}
ini_set("date.timezone", "Europe/London");