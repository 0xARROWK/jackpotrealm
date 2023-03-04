<template>

    <div v-if="!this.userLoading">

        <router-view></router-view>

    </div>

</template>

<script>

    import { mapActions, mapGetters } from 'vuex';
    import GlobalStore from "./store/GlobalStore";

    export default {

        name: 'App',

        store: GlobalStore,

        computed: {

            ...mapGetters(['user'])

        },

        data: function () {
            return {
                userLoading: true
            }
        },

        created: function() {

            // check if user request logout
            if (window.location.search.endsWith('?logout')) {
                this.logout()
            }

            // check if user is logged
            this.checkAuth()

            // check language used
            this.checkLanguage()

            // save path
            if (this.getLSI('last_path') === null || this.getLSI('current_path') === null) {
                this.setLSI('last_path', '/')
                this.setLSI('current_path', '/')
            } else {
                this.setLSI('last_path', this.getLSI('current_path'))
                this.setLSI('current_path', this.$route.path)
            }

            this.setPageLoading(false)

        },

        methods: {

            // allow to add a user in the store
            ...mapActions(['addUser', 'setPageLoading', 'setLogged']),

            // check if language cookie exist or if it must be created
            checkLanguage: function() {

                let cookieLanguage = this.getCookie("language");

                if (cookieLanguage !== undefined && cookieLanguage !== null && cookieLanguage !== "") {

                    this.eraseCookie("language");
                    this.setCookie("language", cookieLanguage, 364);

                } else {

                    this.setCookie("language", this.__('Default language'), 364);

                }

            },

            // check if a user and his api token are valid
            checkAuth: function() {

                if (this.getLSI('api_token')) {

                    this.setLogged(true)

                    let continueButton = [{
                        key: 0,
                        content: 'Continue',
                        action: function() {
                            Vue.swal.close()
                        }
                    }];

                    axios
                        .get(this.$apiURL + '/user', {
                            headers: {
                                Authorization: 'Bearer ' + this.getLSI('api_token')
                            }
                        })
                        .then(response => {
                            try {
                                if (response.data) {
                                    this.addUser({
                                        id: response.data.id,
                                        username: response.data.username,
                                        email: response.data.email,
                                        verified: response.data.email_verified_at,
                                        role: response.data.role.split(','),
                                        birthdate: response.data.birthdate,
                                        color: response.data.chat_color
                                    })
                                }
                                this.userLoading = false
                            } catch (error) {
                                this.setLogged(false)
                                this.userLoading = false
                                this.VueSwal2('swalWarning', {'title': 'Error', 'message': 'We are unable to retrieve your data', 'showButtons': true, 'buttons': continueButton}, null, () => {this.logout()})
                            }
                        })
                        .catch(() => {
                            this.setLogged(false)
                            this.userLoading = false
                            this.VueSwal2('swalWarning', {'title': 'Error', 'message': 'Invalid user access token', 'showButtons': true, 'buttons': continueButton}, null, () => {this.logout()})
                        })

                } else {
                    this.userLoading = false
                }

            }

        },

        watch: {

        	$route(to, from) {

                this.setLSI('last_path', from.path)
                this.setLSI('current_path', to.path)

                this.checkLanguage()

        	}

        }

    }

</script>
