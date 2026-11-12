<?php
require_once __DIR__ . '/includes/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$game = false;

if ($id && $id > 0) {
    $stmt = $pdo->prepare(
        'SELECT games.titel, games.prijs, games.beschrijving, games.releasedatum,
                games.cover_afbeelding, games.banner_afbeelding, genres.naam AS genre
         FROM games
         JOIN genres ON genres.id = games.genre_id
         WHERE games.id = :id'
    );
    $stmt->execute(['id' => $id]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$game) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($game ? $game['titel'] : 'Game niet gevonden') ?> | Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#1A1B1E] text-white">
    <main class="mx-auto max-w-5xl p-6">
        <a href="games.php" class="mb-6 inline-block underline">Terug naar games</a>

        <?php if (!$game): ?>
            <h1 class="text-2xl font-bold">Game niet gevonden</h1>
        <?php else: ?>
            <h1 class="mb-6 text-3xl font-bold"><?= htmlspecialchars($game['titel']) ?></h1>

            <?php if (!empty($game['banner_afbeelding'])): ?>
                <img class="mb-6 h-56 w-full rounded-xl object-cover"
                     src="uploads/<?= rawurlencode($game['banner_afbeelding']) ?>" alt="">
            <?php endif; ?>

            <div class="grid gap-6 md:grid-cols-[220px_1fr]">
                <img class="w-full max-w-[220px] rounded-xl"
                     src="uploads/<?= rawurlencode($game['cover_afbeelding']) ?>"
                     alt="Cover van <?= htmlspecialchars($game['titel']) ?>">

                <section class="space-y-3">
                    <p><strong>Prijs:</strong> €<?= number_format((float) $game['prijs'], 2, ',', '.') ?></p>
                    <p><strong>Genre:</strong> <?= htmlspecialchars($game['genre']) ?></p>
                    <p><strong>Releasedatum:</strong> <?= htmlspecialchars($game['releasedatum']) ?></p>
                    <p><?= nl2br(htmlspecialchars($game['beschrijving'])) ?></p>
                </section>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>