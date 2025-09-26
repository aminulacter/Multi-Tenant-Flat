<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Role, HouseOwner, Tenant, User } from '@/types'

interface UserFormData {
    name: string
    email: string
    password: string
    password_confirmation: string
    role_id: number | null
    house_owner: HouseOwner
    tenant: Tenant
}

const page = usePage()
const user = page.props.user as User
const roles = (page.props.roles as Role[]) || []
const houseOwners = (page.props.houseOwners as any[]) || []

// Form data
const form = ref<UserFormData>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: null,
    house_owner: {
        name: '',
        email: '',
        address: '',
        city: '',
        zip: ''
    } as HouseOwner,
    tenant: {
        name: '',
        email: '',
        address: '',
        city: '',
        zip: '',
        house_owner_id: null as number | null
    } as Tenant
})

// Form state
const errors = ref({})
const isSubmitting = ref(false)

// Computed properties
const selectedRole = computed(() => {
    return roles.find(role => role.id == form.value.role_id)
})

const isHouseOwner = computed(() => {
    return selectedRole.value?.name === 'House Owner'
})

const isTenant = computed(() => {
    return selectedRole.value?.name === 'Tenant'
})

// Initialize form with user data
onMounted(() => {
    console.log('user', user)
    form.value.name = user.name || ''
    form.value.email = user.email || ''
    form.value.role_id = user.role_id || null
    
    // Pre-fill role-specific data if it exists
    if (user.house_owner) {
        form.value.house_owner = {
            name: user.house_owner.name || '',
            email: user.house_owner.email || '',
            address: user.house_owner.address || '',
            city: user.house_owner.city || '',
            zip: user.house_owner.zip || ''
        }
    }
    
    if (user.tenant) {
        form.value.tenant = {
            name: user.tenant.name || '',
            email: user.tenant.email || '',
            address: user.tenant.address || '',
            city: user.tenant.city || '',
            zip: user.tenant.zip || '',
            house_owner_id: user.tenant.house_owner_id ? Number(user.tenant.house_owner_id) : null
        }
    }
    console.log('form', form.value)
    console.log('form.tenant', form.value.tenant)
    console.log('user.tenant', user.tenant)
})

// Watch for user data changes to update form
watch(() => user, (newUser) => {
    if (newUser) {
        form.value.name = newUser.name || ''
        form.value.email = newUser.email || ''
        form.value.role_id = newUser.role_id || null
        
        // Pre-fill role-specific data if it exists
        if (newUser.house_owner) {
            form.value.house_owner = {
                name: newUser.house_owner.name || '',
                email: newUser.house_owner.email || '',
                address: newUser.house_owner.address || '',
                city: newUser.house_owner.city || '',
                zip: newUser.house_owner.zip || ''
            }
        }
        
        if (newUser.tenant) {
            form.value.tenant = {
                name: newUser.tenant.name || '',
                email: newUser.tenant.email || '',
                address: newUser.tenant.address || '',
                city: newUser.tenant.city || '',
                zip: newUser.tenant.zip || '',
                house_owner_id: newUser.tenant.house_owner_id ? Number(newUser.tenant.house_owner_id) : null
            }
        }
    }
}, { immediate: true, deep: true })

// Watch for role changes to reset role-specific data
watch(() => form.value.role_id, (newRoleId, oldRoleId) => {
    if (newRoleId !== oldRoleId) {
        // Reset role-specific data when role changes
        form.value.house_owner = {
            name: '',
            email: '',
            address: '',
            city: '',
            zip: ''
        } as HouseOwner
        form.value.tenant = {
            name: '',
            email: '',
            address: '',
            city: '',
            zip: '',
            house_owner_id: null
        } as Tenant
    }
})

