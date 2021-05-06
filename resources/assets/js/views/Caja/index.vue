<template>

<div class="row" >

<div class="md-layout md-gutter" >
	  


<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-50 md-small-size-100 md-xsmall-size-100" >
    <p>Tipo de comprobante</p>
<hr></hr>

 </div>
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
      <md-radio v-model="form.fradio" v-on:change="cargardatos" value="factura">Factura</md-radio>
</div>
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
       <md-radio v-model="form.fradio"v-on:change="cargardatos"  value="boleta">Boleta</md-radio>
</div>   
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
      
      <md-radio v-model="form.fradio" v-on:change="cargardatos" value="notacdredito">Nota de Crédito</md-radio>
</div> 
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
      <md-radio v-model="form.fradio" v-on:change="cargardatos" value="notadebitp">Nota de Débito</md-radio>
</div> 

<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-50 md-small-size-50 md-xsmall-size-100" >
    <p>Documento</p>
<hr></hr>
 </div>
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
      <md-field>
            <label>Serie:</label>
            <md-input v-model="form.serie" disabled></md-input>
      </md-field>
</div>
    <div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
      <md-field>
            <label>Número (6 números):</label>
            <md-input v-model="form.numero_doc" disabled></md-input>
      </md-field>
</div>
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
      <md-field>
            <label>Fecha Doc:</label>
            <md-input v-model="form.fecha_doc" disabled></md-input>
      </md-field>
</div>
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
        <div class="md-layout-item">
        <md-field>
          
          <label for="food">Moneda</label>
          <md-select v-model="form.moneda" name="country" id="country" placeholder="Moneda">
            <md-option value="PEN">Soles</md-option>
            <md-option value="USD">Dólares</md-option>
            <md-option value="EUR">Euros</md-option>
          </md-select>
        </md-field>
      </div>
</div>

<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-50 md-small-size-50 md-xsmall-size-100" >
    <p>Cliente</p>
<hr></hr>
 </div>
<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
        <div class="md-layout-item">
        <md-field>
          
          <label for="food">Tipo de Documento: </label>
          <md-select v-model="form.tipo_documento" name="country" id="country" placeholder="Tipo Documento">
            <md-option value="1">DNI</md-option>
            <md-option value="0">Doc.Trib.NO.Dom.Sin.RUC</md-option>
            <md-option value="4">Carné de Extranjería</md-option>
              <md-option value="6">RUC</md-option>
            <md-option value="7">Pasaporte</md-option>
             <md-option value="A">Ced. Diplomática de Identidad</md-option>
          </md-select>
        </md-field>
      </div>
</div>

<div class="md-layout-item md-xlarge-size-30 md-large-size-30 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
      <md-field>
            <label>N° de Documento:</label>
            <md-input ref="myref" v-model="form.documento_identidad" ></md-input>
      <md-button class="md-icon-button md-primary " @click="buscar(form.tipo_documento,form.documento_identidad)">
        <md-icon>search</md-icon>
      </md-button>
      </md-field>
     

</div>

<div class="md-layout-item md-xlarge-size-20 md-large-size-20 md-medium-size-30 md-small-size-30 md-xsmall-size-30" >
   
      <md-field>
            <label>Nombre Cliente:</label>
            <md-input v-model="form.nombre_cliente" ></md-input>
      </md-field>
</div>
<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100 md-xsmall-size-100" >
   
       <md-field>
    
            <label>Dirección:</label>
            <md-input v-model="form.direccion" ></md-input>
      
    </md-field>
</div>
<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-50 md-small-size-50 md-xsmall-size-100" >
    <p>Productos</p>
<hr></hr>
 </div>

