<?php

namespace App\Database\Models;

use App\Exceptions\System\DatabaseQueryException;

class LikeModel extends BaseModel
{
    private string $table = 'likes';

    /**
     * @throws DatabaseQueryException
     */
    public function getLikesCountByUserId(int $userId): int
    {
        $results = $this->executeQuery(
            query: "SELECT COUNT(*) as count FROM $this->table WHERE user_id = :user_id",
            params: [':user_id' => $userId],
            fetchSingle: true
        );

        return $results['count'] ?? 0;
    }
}