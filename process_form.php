<?php
include 'conn-d.php';

if (isset($_POST['ruaj'])) {
  // Validate and sanitize inputs
  $stafi = filter_var($_POST['stafi'], FILTER_VALIDATE_INT);
  $shuma = filter_var($_POST['shuma'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $data = isset($_POST['data']) ? htmlspecialchars($_POST['data']) : '';
  $pershkrimi = isset($_POST['pershkrimi']) ? filter_input(INPUT_POST, 'pershkrimi', FILTER_SANITIZE_STRING) : '';
  $linku = filter_var($_POST['youtubeLinks'], FILTER_SANITIZE_URL);

  // Split the links into an array
  $linksArray = array_map('trim', explode(',', $linku));
  
  // Limit the number of links to 6
  $limitedLinksArray = array_slice($linksArray, 0, 6);
  
  // Combine the limited links back into a string
  $limitedLinksString = implode(',', $limitedLinksArray);
  
  // Now, $limitedLinksString contains up to 6 YouTube links
  
  if ($stafi === false || $shuma === false || empty($data) || empty($linku)) {
    echo "Invalid input data.";
    exit;
  }

  $gstai = $conn->query("SELECT * FROM klientet WHERE id='$stafi'");
  $gstai2 = mysqli_fetch_array($gstai);

  // Use prepared statement to prevent SQL injection
  $stmt = $conn->prepare("INSERT INTO yinc (kanali, shuma, pershkrimi, data, linku_i_kenges) VALUES (?, ?, ?, ?, ?)");

  if ($stmt) {
    // Bind parameters
    $stmt->bind_param("sssss", $stafi, $shuma, $pershkrimi, $data, $limitedLinksString);

    // Execute the statement
    if ($stmt->execute()) {
      header("Location: yinc.php");
    } else {
      // Log the error
      error_log("Error executing SQL statement: " . $stmt->error);
      echo "An unexpected error occurred. Please try again later.";
    }

    // Close the statement
    $stmt->close();
  } else {
    // Log the error
    error_log("Error preparing SQL statement: " . $conn->error);
    echo "An unexpected error occurred. Please try again later.";
  }
}
