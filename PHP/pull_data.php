<?php
$rootDir = "/var/www/html/qmb/";
//$rootDir = "";
header("content-type: application/json");
ini_set("date.timezone", "Europe/London");
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);
if (!file_exists($rootDir . "cache"))
    mkdir($rootDir . "cache");
$config = json_decode(file_get_contents("config.json"));

$_AccessPath = $config->timetable_endpoint . "&identifier=" . implode("&identifier=", $config->rooms);

$events = [];
$html = file_get_contents($_AccessPath, false, stream_context_create($arrContextOptions));
$modules = json_decode(file_get_contents($rootDir . "cache/modules.json"));
if($modules===null)
    $modules = new stdClass();

$doc = new DOMDocument();
@$doc->loadHTML($html);
$xpath = new DOMXPath($doc);

// Get all the tables with class 'spreadsheet'
$tables = $xpath->query("//table[contains(@class, 'spreadsheet')]");

// Loop through the tables and extract data
$room_name = "";
$week = [
    "number"=>"",
    "dates"=>""
];
$rooms = [];
foreach ($tables as $table) {
    // Get the day of the week
    $dayEl = $xpath->query("preceding-sibling::p[1]/span", $table)->item(0);
    $day = $dayEl->nodeValue;
    if ($day === "Monday") {
        // Get the table element preceding the "Monday" element
        $headTable = $xpath->query("preceding-sibling::table[1]", $dayEl->parentNode)->item(0);

        // If a table is found, extract the room name and week number
        if ($headTable) {
            $rows = $xpath->query(".//tr", $headTable);

            // Extract room name and week number from the first row (assuming it's the header row)
            if ($rows->length > 0) {
                $headerRow = $rows->item(10);
                $columns = $xpath->query(".//td", $headerRow);


                // Extract room name and week number from the appropriate columns (adjust index as needed)
                $room = $xpath->query(".//span", $columns->item(2))->item(2)->nodeValue; // Adjust the index as needed
                $weekNumber = $xpath->query(".//span", $columns->item(3))->item(2)->nodeValue; // Adjust the index as needed
                $dates = $xpath->query(".//span", $columns->item(3))->item(4)->nodeValue; // Adjust the index as needed

                $room_name = $room;
                $week = [
                    "number"=>$weekNumber,
                    "dates"=>$dates
                ];
                if (!in_array($room, $rooms)) {
                    $rooms[] = $room;
                }
            }
        }

    }

    // Get the rows of the table
    $rows = $xpath->query(".//tr", $table);
    $days = ["Monday"=>0, "Tuesday"=>1, "Wednesday"=>2, "Thursday"=>3, "Friday"=>4, "Saturday"=>5, "Sunday"=>6];
    $week_start = strtotime(explode("-", $week['dates'])[0]);

    // Loop through the rows and extract data for each activity
    for ($i = 1; $i < $rows->length; $i++) {
        $row = $rows->item($i);
        $columns = $xpath->query(".//td", $row);

        // Extract the information you need from the columns
        $activity = $columns->item(0)->nodeValue;
        $type = $columns->item(1)->nodeValue;
        $start = $columns->item(2)->nodeValue;
        $end = $columns->item(3)->nodeValue;
        $duration = $columns->item(4)->nodeValue;
        $weeks = $columns->item(5)->nodeValue;
        $staff = $columns->item(6)->nodeValue;
        $room = $columns->item(7)->nodeValue;
        $event = [
            "day"=>$day,
            "title"=>$activity,
            "type"=>$type,
            "start"=>strtotime($start . " " . date("d M y", $week_start + ($days[$day]*24*60*60))),
            "end"=>strtotime($end . " " . date("d M y", $week_start + ($days[$day]*24*60*60))),
            "staff"=>$staff,
            "room"=>$room_name,
            "week"=>$week,
            "module"=>[]
        ];
        $pattern = '/^[A-Z]{2}\d{5}$/';
        $module_code = substr($activity, 0, 7);
        if (preg_match($pattern, $module_code)) {
            if (!isset($modules->$module_code)) {
                $html1 = file_get_contents($config->modules_endpoint . $module_code);
                if ($html1) {
                    $doc1 = new DOMDocument();
                    @$doc1->loadHTML($html1);
                    $xpath1 = new DOMXPath($doc1);
                    $modules->$module_code = $xpath1->query("//h1[contains(@class, 'hero__title')]")->item(0)->textContent;
                }
            }
            $event["module"] = [
              "code" => $module_code,
              "name" => $modules->$module_code
            ];
        }
        $events[] = $event;
    }
}

$curr_week = json_decode(file_get_contents($rootDir . "cache/week.json"));
if ($curr_week->week->number != $week['number']) {
    rename($rootDir . "cache/week.json", $rootDir . "cache/history/week_" . $curr_week->week->number . ".json");
}

file_put_contents($rootDir . "cache/week.json", json_encode(["pull_time"=>time(), "week"=>$week, "events"=>$events, "rooms"=>$rooms]));
file_put_contents($rootDir . "cache/modules.json", json_encode($modules));
echo "Timetable cache updated - week: " . $week['number'] . " - " . sizeof($events) . " events loaded.";
ini_restore("date.timezone");