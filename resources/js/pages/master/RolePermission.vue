<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
    {
        title: 'Role',
        href: '/master-data/role',
    },
    {
        title: 'Role Permission Setting',
        href: ''
    }
]
</script>

<template>
    <Head title="Role Permission Setting" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        </div>
    </AppLayout>
</template>
