<template>
  <li class="search-keywords__item">
    <button class="search-keywords__button">
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
  components: { IconClear }
}
</script>