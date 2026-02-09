<template>
	<view class="u-skeleton">
		<!-- 顶部导航 -->
		<fa-navbar title="视频回放" :border-bottom="false"></fa-navbar>

		<view class="bg-white u-m-t-30">
			<view style="padding: 20rpx;">

				<view style="margin-top: 20rpx;">
					<u-subsection :list="list" active-color="#fff" buttonColor="#367bf7" mode="button"  :current="curNow" @change="sectionChange"></u-subsection>
				</view>

				<view>
					<!-- 视频列表 (type=0或1) -->
					<view v-if="curNow <= 1" class="item"  v-for="(item, index) in videoList" :key="index">
						<view class="head" style="display: flex;justify-content: space-between;align-items: center;">
							<view>
								<view class="" style="padding: 3px 5px;background: #2279fb; font-size: 24rpx;border-radius: 4rpx;">
									{{item.tag}}
								</view>
							</view>

							<view style="font-size: 22rpx;color: #fff;">
								播放时长:{{item.shichang}}
							</view>
						</view>
						<view style="margin-top: 20rpx;" >
							<view style="font-size: 50rpx;">
								{{item.title}}
							</view>
						</view>

						<view style="font-size: 24rpx;margin-top: 20rpx;">
							亮点:{{item.liangdian}}
						</view>

						<view style="margin-top: 20rpx;display: flex;justify-content: flex-end;" >
							<view  @click="openVideo(index)" style="padding: 4rpx 40rpx; background: #f9511f;border-radius: 100rpx;width: 200rpx;text-align: center;">点击回放</view>
						</view>
					</view>

					<!-- 图片列表 (type=2 有封条，不掉包) -->
					<view v-if="curNow === 2" class="image-item" v-for="(item, index) in videoList" :key="index">
						<view class="head" style="display: flex;justify-content: space-between;align-items: center;">
							<view>
								<view class="" style="padding: 3px 5px;background: #2279fb; font-size: 24rpx;border-radius: 4rpx;">
									{{item.tag}}
								</view>
							</view>
						</view>
						<view style="margin-top: 20rpx;">
							<view style="font-size: 50rpx;">
								{{item.title}}
							</view>
						</view>

						<view style="font-size: 24rpx;margin-top: 20rpx;" v-if="item.liangdian">
							说明:{{item.liangdian}}
						</view>

						<!-- 图片展示 -->
						<view style="margin-top: 20rpx;" v-if="item.image">
							<image :src="item.image" mode="widthFix" style="width: 100%; border-radius: 10rpx;" @click="previewImage(item.image)"></image>
						</view>
					</view>
				</view>

			</view>
		</view>

	</view>
</template>

<script>
// #ifdef H5
import { weixinShare } from '@/common/fa.weixin.mixin.js';
// #endif
export default {
	mixins: [
		// #ifdef H5
		weixinShare
		// #endif
	],
	onLoad(e) {
		this.id = e.id || '';
		this.date = e.date || '';  // 接收日期参数
		this.getVideoList();
	},
	onShow() {
		if (this.vuex_token) {
			this.getCartNums();
		}
	},
	data() {
		return {
			id: '',
			date: '',  // 日期参数
			cart_nums: 0,
			list: ['配料全程回放', '精彩瞬间', '有封条，不掉包'],
			curNow: 0,
			videoList: [],
			videoshow: false,
			videourl: ''
		};
	},
	methods: {
		previewImage(url) {
			uni.previewImage({
				urls: [url],
				current: url
			});
		},
		ss() {
			this.videoshow = false;
		},
		openVideo(i) {
			uni.navigateTo({
				url: "/packageD/video/video?url=" + this.videoList[i].video_url
			});
		},
		sectionChange(index) {
			this.curNow = index;
			this.getVideoList();
		},
		getVideoList() {
			let params = {
				type: this.curNow,
				ingredientreplayitem_id: this.id
			};

			// 如果有日期参数，添加到请求中
			if (this.date) {
				params.date = this.date;
			}

			this.$api.getIngredientReplayItemVideoList(params).then(res => {
				this.videoList = res.data;
			});
		},
		getCartNums() {
			this.$api.cart_nums().then(res => {
				if (res.code) {
					this.cart_nums = res.data || 0;
				}
			});
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #f4f6f8;
}
.dd{
	width: 13%;
	padding: 12rpx 0rpx;
	text-align: center;
	background: #7cc5ef;
	margin: 6rpx;
	border-radius: 10rpx;
	line-height: 50rpx;
	color: #fff;
}
</style>
<style lang="scss" scoped>
.item{
	background: #7cc5ef;
	padding: 40rpx;
	color: #fff;
	border-radius: 10rpx;
	margin-top: 20rpx;
}
.image-item{
	background: #7cc5ef;
	padding: 40rpx;
	color: #fff;
	border-radius: 10rpx;
	margin-top: 20rpx;
}
.ac{
	background: #367bf7;
}
</style>
