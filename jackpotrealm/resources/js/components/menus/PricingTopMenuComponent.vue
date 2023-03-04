<template>

    <nav class="bg-th-menu shadow fixed w-screen" style="top: 0px; z-index: 1010;">

        <div class="w-100 mx-auto px-2 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-between h-16">
                <div class="absolute inset-y-0 left-0 flex items-center">

                    <!-- Application name -->
                    <div @click="togglePricing" class="font-semibold text-md text-white cursor-pointer">Go back</div>
                    <!--<a class="block lg:hidden" href="/"><img class="h-8 w-auto ml-2" src="/app/images/logo.png" alt="Jackpotrealm"></a>
                    <a class="hidden lg:flex justify-start" href="/"><img class="h-8 w-auto" src="/app/images/logo.png" alt="Jackpotrealm"><h4 class="ml-3 font-extrabold text-th-color-2 text-2xl uppercase">Jackpotrealm</h4></a>-->

                    <!-- Application links -->
                    <div class="hidden sm:block sm:ml-6">
                        <div class="flex space-x-4">
                            <!--<a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-white bg-gray-900">Dashboard</a>
                            <router-link to="/settings" href="#" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700">Settings</router-link>
                            <a href="#" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700">Team</a>
                            <a href="#" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700">Projects</a>
                            <a href="#" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700">Calendar</a>-->
                        </div>
                    </div>

                </div>

                <!-- Application settings -->
                <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                    <div class="flex items-center cursor-pointer">

                        <!-- Mobile menu button-->
                        <button type="button" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">

                            <svg id="open-menu" @click="toggleMobileMenu()" :class="this.open ? 'hidden' : 'block'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>

                            <svg id="close-menu" @click="toggleMobileMenu()" :class="this.open ? 'block' : 'hidden'" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>

                        </button>

                        <!-- Application settings -->
                        <a v-if="this.logged" @click="performLogout()" class="hidden sm:block px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 cursor-pointer">{{ __('Log Out') }}</a>
                        <span v-else class="hidden sm:block">
                            <router-link to="/app/login" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 cursor-pointer">{{ __('Log In') }}</router-link>
                            <router-link to="/app/register" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 cursor-pointer">{{ __('Sign Up') }}</router-link>
                        </span>

                        <div v-if="this.logged" class="ml-3 relative hidden sm:block">
                            <router-link to="/settings">
                                <button class="bg-gray-800 flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-th-btn-card-ring-offset focus:ring-th-btn-ring" id="user-menu" aria-haspopup="true">
                                    <img class="h-8 w-8 rounded-full" :src="this.profilePicture + '?t=' + new Date()" alt="">
                                </button>
                            </router-link>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state -->
        <div :class="this.open ? 'block' : 'hidden'" class="sm:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <div v-if="this.logged">
                    <router-link to="/settings" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 rounded-md">
                        <button class="flex items-center text-sm rounded-full font-medium focus:outline-none" id="user-mobile-menu" aria-haspopup="true">
                            <img class="h-8 w-8 rounded-full" :src="this.profilePicture" alt="">
                            <span class="ml-3">Profile</span>
                        </button>
                    </router-link>
                </div>
                <a v-if="this.logged" @click="performLogout()" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-sm font-medium">{{ __('Log Out') }}</a>
                <span v-else>
                    <router-link to="/app/login" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-sm font-medium">{{ __('Log In') }}</router-link>
                    <router-link to="/app/register" class="text-gray-300 hover:text-white hover:bg-gray-700 block px-3 py-2 rounded-md text-sm font-medium">{{ __('Sign Up') }}</router-link>
                </span>
            </div>
        </div>
    </nav>

</template>

<script>

    import {mapGetters} from "vuex";
    import GlobalStore from "../store/GlobalStore";

    export default {

        name: 'PricingTopMenu',

        store: GlobalStore,

        computed: {

            ...mapGetters(['user', 'logged'])

        },

        data: function() {
            return {
                profilePicture: null,
                open: false
            }
        },

        created: function() {
            if (this.user) this.profilePicture = 'storage/users/' + this.user.username + '/profile_picture.jpg'
        },

        methods: {

            performLogout: function () {
                this.$router.push('?logout')
                this.$router.go(0)
            },

            toggleMobileMenu: function() {
                this.open = !this.open
            },

            togglePricing: function () {
                this.$emit('togglePricing', false)
            }

        }

    }

</script>
