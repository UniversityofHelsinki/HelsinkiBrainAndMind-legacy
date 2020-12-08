<template>
  <ul class="search-suggestions">
    <SearchFieldSuggestionsItem v-for="suggestion in suggestions" :key="suggestion" :inputReset="inputReset">
      {{suggestion}}
    </SearchFieldSuggestionsItem>
  </ul>
</template>

<script>
import SearchFieldSuggestionsItem from './search.field.suggestions.item';
import './search-field.suggestions.scss';

export default {
  name: 'SearchFieldSuggestions',
  props: {
    suggestions: Array,
    inputReset: Function
  },
  components: {
    SearchFieldSuggestionsItem
  }
}
</script>