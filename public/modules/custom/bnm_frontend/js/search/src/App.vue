<template>
  <SearchPage></SearchPage>
</template>

<script>
import SearchPage from './pages/SearchPage.vue'
export default {
  name: 'App',
  components: {
    SearchPage,
  },
  props: {
    environment: String,
  },
}
</script>

<style lang="scss">
  #app {
    font-family: $default-font-family;
    padding-bottom: $spacing-medium;
    padding-top: $spacing-medium;

    @media screen and (min-width: $small) {
      padding-bottom: $spacing-large;
      padding-top: $spacing-large;
    }

    @media screen and (min-width: $large) {
      padding-bottom: $spacing-extra-large;
      padding-top: $spacing-extra-large;
    }
  }
</style>
