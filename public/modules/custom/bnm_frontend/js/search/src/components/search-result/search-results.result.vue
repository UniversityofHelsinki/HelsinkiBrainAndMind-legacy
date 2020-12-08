<template>
  <article class="search-result">
    <div class="search-result__header">
      <h2 class="search-result__title">
        {{ field_firstname }}<br/>
        {{ field_lastname}}
      </h2>
      <p class="search-result__email">
        {{ field_email }}
      </p>
    </div>
    <ul class="description-list">
      <li class="description-list__item">
        <h3 class="description-list__label">
          Group name
        </h3>
        <p class="description-list__description">
          {{ field_research_group_name }}
        </p>
      </li>
      <li class="description-list__item">
        <h3 class="description-list__label">
          Main affiliation, faculty
        </h3>
        <p class="description-list__description">
          {{ field_main_affiliation }}, {{ field_faculty_unit }}
        </p>
      </li>
      <li class="description-list__item">
        <h3 class="description-list__label">
          Other affiliations
        </h3>
        <p class="description-list__description">
          {{ field_other_affiliations }}
        </p>
      </li>
      <li class="description-list__item">
        <h3 class="description-list__label">
          Links
        </h3>
        <a v-for="link in field_links" :key="link" href="{{ link.href }}" class="description-list__link">{{ link.title }}</a>
      </li>
      <li class="description-list__item">
        <h3 class="description-list__label">
          Keywords
        </h3>
        <ul class="description-list__list">
          <li v-for="keyword in field_keywords" :key="keyword">
            <span class="search-result__keyword">{{ keyword }}</span>
          </li>
        </ul>
      </li>
    </ul>
  </article>
</template>

<script>
export default {
  name: 'SearchResult',
  props: {
    url: String,
    title: String,
    field_email: String,
    field_faculty_unit: String,
    field_other_affiliations: String,
    field_research_group_name: String,
    field_firstname: String,
    field_lastname: String,
    field_links: Array,
    field_keywords: Array,
    field_main_affiliation: String
  }
}
</script>