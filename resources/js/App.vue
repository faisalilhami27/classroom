<template>
  <div>
    <router-view></router-view>
  </div>
</template>

<script>
  export default {
    name: "App",
    props: ['user'],
    created: function() {
      this.$store.dispatch('setUser', this.user);
    }
  }
</script>

<style scoped>

</style>
