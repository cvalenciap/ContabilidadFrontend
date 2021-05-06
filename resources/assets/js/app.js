import Vue from 'vue'

import App from './App.vue'
import router from './router'

import VueProgressBar from 'vue-progressbar'

const options = {
	color: '#4CAF50',
	failedColor: '#874b4b',
	thickness: '5px',
	transition: {
	  speed: '0.1s',
	  opacity: '0.6s',
	  termination: 600
	},
	autoRevert: true,
	location: 'top',
	inverse: false
	}
	

	Vue.use(VueProgressBar, options);
   

const app = new Vue({
	el: '#root',
	template: `<app></app>`,
	components: { App },
	router
})
