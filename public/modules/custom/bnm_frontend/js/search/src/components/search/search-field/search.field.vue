<template>
  <div class="search">
    <div class="search-field">
      <label for="keywords" class="search-field__label">Keywords</label>
      <input type="text" id="keywords" class="search-field__input">
      <SearchFieldSuggestions></SearchFieldSuggestions>
    </div>
  </div>
</template>

<script>
import './search-field.scss';
import SearchFieldSuggestions from './search.field.suggestion.vue';

export default {
  components: { SearchFieldSuggestions },
  name: 'SearchField'
}
</script>