<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Calculator, Folder, GraduationCap, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const permissions = page.props.auth.user.permissions;

const navItems: { [key: string]: NavItem[] } = {
    platform: [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Calculator',
            href: '/test',
            icon: Calculator,
            items: [
                {
                    title: 'DFI R-Series',
                    href: '/test',
                    icon: LayoutGrid,
                },
                {
                    title: 'XN-Series Sysmex',
                    href: '/test',
                    icon: LayoutGrid,
                },
                {
                    title: 'CN-Series Sysmex',
                    href: '/test',
                    icon: LayoutGrid,
                },
            ],
        }
    ],
    admin: [
        {
            title: 'Data Master',
            href: '/master-data',
            icon: GraduationCap,
            permission: 'master-data',
        },
    ],
    footer: [
        {
            title: 'Github Repo',
            href: 'https://github.com/laravel/vue-starter-kit',
            icon: Folder,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits',
            icon: BookOpen,
        },
    ],
};

function filterNavItems(items: NavItem[]): NavItem[] {
    return items.filter(item => !item.permission || permissions.includes(item.permission));
}
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <template v-for="(items, key) in navItems" :key="key">
                <NavMain v-if="key !== 'footer' && filterNavItems(items).length > 0" :label="String(key).charAt(0).toUpperCase() + String(key).slice(1)" :items="filterNavItems(items)"/>
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="filterNavItems(navItems.footer)" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
