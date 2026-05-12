# Survos Data Contracts

Shared PHP-only contracts for data-bearing Survos packages.

This package intentionally contains no Symfony bundle class, Doctrine mapping,
commands, providers, dataset path services, or container wiring. Bundles can
depend on it when they only need stable vocabulary, content type, or DTO
contracts.

## Contents

- `Survos\DataContracts\Vocabulary\DcTerms`
- `Survos\DataContracts\Metadata\ContentType`
- `Survos\DataContracts\Dto\Item\BaseItemDto`
- `Survos\DataContracts\Dto\Item\PhotographDto`
- `Survos\DataContracts\Dto\Item\PostcardDto`
- `Survos\DataContracts\Dto\Item\NewspaperDto`
- `Survos\DataContracts\Dto\Item\MapDto`
