<?php
include_once "api_header.php";
echo json_encode(json_decode(file_get_contents("config.json"))->room_notices);