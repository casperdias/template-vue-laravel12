<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, ChevronDown, FileCode, FileSpreadsheet, Folder, GraduationCap, LayoutGrid } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const permissions = page.props.auth.user.permissions;
const openMenus = ref<Record<string, boolean>>({});

const navItems: { [key: string]: NavItem[] } = {
    platform: [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Calculator',
            icon: FileCode,
            items: [
                {
                    title: 'DFI R-Series',
                    href: '/up',
                    icon: FileSpreadsheet,
                },
                {
                    title: 'XN-Series Sysmex',
                    href: '/up',
                    icon: FileSpreadsheet,
                },
                {
                    title: 'CN-Series Sysmex',
                    href: '/up',
                    icon: FileSpreadsheet,
                },
            ],
        },
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
    return items.filter((item) => !item.permission || permissions.includes(item.permission));
}

function toggleMenu(index: string) {
    openMenus.value[index] = !openMenus.value[index];
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
                <template v-if="key !== 'footer' && filterNavItems(items).length > 0">
                    <h4 class="px-2 text-sm font-semibold">{{ String(key).charAt(0).toUpperCase() + String(key).slice(1) }}</h4>
                    <SidebarMenu>
                        <template v-for="(item, index) in filterNavItems(items)" :key="item.title">
                            <Collapsible v-if="item.items" v-model="openMenus[index]">
                                <CollapsibleTrigger
                                    class="group flex w-full cursor-pointer items-center justify-between rounded-md hover:bg-sidebar-accent"
                                    @click="toggleMenu(String(index))"
                                >
                                    <SidebarMenuButton class="flex items-center text-sm">
                                        <component :is="item.icon" class="h-4 w-4" />
                                        <span class="flex-1">{{ item.title }}</span>
                                    </SidebarMenuButton>
                                    <ChevronDown :class="{ 'rotate-180': openMenus[index] }" class="h-4 w-4 transition-transform" />
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenu class="pl-6">
                                        <SidebarMenuItem v-for="subItem in item.items" :key="subItem.title">
                                            <SidebarMenuButton as-child :is-active="page.url.startsWith(subItem.href || '')">
                                                <Link :href="subItem.href || '#'">
                                                    <component :is="subItem.icon" class="h-4 w-4" />
                                                    {{ subItem.title }}
                                                </Link>
                                            </SidebarMenuButton>
                                        </SidebarMenuItem>
                                    </SidebarMenu>
                                </CollapsibleContent>
                            </Collapsible>
                            <SidebarMenuItem v-else>
                                <SidebarMenuButton as-child :is-active="page.url.startsWith(item.href)">
                                    <Link :href="item.href">
                                        <component :is="item.icon" class="h-5 w-5" />
                                        {{ item.title }}
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </template>
                    </SidebarMenu>
                </template>
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="filterNavItems(navItems.footer)" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
