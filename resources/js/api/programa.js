import request from '@/utils/request';
import Resource from '@/api/resource';

class ProgramaResource extends Resource {
  constructor() {
    super('programas');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }
}

export { ProgramaResource as default };
