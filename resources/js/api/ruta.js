import request from '@/utils/request';
import Resource from '@/api/resource';

class RutaResource extends Resource {
  constructor() {
    super('rutas');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }
}

export { RutaResource as default };
