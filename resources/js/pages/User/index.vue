<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, onMounted } from "vue";
  import Vue3Datatable from "@bhplugin/vue3-datatable";
  import "@bhplugin/vue3-datatable/dist/style.css";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
onMounted(() => {
        getUsers();
    });
    const loading: any = ref(true);
    const total_rows = ref(0);
    const params = reactive({
        current_page: 1,
        pagesize: 10,
        sort_column: 'id',
        sort_direction: 'asc',
    });
    const rows: any = ref(null);
    const cols =
        ref([
            { field: 'id', title: 'ID', isUnique: true, type: 'number' },
            { field: 'name', title: 'Name' },
            { field: 'email', title: 'Email' },
            { field: 'role_name', title: 'Role' },
            { field: 'actions', title: 'Actions', sort: false },
        ]) || [];


        const getUsers = async () => {
        try {
            loading.value = true;

            const queryParams = new URLSearchParams({
                page: params.current_page.toString(),
                per_page: params.pagesize.toString(),
                sort_column: params.sort_column,
                sort_direction: params.sort_direction,
            });

            const response = await fetch(`/getusers?${queryParams}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch: ${response.status}`);
            }

            const data = await response.json();

            rows.value = data?.data;
            total_rows.value = data?.pagination?.total;
        } catch (error) {
            console.error('Error fetching users:', error);
        }

        loading.value = false;
    };
    const changeServer = (data: any) => {
        params.current_page = data.current_page;
        params.pagesize = data.pagesize;
        params.sort_column = data.sort_column;
        params.sort_direction = data.sort_direction;

        getUsers();
    };

    const deleteUser = (userId: number) => {
        if (confirm('Are you sure you want to delete this user?')) {
            router.delete(`/users/${userId}`, {
                onSuccess: () => {
                    // Refresh the table after successful deletion
                    getUsers();
                },
                onError: (errors) => {
                    console.error('Error deleting user:', errors);
                }
            });
        }
    };
</script>
<template>
   <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex justify-between items-center px-4 py-2">
            <h1>Users</h1>
            <a :href="`/users/create`" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors">Create User</a>
        </div>
        <vue3-datatable :rows="rows" :columns="cols"
            :loading="loading"
            :totalRows="total_rows"
            :isServerMode="true"
            :pageSize="params.pagesize"
            :sortable="true"
            :sortColumn="params.sort_column"
            :sortDirection="params.sort_direction"
            class="advanced-table whitespace-nowrap"
            @change="changeServer"
        >
        <template #id="data">
                <strong class="text-info">#{{ data.value.id }}</strong>
            </template>
            <template #name="data">
                <div class="flex items-center gap-2">
                    <div class="font-semibold">{{ data.value.name }}</div>
                </div>
            </template>
            
            <template #email="data">
                <a :href="`mailto:${data.value.email}`" class="text-primary hover:underline" @click.stop>{{ data.value.email }}</a>
            </template>
            <template #role_name="data">
                <div class="flex items-center gap-2">
                    <div class="text-gray-600">{{ data.value.role_name }}</div>
                </div>
            </template>
            <template #actions="data">
                <div class="flex items-center gap-2">
                    <Link :href="`/users/${data.value.id}/edit`" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">Edit</Link>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors" @click="deleteUser(data.value.id)">Delete</button>
                </div>
            </template>
        
        </vue3-datatable>
    </AppLayout>
   
</template>



<style scoped>

</style>