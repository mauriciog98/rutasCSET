import request from '@/utils/request';
import Resource from '@/api/resource';

class RedResource extends Resource {
  constructor() {
    super('redes');
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

export { RedResource as default };
