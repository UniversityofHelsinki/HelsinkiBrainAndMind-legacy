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
    <DescriptionList>
      <DescriptionListItem label="Group name" v-if="field_research_group_name">
        {{ field_research_group_name }}
      </DescriptionListItem>
      <DescriptionListItem label="Main affiliation" v-if="field_main_affiliation && field_faculty_unit">
        {{ field_main_affiliation }}, {{ field_faculty_unit }}
      </DescriptionListItem>
      <DescriptionListItem label="Other affiliations" v-if="field_other_affiliations">
        {{ field_other_affiliations }}
      </DescriptionListItem>
      <DescriptionListItem label="Description" v-if="body">
        {{ strippedBody }}
      </DescriptionListItem>
      <DescriptionListItem label="Interested in industrial collaboration" v-if="field_industrial_collaboration">
        {{ field_industrial_collaboration.charAt(0).toUpperCase() + field_industrial_collaboration.slice(1) }}
      </DescriptionListItem>
      <DescriptionListItem label="Links" v-if="field_links.length > 0">
        <ul class="description-list__list">
          <li v-for="link in field_links" :key="link" class="description-list__list-item">
            <CustomLink :link="link.url" class="description-list__link" :isBlank="true">
              {{ link.title }}
            </CustomLink>
          </li>
        </ul>
      </DescriptionListItem>
      <DescriptionListItem label="Keywords" v-if="field_keywords.length > 0">
        <ul class="description-list__list description-list__list--keywords">
          <li v-for="keyword in field_keywords" :key="keyword" class="description-list__list-item">
            <span class="research-group-teaser__keyword">{{ keyword }}</span>
          </li>
        </ul>
      </DescriptionListItem>
    </DescriptionList>
  </article>
</template>

<script>
import DescriptionList from './description-list/description-list.vue'
import DescriptionListItem from './description-list/description-list.item.vue'
import CustomLink from '../custom-link/custom-link.vue';
import './research-group-teaser.scss';

export default {
  name: 'ResearchGroupTeaser',
  components: {
    DescriptionList, DescriptionListItem, CustomLink
  },
  props: {
    body: String,
    field_email: String,
    field_faculty_unit: String,
    field_firstname: String,
    field_industrial_collaboration: String,
    field_keywords: Array,
    field_lastname: String,
    field_links: Array,
    field_main_affiliation: String,
    field_other_affiliations: String,
    field_research_group_name: String,
  },
  data: function () {
    return {
      strippedBody: this.body?.replace(/(<([^>]+)>)/gi, "")
    }
  }
}
</script>
