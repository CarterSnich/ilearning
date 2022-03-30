<?php

/* CONSTANTS */

define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

/** @var array $subjects Subject codes. */
$subjects = array(
    'css' => 'Computer System Servicing',
    'english' => 'English',
    'filipino' => 'Filipino',
    'homeroomguidance' => 'Homeroom Guidance',
    'pe' => 'Physical Education',
    'philosophy' => 'Philosophy',
    'practicalresearch' => 'Practical Research'
);

/** @var array $subjects Subject banner background image. */
$subjectBackgroundImages = array(
    'css' => 'banner_css.jpg',
    'english' => 'banner_english.jpg',
    'filipino' => 'banner_filipino.jpg',
    'homeroomguidance' => 'banner_hrg.jpg',
    'pe' => 'banner_pe.jpg',
    'philosophy' => 'banner_philosophy.jpg',
    'practicalresearch' => 'banner_research.jpg'
);


/** @var array $error_css Subject codes. */
$errorCSS = array(
    0 => 'success',
    1 => 'warning',
    2 => 'danger',
    3 => 'danger'
);


/* FUNCTIONS */

/**
 * undocumented function summary
 *
 * Undocumented function long description
 *
 * @param Type $var Description
 * @return string Full link of the current page.
 * @throws conditon
 **/
function getFullLink()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}


/**
 * Redirect to a page.
 *
 * A function that redirects to a page using the provided location.
 *
 * @param string $location Location of the redirectory.
 * @return null
 **/
function redirectToPage(string $location)
{
    header("location: $location");
    exit();
}

/**
 * Count age using birthdate.
 *
 * A function that counts age using birthdate in `Y-m-d` format.
 *
 * @param string $birthdate Birthdate in `Y-m-d` format
 * @return int Calculated age.
 **/
function calculateAge(string $birthdate)
{
    $bd = new DateTime($birthdate);
    $bd = explode("/", date_format($bd, 'm/d/Y'));
    $age = (date("md", date("U", mktime(0, 0, 0, $bd[0], $bd[1], $bd[2])))) > date("md") ? ((date("Y") - $bd[2]) - 1) : (date("Y") - $bd[2]);
    return $age;
}

/**
 * Upload image file to folder.
 *
 * Upload image file to folder.
 *
 * @param array $file File parameter from `$_FILES`.
 * @param string $target_dir File upload directory.
 * @return boolean Returns `true` if file uploaded successfully, `false` otherwise.
 **/
function uploadImage(array $file, string $target_dir)
{
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = true;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if (getimagesize($file["tmp_name"]) !== false) {
        $uploadOk = 1;
    } else {
        $err_msg = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $err_msg = "File already exists.";
        $uploadOk = 0;
    }
    
    // Check file size
    if ($file["size"] > 1*MB) {
        $err_msg = "Image must be less than 1 MB.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        $err_msg = "Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return array(
            "success" => false,
            "msg" => $err_msg
        );
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return array(
                "success" => true,
                "msg" => null
            );
        } else {
            $err_msg = "Failed to upload file.";
            return array(
                "success" => false,
                "msg" => $err_msg
            );
        }
    }
}


/**
 * Upload module file to folder.
 *
 * Upload module file to folder.
 *
 * @param array $file File parameter from `$_FILES`.
 * @return array `success`: boolean, `msg`: string message.
 **/
function uploadModule(array $file)
{
    $moduleDir = 'activities/';
    $target_file = $moduleDir . basename($file["name"]);
    $uploadOk = true;
    // $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        $err_msg = "File already exists.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return array(
            "success" => false,
            "msg" => $err_msg
        );
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return array(
                "success" => true,
                "msg" => null
            );
        } else {
            $err_msg = "Failed to upload file.";
            return array(
                "success" => false,
                "msg" => $err_msg
            );
        }
    }
}

/**
 * Format date to 'F j, Y' format.
 *
 * Format date to 'F j, Y' format specified only for this project.
 *
 * @param string $timestamp Timestamp in string type.
 * @param string $format Date format in string type.
 * @return string Returns formatted date in string type.
 * @return mixed Returns NULL if $dateString is null or empty.
 **/
function formatDate(string $timestamp, string $format)
{
    if ($timestamp == null) return;
    $date_instance = date_create($timestamp);
    $formatted_date = date_format($date_instance, $format);
    return $formatted_date;
}


/**
 * Get human readable filesize.
 *
 * Extremely simple function to get human filesize.
 *
 * @param string $bytes
 * @param int $decimals Default 2.
 * @return mixed Returns NULL if $dateString is null or empty.
 **/
function human_filesize(string $bytes, int $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

/**
 * Upload module file to folder.
 *
 * Upload module file to folder.
 *
 * @param array $file File parameter from `$_FILES`.
 * @return array `success`: boolean, `msg`: string message.
 **/
function uploadAnswer(array $file)
{
    $moduleDir = 'submissions/';
    $target_file = $moduleDir . basename($file["name"]);
    $uploadOk = true;
    // $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        $err_msg = "File already exists.";
        $uploadOk = false;
    }

    // Check if $uploadOk is set to 0 by an error
    if (!$uploadOk) {
        return array(
            "success" => false,
            "msg" => $err_msg
        );
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return array(
                "success" => true,
                "msg" => null
            );
        } else {
            return array(
                "success" => false,
                "msg" => "Failed to upload file."
            );
        }
    }
}

/**
 * Compares dates.
 *
 * Compare if the first given date is greater than the second. Dates must be in `Y-m-d`. To compare it today, insert format instead.
 *
 * @param string $date1 Date to check.
 * @param string $date2 Date to compared to.
 * @return int 0: `$date1` is greater , 2: `$date2` is greater, 3: equals.
 **/
function dateCompare(string $date1, string $date2)
{
    $date1 = new DateTime(date($date1));
    $date2 = new DateTime(date($date2));

    if ($date1 > $date2) return 0;
    elseif ($date1 < $date2) return 1;
    else return 2;
}


/**
 * undocumented function summary
 *
 * Undocumented function long description
 *
 * @param Type $var Description
 * @return type
 * @throws conditon
 **/
function formatPhoneNumber(string $phoneNumber = null)
{
    if (preg_match('/^(\d{4})(\d{3})(\d{4})$/', $phoneNumber,  $matches)) {
        $result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        return $result;
    } else {
        return null;
    }
}
