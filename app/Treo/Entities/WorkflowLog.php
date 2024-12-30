<?php

declare(strict_types=1);

namespace Treo\Entities;

use Espo\Core\ORM\Entity;
use DateTime;

class WorkflowLog extends Entity
{
    public const ENTITY_TYPE = 'WorkflowLog';

    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    public const KEY_WORKFLOW_ID = 'workflowId';
    public const KEY_CREATED_AT = 'createdAt';
    public const KEY_STATUS = 'status';
    public const KEY_MESSAGE = 'message';
    public const KEY_TARGET_ENTITY_TYPE = 'targetEntityType';
    public const KEY_ACTION_TYPE = 'actionType';
    public const KEY_FORMULA_IS_APPLIED = 'formulaIsApplied';
    public const KEY_APPLIED_FORMULA = 'appliedFormula';
    public const KEY_ENTITY_BEFORE_FORMULA_APPLY = 'entityBeforeFormulaApply';
    public const KEY_ENTITY_AFTER_FORMULA_APPLY = 'entityAfterFormulaApply';

    protected $entityType = self::ENTITY_TYPE;

    public function getWorkflowId(): ?string
    {
        return $this->get(self::KEY_WORKFLOW_ID);
    }

    public function setWorkflowId(string $workflowId): self
    {
        $this->set(self::KEY_WORKFLOW_ID, $workflowId);
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->get(self::KEY_CREATED_AT);
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->set(self::KEY_CREATED_AT, $createdAt);
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->get(self::KEY_STATUS);
    }

    public function setStatus(string $status): self
    {
        $this->set(self::KEY_STATUS, $status);
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->get(self::KEY_MESSAGE);
    }

    public function setMessage(string $message): self
    {
        $this->set(self::KEY_MESSAGE, $message);
        return $this;
    }

    public function getTargetEntityType(): ?string
    {
        return $this->get(self::KEY_TARGET_ENTITY_TYPE);
    }

    public function setTargetEntityType(string $targetEntityType): self
    {
        $this->set(self::KEY_TARGET_ENTITY_TYPE, $targetEntityType);
        return $this;
    }

    public function getActionType(): ?string
    {
        return $this->get(self::KEY_ACTION_TYPE);
    }

    public function setActionType(string $actionType): self
    {
        $this->set(self::KEY_ACTION_TYPE, $actionType);
        return $this;
    }

    public function getFormulaIsApplied(): ?bool
    {
        return $this->get(self::KEY_FORMULA_IS_APPLIED);
    }

    public function setFormulaIsApplied(bool $formulaIsApplied): self
    {
        $this->set(self::KEY_FORMULA_IS_APPLIED, $formulaIsApplied);
        return $this;
    }

    public function getAppliedFormula(): ?string
    {
        return $this->get(self::KEY_APPLIED_FORMULA);
    }

    public function setAppliedFormula(string $appliedFormula): self
    {
        $this->set(self::KEY_APPLIED_FORMULA, $appliedFormula);
        return $this;
    }

    public function getEntityBeforeFormulaApply(): ?array
    {
        return $this->get(self::KEY_ENTITY_BEFORE_FORMULA_APPLY);
    }

    public function setEntityBeforeFormulaApply(array $entityBeforeFormulaApply): self
    {
        $this->set(self::KEY_ENTITY_BEFORE_FORMULA_APPLY, $entityBeforeFormulaApply);
        return $this;
    }

    public function getEntityAfterFormulaApply(): ?array
    {
        return $this->get(self::KEY_ENTITY_AFTER_FORMULA_APPLY);
    }

    public function setEntityAfterFormulaApply(array $entityAfterFormulaApply): self
    {
        $this->set(self::KEY_ENTITY_AFTER_FORMULA_APPLY, $entityAfterFormulaApply);
        return $this;
    }
}
