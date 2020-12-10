<template>
  <li class="description-list__item" >
    <h3 class="description-list__label">
      {{ label }}
    </h3>
    <p class="description-list__description">
      <slot />
    </p>
  </li>
</template>

<script>

export default {
  name: 'DescriptionListItem',
  props: {
    label: String,
  }
}
</script>