<?php
session_start();
require_once("../includes/database.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];
    $sql = "SELECT * FROM beheerders WHERE gebruikersnaam = :gebruikersnaam";
    $statement = $pdo->prepare($sql);
    $statement->bindValue(':gebruikersnaam', $gebruikersnaam);
    $statement->execute();
    $beheerder = $statement->fetch(PDO::FETCH_ASSOC);

    if ($beheerder && password_verify($wachtwoord, $beheerder['wachtwoord_hash'])) {
        session_regenerate_id(true);
        $_SESSION['beheerder_id'] = $beheerder['id'];
        header('location: index.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body
    class="min-h-screen bg-[#1A1B1E] bg-[url('../assets/images/login_page_background.svg')] bg-cover bg-center bg-no-repeat text-white">
    <main class="grid min-h-screen place-items-center bg-black/60 px-4 py-10">
        <section aria-labelledby="login-titel"
            class="w-full max-w-md overflow-hidden rounded-xl border border-white/10 bg-[#25282C] shadow-xl">

            <header class="border-b border-white/10 px-6 py-5">
                <p class="text-sm font-semibold text-lime-400">Ybox Shop</p>
                <h1 id="login-titel" class="mt-1 text-2xl font-bold">Inloggen</h1>
                <p class="mt-1 text-sm text-gray-300">Log in om je games en pagina’s te beheren.</p>
            </header>

            <form method="post" class="space-y-4 p-6">
                <div>
                    <label for="gebruikersnaam" class="mb-1 block text-sm font-medium">Gebruikersnaam</label>
                    <input type="text" id="gebruikersnaam" name="gebruikersnaam" autocomplete="username" required
                        class="w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2 focus:border-lime-400">
                </div>

                <div>
                    <label for="wachtwoord" class="mb-1 block text-sm font-medium">Wachtwoord</label>
                    <input type="password" id="wachtwoord" name="wachtwoord" autocomplete="current-password" required
                        class="w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2 focus:border-lime-400">
                </div>

                <button type="submit"
                    class="w-full cursor-pointer rounded-lg bg-lime-400 px-4 py-2.5 font-semibold text-[#1A1B1E] hover:bg-lime-300">
                    Inloggen
                </button>

                <a href="../index.php" class="block text-center text-sm text-gray-300 hover:text-white hover:underline">
                    Terug naar de homepage
                </a>
            </form>
        </section>
    </main>
</body>

</html>