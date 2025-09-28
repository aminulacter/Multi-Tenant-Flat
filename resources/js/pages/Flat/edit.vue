<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as flats } from '@/routes/flats';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, onMounted, watch } from 'vue';

interface Building {
    id: number;
    name: string;
    house_owner_id: number;
}

interface Tenant {
    id: number;
    name: string;
    user_id: number;
}

interface Flat {
    id: number;
    name: string;
    building_id: number;
    tenant_id: number | null;
    house_owner_id: number | null;
}

interface Props {
    flat: Flat;
    buildings: Building[];
    tenants: Tenant[];
}

const props = defineProps<Props>();
const { flat } = props;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Flats', href: flats().url },
    { title: 'Edit Flat', href: '#' }
];

const isSubmitting = ref(false);
const errors = ref<Record<string, string>>({});
const availableTenants = ref<any[]>([]);
const loadingTenants = ref(false);

const form = reactive({
    name: '',
    building_id: null as number | null,
    tenant_id: null as number | null,
});

const submitForm = () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        const formData = {
            name: form.name,
            building_id: form.building_id,
            tenant_id: form.tenant_id,
        };

        router.put(`/flats/${flat.id}`, formData, {
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

const fetchTenantsForBuilding = async (buildingId: number) => {
    if (!buildingId) {
        availableTenants.value = [];
        return;
    }

    loadingTenants.value = true;
    try {
        // Find the building to get house_owner_id
        const building = props.buildings.find(b => b.id === buildingId);
        if (building) {
            const response = await fetch(`/getavailabletenants?house_owner_id=${building.house_owner_id}`);
            const data = await response.json();
            // Add current tenant if not in the list
            if (flat.tenant_id && !data.find((t: any) => t.id === flat.tenant_id)) {
                const currentTenant = props.tenants.find(t => t.id === flat.tenant_id);
                if (currentTenant) {
                    data.push(currentTenant);
                }
            }
            availableTenants.value = data;
        }
    } catch (error) {
        console.error('Error fetching tenants:', error);
        availableTenants.value = [];
    } finally {
        loadingTenants.value = false;
    }
};

// Watch for building selection changes
watch(() => form.building_id, (newBuildingId) => {
    if (newBuildingId) {
        fetchTenantsForBuilding(newBuildingId);
    } else {
        availableTenants.value = [];
    }
});

const resetForm = () => {
    form.name = flat.name || '';
    form.building_id = flat.building_id || null;
    form.tenant_id = flat.tenant_id || null;
    availableTenants.value = [];
    errors.value = {};
};

onMounted(() => {
    resetForm();
    // Load tenants for the current building
    if (flat.building_id) {
        fetchTenantsForBuilding(flat.building_id);
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Edit Flat" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Edit Flat</h1>
                            <Link 
                                :href="flats().url" 
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Back
                            </Link>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <!-- Flat Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Flat Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.name }"
                                    placeholder="Enter flat name"
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <!-- Building -->
                            <div>
                                <label for="building_id" class="block text-sm font-medium text-gray-700">
                                    Building <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="building_id"
                                    v-model="form.building_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.building_id }"
                                >
                                    <option value="">Select a building</option>
                                    <option v-for="building in buildings" :key="building.id" :value="building.id">
                                        {{ building.name }}
                                    </option>
                                </select>
                                <p v-if="errors.building_id" class="mt-1 text-sm text-red-600">{{ errors.building_id }}</p>
                            </div>

                            <!-- Tenant (Optional) -->
                            <div>
                                <label for="tenant_id" class="block text-sm font-medium text-gray-700">
                                    Tenant (Optional)
                                </label>
                                <select
                                    id="tenant_id"
                                    v-model="form.tenant_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.tenant_id }"
                                    :disabled="!form.building_id || loadingTenants"
                                >
                                    <option value="">
                                        {{ !form.building_id ? 'Select a building first' : loadingTenants ? 'Loading tenants...' : 'Select a tenant (optional)' }}
                                    </option>
                                    <option v-for="tenant in availableTenants" :key="tenant.id" :value="tenant.id">
                                        {{ tenant.name }}
                                    </option>
                                </select>
                                <p v-if="errors.tenant_id" class="mt-1 text-sm text-red-600">{{ errors.tenant_id }}</p>
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
                                    {{ isSubmitting ? 'Updating...' : 'Update Flat' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