// Methods
const submitForm = async () => {
    isSubmitting.value = true
    errors.value = {}

    try {
        const formData: any = {
            name: form.value.name,
            email: form.value.email,
            role_id: form.value.role_id
        }

        // Only include password if provided
        if (form.value.password) {
            formData.password = form.value.password
            formData.password_confirmation = form.value.password_confirmation
        }

        // Add role-specific data
        if (isHouseOwner.value) {
            formData.house_owner = {
                name: form.value.name,
                email: form.value.email,
                address: form.value.house_owner.address,
                city: form.value.house_owner.city,
                zip: form.value.house_owner.zip
            }
        } else if (isTenant.value) {
            formData.tenant = {
                name: form.value.name,
                email: form.value.email,
                address: form.value.tenant.address,
                city: form.value.tenant.city,
                zip: form.value.tenant.zip,
                house_owner_id: form.value.tenant.house_owner_id
            }
        }

        await router.put(`/users/${user.id}`, formData, {
            onError: (errs) => {
                errors.value = errs
            },
            onSuccess: () => {
                // The controller will handle the redirect with flash message
            }
        })
    } catch (error) {
        console.error('Form submission error:', error)
    } finally {
        isSubmitting.value = false
    }
}

const resetForm = () => {
    form.value = {
        name: user.name || '',
        email: user.email || '',
        password: '',
        password_confirmation: '',
        role_id: user.role_id || null,
        house_owner: {
            name: user.house_owner?.name || '',
            email: user.house_owner?.email || '',
            address: user.house_owner?.address || '',
            city: user.house_owner?.city || '',
            zip: user.house_owner?.zip || ''
        } as HouseOwner,
        tenant: {
            name: user.tenant?.name || '',
            email: user.tenant?.email || '',
            address: user.tenant?.address || '',
            city: user.tenant?.city || '',
            zip: user.tenant?.zip || '',
            house_owner_id: user.tenant?.house_owner_id ? Number(user.tenant.house_owner_id) : null
        } as Tenant
    }
    errors.value = {}
}
</script>

<template>
    <Head :title="`Edit User - ${user.name}`" />
    
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex justify-between items-center px-4 py-2">
                            <h2 class="text-2xl font-bold text-gray-900">Edit User</h2>
                            <a class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors" :href="`/users`">Back</a>
                        </div>
                        
                        <p class="mt-1 text-sm text-gray-600">Update the user information below.</p>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Basic User Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                            
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': errors.name }"
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': errors.email }"
                                />
                                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': errors.password }"
                                    placeholder="Leave blank to keep current password"
                                />
                                <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
                            </div>

                            <!-- Password Confirmation -->
                            <div v-if="form.password">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role_id" class="block text-sm font-medium text-gray-700">Role *</label>
                                <select
                                    id="role_id"
                                    v-model="form.role_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': errors.role_id }"
                                >
                                    <option value="">Select a role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <p v-if="errors.role_id" class="mt-1 text-sm text-red-600">{{ errors.role_id }}</p>
                            </div>
                        </div>

                        <!-- House Owner Information -->
                        <div v-if="isHouseOwner" class="space-y-4 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900">House Owner Information</h3>
                            <p class="text-sm text-gray-600">Name and email will be taken from the basic information above.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="house_owner_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input
                                        id="house_owner_address"
                                        v-model="form.house_owner.address"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="house_owner_city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input
                                        id="house_owner_city"
                                        v-model="form.house_owner.city"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="house_owner_zip" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                                    <input
                                        id="house_owner_zip"
                                        v-model="form.house_owner.zip"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Information -->
                        <div v-if="isTenant" class="space-y-4 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Tenant Information</h3>
                            <p class="text-sm text-gray-600">Name and email will be taken from the basic information above.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="tenant_house_owner" class="block text-sm font-medium text-gray-700">House Owner</label>
                                    <select
                                        id="tenant_house_owner"
                                        v-model="form.tenant.house_owner_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                        <option value="">Select a house owner (optional)</option>
                                        <option v-for="owner in houseOwners" :key="owner.id" :value="owner.id">
                                            {{ owner.name || owner.user.name }}
                                        </option>
                                        
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="tenant_address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input
                                        id="tenant_address"
                                        v-model="form.tenant.address"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="tenant_city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input
                                        id="tenant_city"
                                        v-model="form.tenant.city"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>

                                <div>
                                    <label for="tenant_zip" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                                    <input
                                        id="tenant_zip"
                                        v-model="form.tenant.zip"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                            <button
                                type="button"
                                @click="resetForm"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Reset
                            </button>
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="isSubmitting">Updating...</span>
                                <span v-else>Update User</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional custom styles if needed */
</style>
