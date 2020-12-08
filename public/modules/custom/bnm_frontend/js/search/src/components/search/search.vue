<template>
  <Container class="search">
    <SearchField class="search__item"></SearchField>
    <SearchDropdown class="search__item"></SearchDropdown>
  </Container>
</template>

<script>
import Container from '../container/container';
import SearchField from './search-field/search.field';
import SearchDropdown from './search-dropdown/search.dropdown';

export default {
  name: 'Search',
  components: {
    Container,
    SearchField,
    SearchDropdown
  }
}
</script>