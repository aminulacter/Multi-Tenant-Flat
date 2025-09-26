<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '../routes';
import { index as users } from '../routes/users';
import { index as buildings } from '../routes/buildings';
import { index as flats } from '../routes/flats';
import { index as billCategories } from '../routes/bill-categories';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, User, Building, Home, Receipt } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';


const page = usePage();
const user = page.props.auth.user;

const mainNavItems = computed((): NavItem[] => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        }
    ];

    // Only show Users and Buildings links for SuperAdmin (role_id = 1)
    if (user && user.role_id === 1) {
        items.push(
            {
                title: 'Users',
                href: users(),
                icon: User,
            },
            {
                title: 'Buildings',
                href: buildings(),
                icon: Building,
            }
        );
    }

    // Show Flats link for SuperAdmin (role_id = 1) and House Owner (role_id = 2)
    if (user && (user.role_id === 1 || user.role_id === 2)) {
        items.push({
            title: 'Flats',
            href: flats(),
            icon: Home,
        });
    }

    // Show Bill Categories link for House Owner (role_id = 2) only
    if (user && user.role_id === 2) {
        items.push({
            title: 'Bill Categories',
            href: billCategories(),
            icon: Receipt,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
