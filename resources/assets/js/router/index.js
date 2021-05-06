import Vue from 'vue'
import VueRouter from 'vue-router'

import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import RecipeIndex from '../views/Recipe/Index.vue'
import RecipeShow from '../views/Recipe/Show.vue'
import RecipeForm from '../views/Recipe/Form.vue'
import NotFound from '../views/NotFound.vue'
import ExporttoExcel from '../views/ExporttoExcel/index.vue'
import ListarBienes from '../views/ListarBienes/index.vue'
import VueMaterial from 'vue-material'
import Caja from '../views/Caja/index.vue'
import DocumentosFacturados from '../views/DocumentosFacturados/index.vue'


Vue.use(VueMaterial)

Vue.use(VueRouter)

const router = new VueRouter({
	mode: 'history',
	routes: [
		{ path: '/', component: RecipeIndex },
		{ path: '/recipes/create', component: RecipeForm, meta: { mode: 'create' }},
		{ path: '/recipes/:id/edit', component: RecipeForm, meta: { mode: 'edit' }},
		{ path: '/recipes/:id', component: RecipeShow },
		{ path: '/register', component: Register },
		{ path: '/login', component: Login },
		{ path: '/not-found', component: NotFound },
		{ path: '*', component: NotFound },
		{ path: '/exporttoexcel', component: ExporttoExcel },
		{ path: '/listarbienes', component: ListarBienes },
		{ path: '/facturacion-electronica', component: Caja },
		{ path: '/documentos-facturados', component: DocumentosFacturados }
	]
})

export default router
