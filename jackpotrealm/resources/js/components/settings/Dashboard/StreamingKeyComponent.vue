<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-menu">

        <div class="items-center leading-none p-2 sm:p-4">
            <p class="font-semibold text-md text-th-text-color">{{ __('Streaming key') }}</p>
            <form-input id="streaming-key" label="" :input-value="this.sk" input-class="mt-5" :input-type="this.skInputType" :input-disabled="true" ref="dashboardStreamingKey"></form-input>
            <div class="flex justify-between mt-5 text-sm">
                <div>
                    <button :class="this.skRegenerated ? 'bg-th-btn-2 text-th-card' : 'text-gray-300 hover:text-th-text-color hover:bg-gray-700'" class="px-3 py-1 rounded-md font-medium" @click="regenerateSK">{{ __(this.skRegenerated ? 'Regenerated' : 'Regenerate') }}</button>
                    <button :class="this.skCopied ? 'bg-th-btn-2 text-th-card' : 'text-gray-300 hover:text-th-text-color hover:bg-gray-700'" class="px-3 py-1 rounded-md font-medium" @click="copySK">{{ __(this.skCopied ? 'Copied' : 'Copy') }}</button>
                </div>
                <button-loader @submitted="showSK" :text="this.displayText" :take-full-width="false" :prevent-submit="false" :can-load="false"></button-loader>
            </div>
        </div>

    </div>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";

    import {mapGetters} from "vuex";
    import GlobalStore from "../../store/GlobalStore";

    export default {

        name: "StreamingKey",

        components: {
            FormInput,
            ButtonLoader
        },

        store: GlobalStore,

        data: function () {

            return {

                sk: '',
                skInputType: 'password',
                displayText: 'Show',
                skCopied: false,
                skRegenerated: false

            }

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

            this.getSK()
        },

        methods: {

            getSK: function () {

                axios
                    .get(this.$apiURL + '/user/dashboard/sk', {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            this.sk = response.data.success.key
                            this.$refs.dashboardStreamingKey.resetErrorMsg()
                        } else {
                            this.sk = ''
                            this.$refs.dashboardStreamingKey.showErrorMsg(response.data.error)
                        }
                    })
                    .catch(error => {
                        this.$refs.dashboardStreamingKey.showErrorMsg('We are unable to retrieve your data')
                    })

            },

            showSK: function () {

                this.skInputType === 'password' ? this.skInputType = 'text' : this.skInputType = 'password'
                this.displayText === 'Show' ? this.displayText = 'Hide' : this.displayText = 'Show'

            },

            regenerateSK: function () {

                if (!this.skRegenerated) {

                    axios
                        .post(this.$apiURL + '/user/dashboard/sk/regenerate', {}, {
                            headers: {
                                Authorization: 'Bearer ' + this.getLSI('api_token')
                            }
                        })
                        .then(response => {
                            if (response.data && response.data.success) {
                                this.$refs.dashboardStreamingKey.resetErrorMsg()
                                this.getSK()
                            } else {
                                this.$refs.dashboardStreamingKey.showErrorMsg(response.data.error)
                            }
                        })
                        .catch(error => {
                            this.$refs.dashboardStreamingKey.showErrorMsg('We are unable to retrieve your data')
                        })

                    this.skRegenerated = true
                    setTimeout(() => {
                        this.skRegenerated = false
                    }, 3000)

                }

            },

            copySK: function () {

                if (!this.skCopied) {

                    let el = document.createElement('textarea');
                    el.value = this.sk;
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);

                    this.skCopied = true
                    setTimeout(() => {
                        this.skCopied = false
                    }, 3000)

                }

            }

        }


    }

</script>
