<template>

    <div class="flex items-center justify-center h-screen">

        <div class="bg-th-card rounded-lg shadow mx-2 xs:max-w-sm md:max-w-xl width-login">

            <div class="grid grid-cols-12 gap-4 bg-th-card m-10">

                <form class="bg-th-card rounded mb-2 flex flex-col col-span-full md:col-start-2 md:col-span-10">

                    <form-input @validate="usernameLoginValidation" key="login-username"
                                label="Username" placeholder="Enter your username" ref="loginUsername"></form-input>

                    <form-input @validate="passwordLoginValidation" key="login-password" input-type="password"
                                label="Password" placeholder="************" ref="loginPassword"></form-input>

                    <form-error :error-msg="this.checkupLoginError[0]" :error-count="this.checkupLoginError[1]" :error-replace="this.checkupLoginError[2]"></form-error>

                    <div class="w-full mt-5">
                        <button-loader @submitted="submitLogin" key="loginSubmitButton" text="Log In" ref="submitLoginButton"></button-loader>
                    </div>

                    <div class="w-full mt-5">
                        <router-link to="/"><button class="bg-th-btn-2 hover:bg-th-btn-hover-2 focus:ring-2 focus:ring-th-btn-ring-2 focus:ring-offset-2 focus:ring-offset-th-btn-card-ring-offset w-full flex-shrink-0 text-th-card text-base font-semibold py-2 px-3 rounded-lg shadow-md focus:outline-none">{{ __('Cancel') }}</button></router-link>
                    </div>

                    <p class="text-xs font-bold text-th-text-color mt-5">{{ __('You don\'t have an account') }} <router-link to="/app/register" class="router-link">{{ __('Sign In') }}</router-link></p>
                    <p class="text-xs font-bold text-th-text-color mt-5"><router-link to="/app/password/email" class="router-link">{{ __('Forgot my password') }}</router-link></p>

                </form>

            </div>

        </div>

    </div>

</template>

<script>

import FormLabel from "../general/form/FormLabelComponent";
import FormInput from "../general/form/FormInputComponent";
import FormError from '../general/form/FormErrorComponent';
import ButtonLoader from '../general/form/ButtonLoaderComponent';

import {mapGetters} from "vuex";
import GlobalStore from "../store/GlobalStore";

export default {

    name: "Login",

    store: GlobalStore,

    components: {
        FormLabel,
        FormInput,
        FormError,
        ButtonLoader
    },

    data: function() {

        return {

            // login form values
            loginUsername: null,
            loginPassword: null,

            // checkup forms error
            checkupLoginError: []

        }
    },

    computed: {

        ...mapGetters(['logged'])

    },

    created() {

        if (this.logged) {
            this.$router.push('/')
        }

    },

    methods: {

        /*
         * LOGIN FORM VALIDATION
         */

        // check presence of username
        usernameLoginValidation: function(value) {
            this.loginUsername = value
            this.checkLoginForm()
        },

        // check presence of password
        passwordLoginValidation: function(value) {
            this.loginPassword = value
            this.checkLoginForm()
        },

        // verifies that all fields are correctly filled in and authorises the submission of the login form
        checkLoginForm: function() {

            if (this.loginUsername && this.loginPassword) {
                this.$refs.submitLoginButton.setSubmitError(false)
            } else {
                this.$refs.submitLoginButton.setSubmitError(true)
            }

        },

        // submit login form
        submitLogin: function() {

            this.checkupLoginError = []

            let submitted = false,
                url = this.$appURL + '/login',
                data = {
                    _token: this.getCSRFToken(),
                    username: this.loginUsername,
                    password: this.loginPassword
                };

            axios
                .post(url, data, {responseType: 'json'})
                .then(response => {
                    if (response.data && response.data.success) {
                        this.setLSI('api_token', response.data.token)
                        this.setCookie('api_token_copy', response.data.token, 365)
                        submitted = true
                    } else {
                        this.checkupLoginError = response.data.error
                    }
                })
                .catch(error => {
                    if (error.response && error.response.status === 419) {
                        this.checkupLoginError = ['Session has expired, please reload this page', 1, []]
                    } else {
                        this.checkupLoginError = ['An error occurred', 1, []]
                    }
                })
                .finally(() => {
                    this.$refs.submitLoginButton.stopLoader()
                    if (submitted) {
                        window.location.href = '/'
                    }
                })

        }

    }

}

</script>
