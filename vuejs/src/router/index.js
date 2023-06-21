import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'


const router = createRouter({
  history: createWebHistory(document.location.origin+document.location.pathname+'?page=formcat#'),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },

    {
      path: '/forms',
      name: 'forms',
      component: () =>  import('../views/Forms.vue'),
    },
    {
      path: '/submissions/:form_id',
      name: 'submissions',
      component: () =>  import('../views/Submissions.vue'),
    }
  ]
})

export default router
