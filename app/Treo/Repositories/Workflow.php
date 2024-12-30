<?php

declare(strict_types=1);

namespace Treo\Repositories;

use Espo\Core\Templates\Repositories\Base;
use Espo\ORM\EntityCollection;
use Treo\Entities\Workflow as WorkflowEntity;

class Workflow extends Base
{
    public function getActiveWorkflowsByEntityAndActionTypes(string $entityType, string $actionType): EntityCollection
    {
        return $this->where([
            WorkflowEntity::KEY_ACTION_TYPE => $actionType,
            WorkflowEntity::KEY_TARGET_ENTITY_TYPE => $entityType,
            WorkflowEntity::KEY_IS_ACTIVE => true,
        ])->find();
    }
}
