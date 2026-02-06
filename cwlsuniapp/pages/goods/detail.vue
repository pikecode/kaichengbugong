<template>
	<view class="u-skeleton">
		<!-- 顶部导航 -->
		<fa-navbar title="商品详情" :border-bottom="false"></fa-navbar>
		<view class="u-skeleton-rect">
			<u-swiper :list="info.images" :autoplay="false" @click="preview(info.images, swiperIndex)" @change="swiperChange" height="400" border-radius="0"></u-swiper>
		</view>
		<view class="bg-white goods-detail">
			<view class="title u-font-30 u-p-30 u-skeleton-rect">{{ info.title }}</view>
			<view class="u-p-l-30 u-p-r-30 u-p-b-30 u-skeleton-rect u-tips-color">{{ info.subtitle }}</view>
			<view class="u-flex u-p-30 u-border-top u-row-between">
				<view class="u-skeleton-rect">
					<text class="price u-font-40">￥{{ info.price }}</text>
					<text class="market_price u-tips-color" v-if="  info.marketprice  > 0">￥{{ info.marketprice }}</text>
				</view>
				<view class="u-flex u-skeleton-rect">
					<view class="u-text-center" @click="showShare = true">
						<u-icon size="35" name="share" :color="theme.bgColor"></u-icon>
						<view class="u-font-22">分享</view>
					</view>
					<view class="u-text-center u-m-l-30" @click="optionCollect" :style="{ color: info.is_collect ? theme.bgColor : '#333' }">
						<u-icon size="35" :name="info.is_collect ? 'star-fill' : 'star'" :color="theme.bgColor"></u-icon>
						<view class="u-font-22">{{ info.is_collect ? '已收藏' : '收藏' }}</view>
					</view>
				</view>
			</view>
		</view>

		<view class="bg-white u-m-t-30" v-if="info.coupon && info.coupon.length">
			<view class="u-p-l-30 u-p-r-30 u-p-t-30 text-weight">优惠券</view>
			<view class="u-p-30 u-flex u-row-between" @click="showCoupon = true">
				<view class="u-flex u-flex-wrap">
					<view class="u-m-r-15 u-m-b-15 u-flex coupon" v-for="(item, index) in info.coupon" :key="index">
						满{{ item.result_data && item.result_data.money }} {{ item.result == 1 ? '减' : '打' }}
						{{ item.result_data && item.result_data.number }} {{ item.result == 1 ? '元' : '折' }}
					</view>
				</view>
				<u-icon name="arrow-right" color="#909399"></u-icon>
			</view>
		</view>

		<view class="bg-white u-m-t-30" v-if="info.sku_spec && info.sku_spec.length">
			<view class="u-p-l-30 u-p-r-30 u-p-t-30 text-weight">商品规格</view>
			<view class="u-p-30 u-flex u-row-between" @click="(sceneval = 0), (show = true)">
				<text class="u-line-1" v-text="skuSpecAttr"></text>
				<u-icon name="arrow-right" color="#909399"></u-icon>
			</view>
		</view>

		<!-- <view class="bg-white u-m-t-30" v-if="info.guarantee && info.guarantee.length">
			<view class="u-p-l-30 u-p-r-30 u-p-t-30 text-weight">服务保障</view>
			<view class="u-p-30 u-flex u-row-between" @click="guarantee_show = true">
				<view class="u-flex u-flex-wrap">
					<view class="u-m-r-15 u-m-b-15" v-for="(item, index) in info.guarantee" :key="index">
						<u-icon name="checkmark-circle" color="#f2140c"></u-icon>
						<text class="u-m-l-5">{{ item.name }}</text>
					</view>
				</view>
				<u-icon name="arrow-right" color="#909399"></u-icon>
			</view>
		</view> -->

		<view class="bg-white u-m-t-30" v-if="info.attributes && info.attributes.length">
			<view class="u-p-l-30 u-p-r-30 u-p-t-30 text-weight">商品属性</view>
			<view class="u-p-30">
				<u-table>
					<!-- <u-tr class="u-tr">
						<u-th width="35%" class="u-th">名称</u-th>
						<u-th width="65%" class="u-th">属性值</u-th>
					</u-tr> -->
					<u-tr class="u-tr" v-for="(item, index) in info.attributes" :key="index">
						<u-td width="35%" class="u-td">
							<text class="text-weight">{{ item.name }}</text>
						</u-td>
						<u-td width="65%" class="u-td">{{ item.attribute_value.map(val => val.name).join(',') }}</u-td>
					</u-tr>
				</u-table>
			</view>
		</view>

		<!-- 溯源回放入口 -->
		<view class="bg-white u-m-t-30" v-if="info.traceability_url">
			<view class="u-p-l-30 u-p-r-30 u-p-t-30 text-weight">溯源回放</view>
			<view class="u-p-30 u-flex u-row-between" @click="goToTraceability">
				<view class="u-flex">
					<u-icon name="video" color="#367bf7" size="32"></u-icon>
					<text class="u-m-l-15 u-font-28">查看溯源回放</text>
				</view>
				<u-icon name="arrow-right" color="#909399"></u-icon>
			</view>
		</view>

		<view class="bg-white u-m-t-30">
			<view style="padding: 20rpx;">
				<view style="width: 100%;text-align: center;padding: 10px;font-size: 30rpx;font-weight: 900;">
					选择商品生产日期，查看该件商品投料全过程溯源
				</view>
				 <!-- <view style="width: 100%;text-align: center;padding: 10px;font-size: 30rpx;font-weight: 900;">可查看该件商品投料溯源</view> -->
				<!-- <view style="width: 100%;text-align: center;padding: 10px;font-size: 30rpx;font-weight: 900;">
					<span style="font-size: 50rpx
					;">👇</span>
				</view> -->
				<view @click="riqishow = true"  @change="changedate" style="margin: 6rpx;background: #2573484d;width: 200rpx;text-align: center;padding: 10rpx;border-radius: 20rpx;">
					{{dy}} ▼
				</view>
				<view style="display: flex;justify-content: center;">
					<view :class="(index == riindex) ? 'dd ac' : 'dd'" v-for="(item, index) in datearr" @click="xx(index)">
						{{item.txt}}<br>
						{{item.day}}
					</view>
				</view>
				<view style="margin-top: 20rpx;">
					<u-subsection :list="list" active-color="#fff" buttonColor="#367bf7" mode="button"  :current="curNow" @change="sectionChange"></u-subsection>
				</view>
				
				<view>
					<view class="item"  v-for="(item, index) in videoList">
						<view class="head" style="display: flex;justify-content: space-between;align-items: center;">
							<view>
								<view class="" style="padding: 3px 5px;background: #2279fb; font-size: 16rpx;border-radius: 4rpx;">
									{{item.tag}}
								</view>
							</view>
							<view>
								{{item.title}}
							</view>
							<view style="font-size: 20rpx;color: #dad9d9;">
								播放时长:{{item.shichang}}
							</view>
						</view>
						<view style="display: flex;justify-content: space-between;align-items: center;  padding: 12rpx 0px;">
							<view style="font-size: 50rpx;">
								{{item.s_time}} - {{item.e_time}}
							</view>
							<view>
								<view  @click="openVideo(index)" style="padding: 4rpx 40rpx; background: #f9511f;border-radius: 100rpx;">点击回放</view>
							</view>
						</view>
						
						<view style="font-size: 24rpx;">
							亮点:{{item.liangdian}}
						</view>
					</view>
				</view>
				
			</view>
		</view>
		<view class="bg-white u-m-t-30">
			<view class="u-p-30 text-weight">详情</view>
			<view class="u-p-l-30 u-p-r-30 u-p-b-30 u-skeleton-rect">
				<u-parse
					:html="info.content"
					:tag-style="vuex_parse_style"
					:domain="vuex_config.config ? vuex_config.config.upload.cdnurl : ''"
					@linkpress="diylinkpress"
				></u-parse>
			</view>
		</view>
