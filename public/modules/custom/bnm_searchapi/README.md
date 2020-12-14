#BNM searchapi

Module contains search-api related modifications.

## Custom processors

### Underscore processor

By default search api indexes keywords word by word (separated by whitespace)

Underscore processor has been made to enable autocompleting of full keywords.

It does so by replacing the whitespaces with underscore

For example:
keyword "dru" would suggest only "drug" and "drugs"

after applying underscore processor:
keyword "dru" suggests drug, drugs, drug discovery, drug addiction, drug delivery
