# BNM rest module

Module contains rest-api related modifications.

## Endpoints

### Initial Frontend Data

```
// Endpoint returns {footer_menu} and {affiliates} objects.
$ GET /initial-frontend-data
// Footer menu
Return: Array of footer menu items as objects [{ title: string, link: string, external: boolean, downloadable: boolean }, ...]
// Affiliates
Return: Array of affiliate taxonomy term objects [{ id: int, name: string, children: [int] }, ...]
```

### Suggestions

```
// Parameters: q - string: keyword for autocomplete
$ GET /search_suggestions
// Example query
$ /search_suggestions?q=uni
// Example query returns
$ ['unit', 'unity', 'universe', 'university']
```

### Researchgroup search

```
// Parameters q - string: comma separated list of keywords, affiliate - int: tid of affiliate
$ GET /researchgroup_search
// Example query
$ /researchgroup_search?q=brain,uni&affiliate=2
// Example query returns
$ Array of search results objects (Check the endpoint for full list)
```