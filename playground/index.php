<?php
// If the form is submitted, process the file uploads
$uploadDir = "uploads/";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['userfiles'])) {
    // Create the uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $uploadedImages = []; // Array to hold paths of successfully uploaded images

    $upload_err = "";
    var_dump($_FILES);
    $filename = $_FILES['userfiles']['name'];
    $targetFile = $uploadDir . uniqid() . $filename;
    // Move the file to the permanent location
    if (move_uploaded_file($_FILES['userfiles']['tmp_name'], $targetFile)) {
        $uploadedImages[] = $targetFile;
    } else {
        echo "\n".$_FILES['userfiles']['full_path'];
        echo "\nError uploading file: $filename";
    }
    /*
    // Loop through each uploaded file
    foreach ($_FILES['userfiles']['tmp_name'] as $key => $tmpName) {
        // Check for any upload errors
        if ($_FILES['userfiles']['error'][$key] === UPLOAD_ERR_OK) {
            $filename = basename($_FILES['userfiles']['name'][$key]);
            $targetFile = $uploadDir . uniqid() . $filename;
            // Move the file to the permanent location
            if (move_uploaded_file($tmpName, $targetFile)) {
                $uploadedImages[] = $targetFile;
            } else {
                $_SESSION['upload_errors'] .= "Error uploading file: $filename,";
                // echo "Error uploading file: $filename";
            }
        }
    }
    */

    // Save errors and/or success info in session so they persist after redirect

    // Redirect to avoid form re-submission on refresh
    header("Location: /playground");
    exit();
}
echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Input Using PHP</title>
</head>

<body>

    <div id="messages">
        <?php
        // Display error messages if any
        if (isset($_SESSION['upload_errors'])) {
            echo $_SESSION['upload_errors'];
        }
        ?>
    </div>
    <!-- action set to empty string so that it refers back to itself -->

    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="userfiles" value="">
        <!-- <input type="file" name="userfiles[]" value="" multiple=""> -->
        <input type="submit" name="submit" id="upload">
    </form>

    <pre>

<?php
// $mysqli = new mysqli('localhost', 'root', '', 'test') or die($mysqli->connect_error);
// $table = 'images';

// //$_FILES global associative array
// if (isset($_FILES['userfiles'])) {
//     echo "userfiles set";
//     $file_array = $_FILES['userfiles'];
//     print_r($file_array);
// } else {
//     echo "userfiles not set";
// }

?>
    </pre>

    <div id="image-display">

        <?php
        $files = glob($uploadDir . '*{.png,.jpeg,.jpg,.webp}*', GLOB_BRACE);

        foreach ($files as $image) {
            // Display each image with an <img> tag
            echo "<img src='$image' alt='Uploaded Image' style='width:300px; height:300px; object-fit:cover; margin:10px;' />";
        }
        ?>
    </div>
</body>

</html>