<!-- 
		<view class="bg-white u-m-t-30">
			<view class="u-p-30 text-weight u-flex u-row-between">
				<text>评价</text>
				<text class="u-tips-color u-font-24 text-normal">好评度:{{ info.favor_rate }}%</text>
			</view>
			<view class="u-flex u-row-center fa-empty u-p-b-30" v-if="noComment(info.comment)">
				<image src="../../static/image/comment.png" mode=""></image>
				<view class="u-tips-color">暂无更多评论~</view>
			</view>
			<view class="comment u-border-bottom" v-for="(res, index) in info.comment" :key="res.id">
				<view class="left"><image :src="res.user && res.user.avatar" mode="aspectFill"></image></view>
				<view class="right">
					<view class="top" :style="[{ color: theme.bgColor }]">
						<view class="name">{{ res.user && res.user.nickname }}</view>
						<view class="like">
							<view class="num"><u-rate :count="5" :disabled="true" v-model="res.star"></u-rate></view>
						</view>
					</view>
					<view class="content">{{ res.content }}</view>
					<view class="images">
						<image class="thumb" @click="preview(res.images, key)" v-for="(img, key) in res.images" :key="key" :src="img" mode="aspectFill"></image>
					</view>
					<view class="reply-box">
						<view class="item" v-for="(item, index) in res.reply" :key="index">
							<view class="">{{ item.manage && item.manage.nickname }}</view>
							<view class="u-m-t-5">{{ item.content }}</view>
						</view>
					</view>
					<view class="bottom u-flex u-row-between">
						<text>{{ res.createtime | timeFrom('yyyy-mm-dd') }}</text>
						<view class="" :style="[{ color: theme.bgColor }]" @click="(showReplay = true), (replyId = res.id)">
							<u-icon name="chat" :color="theme.bgColor"></u-icon>							
							<text class="u-m-l-10">回复</text>
						</view>
					</view>
				</view>
			</view>
			<view class="u-text-center u-p-30 u-tips-color" v-if="!noComment(info.comment)" @click="goPage('/pages/remark/lists?goods_id=' + id)">
				<text class="u-m-r-10">更多评论</text>
				<u-icon name="arrow-right-double"></u-icon>
			</view>
		</view> -->

		<view class="recommends bg-white u-m-t-30">
			<view class="u-font-30 title">
				<text class="stroke"></text>
				热门商品
			</view>
			<view class="goods-list">
				<view class="item" v-for="(item, index) in recommends" :key="index" @click="goPage('/pages/goods/detail?id=' + item.id)">
					<view class="images"><image :src="item.image" mode="aspectFill"></image></view>
					<view class="u-p-15 name" style="font-size: 20rpx;">
						<text class="u-line-2">{{ item.title }}</text>
					</view>
					<!-- <view class="foot u-flex u-row-between u-tips-color">
						<view class="">
							<text class="u-m-r-10">销量</text>
							<text>{{ item.sales }}</text>
						</view>
						<view class="">
							<text class="u-m-r-10">浏览</text>
							<text>{{ item.views || 0 }}</text>
						</view>
					</view> -->
					<view class="u-flex u-row-between u-p-15">
						<view class="price">
							<text>￥{{ item.price }}</text>
						</view>
						<text class="market_price u-tips-color" v-if=" item.marketprice  > 0">￥{{ item.marketprice }}</text>
					</view>
				</view>
				
				<view class="u-flex u-row-center fa-empty u-p-b-60" v-if="!recommends.length">
					<image src="../../static/image/data.png" mode=""></image>
					<view class="u-tips-color">暂无更多的推荐商品~</view>
				</view>
			</view>
		</view>

		<u-gap height="150" bg-color="#f4f6f9"></u-gap>
		<!-- 底部导航条 -->
		<view class="footer-bar u-flex u-row-between u-border-top">
			<view class="u-flex-1 u-text-center" >
				<button class="share-btn u-flex u-row-between" style="display: flex; justify-content: center;" open-type="contact">
					<view class="fa-cell">
						<u-icon size="35"  name="server-man"></u-icon>
					</view>
				</button>
				<view>客服</view>
			</view>
			
			<view class="u-flex-1 u-text-center" style="position: relative" @click="goPage('/pages/cart/cart')">
				<fa-u-badge v-if="cart_nums" type="error" :offset="[-20, -60]" :count="cart_nums"></fa-u-badge>
				<view class=""><u-icon name="shopping-cart" size="35"></u-icon></view>
				<view class="">购物车</view>
			</view>
			<view class="u-flex u-row-between u-flex-1">
				<view class="u-m-l-15">
					<u-button
						size="medium"
						type="primary"
						hover-class="none"
						:custom-style="{ width: '25vw', backgroundColor: '#ffba0d', color: theme.color }"
						shape="circle"
						@click="(sceneval = 1), (show = true)"
					>
						加入购物车
					</u-button>
				</view>
				<view class="u-m-l-20">
					<u-button
						size="medium"
						type="primary"
						hover-class="none"
						:custom-style="{ width: '25vw', backgroundColor: theme.bgColor, color: theme.color }"
						shape="circle"
						@click="(sceneval = 2), (show = true)"
					>
						<text>{{ parseFloat(info.price)==0?'免费获取':'立即购买'}}</text>
					</u-button>
				</view>
			</view>
		</view>
		<fa-skus v-if="info.id" v-model="show" :sceneval="sceneval" :goodsInfo="info" @success="getCartNums"></fa-skus>
		<u-popup v-model="guarantee_show" mode="bottom" border-radius="30">
			<view class="guarantee-content">
				<view class="u-text-center u-p-10">服务保障</view>
				<scroll-view scroll-y="true" :style="{ height: (info.guarantee && info.guarantee.length > 2 ? 600 : 350) + 'rpx' }">
					<view class="u-p-t-30">
						<u-cell-group>
							<u-cell-item
								icon="checkmark-circle"
								:icon-style="{ color: '#f2140c' }"
								:arrow="false"
								v-for="(item, index) in info.guarantee"
								:key="index"
								:title="item.name"
								:label="item.intro"
							></u-cell-item>
						</u-cell-group>
					</view>
				</scroll-view>
				<view class="confrim-btn u-p-t-20">
					<u-button
						size="medium"
						type="primary"
						hover-class="none"
						:custom-style="{ width: '80vw', backgroundColor: theme.bgColor, color: theme.color }"
						shape="circle"
						@click="guarantee_show = false"
					>
						确定
					</u-button>
				</view>
			</view>
		</u-popup>
		<!-- 海报分享 -->
		<view class="" v-if="info.id">
			<fa-share
				:goods-id="info.id"
				:title="info.name"
				:summary="info.intro"
				:imageUrl="info.image_text"
				v-model="showShare"
				@shares="showPoster = true"
			></fa-share>
			<fa-poster v-model="showPoster" :goods="info"></fa-poster>
		</view>
		<fa-coupon v-model="showCoupon" :couponList="info.coupon"></fa-coupon>
		<!-- <fa-replys v-model="showReplay" :pid="replyId"></fa-replys> -->
		<u-skeleton :loading="loading" :animation="true" bgColor="#FFF"></u-skeleton>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
		
		<u-calendar v-model="riqishow" @change="changedate" :arr="info.ri"></u-calendar>
		
		<!-- <u-popup v-model="videoshow" mode="center" @close="ss">
			 <video v-if="videoshow" class="videoBox" 	:src="videourl"	:autoplay="true"></video>
		</u-popup> -->
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

		// 接收日期参数（用于二维码扫码定位）
		if (e.date) {
			this.targetDate = e.date;
		}

		// 接收视频分类参数（用于二维码扫码定位）
		if (e.type !== undefined) {
			this.curNow = parseInt(e.type);
			this.targetType = parseInt(e.type);
		}

		let invite_id = e.invite_id || '';
		if (e.scene) {
			const scene = decodeURIComponent(e.scene);
			let goods_id = this.$util.getQueryString('goods_id', scene);
			if (goods_id) {
				this.id = goods_id;
			}
			invite_id = this.$util.getQueryString('invite_id', scene) || invite_id;
		}
		if (invite_id) {
			this.$u.vuex('vuex_invite_id', invite_id);
		}
		this.getGoodsInfo();
		this.getDateList('');
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
			list: ['生产全程回放', '精彩瞬间', '多视角回放'],
			riqishow:false,
			datearr:[],
			dy:"2024-10",
			riindex:3,
			curNow:0,
			videoList:[],
			videoshow:false,
			videourl:'',
			targetDate: null,  // 目标日期（从URL参数获取）
			targetType: null   // 目标视频分类（从URL参数获取）
		};
	},
	methods: {
		ss(){
			console.log(222);
			this.videoshow = false;
		},
		openVideo(i){
			uni.navigateTo({
				url:"/pages/video/video?url="+this.videoList[i].video_url
			})
			// 22
			// this.videourl = this.videoList[i].video_url;
			// this.videoshow = true;

		},
		goToTraceability() {
			if (this.info.traceability_url) {
				uni.navigateTo({
					url: this.info.traceability_url
				});
			}
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
		  	console.log(e)
			this.getDateList(e.result)
			this.dy = e.year+"-"+e.month
			this.getVideoList()
		},
		getDateList(date){
			// getdateList
			// var thas - t
			this.$api.getdateList({ date:date }).then(res => {
				this.datearr = res.data;

				// 如果有目标日期，自动选择对应的日期
				if (this.targetDate && this.datearr.length > 0) {
					const targetIndex = this.datearr.findIndex(item => item.riqi === this.targetDate);
					if (targetIndex !== -1) {
						this.riindex = targetIndex;
						// 更新年月显示
						const dateObj = new Date(this.targetDate);
						this.dy = dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1);
					}
					this.targetDate = null; // 清空，避免重复触发
				}

				this.getVideoList()
			});
		},
		getVideoList(){
			// getdateList
			this.$api.getVideoList({ date:this.datearr[this.riindex].riqi,type:this.curNow,goods_id:this.id}).then(res => {
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
						path: '/pages/goods/detail?id=' + this.id + '&invite_id=' + (this.vuex_user.id || '')
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
