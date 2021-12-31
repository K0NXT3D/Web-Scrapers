<?php
/*
 * WALTRII - Website Active Link Reporting Tool
 * R. Seaverns 2021 
 * View Collected Data From WALTR2 MySQL Databse
 * Error Reporting - ON (Default)
*/
?>

<style>
body {
    background-color:#0c0c0c;
    color:#fff;
    line-height:150%;
    margin:2%;
    font-family:Roboto;
    font-weight:600;
}

a, a:visited {
    text-decoration:none;
    color:#fff;
}

a:hover {
    color:lime;
}
</style>
<h1>WALTR2 - Website Active Link Reporting Tool</h1>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// MySQL Server Config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "WALTR";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT URL, LINKS FROM URLS";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "URL: " . $row["URL"]. " - " . "Link: <a href=\"" . $row["LINKS"]. "\" target=\"_blank\" >" . $row["LINKS"]. "</a><br>";
  }
} else {
  echo "0 results";
}
$conn->close();
?>
