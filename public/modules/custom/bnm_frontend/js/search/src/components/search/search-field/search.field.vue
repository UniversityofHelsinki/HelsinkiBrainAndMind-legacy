<template>
  <div class="search-field">
    <div role="combobox" :aria-expanded="suggestions.length > 0 ? 'true' : 'false'" aria-owns="listbox-suggestions" aria-haspopup="listbox" id="combobox-suggestions">
      <label for="keywords" id="keywords-label" class="search-field__label">Keywords</label>
      <input type="text" id="keywords" :value="currentKeyword" class="search-field__input" placeholder="Search ..." @input="handleSuggestions" @keyup.enter="handleAddKeyword" @keyup.esc="suggestionsReset" aria-autocomplete="list" aria-controls="listbox-suggestions" aria-activedescendant="IDREF">
    </div>
    <SearchFieldSuggestions :class="{ 'is-open': suggestions.length > 0 }" :inputReset="inputReset" :suggestions="this.suggestions" aria-labelledby="keywords-label" role="listbox" id="listbox-suggestions"></SearchFieldSuggestions>
  </div>
</template>

<script>
import './search-field.scss';
import SearchFieldSuggestions from './search.field.suggestions.vue';

const BACKEND_URL = process.env.VUE_APP_BACKEND_URL;

export default {
  components: { SearchFieldSuggestions },
  name: 'SearchField',
  props: {
    currentKeyword: String,
  },
  data(){
    return {
      suggestions: [],
      inputKeywordsValue: '',
    }
  },
  methods: {
    handleInputValueChange(keyword) {
      this.$emit('handleUpdate', keyword);
    },
    handleSuggestions(event) {
      const { value: keyword } = event.target;
      this.handleInputValueChange(keyword);

      if (keyword.length >= 2) {
        setTimeout(() => {
          this.axios.get(`${BACKEND_URL}/search_suggestions?q=${keyword}`, {}, {
            headers: {
              'Content-type': 'application/json',
            },
          })
          .then(({data: results}) => {
              this.suggestions = results;
          })
        }, 500)

        this.inputKeywordsValue = keyword;
      }
    },
    handleAddKeyword() {
      this.$emit('handleKeywordSubmit', event.target.value);
      this.inputReset();
    },
    suggestionsReset() {
      this.suggestions = [];
    },
    inputReset() {
      this.suggestionsReset();
      this.inputKeywordsValue = '';
    }
  }
}
</script>
