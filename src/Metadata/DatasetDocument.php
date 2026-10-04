<?php

declare(strict_types=1);

namespace Survos\DataContracts\Metadata;

/** A generated document retains independent writer snapshots; overrides never destroy them. */
final class DatasetDocument
{
    /** One-time adoption of pre-contract work/vault snapshots; newer fields win, unknown fields survive. */
    public static function mergeLegacy(array $primary, array $secondary): array
    {
        if (isset($primary['_metadata'])) { return $primary; }
        $dataset = [];
        foreach (self::flatten($primary['dataset'] ?? []) + self::flatten($secondary['dataset'] ?? []) as $path => $value) {
            self::assign($dataset, explode('/', $path), $value);
        }
        return ['dataset' => $dataset];
    }

    public static function update(array $existing, array $incoming, string $owner, array $overrides = [], array $provenance = []): array
    {
        $fields = $existing['_metadata']['fields'] ?? [];
        if ($fields === []) {
            foreach (self::flatten($existing['dataset'] ?? []) as $path => $value) {
                $fields[$path] = PropertyValue::create($value, 'import', 'legacy')->toArray();
            }
        }
        $next = self::flatten($incoming);
        foreach ($fields as $path => $field) {
            if ($field['owner'] === $owner && !array_key_exists($path, $next)) {
                unset($fields[$path]);
            }
        }
        foreach ($next as $path => $value) {
            // A scalar/empty-list replacement cannot erase another owner's nested fields.
            // Likewise a nested field cannot turn another owner's scalar into a map.
            $conflict = false;
            foreach ($fields as $existingPath => $field) {
                if (!in_array($field['owner'], [$owner, 'legacy'], true)
                    && (str_starts_with($existingPath, $path.'/') || str_starts_with($path, $existingPath.'/'))) {
                    $conflict = true;
                    break;
                }
            }
            if ($conflict) { continue; }
            foreach ($fields as $existingPath => $field) {
                if (in_array($field['owner'], [$owner, 'legacy'], true)
                    && (str_starts_with($existingPath, $path.'/') || str_starts_with($path, $existingPath.'/'))) {
                    unset($fields[$existingPath]);
                }
            }
            $old = isset($fields[$path]) ? PropertyValue::fromArray($fields[$path]) : null;
            if ($old !== null && !in_array($old->owner, [$owner, 'legacy'], true)) {
                continue;
            }
            if ($old !== null && $old->owner === $owner && json_encode($old->value, JSON_THROW_ON_ERROR) === json_encode($value, JSON_THROW_ON_ERROR) && $old->provenance === $provenance) {
                continue;
            }
            $fields[$path] = PropertyValue::create($value, 'meta', $owner, $provenance)->toArray();
        }
        $dataset = [];
        foreach ($fields as $path => $field) {
            self::assign($dataset, explode('/', $path), $field['value']);
        }
        // The identity may not be changed by a writer for another dataset.
        if (isset($existing['dataset']['datasetKey']) && $existing['dataset']['datasetKey'] !== $incoming['datasetKey']) {
            throw new \InvalidArgumentException('Cannot change dataset identity.');
        }
        $properties = DatasetMetadata::properties($dataset);
        foreach ($properties as $key => $property) {
            $path = match ($key) {
                PropertyKey::LABEL, PropertyKey::DESCRIPTION, PropertyKey::TAGS => $key,
                default => str_starts_with($key, PropertyKey::TITLE_PREFIX)
                    ? 'extras/titleRecord/'.substr($key, strlen(PropertyKey::TITLE_PREFIX)) : 'extras/'.$key,
            };
            if ($key === PropertyKey::TAGS && !isset($fields[$path])) { $path = 'extras/tags'; }
            if (isset($fields[$path])) {
                $origin = $fields[$path];
                if (str_starts_with($key, PropertyKey::TITLE_PREFIX) && isset($dataset['extras']['titleRecord']['source'])) {
                    $origin['provenance']['sourceRef'] = $dataset['extras']['titleRecord']['source'];
                }
                $properties[$key] = PropertyValue::fromArray($origin);
            }
        }
        foreach ($overrides as $key => $value) {
            if (in_array($key, [PropertyKey::SCHEMA_VERSION, PropertyKey::ROW_COUNT], true)) {
                throw new \InvalidArgumentException('Cannot override a system property: '.$key);
            }
            PropertyKey::validate($key, $value);
            $old = $existing['dataset']['extras'][PropertyKey::PROPERTIES][$key] ?? null;
            $properties[$key] = $old !== null && $old['source'] === 'human' && json_encode($old['value'], JSON_THROW_ON_ERROR) === json_encode($value, JSON_THROW_ON_ERROR)
                ? PropertyValue::fromArray($old)
                : PropertyValue::create($value, 'human', 'dataset.overrides', ['sourceRef' => '_meta/dataset.overrides.json']);
        }
        return ['dataset' => DatasetMetadata::apply($dataset, $properties), '_metadata' => ['version' => 1, 'fields' => $fields]];
    }

    private static function flatten(array $data, string $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if ($key === PropertyKey::PROPERTIES || $value === null) { continue; }
            $path = $prefix.str_replace(['~', '/'], ['~0', '~1'], (string) $key);
            if (is_array($value) && !array_is_list($value)) {
                $result += self::flatten($value, $path.'/');
            } else {
                $result[$path] = $value;
            }
        }
        return $result;
    }

    private static function assign(array &$data, array $parts, mixed $value): void
    {
        $key = str_replace(['~1', '~0'], ['/', '~'], array_shift($parts));
        if ($parts === []) { $data[$key] = $value; return; }
        $data[$key] ??= [];
        self::assign($data[$key], $parts, $value);
    }
}
