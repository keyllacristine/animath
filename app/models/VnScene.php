<?php

class VnScene
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM vn_scenes WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** Cria a cena ou atualiza se o slug já existir. */
    public function save(string $slug, ?int $levelId, string $title, string $dataJson): void
    {
        $sql = "
            INSERT INTO vn_scenes (slug, level_id, title, data)
            VALUES (:slug, :level_id, :title, :data)
            ON DUPLICATE KEY UPDATE
                level_id = VALUES(level_id),
                title = VALUES(title),
                data = VALUES(data)
        ";

        $this->db->prepare($sql)->execute([
            ':slug'     => $slug,
            ':level_id' => $levelId,
            ':title'    => $title,
            ':data'     => $dataJson,
        ]);
    }
}
