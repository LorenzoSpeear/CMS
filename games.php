<?php
require_once __DIR__ . '/includes/database.php';

$games = $pdo->query('SELECT id, titel, prijs, cover_afbeelding FROM games ORDER BY titel')
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#1A1B1E] text-white flex flex-col md:flex-row"> <!-- Home, Games en Over ons. -->

    <button id="menu-knop" type="button" aria-controls="hoofdmenu" aria-expanded="false"
        class="m-4 self-start rounded-lg bg-[#25282C] px-4 py-2 md:hidden">
        ☰ Menu
    </button>

    <nav id="hoofdmenu"
        class="hidden flex-col gap-3 bg-[#25282C] p-6 md:sticky md:top-0 md:flex md:h-screen md:w-64 md:shrink-0 md:self-start md:overflow-y-auto">
        <a href="index.php">
            <h1 class="block px-3 py-3 rounded-lg hover:bg-[#515356]-700 transition-colors">Ybox Shop</h1>
        </a>
        <a href="index.php" class="block px-3 py-3 rounded-lg hover:bg-[#515356] transition-colors">Home</a>
        <a href="games.php" class="block px-3 py-3 rounded-lg bg-[#686A6C] transition-colors">Games</a>
        <a href="over-ons.php" class="block px-3 py-3 rounded-lg hover:bg-[#515356] transition-colors">Over Ons</a>
    </nav>
    <main class="flex-1 p-6 "><input
            class="bg-[#25282C] border border-[#515356] rounded-full px-4 py-2 w-full max-w-xl mb-6 flex block mx-auto z-10 sticky top-6"
            id="game-zoeken" type="search" placeholder="Zoek een game…" aria-label="Zoek een game">
        <h2 class="mb-6 text-2xl font-bold">Games</h2>

        <?php if ($games === []): ?>
            <p>Er zijn nog geen games beschikbaar.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
                <?php foreach ($games as $game): ?>
                    <a data-game-kaart href="game.php?id=<?= (int) $game['id'] ?>"
                        class="w-50 rounded-xl relative overflow-hidden bg-[#25282C] ml-6">
                        <img class="w-full h-60 object-cover" src="uploads/<?= rawurlencode($game['cover_afbeelding']) ?>"
                            alt="Cover van <?= htmlspecialchars($game['titel']) ?>">
                        <div class="p-3 ">
                            <h3 class="font-bold"><?= htmlspecialchars($game['titel']) ?></h3>
                            <p>€<?= number_format((float) $game['prijs'], 2, ',', '.') ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <script src="assets/js/main.js"></script>
</body>

</html>