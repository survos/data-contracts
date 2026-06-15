<?php

declare(strict_types=1);

namespace Survos\DataContracts\Workflow;

/**
 * A subject an AI task can run against — an image/document record (e.g. a media
 * row). Identity only; the workflow "marking" (current place) is just a string
 * the driving state machine manages separately, so this contract deliberately
 * does NOT depend on state-bundle's MarkingInterface.
 */
interface WorkflowSubjectInterface
{
    public function getWorkflowSubjectId(): string;

    /** Semantic subject-type key, e.g. 'fortepan' — used for claim storage. */
    public function getWorkflowSubjectType(): string;

    public function getWorkflowScope(): ?string;

    public function isWorkflowLocked(): bool;

    public function setWorkflowLocked(bool $locked): void;
}
