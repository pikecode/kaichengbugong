<template>
	<view>
		<fa-navbar title="观看视频" :border-bottom="false"></fa-navbar>
		<video  class="videoBox" style="width: 100%;"	:src="videourl"	:autoplay="true"></video>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				videourl:''
			}
		},
		onLoad(e) {
			this.videourl = e.url
		},
		methods: {
			
		}
	}
</script>

<style>

</style>
