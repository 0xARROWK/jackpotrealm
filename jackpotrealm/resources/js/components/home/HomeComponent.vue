<template>

	<div>

		<menu-top v-if="!this.showPricing"></menu-top>
        <menu-pricing-top v-else @togglePricing="this.togglePricing"></menu-pricing-top>

        <div v-if="!this.loading && this.loadSuccess">

            <div v-if="!this.showPricing" class="md:grid md:grid-cols-12 md:gap-0 mt-menu">
                <div class="bg-th-menu md:col-span-7 lg:col-span-8 xl:col-span-9 text-white overflow-auto no-scrollbar h-th-mobile-stream-height md:h-th-stream-height">
                    <stream-view></stream-view>
                    <stream-details @togglePricing="togglePricing"></stream-details>
                </div>

                <div class="md:col-span-5 lg:col-span-4 xl:col-span-3 text-white">
                    <stream-chat></stream-chat>
                </div>
            </div>

            <div v-else class="flex items-center justify-center mt-menu lg:h-th-stream-height w-full">
                <div class="inline-block w-full sm:w-1/2 lg:w-auto">
                    <pricing></pricing>
                </div>
            </div>

        </div>
        <div v-else-if="this.loading && !loadSuccess" class="flex items-center justify-center mt-menu h-th-stream-height">
            <div class="animate-spin loader mt-1 mr-2"></div>
        </div>
        <div v-else-if="!this.loading && !this.loadSuccess" class="flex items-center justify-center mt-menu h-th-stream-height text-white">
            {{ __('We are unable to retrieve data of the stream', 1, []) }}
        </div>

    </div>

    <!--<user-notification-banner v-for="n in notifications"
                                      :key="n.key"
                                      :title="n.title"
                                      :message="n.message"
                                      :action="n.action"
                                      :action-message="n.actionMessage"
                                      :action-link="n.actionLink"
                                      :closable="n.closable">
            </user-notification-banner>-->

</template>

<script>

    import MenuTop from '../menus/HomeTopMenuComponent';
    import MenuPricingTop from '../menus/PricingTopMenuComponent';
    import StreamView from "./StreamViewComponent";
    import StreamChat from "./Chat/StreamChatComponent";
    import StreamDetails from "./StreamDetailsComponent";
    import StreamPanels from "./StreamPanelsComponent";
    //import UserNotificationBanner from "../general/notifications/UserNotificationBannerComponent";

    import {mapActions} from "vuex";
    import GlobalStore from "../store/GlobalStore";
    import Pricing from "./PricingComponent";

    export default {

    	name: 'Home',

        components: {
            Pricing,
            MenuTop,
            MenuPricingTop,
            StreamView,
            StreamChat,
            StreamDetails,
            StreamPanels,
            //UserNotificationBanner
        },

        store: GlobalStore,

        data: function() {

    	    return {
    	        loading: true,
                loadSuccess: false,
                showPricing: false
            }

        },

        created: function () {
    	    if (this.getLSI('last_path') === '/app/login' && this.getLSI('api_token')) {
    	        this.$router.go(0)
            } else if (this.getLSI('last_path').startsWith('/settings') && !this.getLSI('api_token')) {
                this.$router.go(0)
            }

    	    axios
                .get(this.$apiURL + '/channel/infos')
                .then(response => {
                    if (response.data && response.data.success) {
                        this.loading = false
                        this.loadSuccess = true
                        let {channelName, streamTitle, streamDescription, streamStatus} = response.data.success
                        this.loadChannelInfos({channelName, streamTitle, streamDescription, streamStatus})
                    } else {
                        this.loading = false
                    }
                })
                .catch(error => {
                    this.loading = false
                })
        },

        methods: {

    	    ...mapActions(['loadChannelInfos']),

            togglePricing: function(value) {
                this.showPricing = !this.showPricing
            }

        }

    }

</script>