<div>
    <md-dialog :md-active.sync="showDialog">
     
      <md-dialog-content>

     <md-table v-model="searched" md-sort="codigo" md-sort-order="asc" md-card md-fixed-header @md-selected="onSelect" >
      <md-table-toolbar>
        <div class="md-toolbar-section-start">
          <h1 class="md-title">Productos</h1>
        </div>
  <md-radio v-model="radio" :value="1" class="md-primary" >Código</md-radio>
    <md-radio v-model="radio" :value="2" class="md-primary" >Producto</md-radio>
        <md-field md-clearable class="md-toolbar-section-end">
          <md-input v-if="radio==1" placeholder="Busqueda por código" v-model="search"  ref="sc"  @keyup.enter="loadData"/>
          <md-input v-else placeholder="Busqueda por producto" v-model="search"  ref="sp"  @keyup.enter="loadData"/>

        </md-field>
      </md-table-toolbar>     

      <md-table-empty-state
        md-label="No se encuentra el producto"
        :md-description="`Ningún bien encontrado '${search}'. Intente un código diferente o importe más datos.`">
       
      </md-table-empty-state>

      <md-table-row slot="md-table-row" slot-scope="{ item }" md-selectable="single" @click="active = true">
        <md-table-cell md-label="ID" md-sort-by="id" md-numeric>{{ item.id }}</md-table-cell>
        <md-table-cell md-label="Código" md-sort-by="codigo">{{ item.codigo }}</md-table-cell>
        <md-table-cell md-label="Producto" md-sort-by="producto">{{ item.producto }}</md-table-cell>
        <md-table-cell md-label="Unidad" md-sort-by="unidad">x{{ item.unidad }}</md-table-cell>
         <md-table-cell md-label="Precio" md-sort-by="precio">{{ formatPrice(item.precio) }}</md-table-cell>

      </md-table-row>
    </md-table>
     </md-dialog-content>
      <md-dialog-actions>
        <md-button class="md-primary" @click="showDialog = false">Cerrar</md-button>
      </md-dialog-actions>
    </md-dialog>



  </div>

<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100 md-xsmall-size-100" align="center" style="margin-bottom:20px;">
  
    <md-button class="md-raised md-primary" @click="showDialog = true" >Agregar Productos</md-button>
    
 </div>


<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-100 md-xsmall-size-100">


   

   <md-table v-model="cart" md-card @md-selected="onSelectRemove" md-fixed-header>
    
       
       <!-- <h2 style="float:right;">{{ formatPrice(cartSubTotal) }}</h2>
       
       
                product.unidad='NIU';
                product.subtotal=(product.precio*product.quantity)/1.18;
                product.igv=(product.precio*product.quantity)-(product.subtotal);
                product.importe=product.precio*product.quantity;
       
       
       -->
      
      <md-table-row slot="md-table-row" slot-scope="{ item }"  md-selectable="single">
        <md-table-cell md-label="COD." md-sort-by="id">{{ item.id }}</md-table-cell>
        <md-table-cell md-label="Producto" md-sort-by="producto">{{ item.producto }}</md-table-cell>
         <md-table-cell md-label="Und/Medida" md-sort-by="unidad">{{ item.unidad }}</md-table-cell>
        <md-table-cell md-label="P.U" md-sort-by="quantity">{{ item.precio }}</md-table-cell>
         <md-table-cell md-label="Cant." md-sort-by="id">{{ item.quantity }}</md-table-cell><md-tooltip md-direction="bottom">Eliminar </md-tooltip>
          <md-table-cell md-label="Subtotal" md-sort-by="subtotal">{{ item.subtotal}}</md-table-cell>
          <md-table-cell md-label="igv" md-sort-by="igv">{{ item.igv}}</md-table-cell>

        <md-table-cell md-label="importe" md-sort-by="importe">{{ item.importe}}</md-table-cell>
      </md-table-row>  
    </md-table>


        <md-divider></md-divider>
       

      <md-snackbar :md-position="position" :md-duration="isInfinity ? Infinity : duration" :md-active.sync="showSnackbar" md-persistent>
      <span>Se elimino de la lista {{selected.producto}}</span>
      <md-button class="md-primary" @click="showSnackbar = false">Retry</md-button>
    </md-snackbar>
  

