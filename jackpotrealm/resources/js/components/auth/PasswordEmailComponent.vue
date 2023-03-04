<template>

    <div class="flex items-center justify-center h-screen">

        <div class="bg-th-card rounded-lg shadow mx-2 xs:max-w-sm md:max-w-xl width-signin">

            <div class="grid grid-cols-12 gap-4 bg-th-card m-10">

                <div v-if="sent" class="bg-th-card rounded mb-2 flex flex-col col-span-full md:col-start-2 md:col-span-10 text-white">
                    {{ __('Password reset link has been sent') }}
                    <div class="w-full mt-5">
                        <router-link to="/app/login"><button class="bg-th-btn-2 hover:bg-th-btn-hover-2 focus:ring-2 focus:ring-th-btn-ring-2 focus:ring-offset-2 focus:ring-offset-th-btn-card-ring-offset w-full flex-shrink-0 text-th-card text-base font-semibold py-1 px-2 rounded-lg shadow-md focus:outline-none">{{ __('Cancel') }}</button></router-link>
                    </div>
                </div>

                <form v-else class="bg-th-card rounded mb-2 flex flex-col col-span-full md:col-start-2 md:col-span-10">

                    <form-input @validate="emailValidation" key="signup-email" input-type="email"
                                label="Email address" placeholder="Enter your email address" ref="sendResetLinkEmail"></form-input>

                    <form-error :error-msg="this.checkupSendResetLinkError[0]" :error-count="this.checkupSendResetLinkError[1]" :error-replace="this.checkupSendResetLinkError[2]"></form-error>

                    <div class="w-full mt-5">
                        <button-loader @submitted="submitSendResetLink" key="sendResetLinkSubmitButton" text="Send password reset link" ref="submitSendResetLinkButton"></button-loader>
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

        name: "PasswordEmail",

        components: {
            FormLabel,
            FormInput,
            FormError,
            ButtonLoader
        },

        data: function() {

            return {

                sent: false,
                email: null,
                checkupSendResetLinkError: []

            }

        },

        methods: {

            emailValidation: function (value) {

                if (/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/.test(value)) {

                    this.email = value
                    this.$refs.sendResetLinkEmail.resetErrorMsg()

                } else {

                    this.email = null
                    this.$refs.sendResetLinkEmail.showErrorMsg("Email.email")

                }

                this.checkSendResetLinkForm()

            },

            checkSendResetLinkForm: function () {

                if (this.email) {
                    this.$refs.submitSendResetLinkButton.setSubmitError(false)
                } else {
                    this.$refs.submitSendResetLinkButton.setSubmitError(true)
                }

            },

            submitSendResetLink: function () {

                if (this.email !== null) {

                    this.checkupSendResetLinkError = []

                    let url = this.$appURL + '/password/email',
                        data = {
                            _token: this.getCSRFToken(),
                            email: this.email
                        }

                    axios
                        .post(url, data, {responseType: 'json'})
                        .then(response => {
                            if (response.data && response.data.success) {
                                this.sent = true
                            } else if (response.status === 419) {
                                this.checkupSendResetLinkError = ['Session has expired, please reload this page', 1, []]
                            } else {
                                this.checkupSendResetLinkError = response.data.error
                            }
                        })
                        .catch(error => {
                            this.checkupSendResetLinkError = ['An error occurred', 1, []]
                            this.$refs.submitSendResetLinkButton.stopLoader()
                        })

                }

            }

        }

    }

</script>
