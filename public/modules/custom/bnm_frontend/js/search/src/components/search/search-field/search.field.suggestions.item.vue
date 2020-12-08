<template>
  <li class="search-suggestions__item"><button @click="handleAddKeyword" class="search-suggestions__button"><slot /></button></li>
</template>

<script>

export default {
  name: 'SearchFieldSuggestionsItem',
  props: {
    inputReset: Function
  },
  methods: {
    handleAddKeyword(event) {
      this.$parent.$parent.$emit('handleKeywordSubmit', event.target.textContent);
      this.inputReset();
    }
  }
}
</script>