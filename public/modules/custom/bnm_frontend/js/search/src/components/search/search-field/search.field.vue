<template>
  <div class="search__item">
    <div class="search-field">
      <label for="keywords" class="search-field__label">Keywords</label>
      <input type="text" id="keywords" class="search-field__input">
      <SearchFieldSuggestions></SearchFieldSuggestions>
    </div>
    <SearchFieldKeywords></SearchFieldKeywords>
  </div>
</template>

<script>
import './search-field.scss';
import SearchFieldSuggestions from './search.field.suggestions.vue';
import SearchFieldKeywords from './search.field.keywords.vue';

export default {
  components: { SearchFieldSuggestions, SearchFieldKeywords },
  name: 'SearchField'
}
</script>