<?php
// Sjekk om vi kjører lokalt eller på Dokploy
if (php_sapi_name() == "cli-server" || $_SERVER['SERVER_NAME'] == "localhost") {
    // Lokal utvikling (XAMPP/MAMP)
   $host = "mysql123.dokploy.no";      // Dokploy host
$user = "app123_user";               // Dokploy databasebruker
$pass = "abc123";                    // Dokploy passord
$db   = "skole";                     // databasenavn på Dokploy

} else {
    // Dokploy – bytt ut med info fra Dokploy databasen din
    $host = "DOKPLOY_HOST";      // f.eks. usn-db.example.com
    $user = "DOKPLOY_USER";      // brukernavn Dokploy gir deg
    $pass = "DOKPLOY_PASSWORD";  // passord Dokploy gir deg
    $db   = "skole";             // databasen du opprettet på Dokploy
}

// Opprett MySQL-tilkobling
$conn = new mysqli($host, $user, $pass, $db);

// Sjekk tilkobling
if ($conn->connect_error) {
    die("Feil ved tilkobling: " . $conn->connect_error);
}

// Sett tegnsett
$conn->set_charset("utf8mb4");
?>
