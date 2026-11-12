<?php
require_once __DIR__ . '/includes/database.php';

$stmt = $pdo->prepare('SELECT titel, inhoud FROM paginas WHERE slug = :slug');
$stmt->execute(['slug' => 'over-ons']);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <a href="games.php" class="block px-3 py-3 rounded-lg hover:bg-[#515356] transition-colors">Games</a>
        <a href="over-ons.php" class="block px-3 py-3 rounded-lg bg-[#686A6C] transition-colors">Over Ons</a>
    </nav>
    <main class="flex-1 p-6 ">
<?php if ($pagina): ?>
    <h1 class="mb-4 text-2xl font-bold"><?= htmlspecialchars($pagina['titel']) ?></h1>
    <p class="whitespace-pre-line"><?= htmlspecialchars($pagina['inhoud']) ?></p>
<?php else: ?>
    <p>Deze pagina is nog niet beschikbaar.</p>
<?php endif; ?>
        <script src="assets/js/main.js"></script>
    </main>
</body>

</html>