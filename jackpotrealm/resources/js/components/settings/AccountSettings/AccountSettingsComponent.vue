<template>

    <div>

        <menu-top></menu-top>

        <!--<div class="w-100 mx-auto px-2 sm:px-6 lg:px-8 grid grid-cols-8 gap-8 mt-menu">

            <div class="bg-th-card col-span-12 md:col-span-6 mt-10 p-5 text-white">
                <update-profile></update-profile>
            </div>

        </div>-->

        <div class="container my-12 mx-auto px-4 md:px-12">
            <div class="flex flex-wrap -mx-1 lg:-mx-4">

                <div class="my-1 px-1 w-full lg:my-4 lg:px-4 lg:w-1/2">
                    <update-profile></update-profile>
                </div>

                <div class="my-1 px-1 w-full lg:my-4 lg:px-4 lg:w-1/2">
                    <profile-picture></profile-picture>
                    <chat-color v-if="this.user.role.includes('sub-1') || this.user.role.includes('sub-2') || this.user.role.includes('sub-3') || this.user.role.includes('streamer')"></chat-color>
                </div>

            </div>
        </div>

    </div>

</template>

<script>

    import MenuTop from '../../menus/SettingsTopMenuComponent';
    import UpdateProfile from "./UpdateProfileComponent";
    import ProfilePicture from "./ProfilePictureComponent";
    import ChatColor from "./ChatColorComponent";

    import {mapGetters} from "vuex";
    import GlobalStore from "../../store/GlobalStore";

    export default {

        name: 'Settings',

        store: GlobalStore,

        components: {
            MenuTop,
            UpdateProfile,
            ProfilePicture,
            ChatColor
        },

        computed: {

            ...mapGetters(['user', 'logged'])

        },

        created: function () {
            if (!this.logged) {
                this.$router.push('/')
            } else if (this.getLSI('last_path') === '/') {
                this.$router.go(0)
            }
        }

    }

</script>
