<template>
	<view class="ingredient-items">
		<view class="items-container">
			<view
				v-for="(item, index) in items"
				:key="index"
				class="item-card"
				@click="goToVideos(item)"
			>
				<image :src="item.image" class="card-image" mode="aspectFill"></image>
				<view class="card-content">
					<text class="card-title">{{ item.title }}</text>
					<text class="card-desc" v-if="item.description">{{ item.description }}</text>
					<view class="card-footer">
						<text class="view-text">查看视频</text>
						<u-icon name="arrow-right" color="#374486" size="28"></u-icon>
					</view>
				</view>
			</view>
		</view>

		<view v-if="items.length === 0" class="empty-state">
			<text class="empty-text">暂无配料项目</text>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			ingredientreplay_id: 0,
			title: '',
			items: []
		};
	},
	onLoad(options) {
		this.ingredientreplay_id = options.id || 0;
		this.title = options.title || '配料项目';
		uni.setNavigationBarTitle({ title: this.title });
		this.loadItems();
	},
	methods: {
		async loadItems() {
			try {
				uni.showLoading({ title: '加载中...' });
				const res = await this.$api.getIngredientReplayItem({
					ingredientreplay_id: this.ingredientreplay_id
				});
				if (res.code === 1) {
					this.items = res.data;
				}
			} catch (e) {
				console.error(e);
				this.$u.toast('加载失败');
			} finally {
				uni.hideLoading();
			}
		},
		goToVideos(item) {
			uni.navigateTo({
				url: `/pages/ingredientreplay/videos?id=${item.id}&title=${item.title}`
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.ingredient-items {
	min-height: 100vh;
	background-color: #f5f5f5;
	padding: 20rpx;
}

.items-container {
	.item-card {
		background-color: #fff;
		border-radius: 16rpx;
		margin-bottom: 24rpx;
		overflow: hidden;
		box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.08);

		.card-image {
			width: 100%;
			height: 360rpx;
		}

		.card-content {
			padding: 24rpx;

			.card-title {
				font-size: 32rpx;
				font-weight: 600;
				color: #333;
				margin-bottom: 12rpx;
				display: block;
			}

			.card-desc {
				font-size: 26rpx;
				color: #666;
				line-height: 1.6;
				margin-bottom: 20rpx;
				display: block;
			}

			.card-footer {
				display: flex;
				align-items: center;
				justify-content: flex-end;

				.view-text {
					font-size: 28rpx;
					color: #374486;
					margin-right: 8rpx;
				}
			}
		}
	}
}

.empty-state {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 60vh;

	.empty-text {
		font-size: 28rpx;
		color: #999;
	}
}
</style>
