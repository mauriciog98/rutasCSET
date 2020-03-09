import request from '@/utils/request';
import Resource from '@/api/resource';

class NivelFormacionResource extends Resource {
  constructor() {
    super('niveles_formacion');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }
}

export { NivelFormacionResource as default };
