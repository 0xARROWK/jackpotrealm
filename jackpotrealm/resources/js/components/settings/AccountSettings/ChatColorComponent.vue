<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5">

        <div class="flex items-center justify-between leading-tight p-2 sm:p-4">
            <h1 class="text-md font-bold">
                <a class="text-white">
                    {{ __('Chat color') }}
                </a>
            </h1>
        </div>

        <div class="flex items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4">
            <div>
                <div v-if="this.current">
                    <div class="grid grid-cols-4 sm:grid-cols-8 lg:grid-cols-6 xl:grid-cols-8">
                        <div v-for="color in this.colors" @click="updateColor(color)">
                            <div :class="current === color ? 'ring-2 ring-offset-2 ring-offset-th-card ring-th-color' : ''"
                                 class="mr-5 mb-3 rounded-full w-10 h-10 cursor-pointer" :style="'background-color: #' + color + ';'"></div>
                        </div>
                    </div>
                    <p class="text-white text-sm">{{ __('Preview') }}</p>
                    <div class="bg-th-chat w-full text-th-chat-text-color mt-3 p-5 rounded-md">
                        <p><b><span :style="'color: #' + this.current + ' !important;'">{{ this.user.username }}</span> : {{ __('Just take a look at this chat color !') }}</b></p>
                    </div>
                </div>
                <form-error v-if="this.error" :error-msg="this.error[0]" :error-count="this.error[1]" :error-replace="this.error[2]"></form-error>
            </div>
        </div>

    </div>

</template>

<script>

    import FormError from "../../general/form/FormErrorComponent";

    import GlobalStore from "../../store/GlobalStore";
    import { mapGetters, mapActions } from "vuex";

    export default {

        name: "ChatColor",

        store: GlobalStore,

        components: {
            FormError
        },

        computed: {
            ...mapGetters(['user'])
        },

        data: function() {

            return {

                current: '',
                colors: '',
                error: []

            }

        },

        created: function() {
            axios
                .get(this.$apiURL + '/user/account-settings/chat-color', {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        this.current = response.data.success.current
                        this.colors = response.data.success.others
                    } else {
                        this.error = ['We are unable to retrieve your data', 1, []]
                    }
                })
        },

        methods: {

            ...mapActions(['updateUser']),

            updateColor: function(color) {

                this.error = []

                axios
                    .post(this.$apiURL + '/user/account-settings/update/chat-color', {
                        color: color
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            this.current = color
                            let id = this.user.id,
                                username = this.user.username,
                                email = this.user.email,
                                verified = this.user.verified,
                                role = this.user.role,
                                birthdate = this.user.birthdate
                            this.updateUser({id, username, email, verified, role, birthdate, color})
                        } else {
                            this.error = ['Your data could not be saved', 1, []]
                        }
                    })

            }

        }

    }

</script>
