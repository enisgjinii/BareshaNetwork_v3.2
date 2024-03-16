<?php
include 'conn-d.php';
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
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
  $id = escape_string($conn, $_GET['id']);
  $select_query = "SELECT * FROM yinc WHERE id=?";
  if ($stmt = $conn->prepare($select_query)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $insert_query = "INSERT INTO recovery_yinc (id, kanali, shuma, data, pershkrimi, lloji, pagoi, linku_i_kenges) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
      if ($stmt = $conn->prepare($insert_query)) {
        $stmt->bind_param("isdsdsss", $row['id'], $row['kanali'], $row['shuma'], $row['data'], $row['pershkrimi'], $row['lloji'], $row['pagoi'], $row['linku_i_kenges']);
        if ($stmt->execute()) {
          $delete_query = "DELETE FROM yinc WHERE id=?";
          if ($stmt = $conn->prepare($delete_query)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
              header("Location: yinc.php");
              exit();
            } else {
              echo "Gabim gjatë fshirjes së regjistrave: " . $stmt->error;
            }
          } else {
            echo "Gabim gjatë përgatitjes së deklaratës së fshirjes: " . $conn->error;
          }
        } else {
          echo "Gabim gjatë lëvizjes së regjistrave në tabelën e rikuperimit: " . $stmt->error;
        }
      } else {
        echo "Gabim gjatë përgatitjes së deklaratës së futjes: " . $conn->error;
      }
    } else {
      echo "Regjistri nuk u gjet.";
    }
  } else {
    echo "Gabim gjatë përgatitjes së deklaratës së zgjedhjes: " . $conn->error;
  }
} else {
  echo "Parametri ID është i pavlefshëm ose mungon.";
}
$conn->close();
