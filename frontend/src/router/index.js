import { createRouter, createWebHistory } from 'vue-router'
import LoginContribuable from '../components/LoginContribuable.vue'
import ImpotsList from '../components/ImpotsList.vue'
import DeclarationForm from '../components/DeclarationForm.vue'
import DeclarationsList from '../components/DeclarationsList.vue'

const routes = [
  { path: '/', name: 'Login', component: LoginContribuable },
  { path: '/impots/:idContribuable', name: 'ImpotsList', component: ImpotsList, props: true },
  { path: '/declarer/:idContribuable/:idTypeImpot', name: 'DeclarationForm', component: DeclarationForm, props: true },
  { path: '/declarations/:idContribuable/:idTypeImpot', name: 'DeclarationsList', component: DeclarationsList, props: true }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
