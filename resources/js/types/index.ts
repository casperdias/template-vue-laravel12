import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

type NavItemBase = {
    title: string;
    icon?: LucideIcon;
    isActive?: boolean;
    permission?: string;
};

export type NavItem =
    | (NavItemBase & { href: string; items?: never })
    | (NavItemBase & { items: NavItem[]; href?: never });

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role_id: number;
    role?: {
        id: number;
        display_name: string;
    };
    permissions: string[]
}

export interface PaginationData<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: { url: string | null; label: string; active: boolean }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export type BreadcrumbItemType = BreadcrumbItem;
