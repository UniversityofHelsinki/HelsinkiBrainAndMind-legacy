<template>
  <div class="search-keywords">
    <h2 class="search-keywords__label">Added keywords</h2>
    <ul class="search-keywords__listing">
      <li class="search-keywords__item">
        <button class="search-keywords__button">
          Matti
          <span>
            <IconClear></IconClear>
          </span>
        </button>
      </li>
          <li class="search-keywords__item">
        <button class="search-keywords__button">
          Brain
          <span>
            <IconClear></IconClear>
          </span>
        </button>
      </li>
          <li class="search-keywords__item">
        <button class="search-keywords__button">
          Helsinki
          <span>
            <IconClear></IconClear>
          </span>
        </button>
      </li>
      <li class="search-keywords__item">
        <button class="search-keywords__button">
          Website
          <span>
            <IconClear></IconClear>
          </span>
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
import IconClear from '../../../assets/icons/icon--clear.vue';
import './search-field.keywords.scss';

export default {
  name: 'SearchFieldKeywords',
  components: { IconClear },
}
</script>