<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

$titel = '';
$slug = '';
$inhoud = '';
$fout = '';

$_SESSION['pagina_token'] ??= bin2hex(random_bytes(32));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['pagina_token'], $token)) {
        http_response_code(403);
        exit('Ongeldig formulier.');
    }

    $titel = trim(filter_input(INPUT_POST, 'titel') ?: '');
    $slug = trim(filter_input(INPUT_POST, 'slug') ?: '');
    $inhoud = trim(filter_input(INPUT_POST, 'inhoud') ?? '');

    if (!preg_match('/\A.{1,150}\z/u', $titel) || $inhoud === '') {
        $fout = 'Vul een titel van maximaal 150 tekens en een inhoud in.';
    } elseif (strlen($slug) > 150 || !preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $slug)) {
        $fout = 'Gebruik voor de slug kleine letters, cijfers en koppeltekens.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO paginas (titel, slug, inhoud) VALUES (:titel, :slug, :inhoud)'
            );
            $stmt->execute(['titel' => $titel, 'slug' => $slug, 'inhoud' => $inhoud]);

            header('Location: paginas.php', true, 303);
            exit;
        } catch (PDOException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                $fout = 'Deze slug is al in gebruik.';
            } else {
                error_log($e->getMessage());
                $fout = 'De pagina kon niet worden opgeslagen.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina toevoegen | Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-[#1A1B1E] text-white">
    <main class="mx-auto max-w-2xl px-4 py-8">
        <section class="rounded-xl border border-white/10 bg-[#25282C] p-6">
            <h1 class="mb-6 text-2xl font-bold">Pagina toevoegen</h1>

            <?php if (!empty($fout)): ?>
                <p role="alert" class="mb-4 text-red-400"><?= htmlspecialchars($fout) ?></p>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <div>
                    <label for="titel">Titel</label>
                    <input id="titel" name="titel" maxlength="150" value="<?= htmlspecialchars($titel) ?>" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                </div>

                <div>
                    <label for="slug">Slug</label>
                    <input id="slug" name="slug" maxlength="150" value="<?= htmlspecialchars($slug) ?>"
                        placeholder="over-ons" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                </div>

                <div>
                    <label for="inhoud">Inhoud</label>
                    <textarea id="inhoud" name="inhoud" rows="6" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2"><?= htmlspecialchars($inhoud) ?></textarea>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <button type="submit"
                        class="rounded-lg bg-lime-400 px-4 py-2 font-semibold text-[#1A1B1E] hover:bg-lime-300">
                        Opslaan
                    </button>
                    <a href="paginas.php" class="text-gray-300 hover:text-white">Annuleren</a>
                </div>
            </form>
        </section>
    </main>
</body>

</html>