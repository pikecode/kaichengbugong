<template>
	<view class="ingredient-replay-list">
		<view class="list-container">
			<view
				v-for="(item, index) in list"
				:key="index"
				class="replay-item"
				@click="goToItems(item)"
			>
				<image :src="item.image" class="item-image" mode="aspectFill"></image>
				<view class="item-info">
					<text class="item-title">{{ item.title }}</text>
					<text class="item-desc" v-if="item.description">{{ item.description }}</text>
				</view>
				<u-icon name="arrow-right" color="#999" size="32"></u-icon>
			</view>
		</view>

		<view v-if="list.length === 0" class="empty-state">
			<text class="empty-text">暂无配料回放数据</text>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			list: []
		};
	},
	onLoad() {
		this.loadList();
	},
	methods: {
		async loadList() {
			try {
				uni.showLoading({ title: '加载中...' });
				const res = await this.$api.getIngredientReplayInfo({ id: 1 });
				if (res.code === 1) {
					// 这里简化处理，实际应该获取所有分类列表
					this.list = [res.data];
				}
			} catch (e) {
				console.error(e);
				this.$u.toast('加载失败');
			} finally {
				uni.hideLoading();
			}
		},
		goToItems(item) {
			uni.navigateTo({
				url: `/pages/ingredientreplay/items?id=${item.id}&title=${item.title}`
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.ingredient-replay-list {
	min-height: 100vh;
	background-color: #f5f5f5;
	padding: 20rpx;
}

.list-container {
	.replay-item {
		display: flex;
		align-items: center;
		background-color: #fff;
		border-radius: 16rpx;
		padding: 24rpx;
		margin-bottom: 20rpx;
		box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.08);

		.item-image {
			width: 120rpx;
			height: 120rpx;
			border-radius: 12rpx;
			margin-right: 24rpx;
			flex-shrink: 0;
		}

		.item-info {
			flex: 1;
			display: flex;
			flex-direction: column;

			.item-title {
				font-size: 32rpx;
				font-weight: 600;
				color: #333;
				margin-bottom: 12rpx;
			}

			.item-desc {
				font-size: 26rpx;
				color: #999;
				line-height: 1.5;
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
