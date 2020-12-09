<template>
  <article class="research-group-teaser">
    <div class="research-group-teaser__header">
      <h2 class="research-group-teaser__title">
        {{ field_firstname }}<br/>
        {{ field_lastname}}
      </h2>
      <p class="research-group-teaser__email">
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
        <ul class="description-list__list">
          <li v-for="link in field_links" :key="link" class="description-list__list-item">
            <a href="{{ link.href }}" class="description-list__link">{{ link.title }}</a>
          </li>
        </ul>
      </li>
      <li class="description-list__item">
        <h3 class="description-list__label">
          Keywords
        </h3>
        <ul class="description-list__list description-list__list--keywords">
          <li v-for="keyword in field_keywords" :key="keyword" class="description-list__list-item">
            <span class="research-group-teaser__keyword">{{ keyword }}</span>
          </li>
        </ul>
      </li>
    </ul>
  </article>
</template>

<script>
import './research-group-teaser.scss';

export default {
  name: 'ResearchGroupTeaser',
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