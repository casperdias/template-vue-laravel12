<script setup lang="ts">
import TablePagination from '@/components/TablePagination.vue';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationData, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    role: Role;
    permissions: PaginationData<Permission>;
}>();

interface Role {
    id: number;
    name: string;
    display_name: string;
}

interface Permission {
    id: number;
    name: string;
    display_name: string;
    description: string;
    status: boolean;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
    {
        title: 'Role',
        href: '/master-data/roles',
    },
    {
        title: `${props.role.display_name}`,
        href: `/master-data/roles/${props.role.id}/setting`,
    },
];

const searchTerm = ref(route().params.search || '');
let searchTimeout: ReturnType<typeof setTimeout>;

watch(searchTerm, (newTerm) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            props.permissions.path,
            {
                search: newTerm,
            },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    }, 500);
});

const setupPermissionWatchers = () => {
    props.permissions.data.forEach((permission) => {
        watch(
            () => permission.status,
            (newStatus) => {
                router.post(
                    `${props.permissions.path}/${permission.id}`,
                    {
                        status: newStatus,
                        page: props.permissions.current_page,
                        search: searchTerm.value,
                    },
                    {
                        preserveState: true,
                        preserveScroll: true,
                    },
                );
            },
        );
    });
};

setupPermissionWatchers();

watch(
    () => props.permissions.data,
    () => {
        setupPermissionWatchers();
    },
    { deep: true },
);
</script>

<template>
    <Head title="Role Permission Setting" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative w-full max-w-sm items-center">
                <Input id="search" type="text" name="search" placeholder="Search..." class="pl-10" v-model="searchTerm" />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="size-6 text-muted-foreground" />
                </span>
            </div>
            <TablePagination
                :columns="[
                    { key: 'name', label: 'Name' },
                    { key: 'display_name', label: 'Display Name' },
                    { key: 'description', label: 'Description' },
                ]"
                :data="permissions.data"
                :pagination="permissions"
                :caption="'Pengaturan Permission Role'"
                :actions="true"
                :routeName="permissions.path"
            >
                <template v-slot:actions="{ item }">
                    <Switch v-model="item.status" />
                </template>
            </TablePagination>
        </div>
    </AppLayout>
</template>
