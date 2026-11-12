<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

$paginas = $pdo->query('SELECT id, titel, slug FROM paginas ORDER BY titel')
    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina’s beheren | Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-[#1A1B1E] text-white">
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-8">
        <a href="index.php" class="text-sm text-gray-300 hover:text-white">
            ← Beheerpaneel
        </a>

        <header class="my-6 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-3xl font-bold">Pagina’s beheren</h1>
            <a href="pagina-toevoegen.php"
                class="rounded-lg bg-lime-400 px-4 py-2 font-semibold text-[#1A1B1E] hover:bg-lime-300">
                + Pagina toevoegen
            </a>
        </header>

        <?php if ($paginas === []): ?>
            <p class="rounded-xl border border-white/10 bg-[#25282C] p-4 text-gray-300">
                Er zijn nog geen pagina’s.
            </p>
        <?php else: ?>
            <div class="overflow-x-auto rounded-xl border border-white/10 bg-[#25282C]">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/5 text-gray-300">
                        <tr>
                            <th scope="col" class="px-4 py-3">Titel</th>
                            <th scope="col" class="px-4 py-3">Slug</th>
                            <th scope="col" class="px-4 py-3">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php foreach ($paginas as $pagina): ?>
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($pagina['titel']) ?></td>
                                <td class="px-4 py-3 text-gray-300"><?= htmlspecialchars($pagina['slug']) ?></td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <a href="pagina-bewerken.php?id=<?= (int) $pagina['id'] ?>"
                                        class="mr-3 text-lime-400 hover:underline">Bewerken</a>
                                    <a href="pagina-verwijderen.php?id=<?= (int) $pagina['id'] ?>"
                                        class="text-red-400 hover:underline">Verwijderen</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>