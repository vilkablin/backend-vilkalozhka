<?php

namespace App\Database\Models;

use App\Exceptions\System\DatabaseQueryException;

class CommentModel extends BaseModel
{
    private string $table = 'comments';

    /**
     * @throws DatabaseQueryException
     */
    public function getCommentsCountByUserId(int $userId): int
    {
        $results = $this->executeQuery(
            query: "SELECT COUNT(*) as count FROM $this->table WHERE user_id = :user_id",
            params: [':user_id' => $userId],
            fetchSingle: true,
        );

        return $results['count'] ?? 0;
    }
}