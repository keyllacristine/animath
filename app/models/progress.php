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
            SELECT *
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

    public function findAll(): array
    {
        $sql = "
            SELECT
                up.*,
                u.username,
                l.id AS level_id
            FROM user_progress up
            INNER JOIN users u
                ON u.id = up.user_id
            INNER JOIN levels l
                ON l.id = up.level_id
            ORDER BY u.username, l.id
        ";

        return $this->db
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(
        int $id,
        int $completed,
        int $hits,
        int $errors
    ): bool {
        $sql = "
            UPDATE user_progress
            SET completed = :completed,
                hits = :hits,
                errors = :errors
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':completed' => $completed,
            ':hits' => $hits,
            ':errors' => $errors
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM user_progress WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }
}