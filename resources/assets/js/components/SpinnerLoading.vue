<template>
    
<div class="bg">


    <md-progress-spinner v-show="loading"  class="md-accent" md-mode="indeterminate" ></md-progress-spinner>



</div>
       


</template>

<script type="text/javascript">
  
     import Vue from 'vue'
     import Auth from '../../store/auth'
     import { get,post } from '../../helpers/api'
import flash from '../../helpers/flash';


	export default{
         name: 'ProgressSpinnerIndeterminate',
        
        
         components:{

             
         },
 
		data() {
			return {
         
                loading:false,
                
                
            }},



        }




</script>
<style lang="scss" scoped>
.bg{

    height: 100%; 
    background:white;
    z-index: 5;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;

}
 .md-progress-spinner {
    margin: 24px;
 
  }
</style>