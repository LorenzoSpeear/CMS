<?php
require_once '../includes/auth.php';
require_once '../includes/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('Ongeldig game-ID.');
}

$stmt = $pdo->prepare('SELECT * FROM games WHERE id = :id');
$stmt->execute(['id' => $id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    http_response_code(404);
    exit('Game niet gevonden.');
}
$genres = $pdo->query('SELECT id, naam FROM genres ORDER BY naam')
    ->fetchAll(PDO::FETCH_ASSOC);
$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titel = trim(filter_input(INPUT_POST, 'titel') ?? '');
    $beschrijving = trim(filter_input(INPUT_POST, 'beschrijving') ?? '');
    $genreId = filter_input(INPUT_POST, 'genre_id', FILTER_VALIDATE_INT);
    $prijs = filter_input(INPUT_POST, 'prijs') ?? '';
    $releasedatum = filter_input(INPUT_POST, 'releasedatum') ?? '';
    $datum = DateTimeImmutable::createFromFormat('!Y-m-d', $releasedatum);
    $geldigeGenreIds = array_map('intval', array_column($genres, 'id'));

    if ($titel === '' || $beschrijving === '') {
        $fout = 'Vul een titel en beschrijving in.';
    } elseif (!in_array($genreId, $geldigeGenreIds, true)) {
        $fout = 'Kies een geldig genre.';
    } elseif (!preg_match('/\A[0-9]{1,6}(?:\.[0-9]{1,2})?\z/', $prijs)) {
        $fout = 'Vul een geldige prijs in, bijvoorbeeld 29.99.';
    } elseif (!$datum || $datum->format('Y-m-d') !== $releasedatum) {
        $fout = 'Vul een geldige releasedatum in.';
    } else {
        $coverNaam = $game['cover_afbeelding'];
        $bannerNaam = $game['banner_afbeelding'];
        $nieuweAfbeeldingen = [];
        $extensies = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach (['cover_afbeelding', 'banner_afbeelding'] as $veld) {
            $bestand = $_FILES[$veld] ?? [];
            $status = $bestand['error'] ?? UPLOAD_ERR_NO_FILE;

            if ($status === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($status !== UPLOAD_ERR_OK || $bestand['size'] > 5 * 1024 * 1024) {
                $fout = 'Kies een afbeelding van maximaal 5 MB.';
                break;
            }

            $type = $finfo->file($bestand['tmp_name']);
            if (!isset($extensies[$type])) {
                $fout = 'Kies een JPEG, PNG of WebP.';
                break;
            }

            $nieuweAfbeeldingen[$veld] = [
                'tmp' => $bestand['tmp_name'],
                'naam' => bin2hex(random_bytes(16)) . '.' . $extensies[$type],
            ];
        }
        $namen = [
            'cover_afbeelding' => $coverNaam,
            'banner_afbeelding' => $bannerNaam,
        ];
        $verplaatst = [];

        if ($fout === '') {
            foreach ($nieuweAfbeeldingen as $veld => $afbeelding) {
                $pad = __DIR__ . '/../uploads/' . $afbeelding['naam'];

                if (!move_uploaded_file($afbeelding['tmp'], $pad)) {
                    $fout = 'De afbeelding kon niet worden opgeslagen.';
                    break;
                }

                $verplaatst[] = $pad;
                $namen[$veld] = $afbeelding['naam'];
            }
        }

        if ($fout === '') {
            try {
                $stmt = $pdo->prepare(
                    'UPDATE games
             SET titel = :titel, genre_id = :genre_id, prijs = :prijs,
                 releasedatum = :releasedatum, beschrijving = :beschrijving,
                 cover_afbeelding = :cover, banner_afbeelding = :banner
             WHERE id = :id'
                );

                if (
                    $stmt && $stmt->execute([
                        'titel' => $titel,
                        'genre_id' => $genreId,
                        'prijs' => $prijs,
                        'releasedatum' => $releasedatum,
                        'beschrijving' => $beschrijving,
                        'cover' => $namen['cover_afbeelding'],
                        'banner' => $namen['banner_afbeelding'],
                        'id' => $id,
                    ])
                ) {
                    header('Location: games.php', true, 303);
                    exit;
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }

            $fout = 'De game kon niet worden bijgewerkt.';
        }

        foreach ($verplaatst as $pad) {
            unlink($pad);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game bewerken | Ybox Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-[#1A1B1E] text-white">
    <main class="mx-auto max-w-2xl px-4 py-8">
        <section class="rounded-xl border border-white/10 bg-[#25282C] p-6">
            <h1 class="mb-6 text-2xl font-bold">Game bewerken</h1>

            <?php if ($fout !== ''): ?>
                <p role="alert" class="mb-4 text-red-400"><?= htmlspecialchars($fout) ?></p>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="titel">Titel</label>
                    <input type="text" id="titel" name="titel" required value="<?= htmlspecialchars($game['titel']) ?>"
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                </div>

                <div>
                    <label for="genre_id">Genre</label>
                    <select id="genre_id" name="genre_id" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= (int) $genre['id'] ?>" <?= (int) $genre['id'] === (int) $game['genre_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre['naam']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="prijs">Prijs (€)</label>
                        <input type="number" id="prijs" name="prijs" min="0" step="0.01" required
                            value="<?= htmlspecialchars($game['prijs']) ?>"
                            class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2">
                    </div>
                    <div>
                        <label for="releasedatum">Releasedatum</label>
                        <input type="date" id="releasedatum" name="releasedatum" required
                            value="<?= htmlspecialchars($game['releasedatum']) ?>"
                            class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2 [color-scheme:dark]">
                    </div>
                </div>

                <div>
                    <label for="beschrijving">Beschrijving</label>
                    <textarea id="beschrijving" name="beschrijving" rows="5" required
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2"><?= htmlspecialchars($game['beschrijving']) ?></textarea>
                </div>

                <div>
                    <label for="cover_afbeelding">Nieuwe cover (optioneel)</label>
                    <input type="file" id="cover_afbeelding" name="cover_afbeelding"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-1 block w-full rounded-lg border border-white/20 bg-[#1A1B1E] px-3 py-2 text-sm file:mr-3 file:rounded file:bg-white/10 file:px-2 file:py-1 file:text-white">
                </div>

                <div>
                    <label for="banner_afbeelding">Nieuwe banner (optioneel)</label>
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