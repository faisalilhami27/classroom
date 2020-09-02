<template>
  <div>
    <v-list dense>
      <v-list-item-group v-model="item" color="primary">
        <div style="overflow: auto">
          <v-list-item
            v-for="(item, index) in materials"
            :key="index"
            @click.prevent="$emit('select-playlist', item.id)"
          >
            <v-list-item-content>
              <v-list-item-title>
                {{ item.position }}. {{ item.title }}
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </div>
      </v-list-item-group>
    </v-list>
  </div>
</template>

<script>
export default {
  name: "Playlist",
  props: ['materials'],
  data() {
    return {
      item: 0,
    }
  }
}
</script>

<style scoped>

</style>
