<template>

    <div class="flex items-center justify-center h-screen">

        <div class="bg-th-card rounded-lg shadow mx-2 xs:max-w-sm md:max-w-xl width-signin">

            <div class="grid grid-cols-12 gap-4 bg-th-card m-10">

                <div v-if="reset" class="bg-th-card rounded mb-2 flex flex-col col-span-full md:col-start-2 md:col-span-10 text-white">
                    {{ __('Your password has been reset') }}
                    <div class="w-full mt-5">
                        <router-link to="/app/login"><button class="bg-th-btn-2 hover:bg-th-btn-hover-2 focus:ring-2 focus:ring-th-btn-ring-2 focus:ring-offset-2 focus:ring-offset-th-btn-card-ring-offset w-full flex-shrink-0 text-th-card text-base font-semibold py-1 px-2 rounded-lg shadow-md focus:outline-none">{{ __('Log In') }}</button></router-link>
                    </div>
                </div>

                <form v-else class="bg-th-card rounded mb-2 flex flex-col col-span-full md:col-start-2 md:col-span-10">

                    <form-input @validate="emailValidation" key="reset-password-email" input-type="email" :input-value="this.email"
                                label="Email address" placeholder="Enter your email address" ref="resetPasswordEmail"></form-input>

                    <form-input @validate="passwordValidation" key="reset-password-password" input-type="password"
                                label="Password" placeholder="************" :strong="this.passwordStrength" ref="resetPasswordPassword"></form-input>

                    <form-input @validate="passwordConfirmationValidation" key="reset-password-password-confirmation" input-type="password"
                                label="Password confirmation" placeholder="************" ref="resetPasswordPasswordConfirmation"></form-input>

                    <form-error :error-msg="this.checkupResetPasswordError[0]" :error-count="this.checkupResetPasswordError[1]" :error-replace="this.checkupResetPasswordError[2]"></form-error>

                    <div class="w-full mt-5">
                        <button-loader @submitted="submitResetPassword" key="resetPasswordSubmitButton" text="Reset password" ref="submitResetPasswordButton"></button-loader>
                    </div>

                    <div class="w-full mt-5">
                        <router-link to="/app/login"><button class="bg-th-btn-2 hover:bg-th-btn-hover-2 focus:ring-2 focus:ring-th-btn-ring-2 focus:ring-offset-2 focus:ring-offset-th-btn-card-ring-offset w-full flex-shrink-0 text-th-card text-base font-semibold py-1 px-2 rounded-lg shadow-md focus:outline-none">{{ __('Cancel') }}</button></router-link>
                    </div>

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

export default {

    name: "PasswordReset",

    components: {
        FormLabel,
        FormInput,
        FormError,
        ButtonLoader
    },

    data: function() {

        return {

            reset: false,
            email: this.$route.params.email,
            password: null,
            passwordConfirmation: null,
            passwordStrength: 50,

            lastPassword: null,
            lastPasswordConfirmation: null,

            checkupResetPasswordError: []

        }

    },

    methods: {

        emailValidation: function (value) {

            if (/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/.test(value)) {

                this.email = value
                this.$refs.resetPasswordEmail.resetErrorMsg()

            } else {

                this.email = null
                this.$refs.resetPasswordEmail.showErrorMsg("Email.email")

            }

            this.checkResetPasswordForm()

        },

        passwordValidation: function (value) {

            // check if confirmation password match
            if (value !== this.lastPasswordConfirmation) {
                this.passwordConfirmation = null
                this.$refs.resetPasswordPasswordConfirmation.showErrorMsg('Confirmed.password')
                this.checkResetPasswordForm()
            } else {
                this.passwordConfirmation = value
                this.$refs.resetPasswordPasswordConfirmation.resetErrorMsg()
                this.checkResetPasswordForm()
            }

            // save the last value for check with the confirmation password input
            this.lastPassword = value

            // check password strength
            this.$refs.resetPasswordPassword.setLoadData(true)

            axios
                .post(this.$apiURL + '/verification/password/', {password: value}, {responseType: 'json'})
                .then(response => {
                    let strength = response.data.strength*25
                    if (strength === 0) strength = 10
                    if (strength === 10 && value.length === 0 || isNaN(strength)) strength = 0
                    this.passwordStrength = strength
                    if (!/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/.test(value)) {
                        this.password = null
                        this.$refs.resetPasswordPassword.showErrorMsg('Regex.password')
                        this.checkResetPasswordForm()
                    } else if (this.passwordStrength < 75) {
                        this.password = null
                        this.$refs.resetPasswordPassword.showErrorMsg('Strength.password')
                        this.checkResetPasswordForm()
                    } else {
                        this.password = value
                        this.$refs.resetPasswordPassword.resetErrorMsg()
                        this.checkResetPasswordForm()
                    }
                })
                .finally(() => {
                    this.$refs.resetPasswordPassword.setLoadData(false)
                })

        },

        passwordConfirmationValidation: function (value) {

            this.lastPasswordConfirmation = value

            if (value !== this.lastPassword) {
                this.passwordConfirmation = null
                this.$refs.resetPasswordPasswordConfirmation.showErrorMsg('Confirmed.password')
            } else {
                this.passwordConfirmation = value
                this.$refs.resetPasswordPasswordConfirmation.resetErrorMsg()
            }

            this.checkResetPasswordForm()

        },

        checkResetPasswordForm: function () {

            if (this.email && this.password && this.passwordConfirmation) {
                this.$refs.submitResetPasswordButton.setSubmitError(false)
            } else {
                this.$refs.submitResetPasswordButton.setSubmitError(true)
            }

        },

        submitResetPassword: function () {

            if (this.email !== null && this.password !== null && this.passwordConfirmation !== null) {

                this.checkupResetPasswordError = []

                let url = this.$appURL + '/password/reset',
                    data = {
                        _token: this.getCSRFToken(),
                        token: this.$route.params.token,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.passwordConfirmation
                    }

                axios
                    .post(url, data, {responseType: 'json'})
                    .then(response => {
                        this.$refs.submitResetPasswordButton.stopLoader()
                        if (response.data && response.data.success) {
                            this.reset = true
                        } else if (response.status === 419) {
                            this.checkupResetPasswordError = ['Session has expired, please reload this page', 1, []]
                        } else {
                            this.checkupResetPasswordError = response.data.error
                        }
                    })
                    .catch(error => {
                        this.checkupResetPasswordError = ['An error occurred', 1, []]
                        this.$refs.submitResetPasswordButton.stopLoader()
                    })

            }

        }

    }

}

</script>
