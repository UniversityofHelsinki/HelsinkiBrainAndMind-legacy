<template>
  <li class="search-keywords__item">
    <button class="search-keywords__button" @click="handleRemoveKeyword">
      <slot />
      <span>
        <IconClear></IconClear>
      </span>
    </button>
  </li>
</template>

<script>
import IconClear from '../../../assets/icons/icon--clear.vue';

export default {
  name: 'SearchKeywordsItem',
  components: { IconClear },
  methods: {
    handleRemoveKeyword(event) {
      this.$parent.$emit('handleRemoveKeyword', event.target.textContent);
    }
  }
}
</script>