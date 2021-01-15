<template>
  <div class="search-field">
    <div role="combobox" :aria-expanded="suggestions.length > 0 ? 'true' : 'false'" aria-owns="listbox-suggestions" aria-haspopup="listbox" id="combobox-suggestions">
      <label for="keywords" id="keywords-label" class="search-field__label">Keywords</label>
      <input type="text" id="keywords" :value="currentKeyword" class="search-field__input" placeholder="Search ..." @input="handleSuggestions" @keyup.enter="handleAddKeyword" @keyup.esc="inputReset" aria-autocomplete="list" aria-controls="listbox-suggestions" :aria-activedescendant="activeDescendant" @focus="handleFocus" @keydown.down="handleArrowDown" @keydown.up="handleArrowUp" @keydown.tab="inputSoftReset">
    </div>
    <SearchFieldSuggestions :class="{ 'is-open': suggestions.length > 0 }" :inputReset="inputReset" :suggestions="this.suggestions" aria-labelledby="keywords-label" role="listbox" id="listbox-suggestions" @handleActiveDescendantChange="handleActiveDescendantChange" :currentlyActiveDescendant="this.activeDescendant"></SearchFieldSuggestions>
  </div>
</template>

<script>
import './search-field.scss';
import SearchFieldSuggestions from './search.field.suggestions.vue';
// import getSearchSuggestions from '../../../services/suggestions.service.js';
import { searchSuggestionRequest } from '../../../services/request-helper'

export default {
  components: { SearchFieldSuggestions },
  name: 'SearchField',
  props: {
    currentKeyword: String,
  },
  data() {
    return {
      fetchTimeout: undefined,
      suggestions: [],
      inputKeywordsValue: '',
      activeDescendant: '',
    }
  },
  methods: {
    handleInputValueChange(keyword) {
      this.$emit('handleUpdate', keyword);
    },
    handleSuggestions(event) {
      const fetchNewSuggestions = async () => {
        this.suggestions = searchSuggestionRequest(keyword, this.$environment)
        .then((response) => {
          this.suggestions = response.data;
          this.inputKeywordsValue = keyword
        })
        .catch((error) => {
          console.log(error);
        });
      };

      const { value: keyword } = event.target;
      this.handleInputValueChange(keyword);

      clearTimeout(this.fetchTimeout);

      if (keyword.length >= 2) {
        this.fetchTimeout = setTimeout(fetchNewSuggestions, 300);
      } else {
        this.suggestionsReset();
      }
    },
    handleAddKeyword() {
      this.$emit('handleKeywordSubmit', event.target.value);
      this.inputReset();
    },
    suggestionsReset() {
      this.suggestions = [];
    },
    inputSoftReset() {
      this.suggestionsReset();
      this.handleActiveDescendantChange('');
      this.inputKeywordsValue = '';
    },
    inputReset() {
      this.inputSoftReset()
      this.handleInputValueChange('');
    },
    handleFocus(event) {
      this.handleSuggestions(event);

      const clickListener = document.addEventListener("click", (e) => {
        const isSearchFormClick = (e.target.closest(".search-field"));

        if (!isSearchFormClick) {
          this.inputSoftReset();
          document.removeEventListener("click", clickListener);
        }
      });
    },
    handleArrowUp(event) {
      if (this.suggestions.length === 0) return;
      event.preventDefault();
      const { parentElement: parent } = event.target;

      const suggestionsListElement = parent.parentElement.querySelector('ul.search-suggestions');
      const suggestionsListItemElements = suggestionsListElement.querySelectorAll('li.search-suggestions__item');
      const suggestionsListButtonElements = suggestionsListElement.querySelectorAll('.search-suggestions__button');
      const suggestionsListLastButtonElement = suggestionsListButtonElements[suggestionsListButtonElements.length - 1];

      this.handleActiveDescendantChange(suggestionsListItemElements[suggestionsListItemElements.length - 1].getAttribute('id'));
      suggestionsListLastButtonElement.focus();
    },
    handleArrowDown(event) {
      if (this.suggestions.length === 0) return;
      event.preventDefault();
      const { parentElement: parent } = event.target;

      const suggestionsListElement = parent.parentElement.querySelector('ul.search-suggestions');
      const suggestionsListItemElements = suggestionsListElement.querySelectorAll('li.search-suggestions__item');
      const suggestionsListButtonElements = suggestionsListElement.querySelectorAll('.search-suggestions__button');
      const suggestionsListFirstButtonElement = suggestionsListButtonElements[0];

      this.handleActiveDescendantChange(suggestionsListItemElements[0].getAttribute('id'));
      suggestionsListFirstButtonElement.focus();
    },
    handleActiveDescendantChange(id) {
      this.activeDescendant = id;
    }
  }
}
</script>
