
<template >
	
<div v-if="auth.api_token ">
 <md-table v-model="searched" md-sort="codigo" md-sort-order="asc" md-card md-fixed-header @md-selected="onSelect">
      <md-table-toolbar>
        <div class="md-toolbar-section-start">
          <h1 class="md-title">Documentos Facturados </h1>
        </div>

         <div class="md-layout-item">
        <md-field>
          <md-select v-model="type" name="type" id="type" placeholder="Tipo Documento" @md-selected="combo">
            <md-option value="TODOS">TODOS</md-option>
            <md-option value="FACTURA">FACTURAS</md-option>
            <md-option value="BOLETA">BOLETAS</md-option>
          </md-select>
        </md-field>
      </div>
   
        <md-field md-clearable class="md-toolbar-section-end">
          <md-input placeholder="Buscar serie" v-model="search" @input="searchOnTable"/>

        </md-field>
      </md-table-toolbar>

      <md-table-empty-state
        md-label="No se encuentra el Documento"
        :md-description="`Ningún documento  encontrado con la serie: '${search}'`">
    
      </md-table-empty-state>

      <md-table-row slot="md-table-row" slot-scope="{ item }" :class="getClass(item)" md-selectable="single" @click="active = true" >
       
       
          <md-table-cell md-label="Serie" md-sort-by="SERIE" md-numeric>
             
          <p>{{ item.serie }} - {{ item.numeracion }}</p>
            
            </md-table-cell>
       
        <md-table-cell md-label="Documento" md-sort-by="COD." md-numeric>
              <div class="md-list-item-text">
           <span>SubTotal: S/. {{ formatPrice(item.gravadas) }}</span>
            <span>IGV: S/. {{ formatPrice(item.igv) }}</span>
          <span>Total: S/.{{ formatPrice(item.total) }}</span>
        </div>
            
            </md-table-cell>
 <md-table-cell md-label="Fecha Emisión" md-sort-by="FECHA" md-numeric>
             
          <p>{{ item.fecha_venta }}</p>
            
            </md-table-cell>

        <md-table-cell md-label="Estado" md-sort-by="ESTADO">
                {{ item.estado }}

        </md-table-cell>

        <md-table-cell md-label="XML" md-sort-by="XML">

           
                <a :href="`files/${item.filename}.xml`" target="_blank">
                <md-button class="md-icon-button">
                            
                                <md-icon>line_style</md-icon>

                            </md-button>

                </a>

        </md-table-cell>
        <md-table-cell md-label="CDR" md-sort-by="CDR">

                <a :href="`files/R-${item.filename}.zip`" target="_blank">
                <md-button class="md-icon-button md-primary">
                            
                                <md-icon>done_all</md-icon>

                            </md-button>

                </a>

        </md-table-cell>
      
       <md-table-cell md-label="PDF" md-sort-by="PDF">

                <a :href="`files/${item.filename}.pdf`" target="_blank">
                <md-button class="md-icon-button md-accent">
                            
                                <md-icon>picture_as_pdf</md-icon>

                            </md-button>

                </a>
        </md-table-cell>
      <md-table-cell md-label="ANULAR" md-sort-by="ANULAR">

                <md-button class="md-icon-button md-accent" @click="baja(item)">
                            
                                <md-icon>delete_forever</md-icon>

                            </md-button>

                
        </md-table-cell>


      
      </md-table-row>

    </md-table>

  <div class="md-layout" align="center">
    <div class="md-layout-item"><h3>Subtotal: {{ formatPrice(gravadaFacturado)  }}</h3></div>
    <div class="md-layout-item"><h3>IGV: {{ formatPrice(igvFacturado)  }}</h3></div>
    <div class="md-layout-item"><h3>Total: {{ formatPrice(totalFacturado)  }}</h3></div>
  </div>



</div>

</template>
<script type="text/javascript">
	import { get,post} from '../../helpers/api'
		import Auth from '../../store/auth'
	 
	  const toLower = text => {
    return text.toString().toLowerCase()
  }



  const searchByName = (items, term) => {
    if (term) {
      return items.filter(item => toLower(item.numeracion).includes(toLower(term)))
    }

    return items
  }



	export default {
     name: 'TableSearch',
     name: 'BasicSelect',
     name: 'LayoutHorizontalColumns',
		data() {
			return {
				documentos: [],
				searched: [],
				search: null,
                auth: Auth.state,
                 selected: {},
                 datos:[],
                 type: 'TODOS',
                 totalFacturado:0,
                 gravadaFacturado:0,
                 igvFacturado:0,
			}
		},
		created() {
		
         this.cargar_documentos();
		},
  methods:{
    
    cargar_documentos(){
	get('/api/facturados/'+this.type)
				.then((res) => {
					this.documentos = res.data
          this.searched = this.documentos

          var total=0;
          var gravado=0;
          var igv=0;

          this.documentos.forEach(function (obj) {
              total += parseFloat(obj.total);
              gravado += parseFloat(obj.gravadas);
              igv += parseFloat(obj.igv);
          });
              this.totalFacturado = total;
              this.gravadaFacturado = gravado;
              this.igvFacturado = igv;
				})
    },

		searchOnTable () {
                this.searched = searchByName(this.documentos, this.search)
              },
        formatPrice(value) {
              let val = (value/1).toFixed(2).replace(',', '.')
              return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
          },
        onSelect (item) {
          this.selected = item
          },
        baja(item){
               

                this.datos[0]={"codigo_documento":item.codigo_documento,"serie":item.serie,"numeracion":item.numeracion,"fecha_venta":item.fecha_venta}
                  

 
                post('api/baja',this.datos[0])
                .then((res) => {
                 


                 this.cargar_documentos();


                })  .catch((err)=>{

                        
                     if(err.response.status === 500){

                         this.error = err.response.data

                         alert("Número de documento; no existe");

                     }

                      


                  })  
            

        },

    combo(item){

      
     this.cargar_documentos();

    },

     getClass: ({ estado }) => ({
        'md-primary': estado === 'PROCESADO',
        'md-accent': estado === 'ANULADO'
      }),

      },
      
    
	}
</script>
