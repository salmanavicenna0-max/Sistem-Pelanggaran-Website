<script setup>
import { ref } from 'vue';

const count = ref(0);
const props = defineProps({
    title: {
        type: String,
        default: 'Komponen Vue Siap Digunakan'
    }
});
</script>

<template>
    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <h3 class="font-semibold text-sm text-[#182A3C]">{{ title }}</h3>
        </div>
        <p class="text-xs text-[#64748B] mb-3">
            Vue 3 + Vite scaffolding aktif dan terintegrasi dengan baik di sistem ini.
        </p>
        <div class="flex items-center gap-3">
            <button
                @click="count++"
                type="button"
                class="px-3 py-1.5 text-xs font-medium bg-[#044A87] text-white rounded-lg hover:bg-[#0360A4] transition-colors"
            >
                Klik Uji Reaktif: {{ count }}
            </button>
            <span v-if="count > 0" class="text-xs text-emerald-600 font-medium">
                Reaktivitas Vue berfungsi!
            </span>
        </div>
    </div>
</template>
