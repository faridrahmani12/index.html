<!DOCTYPE html>
<html lang="no">
<head>
  <meta charset="UTF-8">
  <title>PRG120V vedlikeholdsløsning</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 2rem auto; max-width: 720px; line-height: 1.6; }
    h1 { margin-bottom: 1rem; }
    section { margin-top: 2rem; }
    ul { list-style: none; padding: 0; }
    li { margin: 0.5rem 0; }
    a { text-decoration: none; color: #0c5394; }
    a:hover { text-decoration: underline; }
    code { background: #f4f4f4; padding: 0.2rem 0.4rem; border-radius: 4px; }
  </style>
</head>
<body>
  <h1>Vedlikehold av klasser og studenter</h1>
  <p>
    Velg en handling for å registrere nye data, vise eksisterende rader eller slette informasjon i tabellene
    <code>klasse</code> og <code>student</code>.
  </p>

  <section>
    <h2>Klasse</h2>
    <ul>
      <li><a href="klasse.php?op=registrer">Registrer ny klasse</a></li>
      <li><a href="klasse.php?op=vis">Vis alle klasser</a></li>
      <li><a href="klasse.php?op=slett">Slett klasse</a></li>
    </ul>
  </section>

  <section>
    <h2>Student</h2>
    <ul>
      <li><a href="student.php?op=registrer">Registrer ny student</a></li>
      <li><a href="student.php?op=vis">Vis alle studenter</a></li>
      <li><a href="student.php?op=slett">Slett student</a></li>
    </ul>
  </section>

  <section>
    <h2>Databasestruktur</h2>
    <p>
      Tabellene kan opprettes med SQL-en i <a href="sql/schema.sql">sql/schema.sql</a>. Eksempeldata finner du i
      <a href="sql/sample_data.sql">sql/sample_data.sql</a>.
    </p>
  </section>
</body>
</html>
