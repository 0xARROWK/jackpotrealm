<template>

    <div>

        <menu-top></menu-top>

        <div v-if="this.isStreamer" class="container my-12 mx-auto px-4 md:px-12">
            <div class="flex flex-wrap -mx-1 lg:-mx-4">

                <div class="my-1 px-1 w-full lg:my-4 lg:px-4 lg:w-1/2">
                    <banned-words></banned-words>
                    <banned-users></banned-users>
                </div>

                <div class="my-1 px-1 w-full lg:my-4 lg:px-4 lg:w-1/2">
                    <banned-expressions></banned-expressions>
                    <moderators></moderators>
                </div>

            </div>
        </div>

    </div>

</template>

<script>

    import MenuTop from "../../menus/SettingsTopMenuComponent";
    import BannedWords from "./BannedWordsComponent";
    import BannedExpressions from "./BannedExpressionsComponent";
    import BannedUsers from "./BannedUsersComponent";

    import { mapGetters } from 'vuex';
    import GlobalStore from "../../store/GlobalStore";
    import Moderators from "./ModeratorsComponent";

    export default {

        name: "Moderation",

        store: GlobalStore,

        components: {
            Moderators,
            MenuTop,
            BannedWords,
            BannedExpressions,
            BannedUsers,
        },

        data: function () {
            return {
                isStreamer: false,
                isModerator: false
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
                    if (!response.data || !response.data.success || response.data.success !== true || (!this.user.role.includes('streamer') && !this.user.role.includes('moderator'))) {
                        this.$router.push('/settings')
                    } else {
                        this.isStreamer = this.user.role.includes('streamer')
                        this.isModerator = this.user.role.includes('moderator')
                    }
                })
                .catch(error => {
                    console.log(error)
                })
        }

    }

</script>
