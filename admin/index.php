<?php
require_once('../includes/auth.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<main class="min-h-screen bg-[#1A1B1E] px-4 py-10 text-white sm:px-8">
    <div class="mx-auto max-w-4xl">
        <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-lime-400">Ybox Shop</p>
                <h1 class="mt-1 text-3xl font-bold">Beheerpaneel</h1>
            </div>

            <a href="logout.php" class="rounded-lg bg-[#25282C] px-4 py-2 hover:bg-[#35383C]">
                Uitloggen
            </a>
        </header>

        <div class="grid gap-4 sm:grid-cols-2">
            <a href="games.php" class="rounded-xl border border-white/10 bg-[#25282C] p-6 hover:border-lime-400">
                <h2 class="text-xl font-semibold">Games beheren</h2>
                <p class="mt-2 text-sm text-gray-300">Voeg games toe en pas prijzen, covers en beschrijvingen aan.</p>
            </a>

            <a href="paginas.php" class="rounded-xl border border-white/10 bg-[#25282C] p-6 hover:border-lime-400">
                <h2 class="text-xl font-semibold">Pagina’s beheren</h2>
                <p class="mt-2 text-sm text-gray-300">Bewerk de inhoud van je website, zoals Over ons.</p>
            </a>
        </div>
    </div>
</main>

</html>