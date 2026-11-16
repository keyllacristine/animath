
<?php
class Level
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM levels WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $level = $stmt->fetch(PDO::FETCH_ASSOC);

        return $level ?: null;
    }

    public function findAll(): array
    {
        $sql = "
            SELECT *
            FROM levels
            ORDER BY id ASC
        ";

        return $this->db
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
