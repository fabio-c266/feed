<?php

namespace src\repositories;

use src\config\Database;
use src\core\Query;
use src\helpers\ArrayHelper;

class CommentRepository
{
    private string $tableName = 'comments';

    public function create(array $data): void
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->insert(columns: ['id_public', 'content', 'user_id', 'post_id'], data: $data)
            ->build();

        Database::query($queryContent);
    }

    public function findOne(string $idPublic): ?array
    {
        $queryContent = (new Query(tableName: $this->tableName))
            ->select(['id_public', 'content', 'user_id', 'post_id', 'created_at'])
            ->where('id_public', $idPublic)
            ->build();

        $posts = Database::query($queryContent);
        return $posts ? $posts[0] : null;
    }

    public function findManyByPostId(int $id)
    {
        $queryContent = "SELECT 
        comments.id_public,
        comments.content,
        comments.created_at,
        posts.id_public as `post.id_public`,
        users.id_public AS `user.id_public`,
        users.username AS `user.username`,
        users.avatar_name AS `user.avatar_name`,
        users.created_at AS `user.created_at`
        FROM comments
        INNER JOIN users ON comments.user_id = users.id
        INNER JOIN posts ON comments.post_id = posts.id
        WHERE comments.post_id = '{$id}'";

        return ArrayHelper::formatData(Database::query($queryContent));
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

    public function delete(string $idPublic): void
    {
        $queryContent = (new Query($this->tableName))
            ->delete()
            ->where('id_public', $idPublic)
            ->build();

        Database::query($queryContent);
    }
}
