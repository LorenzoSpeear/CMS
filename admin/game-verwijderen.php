<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Ongeldig game-ID.');
}

$stmt = $pdo->prepare('SELECT titel FROM games WHERE id = :id');
$stmt->execute(['id' => $id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    http_response_code(404);
    exit('Game niet gevonden.');
}

$_SESSION['verwijder_token'] ??= bin2hex(random_bytes(32));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['verwijder_token'], $token)) {
        http_response_code(403);
        exit('Ongeldige bevestiging.');
    }

    $stmt = $pdo->prepare('DELETE FROM games WHERE id = :id');
    $stmt->execute(['id' => $id]);

    header('Location: games.php', true, 303);
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game verwijderen | Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-[#1A1B1E] text-white">
    <main class="mx-auto max-w-2xl px-4 py-8">
        <section class="rounded-xl border border-white/10 bg-[#25282C] p-6">
            <h1 class="mb-4 text-2xl font-bold">Game verwijderen</h1>

            <p class="mb-6 text-gray-300">
                Wil je <strong class="text-white"><?= htmlspecialchars($game['titel']) ?></strong>
                definitief verwijderen?
            </p>

            <form method="post" class="flex flex-wrap items-center gap-4">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['verwijder_token']) ?>">

                <button type="submit" class="rounded-lg bg-red-700 px-4 py-2 font-semibold text-white hover:bg-red-600">
                    Definitief verwijderen
                </button>

                <a href="games.php" class="text-gray-300 hover:text-white">Annuleren</a>
            </form>
        </section>
    </main>
</body>

</html>