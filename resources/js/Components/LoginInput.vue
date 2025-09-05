<template>

    <div class="relative flex items-center border-b border-gray-300">
        <!-- Static error border -->
        <span v-if="error" class="absolute bottom-0 left-0 h-[2px] w-full bg-red-600"></span>

        <!-- Animated focus border -->
        <span class="absolute bottom-0 left-0 h-[2px] w-0 transition-all duration-500 ease-linear" :class="[
            isFocused ? 'w-full' : '',
            !error ? 'bg-blue-600' : ''
        ]"></span>

        <font-awesome-icon class="login-icon" :icon="icon" />
        <input  ref="input" autocomplete="off" v-bind="$attrs"
            class="login-input outline-none bg-transparent flex-1 px-2 py-2" :type="type" :value="modelValue"
            @focus="isFocused = true" @blur="isFocused = false"
            @input="$emit('update:modelValue', $event.target.value)" />
    </div>

    <div v-if="error" class="login-form-error block">{{ error }}</div>

</template>

<script>
export default {
    inheritAttrs: false,
    props: {
        // id: {
        //     type: String,
        //     default() {
        //         return `text-input-${this._uid}`
        //     },
        // },
        type: {
            type: String,
            default: 'text',
        },
        modelValue: {
            type: [String, Number],
            default: "",
        },
        icon: {
            type: String,
            default: "fa-solid fa-user"
        },
        label: String,
        error: String,
    },
    methods: {
        focus() {
            this.$refs.input.focus()
        },
        select() {
            this.$refs.input.select()
        },
        setSelectionRange(start, end) {
            this.$refs.input.setSelectionRange(start, end)
        },
    },
    data() {
        return {
            isFocused: false,
        }
    },
}
</script>
