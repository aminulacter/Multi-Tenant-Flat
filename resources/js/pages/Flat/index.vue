<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as flats, create as createFlat } from '@/routes/flats';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from "vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Flats', href: flats().url }
];

const cols = [
    { field: 'id', title: 'ID', sort: true },
    { field: 'name', title: 'Flat Name', sort: true },
    { field: 'building_name', title: 'Building', sort: true },
    { field: 'tenant_name', title: 'Tenant', sort: true },
    { field: 'house_owner_name', title: 'House Owner', sort: true },
    { field: 'actions', title: 'Actions', sort: false }
];

const rows = ref([]);
const loading = ref(true);
const pagination = ref({
    total: 0,
    per_page: 10,
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0
});

// Modal state
const showAllocateModal = ref(false);
const selectedFlat = ref<any>(null);
const availableTenants = ref<any[]>([]);
const selectedTenantId = ref<number | null>(null);
const loadingTenants = ref(false);

const fetchFlats = async (page = 1) => {
    loading.value = true;
    try {
        const response = await fetch(`/getflats?page=${page}`);
        const data = await response.json();
        rows.value = data.data;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Error fetching flats:', error);
    } finally {
        loading.value = false;
    }
};


const deleteFlat = (flat: any) => {
    if (confirm('Are you sure you want to delete this flat?')) {
        router.delete(`/flats/${flat.id}`, {
            onSuccess: () => {
                // Refresh the table after successful deletion
                fetchFlats();
            },
            onError: (errors) => {
                console.error('Error deleting flat:', errors);
            }
        });
    }
};

const openAllocateModal = async (flat: any) => {
    selectedFlat.value = flat;
    selectedTenantId.value = null;
    loadingTenants.value = true;
    showAllocateModal.value = true;

    try {
        // Fetch tenants for this specific flat's house owner
        const response = await fetch(`/getavailabletenants?house_owner_id=${flat.house_owner_id}`);
        const tenants = await response.json();
        availableTenants.value = tenants;
        
        if (tenants.length === 0) {
            alert('No available tenants to allocate for this house owner.');
            closeAllocateModal();
        }
    } catch (error) {
        console.error('Error fetching available tenants:', error);
        alert('Error fetching available tenants.');
        closeAllocateModal();
    } finally {
        loadingTenants.value = false;
    }
};

const closeAllocateModal = () => {
    showAllocateModal.value = false;
    selectedFlat.value = null;
    availableTenants.value = [];
    selectedTenantId.value = null;
};

const allocateTenant = () => {
    if (!selectedTenantId.value) {
        alert('Please select a tenant.');
        return;
    }

    router.post(`/flats/${selectedFlat.value.id}/allocate-tenant`, { 
        tenant_id: selectedTenantId.value 
    }, {
        onSuccess: () => {
            fetchFlats();
            closeAllocateModal();
        },
        onError: (errors) => {
            console.error('Error allocating tenant:', errors);
        }
    });
};

const removeTenant = (flat: any) => {
    if (confirm('Are you sure you want to remove the tenant from this flat?')) {
        router.delete(`/flats/${flat.id}/remove-tenant`, {
            onSuccess: () => {
                fetchFlats();
            },
            onError: (errors) => {
                console.error('Error removing tenant:', errors);
            }
        });
    }
};

onMounted(() => {
    fetchFlats();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Flats" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Flats</h1>
                            <Link 
                                :href="createFlat().url" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Create Flat
                            </Link>
                        </div>

                        <div v-if="loading" class="text-center py-4">
                            Loading flats...
                        </div>

                        <Vue3Datatable
                            :rows="rows"
                            :columns="cols"
                            :loading="loading"
                            :sortable="true"
                            :searchable="true"
                            :pageSize="pagination.per_page"
                            :pageSizeOptions="[10, 20, 50, 100]"
                            :showPageSize="true"
                            :showPageNumber="true"
                            :showPageInfo="true"
                            :showSearch="true"
                            :showEntries="true"
                            :showPagination="true"
                            :showRefresh="true"
                            :showExport="true"
                            :showImport="false"
                            :showColumnToggle="true"
                            :showRowSelect="true"
                            :showRowExpand="false"
                            :totalRows="pagination.total"
                            :currentPage="pagination.current_page"
                            :lastPage="pagination.last_page"
                            @page-change="(page: number) => fetchFlats(page)"
                        >
                            <template #actions="data">
                                <div class="flex items-center gap-2">
                                    <Link :href="`/flats/${data.value.id}/edit`" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">Edit</Link>
                                    <button v-if="!data.value.tenant_id" @click="openAllocateModal(data.value)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">Allocate Tenant</button>
                                    <button v-if="data.value.tenant_id" @click="removeTenant(data.value)" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm transition-colors">Remove Tenant</button>
                                    <button @click="deleteFlat(data.value)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors">Delete</button>
                                </div>
                            </template>
                        </Vue3Datatable>
                    </div>
                </div>
            </div>
        </div>

        <!-- Allocate Tenant Modal -->
        <div v-if="showAllocateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Allocate Tenant</h3>
                        <button @click="closeAllocateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Flat Details -->
                    <div v-if="selectedFlat" class="mb-4 p-3 bg-gray-50 rounded-md">
                        <h4 class="font-medium text-gray-900 mb-2">Flat Details</h4>
                        <div class="text-sm text-gray-600">
                            <p><strong>Flat Name:</strong> {{ selectedFlat.name }}</p>
                            <p><strong>Building:</strong> {{ selectedFlat.building_name }}</p>
                            <p><strong>House Owner:</strong> {{ selectedFlat.house_owner_name }}</p>
                        </div>
                    </div>

                    <!-- Tenant Selection -->
                    <div class="mb-4">
                        <label for="tenant-select" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Tenant
                        </label>
                        <select
                            id="tenant-select"
                            v-model="selectedTenantId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            :disabled="loadingTenants"
                        >
                            <option value="">
                                {{ loadingTenants ? 'Loading tenants...' : 'Select a tenant' }}
                            </option>
                            <option v-for="tenant in availableTenants" :key="tenant.id" :value="tenant.id">
                                {{ tenant.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end space-x-3">
                        <button
                            @click="closeAllocateModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="allocateTenant"
                            :disabled="!selectedTenantId"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                        >
                            Allocate Tenant
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
