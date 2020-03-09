/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const redRoutes = {
  path: '/redes',
  component: Layout,
  redirect: '/redes/listado',
  name: 'Redes',
  alwaysShow: true,
  meta: {
    title: 'Redes',
    icon: 'admin',
    permissions: ['view menu administrator'],
  },
  children: [
    /** User managements */
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/redes/Edit'),
      name: 'RedEdit',
      meta: { title: 'Datos Red', noCache: true /*, permissions: ['manage user'] */},
      hidden: true,
    },
    {
      path: 'listado',
      component: () => import('@/views/redes/List'),
      name: 'RedList',
      meta: { title: 'Listado', icon: 'list' /*, permissions: ['manage user'] */},
    },
  ],
};

export default redRoutes;
