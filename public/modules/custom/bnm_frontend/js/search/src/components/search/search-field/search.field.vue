<template>
  <div class="search-field">
    <div role="combobox" :aria-expanded="suggestions.length > 0 ? 'true' : 'false'" aria-owns="listbox-suggestions" aria-haspopup="listbox" id="combobox-suggestions">
      <label for="keywords" id="keywords-label" class="search-field__label">Keywords</label>
      <input type="text" id="keywords" :value="currentKeyword" class="search-field__input" placeholder="Search ..." @input="handleSuggestions" @keyup.enter="handleAddKeyword" aria-autocomplete="list" aria-controls="listbox-suggestions" aria-activedescendant="IDREF">
    </div>
    <SearchFieldSuggestions :class="{ 'is-open': suggestions.length > 0 }" :inputReset="inputReset" :suggestions="this.suggestions" aria-labelledby="keywords-label" role="listbox" id="listbox-suggestions"></SearchFieldSuggestions>
  </div>
</template>

<script>
import './search-field.scss';
import SearchFieldSuggestions from './search.field.suggestions.vue';

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

      // TODO add throttling
      if (keyword.length > 2) {
        this.axios.get(`http://Brain:bnm_2020@dev.bnm.druidfi.wod.by/search_suggestions?q=${keyword}`, {}, {
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
