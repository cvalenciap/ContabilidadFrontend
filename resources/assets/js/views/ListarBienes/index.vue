<template>

<div class="row">

<div class="container">
	  
  <vue-progress-bar></vue-progress-bar>

 <div>
    <md-table v-model="searched" md-sort="codigo" md-sort-order="asc" md-card md-fixed-header>
      <md-table-toolbar>
        <div class="md-toolbar-section-start">
          <h1 class="md-title">Bienes Patrimoniales</h1>
        </div>

        <md-field md-clearable class="md-toolbar-section-end">
          <md-input placeholder="Busqueda por código" v-model="search" @input="searchOnTable" />
        </md-field>
      </md-table-toolbar>

      <md-table-empty-state
        md-label="No users found"
        :md-description="`No user found for this '${search}' query. Try a different search term or create a new user.`">
        <md-button class="md-primary md-raised" @click="newUser">Create New User</md-button>
      </md-table-empty-state>

      <md-table-row slot="md-table-row" slot-scope="{ item }">
        <md-table-cell md-label="ID" md-sort-by="id" md-numeric>{{ item.id }}</md-table-cell>
        <md-table-cell md-label="Inventario" md-sort-by="inventario">{{ item.inventario }}</md-table-cell>
        <md-table-cell md-label="Codigo" md-sort-by="codigo">{{ item.codigo }}</md-table-cell>
        <md-table-cell md-label="DEscripción" md-sort-by="descripcion">{{ item.descripcion }}</md-table-cell>
        <md-table-cell md-label="Periodo" md-sort-by="periodo">{{ item.periodo }}</md-table-cell>
      </md-table-row>
    </md-table>
  </div>


</div>
</div>

    
</template>


<script type="text/javascript">
  
     import Vue from 'vue'
     import Auth from '../../store/auth'
     import { get,post } from '../../helpers/api'
     
  const toLower = text => {
    return text.toString().toLowerCase()
  }

  const searchByName = (items, term) => {
    if (term) {
      return items.filter(item => toLower(item.codigo).includes(toLower(term)))
    }

    return items
  }


	export default{
       name: 'TableSearch',
         components:{

             
         },
 
           created(){
             
               this.getResults();
                this.searched = this.patrimonio
       
        },



		data() {
			return {
         
                auth: Auth.state,
                isProcessing: false,
                isExport:false,
                search: null,
                searched: [],
                patrimonio:[ {
          id: 1,
          inventario: "7612",
          codigo: "746406260348",
          descripcion: "ARMARIO DE MELAMINE",
          periodo: "201812"
        }]
            }},

		         methods:{


       
         getResults() {
    
            get('/api/patrimonio')
                .then((res) => {
                /*
                this.patrimonio=res.data
                
                console.log(this.patrimonio[0])*/
            

                })},
          newUser () {
        window.alert('Noop')
      },
      searchOnTable () {
        this.searched = searchByName(this.patrimonio, this.search)
      },




           }





        }




</script>
