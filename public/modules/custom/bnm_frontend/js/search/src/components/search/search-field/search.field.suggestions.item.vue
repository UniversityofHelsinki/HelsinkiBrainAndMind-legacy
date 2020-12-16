<template>
  <li class="search-suggestions__item" :id="id" :aria-selected="id === currentlyActiveDescendant ? true : false">
    <button @keyup.enter="handleAddKeyword" @mousedown="handleAddKeyword" @keydown.down="handleArrowDown" @keydown.up="handleArrowUp" @keyup.esc="this.$parent.$parent.inputReset()" @keydown.tab="this.$parent.$parent.inputSoftReset()" class="search-suggestions__button" tabindex="-1">
      <slot />
    </button>
  </li>
</template>

<script>

export default {
  name: 'SearchFieldSuggestionsItem',
  props: {
    id: String,
    inputReset: Function,
    currentlyActiveDescendant: String,
  },
  methods: {
    handleAddKeyword(event) {
      this.$parent.$parent.$emit('handleKeywordSubmit', event.target.textContent);
      this.inputReset();
    },
    handleArrowUp(event) {
      const { parentElement: parent } = event.target;
      const suggestionsListItemElements = parent.parentElement.querySelectorAll('li.search-suggestions__item');
      const suggestionsListButtonElements = parent.parentElement.querySelectorAll('button.search-suggestions__button');
      const suggestionsListLastButtonElement = suggestionsListButtonElements[suggestionsListButtonElements.length - 1];
      const previousListItemSibling = parent.previousElementSibling;

      if (!previousListItemSibling) {
        this.$parent.$emit('handleActiveDescendantChange', suggestionsListItemElements[suggestionsListItemElements.length - 1].getAttribute('id'));
        suggestionsListLastButtonElement.focus();
        return;
      }

      const previousButtonSibling = previousListItemSibling.querySelector('button.search-suggestions__button');

      this.$parent.$emit('handleActiveDescendantChange', previousListItemSibling.getAttribute('id'));
      previousButtonSibling.focus();
    },
    handleArrowDown(event) {
      const { parentElement: parent } = event.target;
      const suggestionsListItemElements = parent.parentElement.querySelectorAll('li.search-suggestions__item');
      const suggestionsListButtonElements = parent.parentElement.querySelectorAll('button.search-suggestions__button');
      const suggestionsListFirstButtonElement = suggestionsListButtonElements[0];
      const nextListItemSibling = parent.nextElementSibling;

      if (!nextListItemSibling) {
        this.$parent.$emit('handleActiveDescendantChange', suggestionsListItemElements[0].getAttribute('id'));
        suggestionsListFirstButtonElement.focus();
        return;
      }

      const nextButtonSibling = nextListItemSibling.querySelector('button.search-suggestions__button');

      this.$parent.$emit('handleActiveDescendantChange', nextListItemSibling.getAttribute('id'));
      nextButtonSibling.focus();
    }
  }
}
</script>