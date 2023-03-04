<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5">

        <div class="items-center leading-none p-2 sm:p-4">
            <p class="font-semibold text-md text-th-text-color">Bot commands</p>
            <div v-if="!this.commands" class="text-white text-sm mt-3">
                No commands found.
            </div>
            <div v-else v-for="(command, index) in commands" ref="updateCommand"
                 class="grid grid-cols-12 gap-4 w-full mt-3 leading-none" :class="command.edit ? 'items-end' : 'items-center'">
                <div v-if="!command.edit" class="col-span-5 bg-th-chat text-th-chat-text-color p-3 rounded-md">
                    !{{ command.command }}
                </div>
                <div v-if="!command.edit" class="col-span-5 text-white">
                    {{ command.description }}
                </div>
                <div v-if="!command.edit" class="col-span-2">
                    <font-awesome-icon class="text-th-color-2 cursor-pointer" @click="editCommand(index)" icon="edit"></font-awesome-icon>
                    <font-awesome-icon class="text-red-500 cursor-pointer ml-5" @click="deleteCommand(command, index)" icon="trash"></font-awesome-icon>
                </div>
                <div v-if="command.edit" class="col-span-6">
                    <form-input @validate="saveEditCommand($event, 'command', index)" :input-value="command.command" label="Command name"></form-input>
                </div>
                <div v-if="command.edit" class="col-span-6">
                    <form-input @validate="saveEditCommand($event, 'description', index)" :input-value="command.description" label="Command description"></form-input>
                </div>
                <div v-if="command.edit" class="col-span-6">
                    <form-input @validate="saveEditCommand($event, 'response', index)" :input-value="command.response" label="Command response"></form-input>
                </div>
                <div v-if="command.edit" class="col-span-6 grid grid-cols-2 gap-2 items-center leading-none w-full">
                    <button-loader @submitted="confirmEditCommand(index)" btn-class="col-span-1" text="Update" :can-load="false" :prevent-submit="false"></button-loader>
                    <button-loader @submitted="cancelEditCommand(index)" btn-class="col-span-1" text="Cancel" :prevent-submit="false"></button-loader>
                </div>
            </div>
            <form-error v-if="this.commandError" :error-msg="this.commandError[0]" :error-count="this.commandError[1]" :error-replace="this.commandError[2]"></form-error>
            <div class="w-full mt-5 flex justify-end">
                <button-loader @submitted="addCommand" text="Add command" :can-load="false" :prevent-submit="false" :take-full-width="false"></button-loader>
            </div>
        </div>

    </div>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import FormError from "../../general/form/FormErrorComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {

        name: "BotCommands",

        components: {
            FormInput,
            FormError,
            ButtonLoader,
            FontAwesomeIcon
        },

        data: function () {
            return {
                commands: [],
                tempCommand: null,
                tempDescription: null,
                tempResponse: null,
                commandError: []
            }
        },

        created: function () {
            axios
                .get(this.$apiURL + '/chat/list-commands', {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    }
                })
                .then(response => {
                    if (response.data && response.data.success) {
                        this.commands = response.data.success
                    } else {
                        this.commands = null
                    }
                })
                .catch(error => {
                    this.commandError = ['An error occured', 1, []]
                })
        },

        methods: {

            editCommand: function (index) {
                this.$set(this.commands[index], 'edit', true)
            },

            saveEditCommand: function (value, edit, index) {
                this.tempCommand = this.commands[index].command
                this.tempDescription = this.commands[index].description
                this.tempResponse = this.commands[index].response

                if (edit === 'command') this.tempCommand = value
                else if (edit === 'description') this.tempDescription = value
                else if (edit === 'response') this.tempResponse = value
            },

            confirmEditCommand: function (index) {
                this.commandError = []
                let url = this.$apiURL + "/user/dashboard/edit-command",
                    data = {command: this.tempCommand, description: this.tempDescription, response: this.tempResponse, oldCommand: this.commands[index].command}
                if (this.commands[index].toCreate) {
                    url = this.$apiURL + "/user/dashboard/add-command"
                    data = {command: this.tempCommand, description: this.tempDescription, response: this.tempResponse}
                }
                if (this.tempCommand) {
                    axios
                        .post(url, data, {
                            headers: {
                                Authorization: 'Bearer ' + this.getLSI('api_token')
                            }
                        })
                        .then(response => {
                            if (response.data && response.data.success) {
                                this.commands[index].command = this.tempCommand
                                this.commands[index].description = this.tempDescription
                                this.commands[index].response = this.tempResponse
                                this.$set(this.commands[index], 'edit', false)
                                this.$set(this.commands[index], 'toCreate', false)
                            } else {
                                this.commandError = [response.data.error, 1, []]
                            }
                        })
                        .catch(error => {
                            this.commandError = ['An error occurred', 1, []]
                        })
                } else {
                    this.commandError = ['Command name must be filled', 1, []]
                }
            },

            cancelEditCommand: function (index) {
                this.commandError = []
                this.$set(this.commands[index], 'edit', false)
                if (this.commands[index].toCreate) {
                    this.commands.splice(index, 1)
                    if (this.commands.length === 0) this.commands = null
                }
            },

            addCommand: function() {
                if (!this.commands) this.commands = []
                this.tempCommand = 'none'
                this.commands.push({command: 'none', description: '', response: '', edit: true, toCreate: true})
            },

            deleteCommand: function (command, index) {
                this.commandError = []
                axios
                    .post(this.$apiURL + "/user/dashboard/delete-command", {
                        command: command
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (!response.data || !response.data.success) {
                            this.commandError = ['An error occurred', 1, []]
                        } else {
                            this.commandError = []
                            this.commands.splice(index, 1)
                            if (this.commands.length === 0) this.commands = null
                        }
                    })
                    .catch(error => {
                        this.commandError = ['An error occurred', 1, []]
                    })
            }

        }

    }

</script>
