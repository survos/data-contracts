<?php

declare(strict_types=1);

namespace Survos\DataContracts\Metadata;

/** Framework-free projection between dataset declarations and portable folio properties. */
final class DatasetMetadata
{
    public static function titleRecord(array $values): array
    {
        $title = [];
        foreach ($values as $key => $value) {
            if (str_starts_with($key, PropertyKey::TITLE_PREFIX)) {
                $title[substr($key, strlen(PropertyKey::TITLE_PREFIX))] = $value;
            }
        }
        return $title;
    }

    /** @return array<string, PropertyValue> */
    public static function properties(array $dataset): array
    {
        $extras = $dataset['extras'] ?? [];
        $result = [];
        $owner = 'dataset:'.($dataset['aggregator'] ?? 'legacy');
        foreach ([PropertyKey::LABEL, PropertyKey::DESCRIPTION] as $key) {
            if (array_key_exists($key, $dataset)) {
                $result[$key] = PropertyValue::create($dataset[$key], 'meta', $owner);
            }
        }
        foreach ([PropertyKey::CONTENT_TYPE, PropertyKey::TAGS] as $key) {
            if (array_key_exists($key, $extras)) {
                $result[$key] = PropertyValue::create($extras[$key], 'meta', $owner);
            }
        }
        if (isset($dataset[PropertyKey::TAGS])) {
            $result[PropertyKey::TAGS] = PropertyValue::create($dataset[PropertyKey::TAGS], 'meta', $owner);
        }
        $title = $extras[PropertyKey::TITLE_RECORD] ?? [];
        foreach ($title as $key => $value) {
            $result[PropertyKey::TITLE_PREFIX.$key] = PropertyValue::create($value, 'meta', $owner,
                isset($title['source']) ? ['sourceRef' => $title['source']] : []);
        }
        foreach ($extras[PropertyKey::PROPERTIES] ?? [] as $key => $property) {
            $result[$key] = PropertyValue::fromArray($property);
        }
        foreach ($result as $key => $property) {
            PropertyKey::validate($key, $property->value);
        }
        return $result;
    }

    /** Write resolved portable values back to the compatibility dataset representation. */
    public static function apply(array $dataset, array $properties): array
    {
        $encoded = [];
        foreach ($properties as $key => $property) {
            PropertyKey::validate($key, $property->value);
            $encoded[$key] = $property->toArray();
            if (in_array($key, [PropertyKey::LABEL, PropertyKey::DESCRIPTION, PropertyKey::TAGS], true)) {
                $dataset[$key] = $property->value;
            } elseif ($key === PropertyKey::CONTENT_TYPE) {
                $dataset['extras'][$key] = $property->value;
            } elseif (str_starts_with($key, PropertyKey::TITLE_PREFIX)) {
                $dataset['extras'][PropertyKey::TITLE_RECORD][substr($key, strlen(PropertyKey::TITLE_PREFIX))] = $property->value;
            }
        }
        $dataset['extras'][PropertyKey::PROPERTIES] = $encoded;
        return $dataset;
    }
}
