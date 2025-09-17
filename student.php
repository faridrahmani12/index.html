<?php
include "db.php";

$allowed_ops = ['vis', 'registrer', 'slett'];
$op = isset($_GET['op']) && in_array($_GET['op'], $allowed_ops) ? $_GET['op'] : 'vis';
$msg = "";

// Registrer student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrer'])) {
    $bruker = $conn->real_escape_string($_POST['bruker']);
    $fornavn = $conn->real_escape_string($_POST['fornavn']);
    $etternavn = $conn->real_escape_string($_POST['etternavn']);
    $klasse = $conn->real_escape_string($_POST['klasse']);

    $sql = "INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) VALUES ('$bruker', '$fornavn', '$etternavn', '$klasse')";
    if ($conn->query($sql)) {
        $msg = "Ny student registrert!";
    } else {
        $msg = "Feil: " . $conn->error;
    }
}

// Slett student
if (isset($_GET['slett'])) {
    $bruker = $conn->real_escape_string($_GET['slett']);
    if ($conn->query("DELETE FROM student WHERE brukernavn='$bruker'")) {
        $msg = "Student slettet.";
    } else {
        $msg = "Feil ved sletting: " . $conn->error;
    }
}

// Hent klasser til nedtrekksmeny
$klasser = $conn->query("SELECT * FROM klasse ORDER BY klassekode");

// Hent studenter
$studenter = $conn->query("
    SELECT s.*, k.klassenavn 
    FROM student s 
    LEFT JOIN klasse k ON s.klassekode = k.klassekode
    ORDER BY s.brukernavn
");
?>
<!DOCTYPE html>
<html lang="no">
<head><meta charset="UTF-8"><title>Studenter</title></head>
<body>
  <h1>Student-modul</h1>
  <p><a href="index.php">Tilbake til meny</a></p>

  <?php if ($msg): ?>
    <p><strong><?php echo htmlspecialchars($msg); ?></strong></p>
  <?php endif; ?>

  <?php if ($op === 'registrer'): ?>
    <h2>Registrer student</h2>
    <form method="post">
      Brukernavn: <input type="text" name="bruker" required><br>
      Fornavn: <input type="text" name="fornavn" required><br>
      Etternavn: <input type="text" name="etternavn"><br>
      Klasse: 
      <select name="klasse">
        <?php while ($k = $klasser->fetch_assoc()): ?>
          <option value="<?php echo $k['klassekode']; ?>">
            <?php echo $k['klassekode'] . " - " . $k['klassenavn']; ?>
          </option>
        <?php endwhile; ?>
      </select><br>
      <input type="submit" name="registrer" value="Lagre">
    </form>
  <?php elseif ($op === 'vis'): ?>
    <h2>Alle studenter</h2>
    <table border="1" cellpadding="5">
      <tr><th>Brukernavn</th><th>Navn</th><th>Klasse</th></tr>
      <?php while ($s = $studenter->fetch_assoc()): ?>
        <tr>
          <td><?php echo $s['brukernavn']; ?></td>
          <td><?php echo $s['fornavn'] . " " . $s['etternavn']; ?></td>
          <td><?php echo $s['klassekode'] . " - " . $s['klassenavn']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php elseif ($op === 'slett'): ?>
    <h2>Slett student</h2>
    <ul>
      <?php $studenter->data_seek(0); while ($s = $studenter->fetch_assoc()): ?>
        <li>
          <?php echo $s['brukernavn'] . " - " . $s['fornavn'] . " " . $s['etternavn']; ?>
          [<a href="?slett=<?php echo urlencode($s['brukernavn']); ?>" onclick="return confirm('Slette denne studenten?')">Slett</a>]
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>
</body>
</html>
<?php
include "db.php";

$op = $_GET['op'] ?? 'vis';
$msg = "";


$conn->query("INSERT INTO klasse (klassekode, klassenavn, studiumkode)
              VALUES ('IT1', 'IT og ledelse 1. år', 'ITLED'),
                     ('IT2', 'IT og ledelse 2. år', 'ITLED'),
                     ('IT3', 'IT og ledelse 3. år', 'ITLED')
              ON DUPLICATE KEY UPDATE klassenavn=klassenavn");

// 🚀 Sett inn noen eksempelstudenter hvis tabellen er tom
$res = $conn->query("SELECT COUNT(*) AS antall FROM student");
$row = $res->fetch_assoc();
if ($row['antall'] == 0) {
    $conn->query("INSERT INTO student VALUES
        ('gb', 'Geir', 'Bjarvin', 'IT1'),
        ('mrj', 'Marius', 'Johannessen', 'IT1'),
        ('tb', 'Tove', 'Bøe', 'IT2'),
        ('ah', 'Anders', 'Hansen', 'IT3')
    ");
}
