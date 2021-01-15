<template>
  <Container v-if="results.length > 0" class="search-results">
    <Listing :col3="true">
      <ListingItem v-for="result in results" :key="result">
        <ResearchGroupTeaser v-bind="result"></ResearchGroupTeaser>
      </ListingItem>
    </Listing>
  </Container>
</template>

<script>
import Container from '../container/container.vue';
import Listing from '../listing/listing.vue'
import ListingItem from '../listing/listing.item.vue'
import ResearchGroupTeaser from '../research-group-teaser/research-group-teaser.vue'

export default {
  name: 'SearchResults',
  props: {
    results: Array,
  },
  components: {
    Container,
    Listing,
    ListingItem,
    ResearchGroupTeaser
  }
}
</script>
