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
					<view class="item"  v-for="(item, index) in videoList">
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
		// this.getGoodsInfo();
		// this.getDateList('');
		this.getVideoList()
	},
	onShow() {
		if (this.vuex_token) {
			this.getCartNums();
		}
		
		// 获取当前年份
		const currentYear = new Date().getFullYear();
		 
		// 获取当前月份，需要加1，因为getMonth返回的月份是从0开始计算的
		const currentMonth = new Date().getMonth() + 1;
		
		this.dy = currentYear+"-"+currentMonth
	},
	
	computed: {
		noComment() {
			return item => {
				if (!item) {
					return true;
				} else if (!item.length) {
					return true;
				} else {
					return false;
				}
			};
		},
		skuSpecAttr() {
			let arr = [];
			if (this.info.sku_spec) {
				for (let sku of this.info.sku_spec) {
					let valArr = sku.sku_value.map(item => {
						return item.title;
					});
					arr.push(sku.title + ':' + valArr.join(','));
				}
			}
			return arr.join('-');
		}
	},
	data() {
		return {
			info: {},
			id: '',
			cart_nums: 0,
			show: false,
			guarantee_show: false,
			sceneval: 2,
			loading: true,
			swiperIndex: 0,
			showReplay: false,
			replyId: '',
			showShare: false,
			showPoster: false,
			showCoupon:false,
			recommends: [],
			list: ['配料全程回放', '精彩瞬间'],
			riqishow:false,
			datearr:[],
			dy:"2024-10",
			riindex:3,
			curNow:0,
			videoList:[],
			videoshow:false,
			videourl:''
		};
	},
	methods: {
		ss(){
			this.videoshow = false;
		},
		openVideo(i){
			uni.navigateTo({
				url:"/packageD/video/video?url="+this.videoList[i].video_url
			})
			
		},
		sectionChange(index) {
			this.curNow = index;
			this.getVideoList()
		},
		xx(index){
			this.riindex = index
			this.getVideoList()
		},
		changedate(e){
			this.getDateList(e.result)
			this.dy = e.year+"-"+e.month
			this.getVideoList()
		},
		getDateList(date){
			this.$api.getdateList({ date:date }).then(res => {
				this.datearr = res.data;
				
			});
		},
		getVideoList(){
			this.$api.getTraceabilityGoodsVideoList({type:this.curNow,traceabilitygoods_id:this.id}).then(res => {
				this.videoList = res.data
			});
		},
		swiperChange(index) {
			this.swiperIndex = index;
		},
		getGoodsInfo() {
			if (!this.id) {
				return;
			}
			this.$api.getGoodsInfo({ id: this.id }).then(res => {
				if (res.code == 1) {
					this.info = res.data;
					// #ifdef MP-WEIXIN
					this.$u.mpShare = {
						title: res.data.title,
						imageUrl: res.data.image,
						path: '/packageA/goods/detail?id=' + this.id + '&invite_id=' + (this.vuex_user.id || '')
					};
					// #endif
					// #ifdef H5
					if (this.$util.isWeiXinBrowser()) {
						this.wxShare({
							title: res.data.title,
							desc: res.data.description,
							url: window.location.href + `&invite_id=${this.vuex_user.id || ''}`,
							img: res.data.image
						});
					}
					// #endif
					this.loading = false;
					//取同类推荐商品
					this.getGoods(this.info.category_id);
				} else {
					this.$u.toast(res.msg);
					setTimeout(() => {
						uni.$u.route({
							type:'back'
						});
					}, 1500);
				}
			});
		},
		goIndexPage() {
			uni.reLaunch({
				url: '/pages/index/index'
			});
		},
		getCartNums() {
			this.$api.cart_nums().then(res => {
				if (res.code) {
					this.cart_nums = res.data || 0;
				}
			});
		},
		optionCollect() {
			this.$api
				.optionCollect({
					goods_id: this.id
				})
				.then(res => {
					this.$u.toast(res.msg);
					if (res.code) {
						this.$set(this.info, 'is_collect', !this.info.is_collect);
					}
				});
		},
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
		getGoods(category_id) {
			// console.log(222);
			this.$api.getGoodsIndex().then(({code,data:res,msg}) => {
				// console.log(res)
				if (code) {
					this.recommends = res.hots;
					// this.recommends = res.recommends;
				}
			});
			// this.$api.getGoodsList({ category_id }).then(({ code, data: res, msg }) => {
			// 	if (code == 1) {
			// 		this.recommends = res.data;
			// 	}
			// });
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
	    padding: 40rpx;
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
