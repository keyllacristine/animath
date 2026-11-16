
<?php

class Progress
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT user_progress.*, user_progress.erros AS errors
            FROM user_progress
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $progress = $stmt->fetch(PDO::FETCH_ASSOC);

        return $progress ?: null;
    }

    public function findByUser(int $userId): array
    {
        $sql = "
            SELECT
                up.*,
                up.erros AS errors,
                l.id AS level_id
            FROM user_progress up
            INNER JOIN levels l
                ON l.id = up.level_id
            WHERE up.user_id = :user_id
            ORDER BY l.id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasCompletedAll(int $userId): bool
    {
        $sql = "
            SELECT
                (SELECT COUNT(*) FROM levels) AS total,
                (SELECT COUNT(*) FROM user_progress
                 WHERE user_id = :user_id AND completed = 1) AS done
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row
            && (int) $row['total'] > 0
            && (int) $row['done'] >= (int) $row['total'];
    }

    public function save(
        int $userId,
        int $levelId,
        int $hits,
        int $errors
    ): bool {
        $sql = "
            INSERT INTO user_progress (user_id, level_id, completed, hits, erros)
            VALUES (:user_id, :level_id, 1, :hits, :errors)
            ON DUPLICATE KEY UPDATE
                completed = 1,
                hits = hits + VALUES(hits),
                erros = erros + VALUES(erros)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':user_id' => $userId,
            ':level_id' => $levelId,
            ':hits' => $hits,
            ':errors' => $errors,
        ]);
    }
}
