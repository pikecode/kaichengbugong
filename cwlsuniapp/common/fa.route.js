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

				// packageC: 溯源和配料回放
				'/pages/index/sycp': '/packageC/index/sycp',
				'/pages/index/plhf': '/packageC/index/plhf',
				'/pages/index/sycpvideo': '/packageC/index/sycpvideo',
				'/pages/ingredientreplay/': '/packageC/ingredientreplay/',

				// packageD: 个人中心相关
				'/pages/my/profile': '/packageD/my/profile',
				'/pages/my/collect': '/packageD/my/collect',

				// packageD: 登录相关（分包部分）
				'/pages/login/auth': '/packageD/login/auth',
				'/pages/login/openid': '/packageD/login/openid',
				'/pages/login/register': '/packageD/login/register',
				'/pages/login/forgetpwd': '/packageD/login/forgetpwd',

				// packageD: 订单售后相关
				'/pages/order/aftersale': '/packageD/order/aftersale',
				'/pages/order/apply': '/packageD/order/apply',
				'/pages/order/logistics': '/packageD/order/logistics',
				'/pages/order/payment': '/packageD/order/payment',

				// packageD: 地址管理
				'/pages/address/': '/packageD/address/',

				// packageD: 优惠券
				'/pages/coupon/': '/packageD/coupon/',

				// packageD: 评论相关
				'/pages/remark/': '/packageD/remark/',

				// packageD: 积分相关
				'/pages/score/': '/packageD/score/',

				// packageD: 签到相关
				'/pages/signin/': '/packageD/signin/',

				// packageD: 其他
				'/pages/video/': '/packageD/video/',
				'/pages/page/': '/packageD/page/',
				'/pages/help/': '/packageD/help/',
				'/pages/webview/': '/packageD/webview/'
			};

			// 遍历映射表，进行路径转换
			for (let oldPath in pathMappings) {
				if (route.url.startsWith(oldPath)) {
					route.url = route.url.replace(oldPath, pathMappings[oldPath]);
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
