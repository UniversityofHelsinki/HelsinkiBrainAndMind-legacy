<template>
  <ul class="search-suggestions">
    <SearchFieldSuggestionsItem v-for="(suggestion, index) in suggestions" :key="suggestion" :inputReset="inputReset" role="option" :id="'suggestion-' + index" :currentlyActiveDescendant="currentlyActiveDescendant">
      {{ suggestion }}
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
    inputReset: Function,
    currentlyActiveDescendant: String,
  },
  components: {
    SearchFieldSuggestionsItem
  }
}
</script>