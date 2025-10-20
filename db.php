<?php
// Tilkoblingsinformasjon kan settes via miljøvariabler i Dokploy
// (DB_HOST, DB_USER, DB_PASS, DB_NAME). Dersom disse ikke er satt
// brukes fornuftige standardverdier for lokal utvikling og de
// oppgitte Dokploy-verdiene.
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

$isLocal = in_array(php_sapi_name(), ['cli', 'cli-server'])
    || in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']);

if (!$host || !$user || !$db) {
    if ($isLocal) {
        // Lokal utvikling – tilpass etter eget utviklingsmiljø
        $host = $host ?: '127.0.0.1';
        $user = $user ?: 'root';
        $pass = $pass ?: '';
        $db   = $db   ?: 'skole';
    } else {
        // Dokploy – verdier gitt i oppgaven
        $host = $host ?: 'mysql.dokploy.no';
        $user = $user ?: 'farah6535';
        $pass = $pass ?: 'a999farah6535';
        $db   = $db   ?: 'farah6535';
    }
}

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_errno) {
    die('Feil ved tilkobling: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// Sørg for at nødvendige tabeller finnes
$conn->query(
    "CREATE TABLE IF NOT EXISTS klasse (
        klassekode CHAR(5) NOT NULL,
        klassenavn VARCHAR(50) NOT NULL,
        studiumkode VARCHAR(50) NOT NULL,
        PRIMARY KEY (klassekode)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$conn->query(
    "CREATE TABLE IF NOT EXISTS student (
        brukernavn CHAR(7) NOT NULL,
        fornavn VARCHAR(50) NOT NULL,
        etternavn VARCHAR(50) NOT NULL,
        klassekode CHAR(5) NOT NULL,
        PRIMARY KEY (brukernavn),
        CONSTRAINT fk_student_klasse FOREIGN KEY (klassekode)
            REFERENCES klasse (klassekode)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

// Grunndata slik at applikasjonen har noe innhold fra start
$conn->query(
    "INSERT INTO klasse (klassekode, klassenavn, studiumkode) VALUES
        ('IT1', 'IT og ledelse 1. år', 'ITLED'),
        ('IT2', 'IT og ledelse 2. år', 'ITLED'),
        ('IT3', 'IT og ledelse 3. år', 'ITLED')
     ON DUPLICATE KEY UPDATE klassenavn = VALUES(klassenavn), studiumkode = VALUES(studiumkode)"
);

$conn->query(
    "INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) VALUES
        ('gb', 'Geir', 'Bjarvin', 'IT1'),
        ('mrj', 'Marius', 'Johannessen', 'IT1'),
        ('tb', 'Tove', 'Bøe', 'IT2')
     ON DUPLICATE KEY UPDATE fornavn = VALUES(fornavn), etternavn = VALUES(etternavn), klassekode = VALUES(klassekode)"
);
?>
