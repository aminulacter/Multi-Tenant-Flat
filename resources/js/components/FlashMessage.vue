<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { CheckCircle, AlertCircle, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);

const flashMessage = computed(() => {
    return page.props.flash as {
        success?: string;
        error?: string;
        warning?: string;
        info?: string;
    } || {};
});

const messageType = computed(() => {
    if (flashMessage.value.success) return 'success';
    if (flashMessage.value.error) return 'error';
    if (flashMessage.value.warning) return 'warning';
    if (flashMessage.value.info) return 'info';
    return null;
});

const messageText = computed(() => {
    return flashMessage.value.success || 
           flashMessage.value.error || 
           flashMessage.value.warning || 
           flashMessage.value.info || '';
});

const alertVariant = computed(() => {
    switch (messageType.value) {
        case 'success': return 'default';
        case 'error': return 'destructive';
        case 'warning': return 'default';
        case 'info': return 'default';
        default: return 'default';
    }
});

const alertIcon = computed(() => {
    switch (messageType.value) {
        case 'success': return CheckCircle;
        case 'error': return AlertCircle;
        case 'warning': return AlertCircle;
        case 'info': return AlertCircle;
        default: return AlertCircle;
    }
});

const alertTitle = computed(() => {
    switch (messageType.value) {
        case 'success': return 'Success!';
        case 'error': return 'Error!';
        case 'warning': return 'Warning!';
        case 'info': return 'Info';
        default: return '';
    }
});

const alertClasses = computed(() => {
    switch (messageType.value) {
        case 'success': return 'border-green-200 bg-green-50 text-green-800';
        case 'error': return 'border-red-200 bg-red-50 text-red-800';
        case 'warning': return 'border-yellow-200 bg-yellow-50 text-yellow-800';
        case 'info': return 'border-blue-200 bg-blue-50 text-blue-800';
        default: return '';
    }
});

// Watch for flash message changes
watch(flashMessage, (newFlash) => {
    if (messageText.value) {
        show.value = true;
        // Auto-hide after 5 seconds
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
}, { deep: true });

onMounted(() => {
    if (messageText.value) {
        show.value = true;
        // Auto-hide after 5 seconds
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
});

const closeMessage = () => {
    show.value = false;
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
    >
        <div
            v-if="show && messageText"
            class="fixed top-4 right-4 z-50 max-w-sm w-full"
        >
            <Alert :variant="alertVariant" :class="alertClasses">
                <component :is="alertIcon" class="size-4" />
                <AlertTitle>{{ alertTitle }}</AlertTitle>
                <AlertDescription class="flex-1">
                    {{ messageText }}
                </AlertDescription>
                <button
                    @click="closeMessage"
                    class="ml-2 p-1 hover:bg-black/10 rounded-full transition-colors"
                >
                    <X class="size-4" />
                </button>
            </Alert>
        </div>
    </Transition>
</template>
