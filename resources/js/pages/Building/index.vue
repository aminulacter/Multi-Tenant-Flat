<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '../../routes';
import { index as buildings, create as createBuilding } from '../../routes/buildings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from "vue";
import Vue3Datatable from "@bhplugin/vue3-datatable";
import "@bhplugin/vue3-datatable/dist/style.css";

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Buildings', href: buildings().url }
];

const cols = [
    { field: 'id', title: 'ID', sort: true },
    { field: 'name', title: 'Name', sort: true },
    { field: 'house_owner_name', title: 'House Owner', sort: true },
    { field: 'address', title: 'Address', sort: true },
    { field: 'actions', title: 'Actions', sort: false }
];

const rows = ref([]);
const loading = ref(false);
const pagination = ref({
    total: 0,
    per_page: 10,
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0
});

const fetchBuildings = async (page = 1) => {
    loading.value = true;
    try {
        const response = await fetch(`/getbuildings?page=${page}`);
        const data = await response.json();
        rows.value = data.data;
        pagination.value = data.pagination;
    } catch (error) {
        console.error('Error fetching buildings:', error);
    } finally {
        loading.value = false;
    }
};

const deleteBuilding = (building: any) => {
    if (confirm('Are you sure you want to delete this building?')) {
        router.delete(`/buildings/${building.id}`, {
            onSuccess: () => {
                // Refresh the table after successful deletion
                fetchBuildings();
            },
            onError: (errors) => {
                console.error('Error deleting building:', errors);
            }
        });
    }
};

onMounted(() => {
    fetchBuildings();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Buildings" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Buildings</h1>
                            <Link 
                                :href="createBuilding().url" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors"
                            >
                                Create Building
                            </Link>
                        </div>

                        <div v-if="loading" class="text-center py-4">
                            Loading buildings...
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
                            @page-change="(page: number) => fetchBuildings(page)"
                        >
                            <template #actions="data">
                                <div class="flex items-center gap-2">
                                    <Link :href="`/buildings/${data.value.id}/edit`" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">Edit</Link>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors" @click="deleteBuilding(data.value)">Delete</button>
                                </div>
                            </template>
                        </Vue3Datatable>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
