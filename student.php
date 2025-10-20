<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/db.php';

$op = $_GET['op'] ?? 'vis';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lagre'])) {
        $bruker = strtolower(trim($_POST['bruker'] ?? ''));
        $fornavn = trim($_POST['fornavn'] ?? '');
        $etternavn = trim($_POST['etternavn'] ?? '');
        $klasse = trim($_POST['klasse'] ?? '');

        if ($bruker !== '' && $fornavn !== '' && $klasse !== '') {
            $stmt = $conn->prepare('INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) VALUES (?, ?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param('ssss', $bruker, $fornavn, $etternavn, $klasse);
                if ($stmt->execute()) {
                    $msg = 'Student lagret.';
                } else {
                    $msg = 'Feil: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $msg = 'Feil: ' . $conn->error;
            }
        } else {
            $msg = 'Fyll inn alle obligatoriske felter (brukernavn, fornavn, klasse).';
        }
        $op = 'vis';
    }

    if (isset($_POST['slett'])) {
        $bruker = trim($_POST['brukernavn'] ?? '');
        if ($bruker !== '') {
            $stmt = $conn->prepare('DELETE FROM student WHERE brukernavn = ?');
            if ($stmt) {
                $stmt->bind_param('s', $bruker);
                if ($stmt->execute()) {
                    $msg = 'Student slettet.';
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
$klasserFeil = '';
$klasseResult = $conn->query('SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode');
if ($klasseResult instanceof mysqli_result) {
    while ($rad = $klasseResult->fetch_assoc()) {
        $klasser[] = $rad;
    }
    $klasseResult->free();
} elseif ($conn->error) {
    $klasserFeil = $conn->error;
}

$studenter = [];
$studenterFeil = '';
$studentResult = $conn->query('SELECT s.brukernavn, s.fornavn, s.etternavn, s.klassekode, k.klassenavn '
    . 'FROM student s LEFT JOIN klasse k ON s.klassekode = k.klassekode ORDER BY s.brukernavn');
if ($studentResult instanceof mysqli_result) {
    while ($rad = $studentResult->fetch_assoc()) {
        $studenter[] = $rad;
    }
    $studentResult->free();
} elseif ($conn->error) {
    $studenterFeil = $conn->error;
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="utf-8">
    <title>Studenter</title>
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
<h1>Administrer studenter</h1>
<p><a href="index.php">← Til hovedsiden</a></p>
<?php if ($msg): ?>
    <p><strong><?php echo htmlspecialchars($msg); ?></strong></p>
<?php endif; ?>

<?php if ($op === 'registrer'): ?>
    <?php if ($klasser): ?>
        <h2>Registrer ny student</h2>
        <form method="post">
            <label>Brukernavn
                <input type="text" name="bruker" maxlength="7" required>
            </label>
            <label>Fornavn
                <input type="text" name="fornavn" maxlength="50" required>
            </label>
            <label>Etternavn
                <input type="text" name="etternavn" maxlength="50">
            </label>
            <label>Klasse
                <select name="klasse" required>
                    <option value="" disabled selected>Velg klasse</option>
                    <?php foreach ($klasser as $rad): ?>
                        <option value="<?php echo htmlspecialchars($rad['klassekode']); ?>">
                            <?php echo htmlspecialchars($rad['klassekode'] . ' – ' . $rad['klassenavn']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="actions">
                <input type="submit" name="lagre" value="Lagre">
            </div>
        </form>
    <?php elseif ($klasserFeil): ?>
        <p class="error">Kunne ikke hente klasser: <?php echo htmlspecialchars($klasserFeil); ?></p>
    <?php else: ?>
        <p>Registrer minst én klasse før du legger til studenter.</p>
    <?php endif; ?>
<?php endif; ?>

<?php if ($op === 'slett' && $studenter): ?>
    <h2>Slett student</h2>
    <form method="post">
        <label>Velg student som skal slettes
            <select name="brukernavn" required>
                <option value="" disabled selected>Velg student</option>
                <?php foreach ($studenter as $rad): ?>
                    <option value="<?php echo htmlspecialchars($rad['brukernavn']); ?>">
                        <?php echo htmlspecialchars($rad['brukernavn'] . ' – ' . $rad['fornavn'] . ' ' . $rad['etternavn']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="actions">
            <input type="submit" name="slett" value="Slett" onclick="return confirm('Er du sikker på at du vil slette denne studenten?');">
        </div>
    </form>
<?php elseif ($op === 'slett'): ?>
    <p>Ingen studenter å slette.</p>
<?php endif; ?>

<h2>Alle studenter</h2>
<?php if ($studenterFeil): ?>
    <p class="error">Feil ved henting av studenter: <?php echo htmlspecialchars($studenterFeil); ?></p>
<?php elseif ($studenter): ?>
    <table>
        <tr><th>Brukernavn</th><th>Fornavn</th><th>Etternavn</th><th>Klasse</th></tr>
        <?php foreach ($studenter as $rad): ?>
            <?php
            $klassekode = $rad['klassekode'] ?? '';
            $klassenavn = $rad['klassenavn'] ?? '';
            $klasseTekst = $klassekode;
            if ($klassekode && $klassenavn) {
                $klasseTekst .= ' – ' . $klassenavn;
            } elseif ($klassenavn) {
                $klasseTekst = $klassenavn;
            }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($rad['brukernavn']); ?></td>
                <td><?php echo htmlspecialchars($rad['fornavn']); ?></td>
                <td><?php echo htmlspecialchars($rad['etternavn']); ?></td>
                <td><?php echo htmlspecialchars($klasseTekst); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Ingen studenter registrert ennå.</p>
<?php endif; ?>
</body>
</html>
