<?php

declare(strict_types=1);

namespace Treo\Listeners;

use Espo\Core\Exceptions\Error;
use Espo\Core\Hooks\Base;
use Espo\Core\ORM\Entity;
use Treo\Core\Utils\Condition\Condition;
use Treo\Entities\Workflow as WorkflowEntity;
use Treo\Entities\WorkflowLog as WorkflowLogEntity;
use Treo\Services\WorkflowLog;
use Throwable;

class Workflow extends Base
{
    public function execute(string $actionType, Entity $entity): void
    {
        /**
         * @var \Treo\Repositories\Workflow $repository
         * @var WorkflowLog $workflowLogService
         */
        $workflowLogService = $this->getContainer()->get("serviceFactory")->create("WorkflowLog");
        $repository = $this->getEntityManager()->getRepository(WorkflowEntity::ENTITY_TYPE);
        $workflows = $repository->getActiveWorkflowsByEntityAndActionTypes($entity->getEntityType(), $actionType);

        foreach ($workflows as $workflow) {
            $beforeApplyEntityData = $entity->getValues();

            try {
                if ($this->checkConditions($entity, $workflow)) {
                    $this->applyFormulaToEntity($entity, $workflow->get(WorkflowEntity::KEY_SCRIPT));

                    $workflowLogService->createNewLog(
                        $workflow,
                        WorkflowLogEntity::STATUS_SUCCESS,
                        true,
                        '',
                        $beforeApplyEntityData,
                        $entity->getValues()
                    );
                }
            } catch (Throwable $e) {
                $workflowLogService->createNewLog(
                    $workflow,
                    WorkflowLogEntity::STATUS_ERROR,
                    false,
                    $e->getMessage() . $e->getTraceAsString(),
                    $beforeApplyEntityData
                );
            }
        }

    }

    protected function init(): void
    {
        $this->addDependency('formulaManager');
    }

    protected function getFormulaManager()
    {
        return $this->getInjection('formulaManager');
    }

    /**
     * @throws Error
     */
    private function checkConditions(Entity $entity, WorkflowEntity $workflow) : bool
    {
        $isExec = true;

        $conditions = $workflow->get(WorkflowEntity::KEY_CONDITIONS);
        if (!empty($conditions)) {
            $isExec = Condition::isCheck(
                Condition::prepare(
                    $entity,
                    json_decode(json_encode($conditions), true)[WorkflowEntity::CONDITIONS_DEFAULT_KEY]
                )
            );
        }

        return $isExec;
    }

    private function applyFormulaToEntity(Entity $entity, string $formula): void
    {
        $this->getFormulaManager()->run($formula, $entity, (object)[]);
    }
}