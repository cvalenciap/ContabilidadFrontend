<template>

<div class="row">

<div class="container">
	  
  <vue-progress-bar></vue-progress-bar>
   
 <div>
    <md-steppers :md-active-step.sync="active" md-linear>
    <md-step id="first" md-label="Elegir Periodo" :md-done.sync="first" >
                
            <div>
            <md-autocomplete v-model="value" :md-options="periodos" @md-changed="getPeriodos" @md-opened="getPeriodos">
            <label>Periodos</label>


             <template slot="md-autocomplete-item" slot-scope="{ item, term }">
    <md-highlight-text :md-term="term">{{ item.name }}</md-highlight-text>
  </template>
   <template slot="md-autocomplete-empty" slot-scope="{ term }">
    "{{ term }}" no se encuentra el periodo
  </template>
 
            </md-autocomplete>
           </div>

          
            <md-button class="md-dense md-raised md-primary" @click="next" :disabled="isProcessing">
                        
                        Continuar

            </md-button> 
  
      </md-step>


      <md-step id="second" md-label="Importar Excel" :md-done.sync="second" >
  
        <div class="col s12 " id="aviso">
            
        <p>Tamaño máximo de archivo permitido para cargar: 24.38 GB de tipo .csv <a href="https://convertio.co/es/xls-csv/" target="_blank" class="blue-text">Convertir el archivo aqui</a></p>

        </div>
        

            <div class="col s12 m5 l5 p_ex">
                <div class="file_csv">
                        
                        <div class="groupofsale__box">
                        
            <csv-upload v-model="form.archivo"></csv-upload>

                        <small class="error__control" v-if="error.archivo">
                            
                            {{error.archivo[0]}}

                        </small>

                        </div>
                    </div>

            <md-button class="md-dense md-raised md-primary" @click="save" :disabled="isProcessing">
                        
                        Importar

                    </md-button>

            </div>
  
      </md-step>

      <md-step id="third" md-label="Gargar Datos" :md-done.sync="third" :md-error="secondStepError" >

            <div class="col s12 m5 l5" style="margin-bottom: 10px;">

            <center>
            <p class="blue-text"><b>Seleccione el tipo de exportación</b></p>


            <input type="radio" :id="1" :value="1" v-model="form.tipo" >
            
            <label :for="1">Masivo</label>


            <input type="radio" :id="2" :value="2" v-model="form.tipo">

            <label :for="2">Incremental</label>

            </center>

                </div>

            <div class="col s12 m5 l5 ">



                <div class="panel_export" >
                    <center>
                         <md-button class="md-dense md-raised md-primary" @click="remove" :disabled="isExport" >Cargar</md-button>
                
            </center>
                </div>
                

            </div>
   </md-step>  
    </md-steppers>
      </div>
  </div>

</div>

</template>

<script type="text/javascript">
	
 import Vue from 'vue'
     
      import Auth from '../../store/auth'
	import { get,post } from '../../helpers/api'
    import { toMulipartedForm } from '../../helpers/form'
   	import csvUpload from '../../components/csvUpload.vue'


	export default{
        name: 'StepperHorizontal',
        name: 'AutocompleteAsync',
         components:{

              csvUpload
         },
 
           created(){
             
               this.getResults();
       
        },
		data() {
			return {
         
                auth: Auth.state,
                isProcessing: false,
                isExport:false,
                verificar:false,
                active: 'first',
                first: false,
                second: false,
                third: false,
                secondStepError: null,
                selectPeriodo: null,
                periodos: [],
                periodo_list:[],
                value: null,
                            form: {
                    archivo:'',
                    tipo:'',
                    periody:''
                    
                    },
                    error:{}
                    }
                        },

                    error: function(err) {

                    },
		         methods:{


       
         getResults() {
    
  get('/api/periodo')
        .then((res) => {
          
          this.periodo_list=res.data
          
           console.log(this.periodo_list)
    

        })},
        
  getPeriodos(searchTerm) {
        this.periodos = new Promise(resolve => {
          window.setTimeout(() => {
            if (!searchTerm) {
              resolve(this.periodo_list)
            } else {
              const term = searchTerm.toString().toLowerCase()

              resolve(this.periodo_list.filter(({ name }) => name.toString().toLowerCase().includes(term)))
            }
          }, 500)
        })
      },
      next(){
  
     this.form.periody=this.value
     this.setDone('first', 'second')

      },
  save(){
     
              
            this.isProcessing=true
            this.$Progress.start();
            console.log(this.form.archivo);
              const form = toMulipartedForm(this.form, 'api/cargar_inventario')
              post('api/cargar_inventario',form)


                  .then((res)=>{


                      if(res.data.registered){
                        
                        this.setDone('second', 'third')
                        this.$Progress.finish()
                       //  Flash.setSuccess('Se cargo correctamente el archivo a la carpeta raíz')
                        
                     
                      }
                        this.$Progress.fail()
                        this.isProcessing = false

                  })

                  .catch((err)=>{

                     if(err.response.status === 422){

                         this.error = err.response.data

                     }

                         this.isProcessing = false
                      


                  })


           },

            setDone (id, index) {
                        this[id] = true

                        this.secondStepError = null

                        if (index) {
                        this.active = index
                    }
                },
            setError () {
                        this.secondStepError = 'Error al cargar archivo'
                    },
           remove(){


              this.isExport=false

              this.$Progress.start()
              const form = toMulipartedForm(this.form, 'api/exportari')
              post('api/exportari',form)

                  .then((res)=>{

                      if(res.data.registered){

                     this.$Progress.finish()
                   //  Flash.setSuccess('Se Realizo correctamente la Exportación')


                      }
                   
                    

                  })

                  .catch((err)=>{

                     if(err.response.status === 422){
                         this.setError()
                         this.$Progress.fail()
                         this.isExport = false

                     }
                     else if (err.response.status === 500){
                         this.setError()
                         this.$Progress.fail()
                         this.isExport = false

                     }



                  })



           }





        }


	}
</script>