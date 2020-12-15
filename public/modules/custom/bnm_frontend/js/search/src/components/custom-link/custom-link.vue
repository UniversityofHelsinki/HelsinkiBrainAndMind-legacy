<template>
  <a :href="link" :target="isBlank ? '_blank' : '_self'" class="custom-link">
    <slot />
    <span v-if="isBlank && !isDownloadable" class="visually-hidden">(link opens in a new tab)</span>
    <span v-if="isBlank && isDownloadable" class="visually-hidden">(open or download file)</span>
    <span v-if="isBlank">
      <IconOpenNewTab></IconOpenNewTab>
    </span>
  </a>
</template>

<script>
import IconOpenNewTab from '../../assets/icons/icon--open-new.vue';
import './custom-link.scss';

export default {
  name: 'CustomLink',
  components: {
    IconOpenNewTab
  },
  props: {
    link: String,
    isBlank: Boolean,
    isDownloadable: Boolean
  }
}
</script>