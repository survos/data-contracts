<?php

declare(strict_types=1);

namespace Survos\DataContracts\Workflow;

interface ImageSubjectInterface
{
    public function getWorkflowImageUrl(): ?string;
}