</div>
    <md-dialog :md-active.sync="active" style="padding:30px;">
     

      <md-content md-dynamic-height>
 

      <div class="md-layout-item">
        <md-field>
          <label for="movie">Tipo Unidad</label>
          <md-select v-model="unidad" name="movie" id="movie">
            <md-option value="BG">Bolsa</md-option>
            <md-option value="BO">Botella</md-option>
            <md-option value="BX">Caja</md-option>
            <md-option value="DZN">Docena</md-option>
            <md-option value="GRM">Gramo</md-option>
            <md-option value="KGM">Kilogramo</md-option>
            <md-option value="CA">Latas</md-option>
            <md-option value="NIU">Unidad</md-option>
          </md-select>
        </md-field>
      </div>

            
      <md-field>
        <label>Cantidad: </label>
        <md-input v-model="value" @keyup.enter="sentData()" ></md-input>
      </md-field>
      </md-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="active = false">Cerrar</md-button>
        <md-button class="md-primary"  @click="sentData()">Añadir</md-button>
      </md-dialog-actions>
    </md-dialog>

        <div class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 md-xsmall-size-50" >
          
              <md-field>
                    <label>Observación:</label>
                    <md-input v-model="form.observacion" ></md-input>
              </md-field>
        </div>
        <div class="md-layout-item md-xlarge-size-50 md-large-size-50 md-medium-size-50 md-small-size-50 md-xsmall-size-50" >
          
                    <p>Resumen:</p>
                    <hr></hr>
                    <p>Subtotal:   <span style="float:right;">S/ {{ formatPrice(form.cartSubTotal) }} </span></p>
                    <p>IGV (18%):   <span style="float:right;">S/ {{ formatPrice(form.cartIgv) }} </span></p>
                    <p style="font-size:20px;">Total:   <span style="float:right;">S/ {{ formatPrice(form.cartTotal) }} </span></p>


        </div>

</div>



<div class="md-layout-item md-xlarge-size-100 md-large-size-100 md-medium-size-100 md-small-size-50 md-xsmall-size-100" align="center">


    <md-button class="md-raised md-accent"  @click="facturador()"> Enviar SUNAT 
       <md-icon v-if="loading==false">send</md-icon> <md-progress-spinner v-show="loading" 
        :md-diameter="30" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner></md-button>



  </div>

    <md-dialog-alert
      :md-active.sync="first"
      :md-title="`${titleRespuesta}` "
      :md-content="`Documento <strong>${respuesta.message}</strong>`"/>







</div>

</template>


<script type="text/javascript">
  
     import Vue from 'vue'
     import Auth from '../../store/auth'
     import { get,post } from '../../helpers/api'
