<template>
  <Container class="search">
    <SearchField></SearchField>
  </Container>
</template>

<script>
import Container from '../container/container';
import SearchField from './search-field/search.field';

export default {
  name: 'Search',
  components: {
    Container,
    SearchField,
  }
}
</script>