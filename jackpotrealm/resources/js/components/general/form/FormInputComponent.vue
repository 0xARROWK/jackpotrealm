<template>

    <div :class="[this.containerWidth ? this.containerWidth : 'w-full', this.containerClass ? this.containerClass : '']">

        <form-label v-if="this.label" :label-for="this.inputId" :label-msg="this.label" :strong="this.strong" :loading="this.loadData"></form-label>

        <textarea v-if="this.textarea"
                  v-model="entry"
                  :id="this.inputId"
                  :type="this.inputType ? this.inputType : 'text'"
                  :placeholder="this.placeholder ? __(this.placeholder) : ''"
                  class="flex-1 appearance-none py-1 px-2 border-2 rounded-lg text-sm focus:outline-none bg-th-body"
                  :class="[this.error ? 'border-red-600 focus:border-red-600' : 'border-transparent focus:border-th-color', this.inputClass ? this.inputClass : '', this.inputDisabled ? 'text-th-text-chat-color' : 'text-th-text-color', this.autoWidth ? '' : 'w-full']"
                  :rows="this.textareaRows"
                  :cols="this.textareaCols"
                  :disabled="this.inputDisabled">
        </textarea>

        <input v-else
               v-model="entry"
               :id="this.inputId"
               :type="this.inputType ? this.inputType : 'text'"
               :placeholder="this.placeholder ? __(this.placeholder) : ''"
               class="flex-1 appearance-none py-1 px-2 border-2 rounded-lg text-sm focus:outline-none bg-th-body"
               :class="[this.error ? 'border-red-600 focus:border-red-600' : 'border-transparent focus:border-th-color', this.inputClass ? this.inputClass : '', this.inputDisabled ? 'text-th-chat-text-color' : 'text-th-text-color', this.autoWidth ? '' : 'w-full']"
               :disabled="this.inputDisabled"
        >

        <form-error v-if="!this.disableError" :error-msg="this.errorMsg" :error-count="this.errorCount" :error-replace="this.errorReplace"></form-error>

    </div>

</template>

<script>

    import FormLabel from './FormLabelComponent';
    import FormError from './FormErrorComponent';

    export default {

        name: "FormInput",

        components: {
            FormLabel,
            FormError
        },

        props: ['containerWidth', 'containerClass', 'label', 'placeholder', 'inputType', 'inputClass', 'inputDisabled', 'disableError', 'strong', 'loading', 'inputValue', 'autoWidth', 'textarea', 'textareaRows', 'textareaCols'],

        data: function() {

            return {

                entry: this.inputValue ? this.inputValue : '',
                inputId: this.$vnode.key,
                error: false,
                errorMsg: '',
                errorCount: 1,
                errorReplace: [],
                loadData: false

            }

        },

        methods: {

            showErrorMsg: function(key, count = 1, replace = []) {

                if (!this.disableError) {
                    this.error = true
                    this.errorMsg = key
                    this.errorCount = count
                    this.errorReplace = replace
                }

            },

            resetErrorMsg: function() {

                this.error = false
                this.errorMsg = ''
                this.errorCount = 1
                this.errorReplace = []

            },

            showError: function() {

                this.error = true

            },

            resetError: function() {

                this.error = false

            },

            setLoadData: function (value) {

                this.loadData = value

            }

        },

        watch: {

            inputValue: function (newValue, oldValue) {

                this.entry = newValue

            },

            entry: function (newValue, oldValue) {

                this.$emit('validate', newValue)

            }

        }

    }

</script>
