<template>

    <div class="flex items-center justify-center h-screen">

        <div class="bg-th-card text-th-text-color rounded-lg shadow mx-2 xs:max-w-sm md:max-w-xl">

            <p class="font-bold m-5 mt-10 xs:m-10 xs:mb-5">
                {{ __('Verify your email address') }}
            </p>

            <div class="m-5 mt-0 xs:m-10">
                {{ __('A verification link has been sent to your email address, if you did not receive the email, you can request another below') }}
                <form-error :error-msg="this.checkupResendEmailError[0]" :error-count="this.checkupResendEmailError[1]" :error-replace="this.checkupResendEmailError[2]"></form-error>
                <form class="d-inline md:flex md:justify-between">
                    <p @click="goHome" class="hidden md:block btn p-0 m-0 align-baseline mt-5 cursor-pointer text-th-color-2">{{ __('Close') }}</p>
                    <button v-if="!this.wait" @click="waitForRequest(5)" type="submit" class="btn btn-link p-0 m-0 align-baseline mt-5">{{ __('Click here to request another') }}</button>
                    <p v-else class="mt-5 text-th-color">{{ __('You can request another one in 60 seconds', 1, [this.wait]) }}</p>
                    <p @click="goHome" class="md:hidden block btn p-0 m-0 align-baseline mt-5 cursor-pointer text-th-color-2">{{ __('Close') }}</p>
                </form>
            </div>

        </div>

    </div>

</template>

<script>

    import FormError from "../general/form/FormErrorComponent";

    import {mapGetters} from "vuex";
    import GlobalStore from "../store/GlobalStore";

    export default {

        name: "VerifyComponent",

        store: GlobalStore,

        components: {
            FormError,
        },

        data: function () {

            return {

                checkupResendEmailError: [],
                wait: 0

            }

        },

        created: function() {

            /*if (!this.logged) {
                this.$router.push({path: "/"})
            }*/

            if (this.getLSI('verifyTimer') && this.getLSI('verifyTimer') > 0) {
                this.waitForRequest(this.getLSI('verifyTimer'))
            }

        },

        computed: {

            ...mapGetters(['logged'])

        },

        methods: {

            goHome: function() {

                window.location.href = '/'

            },

            waitForRequest: function(time) {

                this.checkupResendEmailError = []

                this.wait = time

                let url = '/email/resend',
                    data = {
                        _token: this.getCSRFToken()
                    },
                    self = this;

                axios
                    .post(url, data, {responseType: 'json'})
                    .then(response => {
                        if (response.status === 419) {
                            this.checkupResendEmailError = ['Session has expired, please reload this page', 1, []]
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    })

                let timer = setInterval(function() {
                    if (self.wait > 0) {
                        self.wait--
                        self.setLSI('verifyTimer', self.wait)
                    } else {
                        self.deleteLSI('verifyTimer')
                        clearInterval(timer)
                    }
                }, 1000)

            }

        }

    }

</script>
