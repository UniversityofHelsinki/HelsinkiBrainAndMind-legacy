<template>
  <SearchPage></SearchPage>
</template>

<script>
import SearchPage from './pages/SearchPage.vue'

export default {
  name: 'App',
  components: {
    SearchPage
  }
}
</script>

<style lang="scss">
  #app {
    font-family: $default-font-family;
  }
</style>
