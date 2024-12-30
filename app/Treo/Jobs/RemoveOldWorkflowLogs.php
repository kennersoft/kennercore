<?php

declare(strict_types=1);

namespace Treo\Jobs;

use Espo\Core\Jobs\Base;
use Treo\Entities\WorkflowLog as WorkflowLogEntity;
use Treo\Repositories\WorkflowLog as WorkflowLogRepository;
use DateTime;

class RemoveOldWorkflowLogs extends Base
{
    private const OLD_LOGS_DATE_MODIFIER = '-7 day';

    public function run(): bool
    {
        /**
         * @var WorkflowLogRepository $repository
         */
        $repository = $this->getEntityManager()->getRepository(WorkflowLogEntity::ENTITY_TYPE);
        $repository->removeLogsOlderThan((new DateTime('now'))->modify(self::OLD_LOGS_DATE_MODIFIER));

        return true;
    }
}
