<template>
	<view>
		<view class="status_bar"></view>
		<view class="mx-30 pt-30">
			<view class="d-flex-between-center">
				<u-icon name="arrow-leftward" size="38" color="#999" @click="back"></u-icon>
				<u-subsection :list="nav" style="width: 630rpx;" :afterWidth="60" v-if="showNav"  @change="(index)=>currentNav=index" inactive-color="#fff" :current="currentNav"></u-subsection>
			</view>
			
			<view class="d-flex-between-center mt-30">
				<view class="d-flex align-items-center font-weight-bold opacity-75" @click="showSelectCoin=true">
					<!-- <u-icon name="list-dot" size="42" class="position-relative" style="top: 2rpx;"></u-icon> -->
					<image src="/static/image/img/menu.png" style="width: 38rpx; height: 38rpx;"></image>
					<text class="mx-10 currency_name">{{currency_name + '/' + legal_name}}</text>
					<text v-if="symbolQuotation.change" class="dot-tag"
						:class="symbolQuotation.change > 0 ? 'dot-tag-success' : 'dot-tag-error'">{{symbolQuotation.change}}%</text>
				</view>
				<view class="d-flex align-items-center">
					<!-- 合约订单 -->
					<image class="order-icon" src="/static/image/img/search1.png" @click="$utils.jump(`/pages/transaction/contract_order?currency_id=${symbolQuotation.currency_id}&legal_id=${symbolQuotation.legal_id}`,'redirectTo')" v-if="currentNav == 0 && symbolQuotation.id"></image>
					<!-- <u-icon name="order"  size="42"  :color="$baseColor" class="mr-20" @click="$utils.jump(`/pages/transaction/contract_order?currency_id=${symbolQuotation.currency_id}&legal_id=${symbolQuotation.legal_id}`,'redirectTo')" v-if="currentNav == 0 && symbolQuotation.id"></u-icon> -->
					<!-- 期权订单 -->
					<image class="order-icon" src="/static/image/img/search1.png" @click="$utils.jump('/pages/transaction/seconds_order?match_id='+symbolQuotation.id,'redirectTo')" v-else-if="currentNav == 1 && symbolQuotation.id"></image>
					<!-- <u-icon name="order"  size="42"  :color="$baseColor" class="mr-20" @click="$utils.jump('/pages/transaction/seconds_order?match_id='+symbolQuotation.id,'redirectTo')" v-else-if="currentNav == 1 && symbolQuotation.id"></u-icon> -->
					<!-- 币币订单 -->
					<image class="order-icon" src="/static/image/img/search1.png" @click="$utils.jump('/pages/transaction/currency_order','redirectTo')" v-else-if="currentNav == 2 && symbolQuotation.id"></image>
					<!-- <u-icon name="order"  size="42"  :color="$baseColor" class="mr-20" @click="$utils.jump('/pages/transaction/currency_order','redirectTo')" v-else-if="currentNav == 2 && symbolQuotation.id"></u-icon> -->
					<!-- <u-icon name="star-fill" size="42" :color="$baseColor" v-if="optionalId" @click="delOptional"></u-icon> -->
					<!-- <u-icon name="star" size="42" :color="$baseColor" v-else @click="addOptional"></u-icon> -->
					<image style="width: 40rpx; height: 40rpx;" src="/static/image/img/collect-active.png" v-if="optionalId" @click="delOptional"></image>
					<image style="width: 40rpx; height: 40rpx;" src="/static/image/img/collect.png" v-else @click="addOptional"></image>
				</view>
			</view>
		</view>
		<!-- 所有行情列表 -->
		<u-popup v-model="showSelectCoin" mode="top" length="780" contentBackgroundColor="#333333"
			:mask-custom-style="{background: 'rgba(0, 0, 0, 0.7)'}" :border-radius="0">
			<view class="status_bar"></view>
			<view class="px-30 market">
				<view class="search d-flex align-items-center py-20">
					<u-icon name="search" size="38"></u-icon>
					<input type="text" class="input" :placeholder="$t('common.search')" confirm-type="search"
						@input="filterQuotation">
				</view>
				<view class="d-grid py-20" style="grid-template-columns:1.1fr 1fr 1fr;">
					<view class="d-flex align-items-center font-size-22 opacity-50" v-for="(item,index) in quotationNav"
						:class="item.align">
						<text>{{item.name}}</text>
					</view>
				</view>
				<scroll-view scroll-y="true" style="height: 600rpx;">
					<view class="market-item" v-for="item in quotation"
						@click="jump(item.currency_name,item.legal_name,item.currency_id)">
						<view class="left">
							<text class="d-block">
								<text class="font-size-28 font-weight-bold">{{item.currency_name}}</text>
								<text class="font-size-22 opacity-50">/{{item.legal_name}}</text>
							</text>
							<text class="d-block font-size-22 opacity-50">24H: {{Number(item.volume) | setPrecision(item.precision_length)}}</text>
						</view>
						<view class="text-center">
							<text class="d-block font-size-30 font-weight-bold">{{item.now_price | setPrecision(item.precision_length)}}</text>
						</view>
						<view class="">
							<view class="right" :style="{backgroundColor:$utils.getColor(item.change)}">
								{{item.change + '%'}}
							</view>
						</view>
					</view>
				</scroll-view>

			</view>
		</u-popup>
		<!-- 一些数据 -->
		<view class="d-flex-between-center p-30" style="height: 152rpx;">
			<block v-if="symbolQuotation.close">
				<view class="">
					<text class="d-block current-money"
						:style="{color:$utils.getColor(symbolQuotation.change)}">{{symbolQuotation.close | setPrecision(symbolQuotation.precision_length)}}</text>
					<view class="d-flex align-items-baseline mt-10">
						<text class="current-fiat">{{(symbolQuotation.now_price * $store.state.fiat.rate).toFixed(2)}}<br/>{{$store.state.fiat.currency_code}}</text>
						<text class="current-change ml-12"
							:style="{color:$utils.getColor(symbolQuotation.change)}">{{symbolQuotation.change}}%</text>
					</view>
				</view>
				<view class="">
					<view class="d-flex">
						<text class="d-block high">{{i18n.dayHigh}}</text>
						<text class="d-block high-text ml-20">{{symbolQuotation.high}}</text>
					</view>
					<view class="d-flex mt-8">
						<text class="d-block high">{{i18n.dayLow}}</text>
						<text class="d-block high-text ml-20">{{symbolQuotation.low}}</text>
					</view>
				</view>
			</block>
			<data-loading v-else></data-loading>
		</view>
		<!-- k线图 -->
		<klinechart :currency_name="currency_name" :legal_name="legal_name" @getSokcetData="getSocketData" :precision="symbolQuotation.precision_length">
		</klinechart>
		
		<!-- 委托挂单 -->
		<!-- <u-gap height="20" bg-color="#333333"></u-gap> -->
		<view class="px-30">
			<text class="d-block font-size-32 py-20 opacity-75">{{i18n.entrustPendingOrder}}</text>
			<view class="d-flex overflow-hidden">
				<view class="w-50">
					<text class="d-block font-size-20 opacity-30 pb-20">{{i18n.buyUp}}</text>
					<view class="d-flex-between-center px-10 py-16 position-relative font-size-22" v-for="item in buyList">
						<text class="opacity-75">{{item[1]}}</text>
						<text  :style="{color:$downColor}">{{item[0]}}</text>
						<view class="position-absolute opacity-20" style="top: 0;bottom: 0;right: 0;" :style="{background:$downColor,width:(item[1]/buyListCount*100*10+'%')}"></view>
					</view>
				</view>
				<view class="w-50">
					<text class="d-block font-size-20 opacity-30 pb-20">{{i18n.buyDown}}</text>
					<view class="d-flex-between-center px-10 py-16 position-relative font-size-22" v-for="item in sellList">
						<text  :style="{color:$upColor}">{{item[0]}}</text>
						<text class="opacity-75" >{{item[1]}}</text>
						<view class="position-absolute opacity-20" style="top: 0;bottom: 0;left: 0;" :style="{background:$upColor,width:(item[1]/sellListCount*100*10+'%')}"></view>
					</view>
				</view>
			</view>
		</view>
		
		<!-- 合约 -->
		<block v-if="currentNav == 0">
			<view class="footer font-size-28" v-if="symbolQuotation.close">
				<view class="float-btn"  @click="$utils.jump(`/pages/transaction/contract?currency_name=${currency_name}&legal_name=${legal_name}&currency_id=${currency_id}&buy_direction=0`,'redirectTo')">
					<text class="d-block">{{i18n.buy + '/' + i18n.long}}</text>
				</view>
				<view class="float-btn"  @click="$utils.jump(`/pages/transaction/contract?currency_name=${currency_name}&legal_name=${legal_name}&currency_id=${currency_id}&buy_direction=1`,'redirectTo')">
					<text class="d-block">{{i18n.sell + '/' + i18n.short}}</text>
				</view>
			</view>
		</block>
		
		<!-- 币币 -->
		<block v-if="currentNav == 2">
			<view class="footer font-size-28" v-if="symbolQuotation.close">
				<view class="float-btn" @click="$utils.jump(`/pages/transaction/currency?currency_name=${currency_name}&legal_name=${legal_name}&currency_id=${currency_id}&buy_direction=0`,'redirectTo')">
					<text class="d-block">{{i18n.buy}}</text>
				</view>
				<view class="float-btn" @click="$utils.jump(`/pages/transaction/currency?currency_name=${currency_name}&legal_name=${legal_name}&currency_id=${currency_id}&buy_direction=1`,'redirectTo')">
					<text class="d-block">{{i18n.sell}}</text>
				</view>
			</view>
		</block>
		
		<!-- 秒合约 -->
		<block v-if="currentNav == 1">
			<!-- 浮动的按钮工具 -->
			<view class="footer font-size-22" v-if="symbolQuotation.close">
				<view class="float-btn" @click="buyDirection=1,showBuyConfirm=true">
					<view>
						<text class="">{{symbolQuotation.close}}</text>
						<text class="d-block font-size-22">{{i18n.buyUp}}</text>
					</view>
				</view>
				<view class="float-btn" @click="buyDirection=2,showBuyConfirm=true">
					<view>
						<text class="">{{symbolQuotation.close}}</text>
						<text class="d-block font-size-22">{{i18n.buyDown}}</text>
					</view>
				</view>
			</view>
			
			<!-- 弹出秒合约购买 -->
			<u-popup v-model="showBuyConfirm" mode="center" length="90%" contentBackgroundColor="rgba(0, 0, 0, .4)"
				 :border-radius="20" >
				<view class="p-30">
					<text class="d-block font-size-30 font-weight-bold">{{i18n.orderConfirm}}</text>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="opacity-50">{{i18n.tradingPair}}</text>
						<text class="font-weight-bold">{{currency_name + '/' + legal_name}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="opacity-50">{{i18n.direction}}</text>
						<text class="font-weight-bold" :class="buyDirection == 1 ? 'text-success' : 'text-error'">{{buyDirection == 1 ? i18n.buyUp : i18n.buyDown}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="opacity-50">{{i18n.currentPrice}}</text>
						<text class="font-weight-bold">{{symbolQuotation.close}}</text>
					</view>
					<view class="border-bottom-white py-16">
						<text class="opacity-50">{{i18n.selectTime}}</text>
						<view class="d-flex-between-center mt-16" v-if="buyDirection == 1">
							<text v-for="item in timers" class="tag w-19 text-center font-size-30 d-block" :class="currentTimer == item ? 'tag-success py-12' : 'tag-plain-success py-10'" @click="currentTimer=item">{{item.seconds}}s-{{item.profit_ratio}}%</text>
						</view>
						<view class="d-flex-between-center mt-16" v-else-if="buyDirection == 2">
							<text v-for="item in timers" class="tag w-19 text-center font-size-30 d-block" :class="currentTimer == item ? 'tag-error py-12' : 'tag-plain-error py-10'" @click="currentTimer=item">{{item.seconds}}s-{{item.profit_ratio}}%</text>
						</view>
					</view>
					<view class="py-16">
						<text class="opacity-50">{{i18n.number}}</text>
						<input type="number" v-model="number" class="trade-input mt-10 " >
						<text class="d-block mt-8 font-size-24 opacity-75">{{i18n.balance + ':' + Number(microInsurance).toFixed(4) + 'USDT'}}</text>
					</view>
					
					<view class="d-flex-between-center mt-30">
						<button class="secondary-button font-size-28 w-48 py-0" @click="showBuyConfirm = false">{{$t("common.cancel")}}</button>
						<button class="w-48 font-size-28 py-0" @click="submit" :class="buyDirection == 1 ? 'success-button' : 'error-button'">{{$t("common.confirm")}}</button>
					</view>
			
				</view>
			</u-popup>
			
			<!-- 秒合约购买后的计时弹窗 -->
			<u-popup v-model="showCountDown" mode="center" length="90%" contentBackgroundColor="rgba(0, 0, 0, .4)"
				 :border-radius="20" :mask-close-able="false">
				<view class="p-30">
					<view class="" v-if="actualPL==null">
						<text class="count-down bg-primary">{{countDownTime}}</text>
					</view>
					<view class="" v-else>
						<text class="actualPL bg-primary" :class="actualPL>0?'green':'red'">{{actualPL>0?'+':''}}{{actualPL}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16 mt-40">
						<text class="opacity-50">{{i18n.tradingPair}}</text>
						<text class="font-weight-bold">{{currency_name + '/' + legal_name}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="opacity-50">{{i18n.direction}}</text>
						<text class="font-weight-bold" :class="buyInfo.type == 1 ? 'text-success' : 'text-error'">{{buyInfo.type == 1 ? i18n.buyUp : i18n.buyDown}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="opacity-50">{{i18n.buyPrice}}</text>
						<text class="font-weight-bold">{{Number(buyInfo.open_price).toFixed(4)}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="opacity-50">{{i18n.currentPrice}}</text>
						<text class="font-weight-bold">{{symbolQuotation.close}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="opacity-50">{{i18n.number}}</text>
						<text class="font-weight-bold">{{buyInfo.number}}USDT</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16 font-weight-bold" v-if="actualPL==null">
						<text class="opacity-50">{{i18n.expectedPL}}</text>
						<text :style="{color:$utils.getColor(expectedPL)}">{{expectedPL}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16 font-weight-bold" v-else>
						<text class="opacity-50">{{i18n.actualPL}}</text>
						<text :style="{color:$utils.getColor(actualPL)}">{{actualPL}}</text>
					</view>
					<button class="mt-30 font-size-28 py-0 primary-button" @click="continueTrade">{{i18n.continueTrade}}</button>
				</view>
			</u-popup>
			<!-- 如还有倒计时，则显示 -->
			<view class="fixed-count-down" v-if="countDownTime && !showCountDown" :style="{backgroundColor:$utils.getColor(expectedPL)}" @click="showCountDown=true">
				<text class="font-size-28">{{countDownTime}}s</text>
				<text class="font-size-36">{{expectedPL}}</text>
			</view>
		</block>
		
	</view>
</template>
<script>
	export default {
		data() {
			return {
				currency_name: '',
				legal_name: '',
				currency_id:0,
				showSelectCoin: false,
				originalQuotation: [],
				quotation: [],
				symbolQuotation: {},
				// 交易数据
				buyList:[],
				sellList:[],
				buyListCount:0,
				sellListCount:0,
				optionalId:0,
				//买张买跌,
				showBuyConfirm:false,
				buyDirection:1, // 1买涨，2：买跌
				timers:[],
				currentTimer:0, //当前选择的时间
				number:'', // 数量,
				microInsurance:0,//秒合约账户余额,
				//倒计时
				showCountDown:false,
				actualPL: null, // 實際盈虧，為null時顯示倒計時
				buyInfo:{},
				countDown:null, //倒计时的interval
				countDownTime:0, //倒计时的总时间,
				showNav:true,
				currentNav:1,
				from:"index"
			};
		},
		onLoad(options) {
			const {
				from,
				currency_name,
				legal_name,
				currency_id
			} = options
			this.from = from || ''
			this.currency_name = currency_name || "BTC"
			this.legal_name = legal_name || "USDT"
			this.currency_id = currency_id || 1
			this.getMarketDepthData()
			
			const _this = this
			uni.setNavigationBarTitle({
				title:_this.$t("home.market")
			})
		},
		onShow() {
			uni.hideTabBar()
			this.showNav = false
			setTimeout(() => {
				this.showNav = true
			}, 100)
			
			//获取秒合约的秒数
			this.getSecondsSeconds()
			
			this.getQuotation()
			//查看是否已添加自选
			this.checkOptional()
			
			//获取易购列表和余额
			this.getPayable()
			
			// uni.request({
			// 	url:'https://nftcc.hujinsuo06.vip/all-seller'
			// })
		
		},
		methods: {
			//获取秒合约的秒数
			getSecondsSeconds(){
				this.$u.api.market.getSecondsSeconds().then(res => {
					
					this.timers = res.message
					this.currentTimer = this.timers[0]
				})
			},
			// 获取所有行情
			getQuotation() {
				this.$u.api.index.getQuotation().then(res => {
					this.originalQuotation = res.message[0].quotation.map(item=>{
						item.precision_length = this.$utils.getPrecisionLength(item.now_price)
						return item
					})
					this.quotation = this.originalQuotation
					
					let has = this.originalQuotation.find(item => item.legal_name == this.legal_name && item.currency_id == this.currency_id)
					has.close = Number(has.now_price)
					has.high = Number(has.now_price)
					has.low = Number(has.now_price)
					has.precision_length = this.$utils.getPrecisionLength(has.now_price)
					this.symbolQuotation = has
				})
			},
			//获取子组件socket返回的data
			getSocketData(data) {
				if (this.currency_name == data.currency_name && this.legal_name == data.legal_name) {
					this.symbolQuotation = {
						...this.symbolQuotation,
						...data
					}
				}
				
				const has = this.originalQuotation.findIndex(item => item.currency_name == data.currency_name && item
					.legal_name == data.legal_name)
				if (has > -1) {
					let obj = {
						...this.originalQuotation[has],
						...data,
					}
					this.originalQuotation.splice(has, 1, obj)
				}
			},
			filterQuotation(e) {
				let val = e.detail.value
				if (!val) {
					this.quotation = this.originalQuotation
				} else {
					this.quotation = this.originalQuotation.filter(el => {
						val = val.toLowerCase()
						let currency_name = el.currency_name.toLowerCase()
						let legal_name = el.legal_name.toLowerCase()
						if (currency_name.indexOf(val) > -1 || legal_name.indexOf(val) > -1) {
							return el
						}
					})
				}
			},
			// 跳转
			jump(currency_name, legal_name,currency_id) {
				if (currency_name == this.currency_name && legal_name == this.legal_name && currency_id == this.currency_id) this.showSelectCoin = false
				const url = `/pages/transaction/index?currency_name=${currency_name}&legal_name=${legal_name}&currency_id=${currency_id}`
				uni.reLaunch({
					url
				})
			},
			//接受市场数据
			getMarketDepthData() {
				this.$store.state.socket.on('market_depth', data => {
					if (this.currency_name == data.currency_name && this.legal_name == data.legal_name) {
						if (typeof(data.bids) == 'string') {
							this.buyList = JSON.parse(data.bids);
							this.sellList = JSON.parse(data.asks).reverse();
						} else {
							this.buyList = data.bids;
							this.sellList = data.asks.reverse();
						}
						
						this.buyListCount = this.buyList.reduce((total,item)=>{
							return total + item[1]*1
						},0);
						
						this.sellListCount = this.sellList.reduce((total,item)=>{
							return total + item[1]*1
						},0);
					}
				});
				
			},
			//查看是否已添加自选
			async checkOptional(){
				const token = this.$store.state.token
				if(!token) return
				const ret = await this.$u.api.market.getOptionalList()
				const item = ret.message.find(item => item.currency_id == this.currency_id)
				if(item){
					this.optionalId = item.id
				}
			},
			//添加自选
			addOptional() {
				this.$u.throttle(()=>{
					const {
						currency_id,
						i18n
					} = this
					this.$u.api.market.addOptional(currency_id).then(res => {
						this.optionalId = res.message.id
						this.$utils.showToast(i18n.addOptionalSuccess)
					})
				},1200)
			},
			// 删除自选
			delOptional() {
				this.$u.throttle(()=>{
					const {
						optionalId,
						i18n
					} = this
					this.$u.api.market.delOptional(optionalId).then(res => {
						this.optionalId = 0
						this.$utils.showToast(i18n.delOptionalSuccess)
					})
				},1200)
			},
			// 获取已购买的秒合约列表和余额
			getPayable(){
				const token = this.$store.state.token
				if(!token) return
				this.$u.api.market.getPayable().then(res=>{
					const data = res.message.find(item=>item.name == this.legal_name)
					console.log(data);
					this.microInsurance = data.user_wallet.micro_with_insurance
				})
			},
			//提交购买
			submit(){
				this.actualPL = null;
				this.$u.throttle(()=>{
					const {buyDirection,symbolQuotation,currentTimer,number,i18n} = this
					// match_id,currency_id,type,seconds,number
					const {id,legal_id} = symbolQuotation
					if(!number || !this.$u.test.amount(number)){
						this.$utils.showToast(i18n.plsIptCrtNumber)
						return false
					}
					
					this.$u.api.market.buySeconds(id,legal_id,buyDirection,currentTimer.seconds,number).then(res=>{
						this.buyInfo = res.message
						this.showCountDown = true
						
						this.countDownTime = Number(this.buyInfo.seconds)
						if(this.countDown) clearInterval(this.countDown)
						this.countDown = setInterval(()=>{
							
							if(this.countDownTime >= 1){
								this.countDownTime--
							}else{
								this.countDownTime = 0;
								clearInterval(this.countDown)
								this.countDown = null
								// 獲取交易結果
								this.$u.api.market.getResult(this.buyInfo.id).then((res) => {
									let _data = res.message;
									this.actualPL = Number(_data.fact_profits)
									console.log(res);
								});
								// this.continueTrade()
							}
						},1000)
					})
				},1200)

			},
			//接收seconds信息
			getSecondsList(data){
				console.log(data);
			},
			//继续交易
			continueTrade(){
				// 关闭所有弹窗
				this.showBuyConfirm = false
				this.showCountDown = false
			},
			//返回
			back(){
				const from = this.from || 'index'
				const url = `/pages/${from}/${from}`
				uni.reLaunch({
					url
				})
				
			}
		},
		computed: {
			i18n() {
				return this.$t("transaction")
			},
			symbol() {
				return this.currency_name + '/' + this.legal_name
			},
			nav() {
				const i18n = this.$t("transaction")
				return [{
					name: i18n.futures
				}, {
					name: i18n.seconds
				}, {
					name: i18n.coins
				}]
			},
			quotationNav(){
				const i18n = this.$t("transaction")
				return [{
					name: i18n.tradingPair,
					align: ''
				},
				{
					name: i18n.lastPrice,
					align: 'justify-content-center'
				},
				{
					name: i18n.todayChange,
					align: 'justify-content-end'
				}]
			},
			//预计盈亏，
			expectedPL(){
				// 计算方法
				// open_price 购买时的价格，newprice实时价格
				
				const {symbolQuotation,buyInfo,countDownTime} = this
				const open_price = buyInfo.open_price ? Number(buyInfo.open_price) : 0
				//如果countDownTime==0，就不用计算了
				//如果还没下单，也不用计算
				if(!open_price || !countDownTime) return 0
				
				const newprice = Number(symbolQuotation.close)
				const buyDirection = Number(buyInfo.type)
				
				// open_price == newprice ，盈亏为0
				if(open_price == newprice) return 0
				
				// 如果购买方向和当前涨跌情况相同,则计算结果为 (number * profit_ratio) / 100
				const number = Number(buyInfo.number)
				const profit_ratio = Number(buyInfo.profit_ratio)
				if((buyDirection == 1 && newprice > open_price) || (buyDirection == 2 && open_price > newprice)){
					return parseFloat(((number * profit_ratio) / 100).toFixed(4))
				}
				
				// 如果购买方向和当前涨跌情况相反,则计算结果为 - number
				return parseFloat((number * -1).toFixed(4))
				
			}
		},
		onHide(){
			// if(this.countDown){
			// 	clearInterval(this.countDown)
			// }
		},
		onUnload() {
			
		}

	}
</script>

<style>
page{
    padding-bottom: 120rpx;
}
</style>
<style lang="scss" scoped>
	page,
	body,
	html {
		padding-bottom: 120rpx;
		
	}
	
	.dot-tag{
		padding: 6rpx 12rpx;
		font-size: 24rpx;
		font-family: Roboto;
		font-weight: 400;
		color: #fff;
		border-radius: 8rpx;
	} 
	.dot-tag-error{
		background: red;
	}
	.dot-tag-success{
		background: #00873c;
	}
	
	.order-icon{
		margin-right: 20rpx;
		width: 38rpx;
		height: 38rpx;
	}
		
	.currency_name{
		font-size: 38rpx;
		font-family: Roboto;
		font-weight: 500;
		color: #333;
	}
	
	.current-money{
		font-size: 44rpx;
		font-family: Roboto;
		font-weight: 500;
	}
	.current-fiat{
		font-size: 24rpx;
		font-family: Roboto;
		font-weight: 500;
		color: #999;
	}
	.current-change{
		font-size: 24rpx;
		font-family: Roboto;
		font-weight: 500;
	}
	
	.high {
	    font-size: 24rpx;
	    font-family: Roboto;
	    font-weight: 500;
	    color: #999;
	}
	.high-text {
	    font-size: 24rpx;
	    font-family: Roboto;
	    font-weight: 500;
	    color: #333;
	}
	
	
	
	.actualPL {
		width: 200rpx;
		height: 200rpx;
		display: flex;
		margin: 0 auto;
		justify-content: center;
		align-items: center;
		font-size: 66rpx;
		color: #fff;
		border-radius: 50%;
		&.green {
			background: #28ba98!important;
		}
		&.red {
			background: #f04a5a!important;
		}
	}
		
	.market {
		color: rgba(255, 255, 255, .7);

		.search {
			border-bottom: 2rpx solid rgba(255, 255, 255, .1);

			.input {
				height: 38rpx;
				flex: 1;

				.input-placeholder {
					color: #fff;
				}
			}
		}

		.market-item {
			overflow: hidden;
			padding: 20rpx 0;
			border-top: 2rpx solid rgba(255, 255, 255, .1);
			align-items: center;
			display: grid;
			grid-template-columns: 1.1fr 1fr 1fr;

			.right {
				margin-left: auto;
				width: 120rpx;
				height: 50rpx;
				line-height: 50rpx;
				text-align: center;
				border-radius: 10rpx;
				background-color: #15be97;
				color: #fff;
				font-size: 24rpx;
			}
		}
	}

	.footer {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 20;
		border-radius: 20rpx 20rpx 0 0;
		padding: 20rpx 30rpx;
		color: rgba(255, 255, 255, .5);
		display: flex;
		align-items: center;
		justify-content: space-between;
		
		.float-btn{
			width: 329rpx;
			height: 102rpx;
			background: #2e5cd1;
			box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
			border-radius: 18rpx;
			font-size: 30rpx;
			font-family: Roboto;
			font-weight: 500;
			color: #fff;
			text-align: center;
			display: flex;
			align-items: center;
			justify-content: center;
			&:nth-child(2) {
				background: red;
			}
		}
		
		// .btn {
		// 	color: rgba(255, 255, 255, .9);
		// 	text-align: center;
		// 	width: 49.8%;
		// 	background-size: 100% 100%;
		// 	height: 80rpx;
		// 	display: flex;
		// 	flex-direction: column;
		// 	justify-content: center;
		// 	align-items: center;
		// 	&.btn1 {
		// 		background-image: url('../../static/image/icon/k-icon-1.png');

		// 	}

		// 	&.btn2 {
		// 		background-image: url('../../static/image/icon/k-icon-2.png');
		// 		position: relative;
		// 		//left: -20rpx;
		// 	}
		// }
	}
	
	.trade-input{
		border: 2rpx solid #eee;
		border-radius: 10rpx;
		display: block;
		padding: 14rpx 20rpx;
		font-weight: bold;
		.input-placeholder{
			color: rgba(51, 51, 51, 0.4);
			font-weight: normal;
		}
	}
	
	.count-down{
		width: 200rpx;
		height: 200rpx;
		display: flex;
		margin: 0 auto;
		justify-content: center;
		align-items: center;
		font-size: 66rpx;
		color: #fff;
		border-radius: 50%;
	}
	
	.fixed-count-down{
		position: fixed;
		right: 30rpx;
		width: 120rpx;
		height: 120rpx;
		border-radius: 50%;
		bottom: 10vh;
		z-index: 10;
		background-color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #fff;
		flex-direction: column;
	}
</style>
