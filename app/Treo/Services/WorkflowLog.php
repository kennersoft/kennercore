<?php

declare(strict_types=1);

namespace Treo\Services;

use Espo\Core\Templates\Services\Base;
use Treo\Entities\Workflow as WorkflowEntity;
use Treo\Entities\WorkflowLog as WorkflowLogEntity;
use DateTime;

class WorkflowLog extends Base
{
    public function createNewLog(
        WorkflowEntity $workflow,
        string $status,
        bool $isFormulaApplied,
        string $message = '',
        array $entityBeforeFormulaApply = [],
        array $entityAfterFormulaApply = []
    ): void {
        $em = $this->getEntityManager();

        /**
         * @var WorkflowLogEntity $workflowLog
         */
        $workflowLog = $em->getEntity('WorkflowLog');
        $workflowLog->setWorkflowId($workflow->get(WorkflowEntity::ID))
            ->setCreatedAt(new DateTime('now'))
            ->setStatus($status)
            ->setMessage($message)
            ->setActionType($workflow->getActionType())
            ->setTargetEntityType($workflow->getTargetEntityType())
            ->setFormulaIsApplied($isFormulaApplied)
            ->setAppliedFormula($workflow->getScript())
            ->setEntityBeforeFormulaApply($entityBeforeFormulaApply)
            ->setEntityAfterFormulaApply($entityAfterFormulaApply);
        $em->saveEntity($workflowLog);
    }
}
