<?php
// Enkel databasekobling for både lokal utvikling og Dokploy.
// Lokalt kan du sette dine egne verdier i miljøvariablene DB_HOST, DB_USER, DB_PASS og DB_NAME,
// eller bruke standardverdiene under.

if (!extension_loaded('mysqli')) {
    http_response_code(500);
    echo 'MySQLi-utvidelsen er ikke tilgjengelig på serveren. Be administratoren aktivere den.';
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);

$hostEnv       = trim((string) getenv('DB_HOST')) ?: null;
$dokployEnv    = trim((string) getenv('DOKPLOY_HOST')) ?: null;
$user          = getenv('DB_USER') ?: 'farah6535';
$pass          = getenv('DB_PASS') ?: 'a999farah6535';
$db            = getenv('DB_NAME') ?: 'farah6535';

if ($hostEnv && strtoupper($hostEnv) === 'DOKPLOY_HOST' && $dokployEnv) {
    $hostEnv = $dokployEnv;
}

$hostCandidates = array_unique(array_filter([
    $hostEnv,
    $dokployEnv,
    'localhost',
    '127.0.0.1',
    'mysql',
    'mariadb',
    'db',
    'database',
    'dokploy-db',
    'dokploy-mysql',
]));

$conn = null;
$errors = [];

foreach ($hostCandidates as $host) {
    try {
        $tmp = @new mysqli($host, $user, $pass, $db);
    } catch (mysqli_sql_exception $exception) {
        $errors[$host] = $exception->getMessage();
        continue;
    }

    if ($tmp->connect_errno) {
        $errors[$host] = $tmp->connect_error;
        $tmp->close();
        continue;
    }

    $conn = $tmp;
    break;
}

if (!$conn) {
    http_response_code(500);
    echo 'Fikk ikke kontakt med databasen. ';    
    if ($hostEnv) {
        echo 'Kontroller at verdien i miljøvariabelen DB_HOST stemmer. ';
    } else {
        echo 'Forsøk å sette miljøvariabelen DB_HOST til adressen på databasen. ';
    }
    if ($errors) {
        echo 'Feilmeldinger: ';
        foreach ($errors as $host => $error) {
            echo htmlspecialchars($host . ': ' . $error) . '\n';
        }
    }
    exit;
}

$conn->set_charset('utf8mb4');
?>
