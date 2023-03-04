<template>

    <nav class="bg-th-menu shadow fixed w-screen" style="top: 0px;">

        <div class="w-100 mx-auto px-2 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-between h-16">
                <div class="absolute inset-y-0 left-0 flex items-center">

                    <!-- Application name -->
                    <a class="block lg:hidden" href="/"><img class="h-8 w-auto ml-2" src="/app/images/logo.png" alt="Jackpotrealm"></a>
                    <a class="hidden lg:flex justify-start" href="/"><img class="h-8 w-auto" src="/app/images/logo.png" alt="Jackpotrealm"><h4 class="ml-3 font-extrabold text-th-color-2 text-2xl uppercase">Jackpotrealm</h4></a>

                    <!-- Application links -->
                    <div class="hidden sm:block sm:ml-6">
                        <div class="flex space-x-4">
                            <router-link v-if="this.user.role.includes('streamer')" to="/settings" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700">{{ __('Account settings') }}</router-link>
                            <router-link v-if="this.user.role.includes('streamer')" to="/settings/moderation" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700">{{ __('Moderation')}} </router-link>
                            <router-link v-if="this.user.role.includes('streamer')" to="/settings/dashboard" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700">{{ __('Dashboard')}} </router-link>
                        </div>
                    </div>

                </div>

                <!-- Application settings -->
                <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <div class="flex items-center cursor-pointer">

                        <a v-if="this.logged" @click="performLogout()" class="px-3 py-2 mr-3 sm:mr-0 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 cursor-pointer">{{ __('Log Out') }}</a>

                        <!-- Mobile menu button-->
                        <button type="button" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">

                            <svg id="open-menu" @click="toggleMobileMenu()" :class="this.open ? 'hidden' : 'block'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>

                            <svg id="close-menu" @click="toggleMobileMenu()" :class="this.open ? 'block' : 'hidden'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>
                </div>

            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state -->
        <div :class="this.open ? 'block' : 'hidden'" class="sm:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <router-link v-if="this.user.role.includes('streamer')" to="/settings" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-sm font-medium cursor-pointer">{{ __('Account settings') }}</router-link>
                <router-link v-if="this.user.role.includes('streamer')" to="/settings/moderation" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-sm font-medium cursor-pointer">{{ __('Moderation') }}</router-link>
                <router-link v-if="this.user.role.includes('streamer')" to="/settings/dashboard" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-sm font-medium cursor-pointer">{{ __('Dashboard') }}</router-link>
            </div>
        </div>
    </nav>


</template>

<script>

    import GlobalStore from "../store/GlobalStore";
    import {mapGetters} from "vuex";

    export default {

        name: 'SettingsTopMenu',

        store: GlobalStore,

        computed: {

            ...mapGetters(['user', 'logged'])

        },

        data: function() {

            return {
                open: false
            }

        },

        methods: {

            performLogout: function () {
                this.$router.push('?logout')
                this.$router.go(0)
            },

            toggleMobileMenu: function () {
                this.open = !this.open
            }

        }

    }

</script>
