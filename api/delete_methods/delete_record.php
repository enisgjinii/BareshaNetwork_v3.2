<?php
include '../../conn-d.php';
header('Content-Type: application/json');

/**
 * Escapes a string for use in a SQL statement.
 *
 * @param Connection $conn The database connection object.
 * @param string $value The string value to be escaped.
 * @return string The escaped string.
 */
function escape_string($conn, $value)
{
  return $conn->real_escape_string($value);
}

$response = ['status' => 'error', 'message' => 'Unexpected error.'];

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
  $id = (int)$_GET['id'];

  // Fetch the record to be deleted
  $select_query = "SELECT * FROM yinc WHERE id=?";
  if ($stmt = $conn->prepare($select_query)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();

      // Insert the record into recovery_yinc
      $insert_query = "INSERT INTO recovery_yinc (id, kanali, shuma, data, pershkrimi, lloji, pagoi, linku_i_kenges) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
      if ($insert_stmt = $conn->prepare($insert_query)) {
        $insert_stmt->bind_param(
          "isdsdsss",
          $row['id'],
          $row['kanali'],
          $row['shuma'],
          $row['data'],
          $row['pershkrimi'],
          $row['lloji'],
          $row['pagoi'],
          $row['linku_i_kenges']
        );

        if ($insert_stmt->execute()) {
          // Delete the record from yinc
          $delete_query = "DELETE FROM yinc WHERE id=?";
          if ($delete_stmt = $conn->prepare($delete_query)) {
            $delete_stmt->bind_param("i", $id);
            if ($delete_stmt->execute()) {
              $response = ['status' => 'success', 'message' => 'Rekordi u fshi me sukses.'];
            } else {
              $response = ['status' => 'error', 'message' => 'Gabim gjatë fshirjes së regjistrave: ' . $delete_stmt->error];
            }
          } else {
            $response = ['status' => 'error', 'message' => 'Gabim gjatë përgatitjes së deklaratës së fshirjes: ' . $conn->error];
          }
        } else {
          $response = ['status' => 'error', 'message' => 'Gabim gjatë lëvizjes së regjistrave në tabelën e rikuperimit: ' . $insert_stmt->error];
        }
      } else {
        $response = ['status' => 'error', 'message' => 'Gabim gjatë përgatitjes së deklaratës së futjes: ' . $conn->error];
      }
    } else {
      $response = ['status' => 'error', 'message' => 'Regjistri nuk u gjet.'];
    }
  } else {
    $response = ['status' => 'error', 'message' => 'Gabim gjatë përgatitjes së deklaratës së zgjedhjes: ' . $conn->error];
  }
} else {
  $response = ['status' => 'error', 'message' => 'Parametri ID është i pavlefshëm ose mungon.'];
}

echo json_encode($response);
$conn->close();
