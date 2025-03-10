<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Collapsible, CollapsibleTrigger, CollapsibleContent } from '@/components/ui/collapsible';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Calculator, Folder, GraduationCap, LayoutGrid, ChevronDown } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { ref } from 'vue';

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
                                <CollapsibleTrigger class="group flex items-center justify-between w-full p-2 space-x-2 cursor-pointer hover:bg-sidebar-accent rounded-md" @click="toggleMenu(String(index))">
                                    <div class="flex items-center space-x-2 text-sm">
                                        <component :is="item.icon" class="w-4 h-4" />
                                        <span class="flex-1">{{ item.title }}</span>
                                    </div>
                                    <ChevronDown :class="{ 'rotate-180': openMenus[index] }" class="transition-transform w-4 h-4" />
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenu class="pl-6">
                                        <SidebarMenuItem v-for="subItem in item.items" :key="subItem.title">
                                            <SidebarMenuButton as-child>
                                                <Link :href="subItem.href">
                                                    <component :is="subItem.icon" class="w-4 h-4" />
                                                    {{ subItem.title }}
                                                </Link>
                                            </SidebarMenuButton>
                                        </SidebarMenuItem>
                                    </SidebarMenu>
                                </CollapsibleContent>
                            </Collapsible>
                            <SidebarMenuItem v-else>
                                <SidebarMenuButton as-child>
                                    <Link :href="item.href">
                                        <component :is="item.icon" class="w-5 h-5" />
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
