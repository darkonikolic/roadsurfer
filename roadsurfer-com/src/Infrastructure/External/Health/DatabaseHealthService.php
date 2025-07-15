<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Health;

use App\Shared\DTO\ServiceHealthDTO;
use Doctrine\DBAL\Connection;

class DatabaseHealthService
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function check(): ServiceHealthDTO
    {
        try {
            $this->connection->executeQuery('SELECT 1');

            return new ServiceHealthDTO(
                status: 'ok',
                connected: true
            );
        } catch (\Exception $e) {
            return new ServiceHealthDTO(
                status: 'error',
                connected: false,
                error: $e->getMessage()
            );
        }
    }
}
