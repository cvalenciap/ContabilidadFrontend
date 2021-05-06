export default {
	state: {
		api_token: null,
		user_id: null,
		name:null
	},
	initialize() {
		this.state.api_token = localStorage.getItem('api_token')
		this.state.user_id = parseInt(localStorage.getItem('user_id'))
		this.state.name = localStorage.getItem('name')
	},
	set(api_token, user_id,name) {
		localStorage.setItem('api_token', api_token)
		localStorage.setItem('user_id', user_id)
		localStorage.setItem('name', name)
		this.initialize()
	},
	remove() {
		localStorage.removeItem('api_token')
		localStorage.removeItem('user_id')
		localStorage.removeItem('name')
		this.initialize()
	}
}
