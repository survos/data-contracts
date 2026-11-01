# Authority references

`AuthorityReference` is a portable reference, not a copy of an authority record.
Serialize a list under `ItemField::AUTHORITIES`. Folio currently preserves this list
in row extras, available identically from SQLite and DuckDB readers.

- `authorityType`: a scheme, using `Authority::GEONAMES`, `GEOBOUNDARIES`, or `WIKIDATA`.
  Other schemes can be registered by consuming apps without changing the DTO.
- `id`: the authority's string identifier, never a local database surrogate ID.
- `relation`: `SAME_ENTITY` or `BOUNDARY`; boundary geometry is a representation of
  a place and must not be conflated with the place identity.
- `iso3`: country scope, required for geoBoundaries shape IDs.
- `revision`: optional pin. Null means the configured current authority store.
  Resolvers must not silently substitute current data for a requested revision.

Hosts resolve records and generate links using their configured authority bundles.
Do not persist host URLs, credentials, polygons, or copied authority facts here.
Source assertions and the original source archive remain separate. A reference is
not permission to overwrite a provider's assertions with a newly fetched value.

Folio detail templates expose a `folio_authorities` block in both readers, allowing
hosts to present references without coupling folio-bundle to individual authorities.
