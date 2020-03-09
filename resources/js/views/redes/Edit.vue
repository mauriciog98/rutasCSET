<template>
  <div class="app-container">
    <el-form v-if="red" :model="red">
      <el-row :gutter="20">
        <el-col :span="6" />
        <el-col :span="18" />
      </el-row>
    </el-form>
  </div>
</template>

<script>
import Resource from '@/api/resource';

const redResource = new Resource('redes');
export default {
  name: 'EditRed',
  data() {
    return {
      red: {},
    };
  },
  watch: {
    '$route': 'getRed',
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getRed(id);
  },
  methods: {
    async getRed(id) {
      const { data } = await redResource.get(id);
      this.red = data;
    },
  },
};
</script>
