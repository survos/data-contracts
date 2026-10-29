<?php

declare(strict_types=1);

namespace Survos\DataContracts\Vocabulary;

/**
 * Who may see a record, as its owner set it. Providers map their own switch (PastPerfect's Public
 * Access checkbox, a "published" flag) onto one of these and never invent another word.
 *
 *  - Private  — staff only; not shown, not indexed publicly.
 *  - Unlisted — reachable by its link, left out of search and listings.
 *  - Public   — shown and searchable.
 */
enum Visibility: string
{
    case Private = 'private';
    case Unlisted = 'unlisted';
    case Public = 'public';
}
