<template>
  <div class="info-icon">
    <button :aria-label="getName()" @blur="setExpandedState()" @focus="setExpandedState()" @mouseleave="setExpandedState()" @mouseenter="setExpandedState()" class="info-icon__button" :aria-expanded="isExpanded" :aria-describedby="this.getId()">
      <component :is="icon"></component>
    </button>
    <component class="info-icon__description" :id="this.getId()" role="tooltip" :is="text" ></component>
  </div>
</template>

<script>
  import './info-icon.scss';
  import Question from '../../assets/icons/icon--questionmark.vue';
  import TextKeywordHelp from '../../assets/texts/text--keyword-help';
  import TextOrganizationsHelp from '../../assets/texts/text--organizations-help';

  export default {
    name: 'InfoIcon',
    components: {
      Question,
      TextKeywordHelp,
      TextOrganizationsHelp
    },
    data() {
      return {
        isExpanded: false
      }
    },
    methods: {
      getId() {
        return this.text;
      },
      getName() {
        return this.iconName;
      },
      setExpandedState() {
        this.isExpanded = !this.isExpanded;
      }
    },
    props: {
      icon: {
        type: String,
        default: 'Question'
      },
      text: {
        type: String,
        default: 'TextKeywordHelp',
      },
      iconName: String,
    }
  }
</script>
