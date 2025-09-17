<?php
include "db.php";

$op = $_GET['op'] ?? 'vis';
$msg = "";

// Registrer klasse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrer'])) {
    $kode = $conn->real_escape_string($_POST['kode']);
    $navn = $conn->real_escape_string($_POST['navn']);
    $studium = $conn->real_escape_string($_POST['studium']);

    $sql = "INSERT INTO klasse (klassekode, klassenavn, studiumkode) VALUES ('$kode', '$navn', '$studium')";
    if ($conn->query($sql)) {
        $msg = "Ny klasse registrert!";
    } else {
        $msg = "Feil: " . $conn->error;
    }
}

// Slett klasse
if (isset($_GET['slett'])) {
    $kode = $conn->real_escape_string($_GET['slett']);
    if ($conn->query("DELETE FROM klasse WHERE klassekode='$kode'")) {
        $msg = "Klasse slettet.";
    } else {
        $msg = "Kan ikke slette – kanskje studenter er knyttet til klassen.";
    }
}

// Hent alle klasser
$klasser = $conn->query("SELECT * FROM klasse ORDER BY klassekode");
?>
<!DOCTYPE html>
<html lang="no">
<head><meta charset="UTF-8"><title>Klasser</title></head>
<body>
  <h1>Klasse-modul</h1>
  <p><a href="index.php">Tilbake til meny</a></p>

  <?php if ($msg): ?>
    <p><strong><?php echo htmlspecialchars($msg); ?></strong></p>
  <?php endif; ?>

  <?php if ($op === 'registrer'): ?>
    <h2>Registrer klasse</h2>
    <form method="post">
      Klassekode: <input type="text" name="kode" required><br>
      Klassenavn: <input type="text" name="navn" required><br>
      Studiumkode: <input type="text" name="studium" required><br>
      <input type="submit" name="registrer" value="Lagre">
    </form>
  <?php elseif ($op === 'vis'): ?>
    <h2>Alle klasser</h2>
    <table border="1" cellpadding="5">
      <tr><th>Kode</th><th>Navn</th><th>Studium</th></tr>
      <?php while ($r = $klasser->fetch_assoc()): ?>
        <tr>
          <td><?php echo $r['klassekode']; ?></td>
          <td><?php echo $r['klassenavn']; ?></td>
          <td><?php echo $r['studiumkode']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php elseif ($op === 'slett'): ?>
    <h2>Slett klasse</h2>
    <ul>
      <?php $klasser->data_seek(0); while ($r = $klasser->fetch_assoc()): ?>
        <li>
          <?php echo $r['klassekode'] . " - " . $r['klassenavn']; ?>
          [<a href="?slett=<?php echo urlencode($r['klassekode']); ?>" onclick="return confirm('Slette denne?')">Slett</a>]
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>
</body>
</html>
