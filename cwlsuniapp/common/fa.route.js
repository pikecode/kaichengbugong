const install = function(vm) {
	uni.$u.routeIntercept = function(route, resolve) {
		// 路径兼容：自动将旧路径（/pages/）转换为新的分包路径
		if (route.url) {
			const pathMappings = {
				// packageA: 商品相关
				'/pages/goods/': '/packageA/goods/',
				'/pages/category/': '/packageA/category/',
				'/pages/search/': '/packageA/search/',

				// packageB: 订单相关
				'/pages/order/list': '/packageB/order/list',
				'/pages/order/detail': '/packageB/order/detail',
				'/pages/order/evaluate': '/packageB/order/evaluate',

				// packageC: 溯源和配料回放
				'/pages/index/sycp': '/packageC/index/sycp',
				'/pages/index/plhf': '/packageC/index/plhf',
				'/pages/index/sycpvideo': '/packageC/index/sycpvideo',
				'/pages/ingredientreplay/': '/packageC/ingredientreplay/',

				// packageD: 其他页面
				'/pages/address/': '/packageD/address/',
				'/pages/article/': '/packageD/article/',
				'/pages/coupon/': '/packageD/coupon/',
				'/pages/distribution/': '/packageD/distribution/',
				'/pages/feedback/': '/packageD/feedback/',
				'/pages/footprint/': '/packageD/footprint/',
				'/pages/integral/': '/packageD/integral/',
				'/pages/invoice/': '/packageD/invoice/',
				'/pages/level/': '/packageD/level/',
				'/pages/login/': '/packageD/login/',
				'/pages/message/': '/packageD/message/',
				'/pages/notice/': '/packageD/notice/',
				'/pages/order/aftersale': '/packageD/order/aftersale',
				'/pages/order/apply': '/packageD/order/apply',
				'/pages/order/logistics': '/packageD/order/logistics',
				'/pages/order/payment': '/packageD/order/payment',
				'/pages/order/refund': '/packageD/order/refund',
				'/pages/payment/': '/packageD/payment/',
				'/pages/recharge/': '/packageD/recharge/',
				'/pages/refundreason/': '/packageD/refundreason/',
				'/pages/returnreason/': '/packageD/returnreason/',
				'/pages/serve/': '/packageD/serve/',
				'/pages/wallet/': '/packageD/wallet/',
				'/pages/webview/': '/packageD/webview/'
			};

			// 遍历映射表，进行路径转换
			for (let oldPath in pathMappings) {
				if (route.url.startsWith(oldPath)) {
					route.url = route.url.replace(oldPath, pathMappings[oldPath]);
					console.log('[路径兼容] 旧路径已转换:', oldPath, '->', pathMappings[oldPath]);
					break;
				}
			}
		}

		resolve(true);
	}
}

export default {
	install
}
