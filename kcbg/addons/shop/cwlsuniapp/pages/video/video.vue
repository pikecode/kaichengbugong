<template>
	<view>
		<fa-navbar title="观看视频" :border-bottom="false"></fa-navbar>
		<video
			class="videoBox"
			style="width: 100%; height: 420rpx;"
			:src="videourl"
			:autoplay="true"
			controls
			@error="onError"
		></video>
		<view v-if="errorMsg" class="error-box">
			<text class="error-text">{{ errorMsg }}</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				videourl: '',
				errorMsg: ''
			}
		},
		onLoad(e) {
			// 对 URL 进行解码
			if (e.url) {
				this.videourl = decodeURIComponent(e.url);
				console.log('视频地址：', this.videourl);
			} else {
				this.errorMsg = '视频地址为空';
				uni.showToast({
					title: '视频地址为空',
					icon: 'none'
				});
			}
		},
		methods: {
			onError(e) {
				console.error('视频播放错误：', e);
				this.errorMsg = '视频加载失败，请检查网络或视频地址';
				uni.showToast({
					title: '视频加载失败',
					icon: 'none'
				});
			}
		}
	}
</script>

<style>
.error-box {
	padding: 40rpx;
	text-align: center;
}
.error-text {
	color: #999;
	font-size: 28rpx;
}
</style>
