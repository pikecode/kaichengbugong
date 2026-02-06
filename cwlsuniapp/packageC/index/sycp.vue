<template>
	<view class="u-skeleton">
		<!-- 顶部导航 -->
		<fa-navbar title="溯源商品" :border-bottom="false"></fa-navbar>
		<view class="u-skeleton-rect">
			<u-swiper :list="info.images" :autoplay="false" @click="preview(info.images, swiperIndex)" @change="swiperChange" height="400" border-radius="0"></u-swiper>
		</view>
		<view class="bg-white goods-detail">
			<view class="title u-font-30 u-p-30 u-skeleton-rect">{{ info.title }}</view>
			<!-- <view class="u-p-l-30 u-p-r-30 u-p-b-30 u-skeleton-rect u-tips-color">{{ info.subtitle }}</view> -->
		</view>


		<view class="recommends bg-white u-m-t-30">
			<view class="u-font-30 title">
				<text class="stroke"></text>
				溯源回放
			</view>
			<view class="goods-list">
				<view class="item" v-for="(item, index) in recommends" :key="index" @click="goPage('/packageC/index/sycpvideo?id=' + item.id)">
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
		this.id = e.id || e.goods_id || '';
		this.getGoodsInfo();
	},
	onShow() {
		this.getGoodsInfo();
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
		getGoodsInfo() {
			if (!this.id) {
				return;
			}
			this.$api.getTraceabilityInfo({ id: this.id }).then(res => {
				if (res.code == 1) {
					this.info = res.data;
					this.getGoods()
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
		
		getGoods() {
			this.$api.getTraceabilityGoods({traceability_id:this.id}).then(({code,data:res,msg}) => {
				this.recommends = res
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
.goods-detail {
	.market_price {
		text-decoration: line-through;
		margin-left: 15rpx;
	}
}
.item{
	    background: #7cc5ef;
	    padding: 20rpx;
	    color: #fff;
	    border-radius: 10rpx;
	    margin-top: 20rpx;
		// line-height: 60rpx;
}
.ac{
	background: #367bf7;
}
.coupon {
	padding: 0 20rpx;
	border: 1rpx solid #f2857b;
	color: #e93323;
	position: relative;
	&:before,
	&:after {
		content: ' ';
		position: absolute;
		width: 8rpx;
		height: 10rpx;
		border-radius: 10rpx;
		border: 1rpx solid #f2857b;
		background-color: #fff;
		bottom: 50%;
		margin-bottom: -8rpx;
	}
	&:before {
		left: -4rpx;
		border-left-color: #fff;
	}
	&:after {
		border-right-color: #fff;
		right: -4rpx;
	}
}

.comment {
	display: flex;
	padding: 30rpx;
	.left {
		image {
			width: 64rpx;
			height: 64rpx;
			border-radius: 50%;
			background-color: #f2f2f2;
		}
	}
	.right {
		flex: 1;
		padding-left: 20rpx;
		font-size: 30rpx;
		.top {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 10rpx;
			.like {
				display: flex;
				align-items: center;
				font-size: 26rpx;
				.num {
					margin-right: 4rpx;
				}
			}
		}
		.content {
			margin-bottom: 10rpx;
		}
		.reply-box {
			background-color: rgb(242, 242, 242);
			border-radius: 12rpx;
			margin-top: 15rpx;
			.item {
				padding: 20rpx;
				font-size: 24rpx;
				&:not(:last-child) {
					border-bottom: solid 2rpx $u-border-color;
				}
			}
			.all-reply {
				padding: 20rpx;
				display: flex;
				align-items: center;
				.more {
					margin-left: 6rpx;
				}
			}
		}
		.bottom {
			margin-top: 20rpx;
			display: flex;
			font-size: 24rpx;
			color: #9a9a9a;
		}
		.images {
			display: flex;
			flex-wrap: wrap;
			.thumb {
				width: 130rpx;
				height: 130rpx;
				margin: 15rpx 15rpx 0 0;
			}
		}
	}
}
.guarantee-content {
	padding: 24rpx;
	text-align: center;
}

.recommends {
	padding: 30rpx 30rpx 0;
	.title {
		position: relative;
		padding-left: 15rpx;
		.stroke {
			&::before {
				content: '';
				width: 8rpx;
				height: 30rpx;
				background-color: #374486;
				position: absolute;
				top: 15%;
				left: 0rpx;
				border-radius: 20rpx;
			}
		}
	}
}

.goods-list {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	margin-top: 30rpx;
	.item {
		width: calc((100vw - 90rpx) / 2);
		background-color: #ffffff;
		box-shadow: 0px 0px 5px rgb(233, 235, 243);
		margin-bottom: 30rpx;
		border-radius: 10rpx;
		overflow: hidden;
		border: 1px solid #e9ebf3;
		.name {
			min-height: 110rpx;
		}
		.foot {
			padding: 0 15rpx;
		}
		.images {
			width: 100%;
			height: 350rpx;
			image {
				width: 100%;
				height: 100%;
			}
		}
		.market_price {
			text-decoration: line-through;
			margin-left: 10rpx;
		}
	}
}
</style>
