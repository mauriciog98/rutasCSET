import request from '@/utils/request';
import Resource from '@/api/resource';

class CompetenciaResource extends Resource {
  constructor() {
    super('competencias');
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

export { CompetenciaResource as default };
