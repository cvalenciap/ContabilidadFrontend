<template>
	<div class="image">
	
		<div class="image__upload">
			<input type="file" accept=".csv" @change="upload">
		</div>
	</div>
</template>
<script type="text/javascript">

	export default {
		components: {

		},
		props: {
			value: {
			    type: [String, File],
			    default: null
			}
		},
		methods: {
			upload(e) {
				const files = e.target.files
				if (files && files.length > 0) {
				  this.$emit('input', files[0])
				}
			}
		}
	}
</script>