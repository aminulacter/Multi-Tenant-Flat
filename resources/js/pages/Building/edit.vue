<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '../../routes';
import { index as buildings } from '../../routes/buildings';
import { type BreadcrumbItem, type Building, type HouseOwner } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const page = usePage();
const building = page.props.building as Building;
const houseOwners = (page.props.houseOwners as HouseOwner[]) || [];

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Buildings', href: buildings().url },
    { title: 'Edit Building', href: '#' }
];

const form = ref({
    name: '',
    house_owner_id: null as number | null,
    address: '',
});

const errors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

// Initialize form with building data
onMounted(() => {
    form.value.name = building.name || ''
    form.value.house_owner_id = building.house_owner_id || null
    form.value.address = building.address || ''
});

const submitForm = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        const formData = {
            name: form.value.name,
            house_owner_id: form.value.house_owner_id,
            address: form.value.address,
        };

        router.put(`/buildings/${building.id}`, formData, {
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
    form.value.name = building.name || ''
    form.value.house_owner_id = building.house_owner_id || null
    form.value.address = building.address || ''
    errors.value = {};
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Edit Building" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Edit Building</h1>
                            <Link 
                                :href="buildings().url" 
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Back
                            </Link>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Building Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Building Name *</label>
                                        <input
                                            type="text"
                                            id="name"
                                            v-model="form.name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.name }"
                                        />
                                        <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                                    </div>

                                    <div>
                                        <label for="house_owner_id" class="block text-sm font-medium text-gray-700">House Owner *</label>
                                        <select
                                            id="house_owner_id"
                                            v-model="form.house_owner_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.house_owner_id }"
                                        >
                                            <option value="">Select a house owner</option>
                                            <option v-for="owner in houseOwners" :key="owner.id" :value="owner.id">
                                                {{ owner.name }}
                                            </option>
                                        </select>
                                        <p v-if="errors.house_owner_id" class="mt-1 text-sm text-red-600">{{ errors.house_owner_id }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input
                                        type="text"
                                        id="address"
                                        v-model="form.address"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.address }"
                                    />
                                    <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 pt-6 border-t">
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
                                    {{ isSubmitting ? 'Updating...' : 'Update Building' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
