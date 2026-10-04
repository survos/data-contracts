# Dataset metadata and provenance

`DatasetDocument` is the shared contract used by dataset-bundle and Folio. It
separates writer ownership from origin. `PropertyValue` contains the JSON value,
source category (`meta`, `build`, `human`, `import`), writer/owner identifier,
UTC `updatedAt`, and a provenance object. Copying a value into a folio preserves
its timestamp and provenance. The timestamp records a value/ownership change,
not the source's publication date or an invented retrieval time.

## Durable files

- `vault/<provider>/<dataset>/_meta/dataset.json` is authoritative.
- `work/<provider>/<dataset>/_meta/dataset.json` is its compatible projection.
- `vault/<provider>/<dataset>/_meta/dataset.overrides.json` holds human overrides.

Existing work and vault metadata are adopted lazily on the next metadata write:
newer fields win, unknown fields from the other copy survive. Existing JSON is
validated before replacement. Writes lock the vault document, merge, and atomically
replace it before updating the work projection. An interrupted work projection is
repaired on retry. Deleting work cannot delete the authoritative metadata.

The outer `dataset` object remains compatible with existing consumers. An outer
`_metadata` object retains generated values and their owners, independently of
human overrides. Its field paths use JSON Pointer escaping for `/` and `~`.
Portable properties with provenance are carried in
`dataset.extras.metadataProperties`; they are a projection, not another source.

## Writer snapshots

Pass a stable `owner` to `DatasetMetadataEnsurer::ensureJson()`. For example,
harvest uses `harvest.loc-title` and `harvest.loc-coverage`. Each replaces only its
own fields, including removing fields it no longer supplies. A producer cannot
claim another producer's fields. The first producer to claim a shared field owns
it; overlapping responsibilities should be resolved explicitly, not by call order
on every refresh. Previously unowned legacy fields can be adopted on first write.

Null configuration fields mean “not supplied”; this prevents the many default-null
fields in DatasetConfiguration from erasing another producer's metadata. Empty
lists remain explicit values. To clear a nullable descriptive value deliberately,
use an explicit-null human override. Unknown legacy configuration fields survive.

Default ownership is `dataset:<aggregator>` for existing callers. Multiple writers
for the same provider should adopt distinct owner IDs, as the two LOC paths now do.

## Human overrides

```json
{
  "properties": {
    "description": "An editor's corrected account of the newspaper.",
    "title.essayContributor": ["Verified contributor"],
    "tags": ["newspaper", "local-history"]
  }
}
```

Run the provider's normal metadata refresh after editing. An empty `properties`
object removes overrides and reveals the retained generated values. Put overrides
in the vault; a pre-existing work-side override file is adopted only when the vault
has none. `rowCount` and `schemaVersion` cannot be human overrides.

Manual corrections made directly inside old dataset.json have no recorded owner;
move them into the override file before a producer intentionally refreshes those
same fields. The system does not invent provenance for historical edits.

## Portable vocabulary

`PropertyKey` validates label, description, tags, contentType, rowCount,
schemaVersion and the documented `title.*` fields. `DatasetMetadata` projects
`extras.titleRecord` one level into `title.*`, preserves list structure, and carries
its source URL into title-field provenance. Top-level tags take precedence over
legacy `extras.tags`. Source configuration, batch coverage and other operational
extras stay in the dataset document, not in Folio's descriptive properties.

Unknown property keys support JSON values. The format records current provenance,
not an append-only audit history or multiple competing assertions per key.
