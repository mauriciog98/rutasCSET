import request from '@/utils/request';
import Resource from '@/api/resource';

class ResultadoResource extends Resource {
  constructor() {
    super('resultados');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }

  updatePermission(id, permissions) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'put',
      data: permissions,
    });
  }
}

export { ResultadoResource as default };
