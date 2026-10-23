<?php

class VnController
{
    private VnScene $scenes;
    private string $imgDir;

    private const KINDS = ['bg', 'chars'];
    private const POSITIONS = ['far-left', 'left', 'mid-left', 'center', 'mid-right', 'right', 'far-right'];
    private const MAX_UPLOAD = 5 * 1024 * 1024; // 5 MB

    public function __construct(PDO $db)
    {
        $this->scenes = new VnScene($db);
        $this->imgDir = __DIR__ . '/../../public/assets/img/vn';
    }

    /* ---------------------------------------------------------------- GET /vn/scene?slug=... */

    public function scene(): void
    {
        $this->requireLogin();

        $slug = $this->cleanSlug($_GET['slug'] ?? '');
        $row = $slug !== '' ? $this->scenes->findBySlug($slug) : null;

        if (!$row) {
            $this->json(['error' => 'not_found'], 404);
        }

        $data = json_decode($row['data'], true) ?: [];

        $this->json([
            'slug'     => $row['slug'],
            'level_id' => $row['level_id'] !== null ? (int) $row['level_id'] : null,
            'title'    => $data['title'] ?? $row['title'],
            'lines'    => $data['lines'] ?? [],
        ]);
    }

    /* ---------------------------------------------------------------- POST /vn/save (admin) */

    public function save(): void
    {
        $this->requireAdmin();

        $body = json_decode(file_get_contents('php://input'), true);
        if (!is_array($body)) {
            $this->json(['error' => 'JSON inválido'], 400);
        }

        $slug = $this->cleanSlug($body['slug'] ?? '');
        if ($slug === '') {
            $this->json(['error' => 'slug inválido'], 400);
        }

        $lines = $this->sanitizeLines($body['lines'] ?? []);
        $title = mb_substr(trim((string) ($body['title'] ?? $slug)), 0, 120);
        $levelId = isset($body['level_id']) && $body['level_id'] !== null ? (int) $body['level_id'] : null;

        $json = json_encode(['title' => $title, 'lines' => $lines], JSON_UNESCAPED_UNICODE);

        $this->scenes->save($slug, $levelId, $title, $json);

        $this->json(['success' => true, 'lines' => count($lines)]);
    }

    /* ---------------------------------------------------------------- GET /vn/assets (admin) */

    public function assets(): void
    {
        $this->requireAdmin();

        $out = [];
        foreach (self::KINDS as $kind) {
            $files = glob($this->imgDir . '/' . $kind . '/*.{png,jpg,jpeg,webp,gif,PNG,JPG,JPEG,WEBP,GIF}', GLOB_BRACE) ?: [];
            $names = array_map('basename', $files);
            sort($names, SORT_NATURAL | SORT_FLAG_CASE);
            $out[$kind] = array_values($names);
        }

        $this->json($out);
    }

    /* ---------------------------------------------------------------- POST /vn/upload (admin) */

    public function upload(): void
    {
        $this->requireAdmin();

        $kind = $_POST['kind'] ?? '';
        if (!in_array($kind, self::KINDS, true)) {
            $this->json(['error' => 'tipo inválido'], 400);
        }

        $file = $_FILES['image'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'falha ao receber o arquivo'], 400);
        }
        if ($file['size'] > self::MAX_UPLOAD) {
            $this->json(['error' => 'arquivo maior que 5 MB'], 400);
        }

        // Confere o conteúdo real (não confia na extensão enviada)
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $ext = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp', 'image/gif' => 'gif'][$mime] ?? null;
        if (!$ext) {
            $this->json(['error' => 'use PNG, JPG, WEBP ou GIF'], 400);
        }

        $base = preg_replace('/[^a-z0-9_-]+/i', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $base = trim($base, '_') ?: 'imagem';

        $dir = $this->imgDir . '/' . $kind;
        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            $this->json(['error' => 'não foi possível criar a pasta'], 500);
        }

        $name = "$base.$ext";
        for ($i = 2; file_exists("$dir/$name"); $i++) {
            $name = "{$base}_$i.$ext";
        }

        if (!move_uploaded_file($file['tmp_name'], "$dir/$name")) {
            $this->json(['error' => 'não foi possível salvar o arquivo'], 500);
        }

        $this->json(['success' => true, 'name' => $name]);
    }

    /* ---------------------------------------------------------------- helpers */

    private function cleanSlug(string $slug): string
    {
        return preg_match('/^[a-z0-9_-]{1,80}$/i', $slug) ? $slug : '';
    }

    private function cleanFile(mixed $name): string
    {
        $name = is_string($name) ? $name : '';

        return preg_match('/^[\w\-. {}]{1,120}\.(png|jpe?g|webp|gif)$/i', $name) ? $name : '';
    }

    /** Só deixa passar os campos conhecidos, com tipo e tamanho controlados. */
    private function sanitizeLines(mixed $lines): array
    {
        if (!is_array($lines)) {
            return [];
        }

        $clean = [];
        foreach (array_slice($lines, 0, 500) as $line) {
            if (!is_array($line)) {
                continue;
            }

            $item = [
                'id'      => mb_substr(preg_replace('/[^\w-]/', '', (string) ($line['id'] ?? '')), 0, 40) ?: uniqid('l'),
                'speaker' => mb_substr(trim((string) ($line['speaker'] ?? '')), 0, 60),
                'text'    => mb_substr((string) ($line['text'] ?? ''), 0, 1000),
            ];

            // marca o início da "parte 2" (explicação) — usado na reentrada rápida
            if ((int) ($line['part'] ?? 0) === 2) {
                $item['part'] = 2;
            }

            $bg = $this->cleanFile($line['bg'] ?? '');
            if ($bg !== '') {
                $item['bg'] = $bg;
            }

            $chars = [];
            foreach (array_slice((array) ($line['chars'] ?? []), 0, 8) as $c) {
                if (!is_array($c)) {
                    continue;
                }
                $id = preg_replace('/[^\w-]/', '', (string) ($c['id'] ?? ''));
                if ($id === '') {
                    continue;
                }
                $cmd = ['id' => mb_substr($id, 0, 40)];

                $src = $this->cleanFile($c['src'] ?? '');
                if ($src !== '') {
                    $cmd['src'] = $src;
                }
                if (in_array($c['pos'] ?? '', self::POSITIONS, true)) {
                    $cmd['pos'] = $c['pos'];
                }
                if (array_key_exists('show', $c)) {
                    $cmd['show'] = (bool) $c['show'];
                }
                $chars[] = $cmd;
            }
            if ($chars) {
                $item['chars'] = $chars;
            }

            $clean[] = $item;
        }

        return $clean;
    }

    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['error' => 'login necessário'], 401);
        }
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            $this->json(['error' => 'acesso negado'], 403);
        }
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
