<?php
require_once __DIR__ . '/includes/database.php';

$gameVelden = 'id, titel, beschrijving, prijs, cover_afbeelding, banner_afbeelding';

$games = $pdo->query(
    "SELECT $gameVelden
     FROM games
     ORDER BY aangemaakt_op DESC, id DESC
     LIMIT 4"
)->fetchAll(PDO::FETCH_ASSOC);

$willekeurigeGames = $pdo->query(
    "SELECT $gameVelden
     FROM games
     ORDER BY RAND()
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$uitgelichteGame = $pdo->query(
    "SELECT $gameVelden
     FROM games
     WHERE banner_afbeelding IS NOT NULL AND banner_afbeelding <> ''
     ORDER BY aangemaakt_op DESC, id DESC
     LIMIT 1"
)->fetch(PDO::FETCH_ASSOC) ?: ($games[0] ?? null);
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
        <a href="/CMS/admin/">
            <h1 class="block px-3 py-3 rounded-lg hover:bg-[#515356]-700 transition-colors">Ybox Shop</h1>
        </a>
        <a href="index.php" class="block px-3 py-3 rounded-lg bg-[#686A6C] transition-colors">Home</a>
        <a href="games.php" class="block px-3 py-3 rounded-lg hover:bg-[#515356] transition-colors">Games</a>
        <a href="over-ons.php" class="block px-3 py-3 rounded-lg hover:bg-[#515356] transition-colors">Over Ons</a>
    </nav>
    <script src="assets/js/main.js"></script>
    <main class="min-w-0 flex-1 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php if ($games === []): ?>
                <p>Er zijn nog geen games beschikbaar.</p>
            <?php endif; ?>

            <?php foreach ($games as $positie => $game): ?>
                <section class="bg-[#25282C] rounded-xl relative overflow-hidden<?= $positie % 2 === 0 ? ' ml-6' : '' ?>">
                    <a href="game.php?id=<?= (int) $game['id'] ?>">
                        <img class="w-full h-85 object-cover"
                            src="uploads/<?= rawurlencode($game['banner_afbeelding'] ?: $game['cover_afbeelding']) ?>"
                            alt="Afbeelding van <?= htmlspecialchars($game['titel']) ?>">

                        <div class="absolute bottom-0 left-0 w-full p-6 bg-gradient-to-t from-black/90 to-transparent">
                            <h2 class="text-2xl font-bold"><?= htmlspecialchars($game['titel']) ?></h2>
                            <p class="text-sm text-gray-300 mt-2"><?= htmlspecialchars($game['beschrijving']) ?></p>
                        </div>
                    </a>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="mt-8">
            <section>
                <div class="p-6 text-2xl ">
                    <h2 class="font-bold">Top paid games</h2>
                    <p class="text-sm text-gray-300 mt-2">Trending games you don't want to miss</p>
                </div>
            </section>

            <section class="ml-6">
                <?php if ($willekeurigeGames): ?>
                    <div class="flex gap-6 overflow-x-auto md:[scrollbar-width:none]">
                        <?php foreach ($willekeurigeGames as $game): ?>
                            <a data-game-kaart href="game.php?id=<?= (int) $game['id'] ?>"
                                class="w-50 shrink-0 rounded-xl relative overflow-hidden bg-[#25282C] ml-6"> <img
                                    class="w-full h-60 object-cover"
                                    src="uploads/<?= rawurlencode($game['cover_afbeelding']) ?>"
                                    alt="Cover van <?= htmlspecialchars($game['titel']) ?>">
                                <div class="p-3 ">
                                    <h3 class="font-bold"><?= htmlspecialchars($game['titel']) ?></h3>
                                    <p>€<?= number_format((float) $game['prijs'], 2, ',', '.') ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-[#25282C]">
                        <p class="text-sm text-gray-300">Er zijn nog geen betaalde games beschikbaar.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <div class="mt-8">
            <section>
                <div class="p-6 text-2xl ">
                    <h2 class="font-bold">Recently added to Game Pass</h2>
                    <p class="text-sm text-gray-300 mt-2">Explore what's new with Game Pass</p>
                </div>
            </section>

            <section class="w-50 rounded-xl relative overflow-hidden ml-6">
                <div class="p-3 bg-[#25282C]">
                    <p class="text-sm text-gray-300">Er zijn nog geen Game Pass-games ingesteld.</p>
                </div>
            </section>
        </div>

        <div class="mt-16">
            <?php if ($uitgelichteGame): ?>
                <section class="bg-[#25282C] rounded-xl relative overflow-hidden">
                    <a href="game.php?id=<?= (int) $uitgelichteGame['id'] ?>">
                        <img class="w-full h-85 object-cover"
                            src="uploads/<?= rawurlencode($uitgelichteGame['banner_afbeelding'] ?: $uitgelichteGame['cover_afbeelding']) ?>"
                            alt="Afbeelding van <?= htmlspecialchars($uitgelichteGame['titel']) ?>">

                        <div class="absolute bottom-0 left-0 w-full p-6 bg-gradient-to-t from-black/90 to-transparent">
                            <h2 class="text-2xl font-bold"><?= htmlspecialchars($uitgelichteGame['titel']) ?></h2>
                            <p class="text-sm text-gray-300 mt-2">
                                <?= htmlspecialchars($uitgelichteGame['beschrijving']) ?>
                            </p>
                        </div>
                    </a>
                </section>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>