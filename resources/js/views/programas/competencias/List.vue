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
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-download" @click="doToggle">
        Exportar
      </el-button>
    </div>

    <el-table
      ref="competenciaTable"
      v-loading="loading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
      :span-method="objectSpanMethod"
      @select="handleSelectionChange"
    >
      <el-table-column align="center" label="Competencia">
        <template slot-scope="scope">
          <span>{{ scope.row.competencia.codigo + ' - ' + scope.row.competencia.nombre }}</span><br>
          <el-button type="primary" size="small" icon="el-icon-plus" @click="handleNewResultado(scope.row.competencia.id)" />
        </template>
      </el-table-column>
      <el-table-column property="orden" type="selection" align="center" :selectable="canSelectRow" />
      <el-table-column label="Resultados" header-align="center">
        <template v-if="scope.row.resultado.codigo" slot-scope="scope">
          <span>{{ scope.row.resultado.codigo + ' - ' + scope.row.resultado.nombre }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Tiempo Ejecución (horas)">
        <template v-if="scope.row.resultado.duracion" slot-scope="scope">
          <span>{{ scope.row.resultado.duracion }}</span>
        </template>
        <template v-if="scope.row.resultado.editing" slot-scope="scope">
          <el-input-number v-model="scope.row.resultado.duracion" />
        </template>
      </el-table-column>
    </el-table>
    <el-dialog :title="'Crear competencia'" :visible.sync="dialogFormVisible">
      <div v-loading="competenciaCreating" class="form-container">
        <el-form ref="competenciaForm" :rules="rules" :model="newCompetencia" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="'Codigo'" prop="codigo">
            <el-input v-model="newCompetencia.codigo" />
          </el-form-item>
          <el-form-item :label="'Nombre'" prop="nombre">
            <el-input v-model="newCompetencia.nombre" />
          </el-form-item>
          <el-form-item :label="'Tipo competencia'" prop="nivel_formacion_id">
            <el-select v-model="newCompetencia.tipo" class="filter-item" placeholder="Seleccione un nivel de formación">
              <el-option v-for="item in tipoCompetencia" :key="item.id" :label="item.nombre | uppercaseFirst" :value="item.id" />
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createCompetencia()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
    <el-dialog :title="'Crear resultado'" :visible.sync="dialogResultadoVisible">
      <div v-loading="resultadoCreating" class="form-container">
        <el-form ref="resultadoForm" :rules="rulesResultado" :model="newResultado" label-position="left" label-width="150px" style="max-width: 500px;">
          <el-form-item :label="'Codigo'" prop="codigo">
            <el-input v-model="newResultado.codigo" />
          </el-form-item>
          <el-form-item :label="'Nombre'" prop="nombre">
            <el-input v-model="newResultado.nombre" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogResultadoVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createResultado()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import CompetenciaResource from '@/api/competencia';
import ResultadoResource from '@/api/resultado';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission';

const competenciaResource = new CompetenciaResource();
const resultadoResource = new ResultadoResource();

export default {
  name: 'CompetenciaList',
  directives: { waves, permission },
  data() {
    return {
      list: null,
      tipoCompetencia: [
        { id: 1, nombre: 'Técnica' },
        { id: 1, nombre: 'Tranversal' },
      ],
      programa: '',
      loading: true,
      downloading: false,
      competenciaCreating: false,
      resultadoCreating: false,
      newCompetencia: { programa_id: this.programa },
      newResultado: {},
      dialogFormVisible: false,
      dialogResultadoVisible: false,
      currentCompetenciaId: 0,
      multipleSelection: [],
      query: {
        keyword: '',
        programa: '',
      },
      currentCompetencia: {
        codigo: '',
        version: '',
        nombre: '',
        resultados: [],
        tipo: '',
        programa_id: '',
      },
      rules: {
        codigo: [{ required: true, message: 'El codigo es requerido', trigger: 'blur' }],
        nombre: [{ required: true, message: 'El nombre es requerido', trigger: 'blur' }],
        tipo: [{ required: true, message: 'El tipo es requerido', trigger: 'blur' }],
      },
      rulesResultado: {
        codigo: [{ required: true, message: 'El codigo es requerido', trigger: 'blur' }],
        nombre: [{ required: true, message: 'El nombre es requerido', trigger: 'blur' }],
      },
    };
  },
  created() {
    this.resetNewCompetencia();
    this.getList();
    this.programa = this.$route.params && this.$route.params.id;
    this.query.programa = this.programa;
  },
  methods: {
    checkPermission,
    async getList() {
      this.loading = true;
      const { data } = await competenciaResource.list(this.query);
      data.forEach((element, index) => {
        element['index'] = index + 1;
      });
      this.list = data.reduce((a, c) => {
        let arr = [];
        if (c.programacion_resultados.length > 0) {
          for (const index in c.programacion_resultados) {
            console.log(c.programacion_resultados[index]);
            if (c.programacion_resultados[index].resultados.length > 0) {
              arr = arr.concat(c.programacion_resultados[index].resultados.map(item => ({
                competencia: { id: c.id, codigo: c.codigo, nombre: c.nombre },
                resultado: { ...item },
                rowspan: c.resultados_count,
                duracion: c.programacion_resultados[index].duracion | 0,
                rowspanDuracion: c.programacion_resultados[index].duracion !== null ? c.programacion_resultados[index].resultados_count : 0,
                selected: c.programacion_resultados[index].duracion !== null,
                almacenado: c.programacion_resultados[index].duracion !== null,
                inicio: a.length ? a[a.length - 1].fin : 0,
                fin: a.length ? a[a.length - 1].fin + c.programacion_resultados[index].resultados.length : c.programacion_resultados[index].resultados.length,
              })));
            } else {
              arr = arr.concat([{
                competencia: { id: c.id, codigo: c.codigo, nombre: c.nombre },
                resultado: {},
                rowspan: 1,
                rowspanDuracion: 0,
                selected: false,
                almacenado: false,
                inicio: a.length ? a[a.length - 1].fin : 0,
                fin: a.length ? a[a.length - 1].fin : 0,
              }]);
            }
          }
        }
        a = a.concat(arr);
        return a;
      }, []);
      this.loading = false;
    },
    objectSpanMethod({ row, column, rowIndex, columnIndex }) {
      if (columnIndex === 0) {
        if (rowIndex === 0) {
          return {
            rowspan: row.rowspan,
            colspan: 1,
          };
        } else if (this.list[rowIndex - 1].competencia.id !== row.competencia.id) {
          return {
            rowspan: row.rowspan,
            colspan: 1,
          };
        } else {
          return {
            rowspan: 0,
            colspan: 0,
          };
        }
      } else if (columnIndex === 3) {
        if (row.rowspanDuracion > 1 && row.selected === true) {
          if (row.inicio === rowIndex) {
            return {
              rowspan: row.rowspanDuracion,
              colspan: 1,
            };
          } else if (rowIndex > row.inicio && rowIndex <= (row.inicio + row.rowspanDuracion)) {
            return {
              rowspan: 0,
              colspan: 0,
            };
          }
          return {
            rowspan: 1,
            colspan: 1,
          };
        }
        return {
          rowspan: 1,
          colspan: 1,
        };
      }
    },
    doToggle(){
      this.$refs.competenciaTable.toggleAllSelection();
    },
    canSelectRow(row, index) {
      if (row.resultado.id === undefined){
        return false;
      }
      return !row.almacenado;
    },
    handleFilter() {
      this.getList();
    },
    handleNewResultado(id) {
      this.resetNewResultado();
      this.dialogResultadoVisible = true;
      this.newResultado.competencia_id = id;
      this.$nextTick(() => {
        this.$refs['resultadoForm'].clearValidate();
      });
    },
    handleCreate() {
      this.resetNewCompetencia();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['competenciaForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('Esto eliminará permanentemente el competencia ' + name + '. Desea continuar?', 'Advertencia', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancelar',
        type: 'warning',
      }).then(() => {
        competenciaResource.destroy(id).then(response => {
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
    createCompetencia() {
      this.$refs['competenciaForm'].validate((valid) => {
        if (valid) {
          this.competenciaCreating = true;
          competenciaResource
            .store(this.newCompetencia)
            .then(response => {
              this.$message({
                message: 'Competencia ' + this.newCompetencia.nombre + ' ha sido creada exitosamente',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewCompetencia();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.competenciaCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    handleSelectionChange(val, row) {
      let arreglo = this.list;
      if (row.selected) {
        row.selected = false;
        arreglo[row.inicio].rowspanDuracion = arreglo[row.inicio].rowspanDuracion - 1;
      } else {
        row.selected = true;
        arreglo[row.inicio].rowspanDuracion = arreglo[row.inicio].rowspanDuracion + 1;
      }
      row.rowspanDuracion = arreglo[row.inicio].rowspanDuracion;
      arreglo = this.partialSort(arreglo, row.inicio, row.fin);
      this.loading = true;
      arreglo.forEach((element, index) => {
        this.$set(this.list, index, element);
      });
      this.loading = false;
      this.multipleSelection = val;
    },
    createResultado() {
      this.$refs['resultadoForm'].validate((valid) => {
        if (valid) {
          this.resultadoCreating = true;
          resultadoResource
            .store(this.newResultado)
            .then(response => {
              this.$message({
                message: 'Resultado ' + this.newResultado.nombre + ' ha sido creado exitosamente',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewResultado();
              this.dialogResultadoVisible = false;
              this.handleFilter();
            })
            .catch(error => {
              console.log(error);
            })
            .finally(() => {
              this.resultadoCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    partialSort(arr, start, end) {
      const preSorted = arr.slice(0, start), postSorted = arr.slice(end);
      const sorted = arr.slice(start, end).sort((a, b) => {
        if (a.selected) {
          return 1;
        }
        return 0;
      });
      arr.length = 0;
      arr.push.apply(arr, preSorted.concat(sorted).concat(postSorted));
      return arr;
    },
    resetNewCompetencia() {
      this.newCompetencia = {
        codigo: '',
        nombre: '',
        tipo: '',
        programa_id: this.programa,
      };
    },
    resetNewResultado() {
      this.newResultado = {
        codigo: '',
        nombre: '',
        competencia_id: '',
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
          filename: 'competencia-list',
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
