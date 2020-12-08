<template>
  <Container class="search">
    <SearchField class="search__item"></SearchField>
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
    Button
  },
  data(){
    return {
      selectedOption: 0,
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
    }
  }
}
</script>