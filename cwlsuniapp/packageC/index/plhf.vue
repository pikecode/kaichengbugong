<template>
	<view class="u-skeleton">
		<!-- 顶部导航 -->
		<fa-navbar title="配料回放" :border-bottom="false"></fa-navbar>
		<view class="u-skeleton-rect">
			<u-swiper :list="info.images" :autoplay="false" @click="preview(info.images, swiperIndex)" @change="swiperChange" height="400" border-radius="0"></u-swiper>
		</view>
		<view class="bg-white goods-detail">
			<view class="title u-font-30 u-p-30 u-skeleton-rect">{{ info.title }}</view>
			<view class="u-p-l-30 u-p-r-30 u-p-b-30 u-skeleton-rect u-tips-color" v-if="info.description">{{ info.description }}</view>
		</view>


		<view class="recommends bg-white u-m-t-30">
			<view class="u-font-30 title">
				<text class="stroke"></text>
				配料项目
			</view>
			<view class="goods-list">
				<view class="item" v-for="(item, index) in recommends" :key="index" @click="goPage('/packageC/ingredientreplay/videos?id=' + item.id + '&title=' + item.title)">
					<view class="images"><image :src="item.image" mode="aspectFill"></image></view>
					<view class="u-p-15 name" >
						<text class="u-line-2" style="color: #000;font-size: 34rpx">{{ item.title }}</text>
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
		this.getInfo();
	},
	onShow() {
		this.getInfo();
	},

	data() {
		return {
			info: {},
			id: '',
			swiperIndex:0,
			recommends:[]
		};
	},
	methods: {

		preview(images, index) {
			uni.previewImage({
				urls: images,
				current: index,
				longPressActions: {
					itemList: ['发送给朋友', '保存图片', '收藏'],
					success: function(data) {
						console.log('选中了第' + (data.tapIndex + 1) + '个按钮,第' + (data.index + 1) + '张图片');
					},
					fail: function(err) {
						console.log(err.errMsg);
					}
				}
			});
		},

		swiperChange(index) {
			this.swiperIndex = index;
		},
		getInfo() {
			if (!this.id) {
				return;
			}
			this.$api.getIngredientReplayInfo({ id: this.id }).then(res => {
				if (res.code == 1) {
					this.info = res.data;
					this.getItems()
				} else {
					this.$u.toast(res.msg);
				}
			});
		},
		goIndexPage() {
			uni.reLaunch({
				url: '/pages/index/index'
			});
		},

		getItems() {
			this.$api.getIngredientReplayItem({ingredientreplay_id:this.id}).then(({code,data:res,msg}) => {
				this.recommends = res
			});
		},
		goPage(url) {
			uni.navigateTo({
				url
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.goods-detail {
	.title {
		font-weight: bold;
	}
}
.recommends {
	padding: 30rpx;
	.title {
		position: relative;
		padding-left: 30rpx;
		margin-bottom: 30rpx;
		font-weight: bold;
		.stroke {
			position: absolute;
			left: 0;
			top: 50%;
			transform: translateY(-50%);
			width: 8rpx;
			height: 32rpx;
			background-color: #374486;
			border-radius: 4rpx;
		}
	}
	.goods-list {
		display: flex;
		flex-wrap: wrap;
		.item {
			width: 48%;
			margin-right: 4%;
			margin-bottom: 30rpx;
			background-color: #fff;
			border-radius: 16rpx;
			overflow: hidden;
			box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.08);
			&:nth-child(2n) {
				margin-right: 0;
			}
			.images {
				width: 100%;
				height: 320rpx;
				image {
					width: 100%;
					height: 100%;
				}
			}
			.name {
				min-height: 100rpx;
			}
		}
	}
}
</style>
