/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const nivelFormacionRoutes = {
  path: '/niveles-formacion',
  component: Layout,
  redirect: '/niveles-formacion/listado',
  name: 'NivelesFormacion',
  alwaysShow: true,
  meta: {
    title: 'Niveles de Formación',
    icon: 'admin',
    permissions: ['view menu administrator'],
  },
  children: [
    /** User managements */
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/niveles-formacion/Edit'),
      name: 'NivelFormacionEdit',
      meta: { title: 'Datos Nivel de Formacion', noCache: true /*, permissions: ['manage user'] */},
      hidden: true,
    },
    {
      path: 'listado',
      component: () => import('@/views/niveles-formacion/List'),
      name: 'NivelFormacionList',
      meta: { title: 'Listado', icon: 'list' /*, permissions: ['manage user'] */},
    },
  ],
};

export default nivelFormacionRoutes;
