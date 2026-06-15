<?php

declare(strict_types=1);

namespace Survos\DataContracts\Workflow;

interface TextSubjectInterface
{
    public function getWorkflowText(): ?string;
}
