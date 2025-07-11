<template>
  <div class="item">
    <i class="icon">
      <slot name="icon"></slot>
    </i>
    <div class="details">
      <h3 class="heading">
        <slot name="heading"></slot>
      </h3>
      <div class="content">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<style scoped>
.item {
  margin-top: 2rem;
  display: flex;
  position: relative;
}

.details {
  flex: 1;
  margin-left: 1rem;
}

.icon {
  display: flex;
  place-items: center;
  place-content: center;
  width: 32px;
  height: 32px;
  @apply text-gray-600 dark:text-gray-400;
}

.heading {
  font-size: 1.2rem;
  font-weight: 500;
  margin-bottom: 0.4rem;
  @apply text-gray-900 dark:text-white;
}

.content {
  @apply text-gray-600 dark:text-gray-300;
}

.content a {
  @apply text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300;
}

@media (min-width: 1024px) {
  .item {
    margin-top: 0;
    padding: 0.4rem 0 1rem calc(var(--section-gap) / 2);
  }

  .icon {
    top: calc(50% - 25px);
    left: -26px;
    position: absolute;
    border-radius: 8px;
    width: 50px;
    height: 50px;
    @apply border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800;
  }

  .item:before {
    content: ' ';
    position: absolute;
    left: 0;
    bottom: calc(50% + 25px);
    height: calc(50% - 25px);
    @apply border-l border-gray-200 dark:border-gray-700;
  }

  .item:after {
    content: ' ';
    position: absolute;
    left: 0;
    top: calc(50% + 25px);
    height: calc(50% - 25px);
    @apply border-l border-gray-200 dark:border-gray-700;
  }

  .item:first-of-type:before {
    display: none;
  }

  .item:last-of-type:after {
    display: none;
  }
}
</style>
