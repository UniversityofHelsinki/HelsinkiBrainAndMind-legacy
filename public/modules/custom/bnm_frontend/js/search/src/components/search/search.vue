<template>
  <Container class="search">
    <div class="search__item">
      <SearchField @handleKeywordSubmit="handleAddKeyword" @handleUpdate="handleInputValueChange" class="search__item"></SearchField>
      <SearchFieldKeywords :keywords="selectedKeywords" v-if="selectedKeywords.length > 0" @handleRemoveKeyword="handleRemoveKeyword"></SearchFieldKeywords>
    </div>
    <SearchDropdown @handleChange="handleDropdownChange" class="search__item"></SearchDropdown>
    <ButtonGroup class="search__item" :isReversed="true">
      <Button @handleClick="handleSearchButtonClick" :isPrimary="true">Search</Button>
      <Button @handleClick="handleResetButtonClick" :isSecondary="true">Reset</Button>
    </ButtonGroup>
  </Container>
</template>

<script>
import Container from '../container/container';
import SearchField from './search-field/search.field';
import SearchDropdown from './search-dropdown/search.dropdown';
import SearchFieldKeywords from './search-keywords/search.keywords'
import ButtonGroup from '../button/button.group.vue';
import Button from '../button/button.vue';
import './search.scss';

export default {
  name: 'Search',
  components: {
    Container,
    SearchField,
    SearchDropdown,
    ButtonGroup,
    Button,
    SearchFieldKeywords
  },
  data(){
    return {
      currentKeyword: '',
      selectedOption: 0,
      selectedKeywords: []
    }
  },
  methods: {
    handleSearchButtonClick() {
      console.log('search');
    },
    handleResetButtonClick() {
      console.log('reset');
    },
    handleDropdownChange(id) {
      this.selectedOption = id;
    },
    handleInputValueChange(keyword) {
      this.currentKeyword = keyword;
    },
    handleAddKeyword(keyword) {
      if (this.selectedKeywords.includes(keyword)) return;

      this.selectedKeywords = [...this.selectedKeywords, keyword]
      this.currentKeyword = '';
    },
    handleRemoveKeyword(keyword) {
      this.selectedKeywords = this.selectedKeywords.filter(selectedKeyword => selectedKeyword !== keyword);
    }
  }
}
</script>