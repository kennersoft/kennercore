<?php

declare(strict_types=1);

namespace Treo\Repositories;

use Espo\Core\Templates\Repositories\Base;
use DateTime;

class WorkflowLog extends Base
{
    private const TABLE_NAME = 'workflow_log';

    public function removeLogsOlderThan(DateTime $date): void
    {
        $query = <<<SQL
DELETE FROM %s WHERE %s.created_at < '%s';
SQL;

        $this->getEntityManager()
            ->nativeQuery(
                sprintf(
                    $query,
                    self::TABLE_NAME,
                    self::TABLE_NAME,
                    $date->format('Y-m-d H:i:s')
                )
            )
            ->execute();
    }
}
