<template>
    <ul>
        <li for="keyword in keywords">
            {{ keyword }}
        </li>
    </ul>
</template>

<script>
  export default {
    name: 'KeywordList',
    props: {
      keywords: {
        type: Array,
      }
    }
  }
</script>

<style lang="scss">
</style>
