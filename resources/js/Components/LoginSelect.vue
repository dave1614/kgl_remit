<template>
  <div class="relative flex items-center border-b border-gray-300">
    <!-- Static error border -->
    <span v-if="error" class="absolute bottom-0 left-0 h-[2px] w-full bg-red-600"></span>

    <!-- Animated focus border -->
    <span
      class="absolute bottom-0 left-0 h-[2px] w-0 transition-all duration-500 ease-linear"
      :class="[isFocused ? 'w-full' : '', !error ? 'bg-blue-600' : '']"
    ></span>

    <!-- Main content wrapper -->
    <div
      class="flex items-center flex-1"
      :class="{ 'opacity-40 pointer-events-none select-none': loading }"
    >
      <font-awesome-icon class="login-icon" :icon="icon" />

      <select
        ref="select"
        v-bind="$attrs"
        class="login-input outline-none bg-transparent flex-1 px-2 py-2"
        v-model.number="internalValue"
        @focus="isFocused = true"
        @blur="isFocused = false"
        @change="$emit('change', internalValue)"
      >
        <option disabled value="">
          {{ placeholder }}
        </option>

        <option
          v-for="opt in options"
          :key="opt.id"
          :value="opt.id"
        >
          {{ opt.name }}
        </option>
      </select>
    </div>

    <!-- Loader overlay -->
    <div
      v-if="loading"
      class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-60 z-20"
    >
      <img class="w-[50px]" :src="Loader" alt="loading..." />
    </div>
  </div>

  <div v-if="error" class="login-form-error block">{{ error }}</div>
</template>

<script>
import Loader from "@/Loaders/loader.gif";

export default {
  inheritAttrs: false,
  props: {
    modelValue: {
      type: [Number, String],
      default: null,
    },
    icon: {
      type: String,
      default: "fa-solid fa-caret-down",
    },
    options: {
      type: Array,
      default: () => [], // [{ id, name }]
    },
    error: String,
    loading: {
      type: Boolean,
      default: false,
    },
    placeholder: {
      type: String,
      default: "Select an option",
    },
  },
  data() {
    return {
      isFocused: false,
      Loader,
    };
  },
  computed: {
    internalValue: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit("update:modelValue", val);
      },
    },
  },
  methods: {
    focus() {
      this.$refs.select.focus();
    },
  },
};
</script>
