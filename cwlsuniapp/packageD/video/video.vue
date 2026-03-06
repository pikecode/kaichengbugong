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
				videourl: '',
				shareUrl: '' // 用于分享的完整 URL 参数
			}
		},
		onLoad(e) {
			this.videourl = e.url
			this.shareUrl = e.url
		},
		onShareAppMessage() {
			return {
				title: '视频回放',
				path: `/packageD/video/video?url=${encodeURIComponent(this.shareUrl)}`,
				imageUrl: '' // 可以设置视频封面图
			}
		},
		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '视频回放',
				query: `url=${encodeURIComponent(this.shareUrl)}`
			}
		},
		// #endif
		methods: {

		}
	}
</script>

<style>

</style>
