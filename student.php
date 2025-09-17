
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "db.php";
$msg = "";

$conn->query("INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) 
VALUES ('gb','Geir','Bjarvin','IT1'),
       ('mrj','Marius','Johannessen','IT1'),
       ('tb','Tove','Bøe','IT2'),
       ('ah','Anders','Hansen','IT3')
ON DUPLICATE KEY UPDATE fornavn=fornavn");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lagre'])) {
    $bruker = $conn->real_escape_string(trim($_POST['bruker']));
    $fornavn = $conn->real_escape_string(trim($_POST['fornavn']));
    $etternavn = $conn->real_escape_string(trim($_POST['etternavn']));
    $klasse = $conn->real_escape_string($_POST['klasse']);

    if ($bruker && $fornavn && $klasse) {
        if ($conn->query("INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) VALUES ('$bruker','$fornavn','$etternavn','$klasse')")) {
            $msg = "Student lagret.";
        } else {
            $msg = "Feil: " . $conn->error;
        }
    } else {
        $msg = "Fyll inn nødvendige felter.";
    }
}

if (isset($_GET['slett'])) {
    $bruker = $conn->real_escape_string($_GET['slett']);
    if ($conn->query("DELETE FROM student WHERE brukernavn='$bruker'")) $msg = "Student slettet.";
    else $msg = "Feil: " . $conn->error;
}

$klasser = $conn->query("SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode");
$studenter = $conn->query("SELECT s.brukernavn, s.fornavn, s.etternavn, s.klassekode, k.klassenavn
                           FROM student s
                           LEFT JOIN klasse k ON s.klassekode = k.klassekode
                           ORDER BY s.brukernavn");
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Studenter</title></head>
<body>
<h1>Administrer studenter</h1>
<p><a href="index.php">← Til hovedsiden</a></p>
<?php if ($msg) echo "<p><strong>$msg</strong></p>"; ?>

<h2>Ny student</h2>
<form method="post">
Brukernavn: <br><input type="text" name="bruker" required><br>
Fornavn: <br><input type="text" name="fornavn" required><br>
Etternavn: <br><input type="text" name="etternavn"><br>
Klasse: <br>
<select name="klasse">
<?php while($k = $klasser->fetch_assoc()): ?>
<option value="<?php echo htmlspecialchars($k['klassekode']); ?>">
<?php echo htmlspecialchars($k['klassekode'].' - '.$k['klassenavn']); ?>
</option>
<?php endwhile; ?>
</select><br><br>
<input type="submit" name="lagre" value="Lagre">
</form>

<h2>Alle studenter</h2>
<table border="1" cellpadding="4" cellspacing="0">
<tr><th>Brukernavn</th><th>Fornavn</th><th>Etternavn</th><th>Klasse</th><th>Slett</th></tr>
<?php while($r = $studenter->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($r['brukernavn']); ?></td>
<td><?php echo htmlspecialchars($r['fornavn']); ?></td>
<td><?php echo htmlspecialchars($r['etternavn']); ?></td>
<td><?php echo htmlspecialchars($r['klassekode'].' '.$r['klassenavn']); ?></td>
<td><a href="?slett=<?php echo urlencode($r['brukernavn']); ?>" onclick="return confirm('Slette denne?')">Slett</a></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
