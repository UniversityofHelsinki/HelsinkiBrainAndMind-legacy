<template>
  <Container class="search">
    <SearchDropdown @handleChange="handleDropdownChange" class="search__item" :selectedOption="this.selectedOption"></SearchDropdown>
    <div class="search__item">
      <SearchField @handleKeywordSubmit="handleAddKeyword" @handleUpdate="handleInputValueChange" class="search__item" :currentKeyword="currentKeyword" ref="suggestionReset"></SearchField>
      <SearchFieldKeywords :keywords="selectedKeywords" v-if="selectedKeywords.length > 0" @handleRemoveKeyword="handleRemoveKeyword"></SearchFieldKeywords>
    </div>
    <div>
    <CollaborationFilter @handleChange="handleCollaborationCheckbox"/>
    </div>
    <ButtonGroup class="search__item" :isReversed="true">
      <Button @handleClick="handleSearchButtonClick" @handleMouseClick="handleMouseClick" :isPrimary="true" :isSubmit="true">Search</Button>
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
import useInitialData from '../../stores/data';
import './search.scss';
import CollaborationFilter from "@/components/collaboration-filter/collaboration.filter.vue";

export default {
  name: 'Search',
  components: {
    CollaborationFilter,
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
      selectedKeywords: [],
      industrialCollaborationOnly: false,
    }
  },
  methods: {
    async handleSearchButtonClick() {
      const keywords = this.selectedKeywords.join(',');
      const affiliate = this.selectedOption;
      const apiEndpoint = 'researchgroup_search';
      const queryString = `?q=${keywords}&affiliate=${affiliate}`;

      this.$refs.suggestionReset.suggestionsReset();
      const { fetchResults, setResultsLoadingStatus } = useInitialData();
      setResultsLoadingStatus();
      fetchResults(apiEndpoint, queryString, this.$environment);
    },
    handleCollaborationCheckbox() {
      console.log('checkbox ticked')
    },
    handleResetButtonClick() {
      this.selectedKeywords = [];
      this.handleDropdownChange(0);
      this.handleInputValueChange('');
      const { resetResults } = useInitialData();
      resetResults();
    },
    handleDropdownChange(id) {
      this.selectedOption = Number(id);
    },
    handleInputValueChange(keyword) {
      this.currentKeyword = keyword;
    },
    handleMouseClick() {
      if (this.selectedKeywords.includes(this.currentKeyword) || this.currentKeyword === '') return;

      this.handleAddKeyword(this.currentKeyword);
    },
    handleAddKeyword(keyword) {
      if (this.selectedKeywords.includes(keyword) || keyword === '') return;

      this.selectedKeywords = [...this.selectedKeywords, keyword]
      this.handleSearchButtonClick();
      this.currentKeyword = '';
    },
    handleRemoveKeyword(keyword) {
      this.selectedKeywords = this.selectedKeywords.filter(selectedKeyword => selectedKeyword !== keyword);
    }
  }
}
</script>
