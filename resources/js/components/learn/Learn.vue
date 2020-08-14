<template>
  <b>Ini tab belajar</b>
</template>

<script>
  export default {
    name: "Learn",
    mounted() {
      console.log(this.$route.params.id + ' Belajar');
    }
  }
</script>

<style scoped>

</style>
