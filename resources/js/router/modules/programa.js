/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const programaRoutes = {
  path: '/programas',
  component: Layout,
  redirect: '/programas/listado',
  name: 'Programas',
  alwaysShow: true,
  meta: {
    title: 'Programas',
    icon: 'admin',
    permissions: ['view menu administrator'],
  },
  children: [
    /** User managements */
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/programas/Edit'),
      name: 'ProgramaEdit',
      meta: { title: 'Datos Programa', noCache: true /*, permissions: ['manage user'] */},
      hidden: true,
    },
    {
      path: 'listado',
      component: () => import('@/views/programas/List'),
      name: 'ProgramaList',
      meta: { title: 'Listado', icon: 'list' /*, permissions: ['manage user'] */},
    },
    {
      path: ':id(\\d+)/competencias',
      component: () => import('@/views/programas/competencias/List'),
      name: 'CompetenciaList',
      meta: { title: 'Listado', icon: 'list' /*, permissions: ['manage user'] */},
    },
  ],
};

export default programaRoutes;
