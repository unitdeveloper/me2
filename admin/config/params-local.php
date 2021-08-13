<?php

$whitelist = array(
    '127.0.0.1',
    '::1'
);


return [
    "api" => !in_array($_SERVER['REMOTE_ADDR'], $whitelist)? "https://api.ewinl.com" : "http://127.0.0.1:3000"
];
