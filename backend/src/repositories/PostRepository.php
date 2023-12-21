<?php

namespace src\repositories;

use src\config\Database;
use src\core\Query;
use src\helpers\ArrayHelper;

class PostRepository
{
    private string $tableName = 'posts';

    public function create(array $data): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->insert(columns: ['id_public', 'content', 'image_name', 'user_id'], data: $data)
            ->build();

        Database::query($queryContent);
    }

    public function findOne(string $idPublic): ?array
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id', 'id_public', 'content', 'image_name', 'user_id', 'created_at'])
            ->where('id_public', $idPublic)
            ->build();

        $posts = Database::query($queryContent);
        return $posts ? $posts[0] : null;
    }

    public function findWhere(string $column, $value): array | null
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id_public'])
            ->where($column, $value)
            ->build();

        $data = Database::query($queryContent);
        return $data ? $data[0] : null;
    }

    public function update(string $idPublic, array $data): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->update($data)
            ->where('id_public', $idPublic)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }

    public function findMany(): array
    {
        $queryContent =
            "SELECT 
        posts.id_public,
        posts.content,
        posts.image_name,
        posts.created_at,
        users.id_public AS `user.id_public`,
        users.username AS `user.username`,
        users.avatar_name AS `user.avatar_name`,
        users.created_at AS `user.created_at`,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS commentsAmount FROM posts
        INNER JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
        return ArrayHelper::formatData(Database::query($queryContent));
    }

    public function delete(string $idPublic): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->delete()
            ->where('id_public', $idPublic)
            ->limit(1)
            ->build();

        Database::query($queryContent);
    }
}
