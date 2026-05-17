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
				videourl: ''
			}
		},
		onLoad(e) {
			// 支持编码和未编码的 URL
			this.videourl = decodeURIComponent(e.url || '')
			console.log('视频地址:', this.videourl)
		},
		onShareAppMessage() {
			return {
				title: '视频回放',
				path: `/packageD/video/video?url=${this.videourl}`,
				imageUrl: ''
			}
		},
		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '视频回放',
				query: `url=${this.videourl}`
			}
		},
		// #endif
		methods: {

		}
	}
</script>

<style>

</style>
