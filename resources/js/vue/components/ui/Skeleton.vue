<template>
  <div
    :class="[
      'animate-pulse bg-gray-200',
      className
    ]"
    :style="customStyle"
  ></div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  className: {
    type: String,
    default: ''
  },
  width: {
    type: String,
    default: null
  },
  height: {
    type: String,
    default: null
  },
  circle: {
    type: Boolean,
    default: false
  },
  rounded: {
    type: String,
    default: 'rounded-lg'
  }
});

const customStyle = computed(() => {
  const styles = {};
  if (props.width) styles.width = props.width;
  if (props.height) styles.height = props.height;
  if (props.circle) styles.borderRadius = '50%';
  return styles;
});
</script>
