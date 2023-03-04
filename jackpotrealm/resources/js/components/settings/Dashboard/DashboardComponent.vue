<template>

    <div>

        <menu-top></menu-top>

        <!--<div v-if="this.isStreamer" class="w-100 mx-auto px-2 sm:px-6 lg:px-8 grid grid-cols-12 gap-8 mt-menu">

            <div class="bg-th-card col-span-12 md:col-span-6 mt-10 p-5 text-th-text-color">
                <streaming-key></streaming-key>
            </div>

        </div>-->

        <div v-if="this.isStreamer" class="container my-12 mx-auto px-4 md:px-12">
            <div class="flex flex-wrap -mx-1 lg:-mx-4">

                <div class="my-1 px-1 w-full lg:my-4 lg:px-4 lg:w-1/2">
                    <streaming-key></streaming-key>
                    <stream-information></stream-information>
                    <bot-commands></bot-commands>
                </div>

                <div class="my-1 px-1 w-full lg:my-4 lg:px-4 lg:w-1/2">
                    <multistream></multistream>
                    <custom-emojis-upload></custom-emojis-upload>
                    <custom-emojis-list></custom-emojis-list>
                </div>

            </div>
        </div>

    </div>

</template>

<script>

    import MenuTop from "../../menus/SettingsTopMenuComponent";
    import StreamingKey from "./StreamingKeyComponent";
    import CustomEmojisUpload from "./CustomEmojisUploadComponent";
    import CustomEmojisList from "./CustomEmojisListComponent";
    import StreamInformation from "./StreamInformationComponent";

    import { mapGetters } from 'vuex';
    import GlobalStore from "../../store/GlobalStore";
    import Multistream from "./MultistreamComponent";
    import BotCommands from "./BotCommandsComponent";

    export default {

        name: "Dashboard",

        store: GlobalStore,

        components: {
            BotCommands,
            Multistream,
            MenuTop,
            StreamingKey,
            CustomEmojisUpload,
            CustomEmojisList,
            StreamInformation
        },

        data: function () {
            return {
                isStreamer: false
            }
        },

        computed: {

            ...mapGetters(['user'])

        },

        created: function () {
            axios
                .post(this.$apiURL + '/verification/access', {
                    role: this.user.role,
                    verified: this.user.username
                }, {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    },
                    responseType: 'json'
                })
                .then(response => {
                    if (!response.data || !response.data.success || response.data.success !== true || !this.user.role.includes('streamer')) {
                        this.$router.push('/settings')
                    } else {
                        this.isStreamer = true
                    }
                })
                .catch(error => {
                    console.log(error)
                })
        }

    }

</script>
