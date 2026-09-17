<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(
        string $username,
        string $password,
        int $avatar
    ): bool {
        $sql = "
            INSERT INTO users (username, password, avatar)
            VALUES (:username, :password, :avatar)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':avatar' => $avatar
        ]);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM users WHERE username = :username";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findAll(): array
    {
        $sql = "
            SELECT id, username, avatar
            FROM users
            ORDER BY id ASC
        ";

        return $this->db
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(
        int $id,
        string $username,
        int $avatar,
        ?string $password = null
    ): bool {
        if ($password !== null && $password !== '') {

            $sql = "
                UPDATE users
                SET username = :username,
                    avatar = :avatar,
                    password = :password
                WHERE id = :id
            ";

            $params = [
                ':id' => $id,
                ':username' => $username,
                ':avatar' => $avatar,
                ':password' => password_hash(
                    $password,
                    PASSWORD_DEFAULT
                )
            ];

        } else {

            $sql = "
                UPDATE users
                SET username = :username,
                    avatar = :avatar
                WHERE id = :id
            ";

            $params = [
                ':id' => $id,
                ':username' => $username,
                ':avatar' => $avatar
            ];
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }
}