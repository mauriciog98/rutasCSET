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
      <el-table-column align="center" label="Red">
        <template v-if="scope.row.nivel_formacion !== null" slot-scope="scope">
          <span>{{ scope.row.nivel_formacion.nombre }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Duracion">
        <template slot-scope="scope">
          <span>{{ scope.row.duracion }} meses</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Duracion Lectiva">
        <template slot-scope="scope">
          <span>{{ scope.row.duracion_lectiva }} meses</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Red">
        <template v-if="scope.row.red !== null" slot-scope="scope">
          <span>{{ scope.row.red.nombre }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Actions" width="350">
        <template slot-scope="scope">
          <router-link :to="'/programas/edit/'+scope.row.id">
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
      <div v-loading="programaCreating" class="form-container">
        <el-form ref="programaForm" :rules="rules" :model="newPrograma" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="'Codigo'" prop="codigo">
            <el-input v-model="newPrograma.codigo" />
          </el-form-item>
          <el-form-item :label="'Version'" prop="version">
            <el-input v-model="newPrograma.version" />
          </el-form-item>
          <el-form-item :label="'Nombre'" prop="nombre">
            <el-input v-model="newPrograma.nombre" />
          </el-form-item>
          <el-form-item :label="'Nivel de formación'" prop="nivel_formacion_id">
            <el-select v-model="newPrograma.nivel_formacion_id" class="filter-item" placeholder="Seleccione un nivel de formación">
              <el-option v-for="item in niveles_formacion" :key="item.id" :label="item.nombre | uppercaseFirst" :value="item.id" />
            </el-select>
          </el-form-item>
          <el-form-item :label="'Duración'" prop="duracion">
            <el-input v-model="newPrograma.duracion" />
          </el-form-item>
          <el-form-item :label="'Duración Etapa Lectiva'" prop="duracion_lectiva">
            <el-input v-model="newPrograma.duracion_lectiva" />
          </el-form-item>
          <el-form-item :label="'Red'" prop="red_id">
            <el-select v-model="newPrograma.red_id" class="filter-item" placeholder="Seleccione una red">
              <el-option v-for="item in redes" :key="item.id" :label="item.nombre | uppercaseFirst" :value="item.id" />
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createPrograma()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import ProgramaResource from '@/api/programa';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission';
import Resource from '@/api/resource'; // Permission checking

const programaResource = new ProgramaResource();
const redesResource = new Resource('redes');
const nivelesFormacionResource = new Resource('niveles_formacion');

export default {
  name: 'ProgramaList',
  components: { Pagination },
  directives: { waves, permission },
  data() {
    return {
      list: null,
      redes: [],
      niveles_formacion: [],
      total: 0,
      loading: true,
      downloading: false,
      programaCreating: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
      },
      newPrograma: {},
      dialogFormVisible: false,
      currentProgramaId: 0,
      currentPrograma: {
        codigo: '',
        version: '',
        nombre: '',
        nivel_formacion_id: '',
        duracion: '',
        duracion_lectiva: '',
        red_id: '',
        estado: '',
      },
      rules: {
        codigo: [{ required: true, message: 'El codigo es requerido', trigger: 'blur' }],
        version: [{ required: true, message: 'La version es requerida', trigger: 'blur' }],
        nombre: [{ required: true, message: 'El nombre es requerido', trigger: 'blur' }],
        nivel_formacion_id: [{ required: true, message: 'El nivel de formacion es requerido', trigger: 'blur' }],
        duracion: [{ required: true, message: 'La duracion es requerida', trigger: 'blur' }],
        duracion_lectiva: [{ required: true, message: 'La duración de etapa lectica es requerida', trigger: 'blur' }],
        red_id: [{ required: true, message: 'La red es requerida', trigger: 'blur' }],
      },
    };
  },
  created() {
    this.resetNewPrograma();
    this.getList();
    this.getRedes();
    this.getNivelesFormacion();
  },
  methods: {
    checkPermission,
    async getRedes() {
      this.loading = true;
      const { data } = await redesResource.list();
      this.redes = data;
      this.loading = false;
    },
    async getNivelesFormacion() {
      this.loading = true;
      const { data } = await nivelesFormacionResource.list();
      this.niveles_formacion = data;
      this.loading = false;
    },
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await programaResource.list(this.query);
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
      this.resetNewPrograma();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['programaForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('Esto eliminará permanentemente el programa ' + name + '. Desea continuar?', 'Advertencia', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }).then(() => {
        programaResource.destroy(id).then(response => {
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
    createPrograma() {
      this.$refs['programaForm'].validate((valid) => {
        if (valid) {
          this.programaCreating = true;
          programaResource
            .store(this.newPrograma)
            .then(response => {
              this.$message({
                message: 'Programa ' + this.newPrograma.nombre + ') ha sido creada exitosamente',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewPrograma();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.programaCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetNewPrograma() {
      this.newPrograma = {
        codigo: '',
        version: '',
        nombre: '',
        nivel_formacion_id: '',
        duracion: '',
        duracion_lectiva: '',
        red_id: '',
        estado: '',
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
          filename: 'programa-list',
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
