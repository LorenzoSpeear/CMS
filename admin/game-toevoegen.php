<?php
require_once('../includes/auth.php');
require_once('../includes/database.php');
$sql = "SELECT * FROM genres ORDER BY naam";
$statement = $pdo->query($sql);
$genres = $statement->fetchAll(PDO::FETCH_ASSOC);
$fouten = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $titel = $_POST['titel'];
        $genre_id = $_POST['genre_id'];
        $prijs = $_POST['prijs'];
        $releasedatum = $_POST['releasedatum'];
        $beschrijving = $_POST['beschrijving'];
        if (trim($titel) === '') {
                $fouten[] = 'Vul een titel in.';
        }
        if (trim($beschrijving) === '') {
                $fouten[] = 'Vul een beschrijving in.';
        }
        if (!is_numeric($prijs) || $prijs < 0) {
                $fouten[] = 'Vul een geldige prijs van 0 of hoger in.';
        }

        $geldigeGenreIds = array_column($genres, 'id');
        if (!in_array($genre_id, $geldigeGenreIds)) {
                $fouten[] = 'Kies een geldig genre.';
        }

        $datum = DateTimeImmutable::createFromFormat('!Y-m-d', $releasedatum);
        if ($datum === false || $datum->format('Y-m-d') !== $releasedatum) {
                $fouten[] = 'Vul een geldige releasedatum in.';
        }
        $coverFout = $_FILES['cover_afbeelding']['error'] ?? UPLOAD_ERR_NO_FILE;
        $bannerFout = $_FILES['banner_afbeelding']['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($coverFout !== UPLOAD_ERR_OK) {
                $fouten[] = 'Upload een geldige cover.';
        }

        if ($bannerFout !== UPLOAD_ERR_OK && $bannerFout !== UPLOAD_ERR_NO_FILE) {
                $fouten[] = 'Upload een geldige banner.';
        }
        if ($fouten === []) {
                $uploadmap = __DIR__ . '/../uploads/';
                $maxBestandsgrootte = 5 * 1024 * 1024; // 5 MB
                $extensies = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                ];

                $bestandsvelden = ['cover_afbeelding' => 'cover'];
                if ($bannerFout === UPLOAD_ERR_OK) {
                        $bestandsvelden['banner_afbeelding'] = 'banner';
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $bestandsnamen = ['banner_afbeelding' => null];

                foreach ($bestandsvelden as $veld => $label) {
                        $bestand = $_FILES[$veld];

                        if ($bestand['size'] > $maxBestandsgrootte) {
                                $fouten[] = "De $label mag maximaal 5 MB zijn.";
                                continue;
                        }

                        $mimeType = $finfo->file($bestand['tmp_name']);
                        if (!isset($extensies[$mimeType])) {
                                $fouten[] = "Kies voor de $label een JPEG, PNG of WebP.";
                                continue;
                        }

                        $bestandsnamen[$veld] = bin2hex(random_bytes(16)) . '.' . $extensies[$mimeType];
                }

                if ($fouten === []) {
                        $verplaatsteBestanden = [];

                        foreach ($bestandsvelden as $veld => $label) {
                                $doelpad = $uploadmap . $bestandsnamen[$veld];

                                if (!move_uploaded_file($_FILES[$veld]['tmp_name'], $doelpad)) {
                                        $fouten[] = "De $label kon niet worden opgeslagen.";
                                        break;
                                }

                                $verplaatsteBestanden[] = $doelpad;
                        }

                        if ($fouten === []) {
                                try {
                                        $stmt = $pdo->prepare(
                                                'INSERT INTO games
                        (titel, genre_id, prijs, releasedatum, beschrijving,
                         cover_afbeelding, banner_afbeelding)
                     VALUES
                        (:titel, :genre_id, :prijs, :releasedatum, :beschrijving,
                         :cover, :banner)'
                                        );

                                        if (
                                                $stmt && $stmt->execute([
                                                        'titel' => trim($titel),
                                                        'genre_id' => $genre_id,
                                                        'prijs' => $prijs,
                                                        'releasedatum' => $releasedatum,
                                                        'beschrijving' => trim($beschrijving),
                                                        'cover' => $bestandsnamen['cover_afbeelding'],
                                                        'banner' => $bestandsnamen['banner_afbeelding'],
                                                ])
                                        ) {
                                                header('Location: games.php', true, 303);
                                                exit;
                                        }

                                        $fouten[] = 'De game kon niet worden opgeslagen.';
                                } catch (PDOException $e) {
                                        error_log($e->getMessage());
                                        $fouten[] = 'De game kon niet worden opgeslagen.';
                                }
                        }

                        foreach ($verplaatsteBestanden as $pad) {
                                unlink($pad);
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
    <title>Game toevoegen | Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-[#1A1B1E] text-white">
    <main class="mx-auto max-w-2xl px-4 py-8">
        <section class="rounded-xl border border-white/10 bg-[#25282C] p-6">
            <h1 class="mb-6 text-2xl font-bold">Game toevoegen</h1>

            <?php foreach ($fouten as $fout): ?>
                <p role="alert" class="mb-4 text-red-400"><?= htmlspecialchars($fout) ?></p>
            <?php endforeach; ?>

            <form method="post" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="titel">Titel</label>
                    <input type="text" id="titel" name="titel" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                </div>

                <div>
                    <label for="genre_id">Genre</label>
                    <select id="genre_id" name="genre_id" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                        <option value="">Kies een genre</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= (int) $genre['id'] ?>">
                                <?= htmlspecialchars($genre['naam']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="prijs">Prijs (€)</label>
                        <input type="number" id="prijs" name="prijs" min="0" step="0.01" required
                            class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                    </div>
                    <div>
                        <label for="releasedatum">Releasedatum</label>
                        <input type="date" id="releasedatum" name="releasedatum" required
                            class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2 [color-scheme:dark]">
                    </div>
                </div>

                <div>
                    <label for="beschrijving">Beschrijving</label>
                    <textarea id="beschrijving" name="beschrijving" rows="5" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2"></textarea>
                </div>

                <div>
                    <label for="cover_afbeelding">Coverafbeelding</label>
                    <input type="file" id="cover_afbeelding" name="cover_afbeelding"
                        accept="image/jpeg,image/png,image/webp" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2 text-sm file:mr-3 file:rounded file:bg-white/10 file:px-2 file:py-1 file:text-white">
                </div>

                <div>
                    <label for="banner_afbeelding">Bannerafbeelding (optioneel)</label>
                    <input type="file" id="banner_afbeelding" name="banner_afbeelding"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2 text-sm file:mr-3 file:rounded file:bg-white/10 file:px-2 file:py-1 file:text-white">
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <button type="submit"
                        class="rounded-lg bg-lime-400 px-4 py-2 font-semibold text-[#1A1B1E] hover:bg-lime-300">
                        Opslaan
                    </button>
                    <a href="games.php" class="text-gray-300 hover:text-white">Annuleren</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>