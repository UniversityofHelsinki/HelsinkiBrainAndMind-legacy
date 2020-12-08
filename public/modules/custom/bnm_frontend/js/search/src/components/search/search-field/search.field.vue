<template>
  <div class="search-field">
    <label for="keywords" class="search-field__label">Keywords</label>
    <input type="text" id="keywords" :value="inputKeywordsValue" class="search-field__input" placeholder="Search ..." @input="handleSuggestions" @keyup.enter="handleAddKeyword">
    <SearchFieldSuggestions :class="{ 'is-open': suggestions.length > 0 }" :suggestions="this.suggestions"></SearchFieldSuggestions>
  </div>
</template>

<script>
import './search-field.scss';
import SearchFieldSuggestions from './search.field.suggestions.vue';

export default {
  components: { SearchFieldSuggestions },
  name: 'SearchField',
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

      // TODO add throttling
      if (keyword.length > 2) {
        this.axios.get(`https://bnm.docker.sh/search_suggestions?q=${keyword}`, {}, {
          headers: {
            'Content-type': 'application/json',
          },
        })
        .then(({data: results}) => {
            this.suggestions = results;
        })

        this.inputKeywordsValue = keyword;
      }
    },
    handleAddKeyword() {
      this.$emit('handleKeywordSubmit', event.target.value);
      this.inputReset();
    },
    inputReset() {
      this.suggestions = [];
      this.inputKeywordsValue = '';
    }
  }
}
</script>