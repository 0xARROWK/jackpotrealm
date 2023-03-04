<template>

    <div v-if="this.currentChannelInfos.streamStatus === 1">
        <div id="player"></div>
    </div>
    <div v-else class="w-full h-th-mobile-stream-height md:h-th-stream-height flex items-center justify-center" style="background-color: #000;">
        <p class="text-lg font-bold text-th-color-2">{{ this.currentChannelInfos.channelName }} is currently offline.</p>
    </div>

</template>

<script>

    import Echo from "laravel-echo";
    import {mapGetters, mapActions} from "vuex";
    import GlobalStore from "../store/GlobalStore";

    export default {

        name: "StreamView",

        store: GlobalStore,

        computed: {
            ...mapGetters(['currentChannelInfos'])
        },

        data: function () {
            return {
                player: null
            }
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
            });

            this.echo.channel('stream-status')
                .listen('NewStreamStatus', (e) => {
                    if (this.player && e.status === 0) {
                        let channelName = this.currentChannelInfos.channelName,
                            streamDescription = this.currentChannelInfos.streamDescription,
                            streamTitle = this.currentChannelInfos.streamTitle,
                            streamStatus = e.status
                        this.loadChannelInfos({channelName, streamTitle, streamDescription, streamStatus})
                        this.player.remove()
                    }
                });

            if (this.currentChannelInfos.streamStatus === 1) {
                let webrtcSources = OvenPlayer.generateWebrtcUrls([
                    {
                        host: 'ws://jackpotrealm.com:3333',
                        application: 'app',
                        stream: this.currentChannelInfos.channelName,
                        label: "Low latency"
                    }
                ]);

                this.player = OvenPlayer.create("player", {
                    sources: webrtcSources,
                    autoStart: true,
                    playBackRates: false
                })
            }
        },

        methods: {
            ...mapActions(['loadChannelInfos'])
        },

        beforeDestroy() {
            if (this.player) this.player.remove()
        }

    }

</script>
