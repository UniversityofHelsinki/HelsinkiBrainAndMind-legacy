<template>
  <div class="search-keywords">
    <h2 class="search-keywords__label">Added keywords</h2>
    <ul class="search-keywords__listing">
      <SearchKeywordsItem v-for="keyword in keywords" :key="keyword">{{ keyword }}</SearchKeywordsItem>
    </ul>
  </div>
</template>

<script>
import SearchKeywordsItem from './search.keywords.item.vue';
import './search.keywords.scss';

export default {
  name: 'SearchKeywords',
  components: { SearchKeywordsItem },
  props: {
    keywords: Array,
  }
}
</script>