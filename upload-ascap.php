<?php
include 'conn-d.php';

// Check if a file was uploaded
if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
    $file = $_FILES["file"]["tmp_name"];

    // Read uploaded file
    $data = array_map('str_getcsv', file($file));

    // Skip the first row (assuming it contains headers)
    array_shift($data);

    // Prepare the INSERT statement
    $sql = "INSERT INTO ascap (Party_ID_1, Party_ID_2, Party_ID, Total_Number_Of_Works, Member_Name, Work_Title, ASCAP_Work_ID, Interested_Parties, IPI_Number, Interested_Party_Status, Role, Society, Own_Percentage, Collect_Percentage, Registration_Date, Registration_Status, Surveyed_Work, ISWC_Number, Work_Licensed_By_ASCAP, Share_Licensed_By_ASCAP)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("iiiiisssssssssddssss", $partyId1, $partyId2, $partyId, $totalWorks, $memberName, $workTitle, $ascapWorkId, $interestedParties, $ipiNumber, $interestedPartyStatus, $role, $society, $ownPercentage, $collectPercentage, $registrationDate, $registrationStatus, $surveyedWork, $iswcNumber, $workLicensedByASCAP, $shareLicensedByASCAP);

        // Insert data into the "ascap" table
        foreach ($data as $row) {
            $partyId1 = intval($row[0]);
            $partyId2 = intval($row[1]);
            $partyId = intval($row[2]);
            $totalWorks = intval($row[3]);
            $memberName = $row[4];
            $workTitle = $row[5];
            $ascapWorkId = $row[6];
            $interestedParties = $row[7];
            $ipiNumber = $row[8];
            $interestedPartyStatus = $row[9];
            $role = $row[10];
            $society = $row[11];
            $ownPercentage = floatval($row[12]);
            $collectPercentage = floatval($row[13]);
            $registrationDate = $row[14];
            $registrationStatus = $row[15];
            $surveyedWork = $row[16];
            $iswcNumber = $row[17];
            $workLicensedByASCAP = $row[18];
            $shareLicensedByASCAP = $row[19];

            // Execute the prepared statement
            $stmt->execute();
        }

        echo "Data inserted successfully.";

        // Close the prepared statement
        $stmt->close();
        // Redirect to ascap.php
        header("Location: ascap.php");
        exit;
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    echo "No file uploaded or an error occurred.";
}
