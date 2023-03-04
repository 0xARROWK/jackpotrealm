<template>

    <button @click.prevent="startLoader"
            v-bind:class="[this.btnClass ? this.btnClass : '', this.takeFullWidth ? 'w-full' : '', this.finishLoadingAnimation && this.finishLoading ? 'bg-th-btn-2 hover:bg-th-btn-2 text-th-card' : '', this.submitError && !this.finishLoading ? 'bg-th-btn-disabled' : 'bg-th-btn hover:bg-th-btn-hover', !this.submitError && !this.finishLoading ? 'focus:ring-2 focus:ring-th-btn-ring focus:ring-offset-2 focus:ring-offset-th-btn-card-ring-offset' : '']"
            class="flex-shrink-0 text-th-text-color text-base font-semibold py-1 px-3 rounded-lg shadow-md focus:outline-none"
    >

        <span v-if="this.isLoading" class="flex flex-row justify-between">
            <!--<div class="loader-bar loader-bar-white">
                <div></div>
                <div></div>
                <div></div>
            </div>-->
            <div class="animate-spin loader mt-1 mr-2"></div>
            {{ __('Processing') }}
        </span>

        <span v-else-if="this.savedText">
            {{ this.finishLoadingAnimation && finishLoading ? __(this.finishLoadingMsg) : __(this.savedText) }}
        </span>

    </button>

</template>

<script>

    export default {

        name: "ButtonLoaderComponent",

        props: {
            text: {
                type: String,
                default: ''
            },
            preventSubmit: {
                type: Boolean,
                default: true
            },
            takeFullWidth: {
                type: Boolean,
                default: true
            },
            canLoad: {
                type: Boolean,
                default: true
            },
            btnClass: {
                type: String,
                default: ''
            },
            finishLoadingAnimation: {
                type: Boolean,
                default: false
            },
            finishLoadingMsg: {
                type: String,
                default: ''
            }
        },

        data: function() {

            return {

                savedText: '',
                isLoading: false,
                finishLoading: false,
                submitError: this.preventSubmit

            }

        },

        created: function() {

            this.savedText = this.text

        },

        methods: {

            startLoader: function() {

                if (this.canLoad) {

                    if (this.isLoading === false && !this.submitError) {

                        this.savedText = ""
                        this.isLoading = true
                        this.$emit('submitted')

                    }

                } else {

                    this.$emit('submitted')

                }

            },

            stopLoader: function(finishLoading = true) {

                this.savedText = this.text
                this.isLoading = false
                if (finishLoading) this.finishLoading = true

            },

            setSubmitError: function(value) {

                this.submitError = value

            }

        },

        watch: {

            text: function (newValue, oldValue) {

                this.savedText = newValue

            },

            finishLoading: function (newValue, oldValue) {

                if (newValue === true) {
                    setTimeout(() => {
                        this.finishLoading = false
                    }, 3000)
                }

            }

        }

    }

</script>
