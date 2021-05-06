<template>

	<div class="container">

		    <md-app md-waterfall md-mode="flexible">
      <md-app-toolbar class="md-large md-primary">
        <div class="md-toolbar-row">
          <div class="md-toolbar-section-start" v-if="auth">
            <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
              <md-icon>menu</md-icon>
            </md-button>
          </div>
						<md-avatar class="md-avatar-icon md-small " v-if="auth">
						<md-icon>person</md-icon> 
					</md-avatar><span style="margin-left:5px;">  {{authState.name}} </span> 
      <md-menu md-size="small"  md-align-trigger>
          <div class="md-toolbar-section-end">
			  
            <md-button class="md-icon-button" md-menu-trigger>
				
              <md-icon>more_vert</md-icon>
            </md-button>
			 <md-menu-content>
				 <md-menu-item v-if="guest">
					<router-link to="/login" style="text-decoration:none;"> Iniciar Sesión</router-link>
			    </md-menu-item>		<a @click.stop="logout" style="cursor:pointer;text-decoration:none;">
						   <md-menu-item v-if="auth" >
					
					     	 Salir
					
						 </md-menu-item>
             </a>
					 </md-menu-content>
          </div>	</md-menu>
        </div>

        <div class="md-toolbar-row md-toolbar-offset">
              <span class="md-display-1">Facturador L&K</span>
        </div>
      </md-app-toolbar>

      <md-app-drawer :md-active.sync="menuVisible">
        <md-toolbar class="md-transparent" md-elevation="0">Facturador</md-toolbar>

        <md-list>
							<router-link to="/facturacion-electronica" style="text-decoration:none;"> 
                  <md-list-item>
                         <span class="md-list-item-text"  @click="menuVisible = !menuVisible">Facturación Electrónica</span>
                  </md-list-item>
							</router-link>

							<router-link to="/documentos-facturados" style="text-decoration:none;"> 
                  <md-list-item>
                         <span class="md-list-item-text"  @click="menuVisible = !menuVisible">Documentos Facturados</span>
                  </md-list-item>
							</router-link>

        </md-list>
      </md-app-drawer>


<md-app-content>
	<router-view>
      
	</router-view>
 </md-app-content>
    </md-app>

	   

	</div>
</template>
<style lang="scss" scoped>


  .md-app {
    height: 98vh;
    border: 1px solid rgba(#000, .12);
  }
   // Demo purposes only
  .md-drawer {
    width: 230px;
    max-width: calc(100vw - 125px);
  }
</style>
<script type="text/javascript">

	import Auth from './store/auth'
	import Flash from './helpers/flash'
	import { post, interceptors } from './helpers/api'
	export default {
		    name: 'Flexible',
		created() {
			// global error http handler
			interceptors((err) => {
				if(err.response.status === 401) {
					Auth.remove()
					this.$router.push('/login')
				}

				if(err.response.status === 500) {
					Flash.setError(err.response.statusText)
				}

				if(err.response.status === 404) {
					this.$router.push('/not-found')
				}
			})
			Auth.initialize()
		},
		data() {
			return {
				authState: Auth.state,
				flash: Flash.state,
				menuVisible: false
			}
		},
		computed: {
			auth() {
				if(this.authState.api_token) {
					return true
				}
				return false
			},
			guest() {
				return !this.auth
			}
		},
		methods: {
			logout() {
				post('/api/logout')
				    .then((res) => {
				        if(res.data.done) {
				            // remove token
				            Auth.remove()
				            Flash.setSuccess('El proceso fue satisfactorio')
				            this.$router.push('/login')
				        }
				    })
			}
		}
	}
</script>
