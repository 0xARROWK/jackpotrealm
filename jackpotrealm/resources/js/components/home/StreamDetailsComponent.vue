<template>

    <div class="flex items-center leading-none p-2 sm:p4 ml-2 mt-2">
        <img :src="'storage/users/' + this.currentChannelInfos.channelName + '/profile_picture.jpg'" alt="Avatar" class="w-14 h-14 sm:w-20 sm:h-20 rounded-full mr-4 border-2 border-th-color">
        <div class="text-sm w-full ml-2">
            <div class="text-xl lg:text-2xl font-bold text-th-color">{{ this.currentChannelInfos.streamTitle }}</div>
            <div class="text-xs lg:text-sm font-bold text-th-color-2">{{ this.currentChannelInfos.channelName }}</div>
        </div>
        <div class="mr-3 text-md flex items-center text-th-color-2">
            <span class="mr-3">{{ this.viewerCount }}</span>
            <font-awesome-icon icon="eye"></font-awesome-icon>
            <button-loader-component @submitted="togglePricing" text="Subscribe" btn-class="ml-5" :take-full-width="false" :can-load="false" :prevent-submit="false"></button-loader-component>
        </div>
    </div>

</template>

<script>

    import {mapGetters} from "vuex";
    import GlobalStore from "../store/GlobalStore";

    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
    import Echo from "laravel-echo";
    import ButtonLoaderComponent from "../general/form/ButtonLoaderComponent";

    export default {

        name: "StreamDetails",

        store: GlobalStore,

        components: {
            ButtonLoaderComponent,
            FontAwesomeIcon
        },

        data: function () {

            return {
                echo: null,
                viewerCount: 0,
                viewers: [],
                viewer: this.user ? this.user.username : 'guest' + Date.now()
            }

        },

        computed: {

            ...mapGetters(['currentChannelInfos', 'user'])

        },

        mounted: function () {

            this.echo = new Echo({
                broadcaster: 'pusher',
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                encrypted: false,
                disableStats: true,
                wsHost: process.env.MIX_WEBSOCKET_HOST,
                wsPort: process.env.MIX_WEBSOCKET_PORT,
                enabledTransports: ['ws']
            })

            this.echo.join('viewerCount')
                .here(users => {
                    this.viewerCount = users.length
                    this.viewers = users
                })
                .joining(user => this.viewerCount++)
                .leaving(user => this.viewerCount--);

        },

        methods: {

            togglePricing: function () {
                this.$emit('togglePricing', true)
            }

        }

    }

</script>
