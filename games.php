<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#1A1B1E] text-white flex">
    <!-- Home, Games en Over ons. -->

    <nav
        class="flex flex-col gap-3 p-6 bg-[#25282C] w-64 min-h-screen self-start h-screen overflow-y-auto sticky top-0">
        <a href="index.php">
            <h1 class="block px-3 py-3 rounded-lg hover:bg-[#515356]-700 transition-colors">Ybox Shop</h1>
        </a>
        <a href="index.php" class="block px-3 py-3 rounded-lg hover:bg-[#515356] transition-colors">Home</a>
        <a href="games.php" class="block px-3 py-3 rounded-lg bg-[#686A6C] transition-colors">Games</a>
        <a href="over-ons.php" class="block px-3 py-3 rounded-lg hover:bg-[#515356] transition-colors">Over Ons</a>
    </nav>
    <main class="flex-1 p-6 "><input
            class="bg-[#25282C] border border-[#515356] rounded-full px-4 py-2 w-full max-w-xl mb-6 flex block mx-auto z-10 sticky top-6"
            type="search" placeholder="Zoek een game…" aria-label="Zoek een game">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


            <div class="mt-8">
                <section>
                    <div class="p-6 text-2xl ">
                        <h2 class="font-bold">Top paid games</h2>
                        <p class="text-sm text-gray-300 mt-2">Trending games you don't want to miss</p>
                    </div>

                </section>

                <section class="w-50 rounded-xl relative overflow-hidden ml-6">
                    <img class="w-full h-60 object-cover bg-fixed" src="assets/images/banner.jpg"
                        alt="Minecraft: Java & Bedrock Edition for PC banner">

                    <div class="p-3 bg-[#25282C]">
                        <h2 class="text-s font-bold">Minecraft: Java & Bedrock Edition for PC</h2>
                        <p class="text-sm text-gray-300 mt-2">€29,99</p>
                    </div>
                </section>

            </div>

    </main>
</body>

</html>