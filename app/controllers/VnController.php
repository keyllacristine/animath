
<?php

class VnController
{
    private VnScene $scenes;

    public function __construct(PDO $db)
    {
        $this->scenes = new VnScene($db);
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

    /* ---------------------------------------------------------------- helpers */

    private function cleanSlug(string $slug): string
    {
        return preg_match('/^[a-z0-9_-]{1,80}$/i', $slug) ? $slug : '';
    }

    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['error' => 'login necessário'], 401);
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