import flash from '../../helpers/flash';


     
  const toLower = text => {
    return text.toString().toLowerCase()
  }



  const searchByName = (items, term) => {
    if (term) {
      
      return items.filter(item => toLower(item.codigo).includes(toLower(term)))
    }

    return items
  }
  const searchByName_two = (items, term) => {
    if (term) {

      return items.filter(item => toLower((item.producto)).includes((toLower(term))))
    }

    return items
  }

	export default{
       name: 'TableSearch',
       name: 'RegularRadio',
       name: 'DialogPrompt',
       name:'Divider',
       name:'TableSingle',
      name: 'ScrollbarExample',
      name:'Direction',
        name: 'SnackbarExample',
        name: 'DialogCustom',
         name: 'ProgressSpinnerIndeterminate',
         name: 'DialogAlert',
        
        
         components:{

             
         },
 
		data() {
			return {
         
                auth: Auth.state,
                 first: false,
                isProcessing: false,
                isExport:false,
                search: '',
                searched: [],
                selected: {},
                patrimonio:[],
                carrito:[],
                active: false,
                value: '',
                unidad:'NIU',
                radio:2,
                fradio:'factura',
                checkoutBool: false,
                cart: [],
                loading:false,
                
                tax: 0.065,
               
                 showSnackbar: false,
                  position: 'center',
                  duration: 4000,
                  isInfinity: false,

      showDialog: false,
      people: [
        
      ],
      datos:[],
      respuesta:[],
      titleRespuesta:'',
      form:{
      fradio:'factura',
      serie:'F0001',
      numero_doc:'004870',
      fecha_doc:'2019-02-13',
      moneda:'PEN',
      tipo_documento:6,
      documento_identidad:'',
      nombre_cliente:'',
      direccion:'',
      observacion:'',
      cartSubTotal: 0,
      cartIgv:0,
      cartTotal: 0
      
      },

                
            }},

           created(){
             

             this.cargardatos(this.form.fradio);
        },


		         methods:{

               cargardatos(radio){
                    get(`/api/documento-formato/${radio}`)
                          .then((res) => {
                               
                               
                this.form.serie=res.data.serie;
                this.form.numero_doc=res.data.correlativo;
                this.form.tipo_documento=res.data.tipo;
                this.form.fecha_doc=res.data.fecha;
                this.$refs.myref.$el.focus();
                                 
                          })

                  

               },

               buscar(tipo,documento){
                
                          
                this.datos[0]={"tipo":tipo,"documento":documento}
                 

               

                post('api/buscar',this.datos[0])
                .then((res) => {
                 
                console.log(res.data.cliente);
                this.form.nombre_cliente=res.data.cliente;
                this.form.direccion=res.data.direccion;

                })  .catch((err)=>{

                        
                     if(err.response.status === 500){

                         this.error = err.response.data

                         alert("Número de documento; no existe");

                     }

                      


                  })

               },

             
               facturador(){
                 
                this.loading=true;

                post('api/facturador', {
                form:this.form,
                cart:this.cart
            })
                .then((res) => {


                   this.respuesta=res.data;
                   this.titleRespuesta = (this.respuesta.status == 'true'?'Envio Satisfactorio':'Error de Envio');
                   this.loading=false;
                  
                   this.first=true;

                }) 
                  .catch((err)=>{

                         
                    console.log(err);

                     })

               },
      formatPrice(value) {
              let val = (value/1).toFixed(2).replace(',', '.')
              return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
          },

         onSelect (items) {
        this.selected = items
      },
        onSelectRemove (items) {

 this.selected = items
         this.removeProduct(items)

      },
      getAlternateLabel (count) {
        let plural = ''

        if (count > 1) {
          plural = 's'
        }

        return `${count} producto${plural} seleccionado${plural}`
      },
        loadData(){


               
                this.form[0]={"busqueda":this.search,"radio":this.radio}
                 


                post('api/productoventa',this.form[0])
                .then((res) => {
                 
                   this.patrimonio=res.data
                   this.searched = this.patrimonio

                })
                

        },      
        
        searchOnTable () {
                this.searched = searchByName(this.patrimonio, this.search)
                this.search.focus()
              },
                 searchOnTable_two () {
                this.searched = searchByName_two(this.patrimonio, this.search)
              },
              onSelect (item) {
        this.selected = item
      },
        addToCart(product) {
      var found = false;


              if(!found) {
                product.quantity = this.value;
                product.unidad=this.unidad;
                product.subtotal=(product.precio*product.quantity)/1.18;
                product.igv=(product.precio*product.quantity)-(product.subtotal);
                product.importe=product.precio*product.quantity;
                this.cart.push(product);
              }

              this.form.cartSubTotal = this.form.cartSubTotal + product.subtotal;
             
              this.form.cartIgv=this.form.cartIgv+product.igv;
               this.form.cartTotal = this.form.cartTotal+product.importe;
              this.checkoutBool = true;
    },
        removeProduct(product) {

         console.log(this.cart[0])
         // this.cart.$remove(product);
        for( var i = this.cart.length - 1; i >= 0; i--){
           
              if ( this.cart[i] === product) {
                    this.cart.splice(i, 1);
                    this.form.cartSubTotal = this.form.cartSubTotal - (product.subtotal);
                    this.form.cartIgv=this.form.cartIgv-product.igv;
                    this.form.cartTotal = this.form.cartSubTotal+this.form.cartIgv;
                    

              }
              else if(this.cart.length <= 0) {
                   this.checkoutBool = false;
               }
              
              }
                      this.showSnackbar = true

          
        },
        sentData(){
            
                 console.log(this.selected);
                 this.addToCart(this.selected);
                 this.active = false;
                 this.unidad='NIU';
                 this.value='';
                 this.search=this.selected.producto;
                 this.searched=this.patrimonio;

        },
        clear(){

            this.value=''

        }
        


           }





        }




</script>
<style lang="scss" scoped>
  .md-field {
    max-width: 300px;
  }
   .md-table + .md-table {
    margin-top: 16px
  }
  p{
    font-weight: 700;
  }
 .md-progress-spinner {
     margin: 2px;
  }
  .list-group{
        list-style: none;
  }

</style>