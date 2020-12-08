<template>
  <Container v-if="results.length > 0" class="search-results">
    <Listing :col3="true">
      <ListingItem v-for="result in results" :key="result">
        <SearchResult v-bind="result"></SearchResult>
      </ListingItem>
    </Listing>
  </Container>
</template>

<script>
import Container from '../container/container.vue';
import Listing from '../listing/listing.vue'
import ListingItem from '../listing/listing.item.vue'
import SearchResult from './search-results.result.vue';

export default {
  name: 'SearchResults',
  props: {
    results: Array,
  },
  components: {
    Container,
    Listing,
    ListingItem,
    SearchResult
  }
}
</script>