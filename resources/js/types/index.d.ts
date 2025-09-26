import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    role_id: number | null;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    house_owner?: HouseOwner & { id: number };
    tenant?: Tenant & { id: number };
}

export interface Role {
    id: number;
    name: string;
    description: string;
}

export interface HouseOwner {
    name: string;
    email: string;
    address: string;
    city: string;
    zip: string;
}

export interface Tenant {
    name: string;
    email: string;
    address: string;
    city: string;
    zip: string;
    house_owner_id: number | null;
}

export interface Building {
    id: number;
    name: string;
    house_owner_id: number;
    address: string;
    created_at: string;
    updated_at: string;
    house_owner_name?: string;
    house_owner?: HouseOwner & { id: number };
    flats?: Flat[];
}

export interface Flat {
    id: number;
    name: string;
    building_id: number;
    tenant_id: number | null;
    house_owner_id: number | null;
    created_at: string;
    updated_at: string;
    building_name?: string;
    building?: Building & { id: number };
    tenant_name?: string;
    tenant?: Tenant & { id: number };
    house_owner_name?: string;
    house_owner?: HouseOwner & { id: number };
}

export interface BillCategory {
    id: number;
    name: string;
    description: string | null;
    building_owner_id: number;
    created_at: string;
    updated_at: string;
    house_owner_name?: string;
    house_owner?: HouseOwner & { id: number };
}

export type BreadcrumbItemType = BreadcrumbItem;
