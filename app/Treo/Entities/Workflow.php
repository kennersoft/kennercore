<?php

declare(strict_types=1);

namespace Treo\Entities;

use Espo\Core\ORM\Entity;

class Workflow extends Entity
{
    public const ENTITY_TYPE = 'Workflow';
    public const ACTION_TYPE_BEFORE_SAVE = 'beforeSave';
    public const ACTION_TYPE_AFTER_SAVE = 'afterSave';
    public const ACTION_TYPE_BEFORE_REMOVE = 'beforeRemove';
    public const ACTION_TYPE_AFTER_REMOVE = 'afterRemove';
    public const ACTION_TYPE_BEFORE_RELATE = 'beforeRelate';
    public const ACTION_TYPE_AFTER_RELATE = 'afterRelate';

    public const KEY_NAME = 'name';
    public const KEY_IS_ACTIVE = 'isActive';
    public const KEY_TARGET_ENTITY_TYPE = 'targetEntityType';
    public const KEY_ACTION_TYPE = 'actionType';
    public const KEY_SCRIPT = 'script';
    public const KEY_CONDITIONS = 'conditions';
    public const CONDITIONS_DEFAULT_KEY = 'conditionGroup';

    protected $entityType = self::ENTITY_TYPE;

    public function getName(): ?string
    {
        return $this->get(self::KEY_NAME);
    }

    public function setName(string $name): self
    {
        $this->set(self::KEY_NAME, $name);
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->get(self::KEY_IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): self
    {
        $this->set(self::KEY_IS_ACTIVE, $isActive);
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

    public function getScript(): ?string
    {
        return $this->get(self::KEY_SCRIPT);
    }

    public function setScript(string $script): self
    {
        $this->set(self::KEY_SCRIPT, $script);
        return $this;
    }
}
