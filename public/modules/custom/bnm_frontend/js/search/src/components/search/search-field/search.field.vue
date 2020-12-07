<template>
  <div class="search">
    <div class="search-field">
      <label for="keywords" class="search-field__label">Keywords</label>
      <input type="text" id="keywords" class="search-field__input">
    </div>
  </div>
</template>

<script>
import './search-field.scss';

export default {
  name: 'SearchField'
}
</script>