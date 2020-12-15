<template>
  <li class="search-suggestions__item">
    <button @click="handleAddKeyword" @keydown.down="handleArrowDown" @keydown.up="handleArrowUp" class="search-suggestions__button" tabindex="-1">
      <slot />
    </button>
  </li>
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
    },
    handleArrowUp(event) {
      const { parentElement: parent } = event.target;
      const suggestionsListButtonElements = parent.parentElement.querySelectorAll('button.search-suggestions__button');
      const suggestionsListLastButtonElement = suggestionsListButtonElements[suggestionsListButtonElements.length - 1];
      const previousListItemSibling = parent.previousElementSibling;

      if (!previousListItemSibling) {
        suggestionsListLastButtonElement.focus();
        return;
      }

      const nextButtonSibling = previousListItemSibling.querySelector('button.search-suggestions__button');

      nextButtonSibling.focus();
    },
    handleArrowDown(event) {
      const { parentElement: parent } = event.target;
      const suggestionsListButtonElements = parent.parentElement.querySelectorAll('button.search-suggestions__button');
      const suggestionsListFirstButtonElement = suggestionsListButtonElements[0];
      const nextListItemSibling = parent.nextElementSibling;

      if (!nextListItemSibling) {
        suggestionsListFirstButtonElement.focus();
        return;
      }

      const nextButtonSibling = nextListItemSibling.querySelector('button.search-suggestions__button');

      nextButtonSibling.focus();
    }
  }
}
</script>