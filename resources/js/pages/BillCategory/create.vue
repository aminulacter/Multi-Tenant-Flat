<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '../../routes';
import { index as billCategories } from '../../routes/bill-categories';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Bill Categories', href: billCategories().url },
    { title: 'Create Bill Category', href: '#' }
];

const isSubmitting = ref(false);
const errors = ref<Record<string, string>>({});

const form = reactive({
    name: '',
    description: '',
});

const submitForm = () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        router.post('/bill-categories', form, {
            onSuccess: () => {
                // Redirect handled by controller
            },
            onError: (errs) => {
                errors.value = errs;
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    } catch (error) {
        console.error('Form submission error:', error);
        isSubmitting.value = false;
    }
};

const resetForm = () => {
    form.name = '';
    form.description = '';
    errors.value = {};
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create Bill Category" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Create Bill Category</h1>
                            <Link 
                                :href="billCategories().url" 
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Back
                            </Link>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.name }"
                                    placeholder="Enter bill category name"
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.description }"
                                    placeholder="Enter description (optional)"
                                ></textarea>
                                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end space-x-4">
                                <button
                                    type="button"
                                    @click="resetForm"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md transition-colors"
                                >
                                    Reset
                                </button>
                                <button
                                    type="submit"
                                    :disabled="isSubmitting"
                                    class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white px-4 py-2 rounded-md transition-colors"
                                >
                                    {{ isSubmitting ? 'Creating...' : 'Create Bill Category' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
