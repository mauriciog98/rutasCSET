<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="query.keyword" :placeholder="$t('table.keyword')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        Buscar
      </el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-plus" @click="handleCreate">
        Agregar
      </el-button>
      <el-button v-waves :loading="downloading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">
        Exportar
      </el-button>
    </div>

    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column align="center" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.index }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Nombre">
        <template slot-scope="scope">
          <span>{{ scope.row.nombre }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Actions" width="350">
        <template slot-scope="scope">
          <router-link :to="'/niveles-formacion/edit/'+scope.row.id">
            <el-button v-permission="['manage user']" type="primary" size="small" icon="el-icon-edit">
              Editar
            </el-button>
          </router-link>
          <el-button type="danger" size="small" icon="el-icon-delete" @click="handleDelete(scope.row.id, scope.row.nombre)">
            Eliminar
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />

    <el-dialog :title="'Create new user'" :visible.sync="dialogFormVisible">
      <div v-loading="nivelFormacionCreating" class="form-container">
        <el-form ref="nivelFormacionForm" :rules="rules" :model="newNivelFormacion" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="'Nombre'" prop="nombre">
            <el-input v-model="newNivelFormacion.nombre" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createNivelFormacion()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import NivelFormacionResource from '@/api/nivel-formacion';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission';

const nivelFormacionResource = new NivelFormacionResource();

export default {
  name: 'NivelFormacionList',
  components: { Pagination },
  directives: { waves, permission },
  data() {
    return {
      list: null,
      instructores: [],
      total: 0,
      loading: true,
      downloading: false,
      nivelFormacionCreating: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
      },
      newNivelFormacion: {},
      dialogFormVisible: false,
      currentNivelFormacionId: 0,
      currentNivelFormacion: {
        nombre: '',
      },
      rules: {
        nombre: [{ required: true, message: 'El nombre es requerido', trigger: 'blur' }],
      },
    };
  },
  created() {
    this.resetNewNivelFormacion();
    this.getList();
  },
  methods: {
    checkPermission,
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await nivelFormacionResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleCreate() {
      this.resetNewNivelFormacion();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['nivelFormacionForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('Esto eliminará permanentemente el nivel de formacion ' + name + '. Desea continuar?', 'Advertencia', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }).then(() => {
        nivelFormacionResource.destroy(id).then(response => {
          this.$message({
            type: 'success',
            message: 'Eliminado exitosamente',
          });
          this.handleFilter();
        }).catch(error => {
          console.log(error);
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Eliminación cancelada',
        });
      });
    },
    createNivelFormacion() {
      this.$refs['nivelFormacionForm'].validate((valid) => {
        if (valid) {
          this.nivelFormacionCreating = true;
          nivelFormacionResource
            .store(this.newNivelFormacion)
            .then(response => {
              this.$message({
                message: 'NivelFormacion ' + this.newNivelFormacion.nombre + ') ha sido creada exitosamente',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewNivelFormacion();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.nivelFormacionCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetNewNivelFormacion() {
      this.newNivelFormacion = {
        nombre: '',
      };
    },
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['id', 'nombre'];
        const filterVal = ['id', 'nombre'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'nivelFormacion-list',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => v[j]));
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
</style>
