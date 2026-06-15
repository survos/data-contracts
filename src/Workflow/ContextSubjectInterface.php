<?php

declare(strict_types=1);

namespace Survos\DataContracts\Workflow;

interface ContextSubjectInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getWorkflowContext(): array;
}
