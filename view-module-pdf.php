<?php

// Store the file name into variable
$file = "./activities/$_GET[module_pdf]";

// Header content type
header("Content-type: application/pdf");

header("Content-Length: " . filesize($file));

// Send the file to the browser.
readfile($file);
