<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/db.php';

$op = $_GET['op'] ?? 'vis';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lagre'])) {
        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $navn = trim($_POST['navn'] ?? '');
        $studium = trim($_POST['studium'] ?? '');

        if ($kode !== '' && $navn !== '' && $studium !== '') {
            $stmt = $conn->prepare('INSERT INTO klasse (klassekode, klassenavn, studiumkode) VALUES (?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param('sss', $kode, $navn, $studium);
                if ($stmt->execute()) {
                    $msg = 'Klasse lagret.';
                } else {
                    $msg = 'Feil: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $msg = 'Feil: ' . $conn->error;
            }
        } else {
            $msg = 'Fyll inn alle feltene.';
        }
        $op = 'vis';
    }

    if (isset($_POST['slett'])) {
        $kode = trim($_POST['klassekode'] ?? '');
        if ($kode !== '') {
            $stmt = $conn->prepare('DELETE FROM klasse WHERE klassekode = ?');
            if ($stmt) {
                $stmt->bind_param('s', $kode);
                if ($stmt->execute()) {
                    $msg = 'Klasse slettet.';
                } else {
                    $msg = 'Feil: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $msg = 'Feil: ' . $conn->error;
            }
        }
        $op = 'vis';
    }
}

$klasser = [];
$klasseFeil = '';
$result = $conn->query('SELECT klassekode, klassenavn, studiumkode FROM klasse ORDER BY klassekode');
if ($result instanceof mysqli_result) {
    while ($rad = $result->fetch_assoc()) {
        $klasser[] = $rad;
    }
    $result->free();
} elseif ($conn->error) {
    $klasseFeil = $conn->error;
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="utf-8">
    <title>Klasser</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem auto; max-width: 720px; line-height: 1.5; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #ccc; padding: 0.5rem; text-align: left; }
        h1 { margin-bottom: 0.5rem; }
        form { margin-top: 1rem; }
        label { display: block; margin-top: 0.5rem; }
        input[type="text"], select { width: 100%; padding: 0.4rem; }
        .actions { margin-top: 1rem; display: flex; gap: 0.5rem; }
        .error { color: #b00020; }
    </style>
</head>
<body>
<h1>Administrer klasser</h1>
<p><a href="index.php">← Til hovedsiden</a></p>
<?php if ($msg): ?>
    <p><strong><?php echo htmlspecialchars($msg); ?></strong></p>
<?php endif; ?>

<?php if ($op === 'registrer'): ?>
    <h2>Registrer ny klasse</h2>
    <form method="post">
        <label>Klassekode
            <input type="text" name="kode" maxlength="5" required>
        </label>
        <label>Klassenavn
            <input type="text" name="navn" maxlength="50" required>
        </label>
        <label>Studiumkode
            <input type="text" name="studium" maxlength="50" required>
        </label>
        <div class="actions">
            <input type="submit" name="lagre" value="Lagre">
        </div>
    </form>
<?php endif; ?>

<?php if ($op === 'slett' && $klasser): ?>
    <h2>Slett klasse</h2>
    <form method="post">
        <label>Velg klasse som skal slettes
            <select name="klassekode" required>
                <option value="" disabled selected>Velg klasse</option>
                <?php foreach ($klasser as $rad): ?>
                    <option value="<?php echo htmlspecialchars($rad['klassekode']); ?>">
                        <?php echo htmlspecialchars($rad['klassekode'] . ' – ' . $rad['klassenavn']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="actions">
            <input type="submit" name="slett" value="Slett" onclick="return confirm('Er du sikker på at du vil slette denne klassen?');">
        </div>
    </form>
<?php elseif ($op === 'slett'): ?>
    <p>Ingen klasser å slette.</p>
<?php endif; ?>

<h2>Alle klasser</h2>
<?php if ($klasseFeil): ?>
    <p class="error">Feil ved henting av klasser: <?php echo htmlspecialchars($klasseFeil); ?></p>
<?php elseif ($klasser): ?>
    <table>
        <tr><th>Kode</th><th>Navn</th><th>Studium</th></tr>
        <?php foreach ($klasser as $rad): ?>
            <tr>
                <td><?php echo htmlspecialchars($rad['klassekode']); ?></td>
                <td><?php echo htmlspecialchars($rad['klassenavn']); ?></td>
                <td><?php echo htmlspecialchars($rad['studiumkode']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Ingen klasser registrert ennå.</p>
<?php endif; ?>
</body>
</html>
