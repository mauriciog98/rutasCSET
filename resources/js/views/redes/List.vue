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
      <el-table-column align="center" label="Lider de red">
        <template v-if="scope.row.lider !== null" slot-scope="scope">
          <span>{{ scope.row.lider.name }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Actions" width="350">
        <template slot-scope="scope">
          <router-link :to="'/redes/edit/'+scope.row.id">
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
      <div v-loading="redCreating" class="form-container">
        <el-form ref="redForm" :rules="rules" :model="newRed" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="'Nombre'" prop="nombre">
            <el-input v-model="newRed.nombre" />
          </el-form-item>
          <el-form-item :label="'Lider de red'" prop="lider_id">
            <el-select v-model="newRed.lider_id" class="filter-item" placeholder="Seleccione un instructor">
              <el-option v-for="item in instructores" :key="item.id" :label="item.name | uppercaseFirst" :value="item.id" />
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createRed()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import RedResource from '@/api/red';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission';
import Resource from '@/api/resource'; // Permission checking

const redResource = new RedResource();
const instructorResource = new Resource('users');

export default {
  name: 'RedList',
  components: { Pagination },
  directives: { waves, permission },
  data() {
    return {
      list: null,
      instructores: [],
      total: 0,
      loading: true,
      downloading: false,
      redCreating: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
      },
      newRed: {},
      dialogFormVisible: false,
      currentRedId: 0,
      currentRed: {
        nombre: '',
        lider_id: '',
      },
      rules: {
        nombre: [{ required: true, message: 'El nombre es requerido', trigger: 'blur' }],
        lider: [{ required: true, message: 'El lider de red es requerido', trigger: 'blur' }],
      },
    };
  },
  created() {
    this.resetNewRed();
    this.getList();
    this.getInstructores();
  },
  methods: {
    checkPermission,
    async getInstructores() {
      this.loading = true;
      const { data } = await instructorResource.list();
      this.instructores = data;
      this.loading = false;
    },
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await redResource.list(this.query);
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
      this.resetNewRed();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['redForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('Esto eliminará permanentemente la red de ' + name + '. Desea continuar?', 'Advertencia', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }).then(() => {
        redResource.destroy(id).then(response => {
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
    createRed() {
      this.$refs['redForm'].validate((valid) => {
        if (valid) {
          this.redCreating = true;
          redResource
            .store(this.newRed)
            .then(response => {
              this.$message({
                message: 'Red ' + this.newRed.nombre + ') ha sido creada exitosamente',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewRed();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.redCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetNewRed() {
      this.newRed = {
        nombre: '',
        lider_id: '',
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
          filename: 'red-list',
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
