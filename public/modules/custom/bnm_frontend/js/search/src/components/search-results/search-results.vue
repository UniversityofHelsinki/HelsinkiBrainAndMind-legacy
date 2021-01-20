<template>
  <Container v-if="results.length > 0" class="search-results">
    <h2 class="search-results__heading">Results found: {{ this.resultsCount }}</h2>
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
import useInitialData from '../../stores/data';
import './search-results.scss';

export default {
  name: 'SearchResults',
  props: {
    results: Array,
  },
  data() {
    return {
      resultsCount: 0,
    }
  },
  components: {
    Container,
    Listing,
    ListingItem,
    ResearchGroupTeaser
  },
  mounted() {
    const { getResultsCount } = useInitialData();

    this.resultsCount = getResultsCount();
  }
}
</script>
