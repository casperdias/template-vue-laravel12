<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
    {
        title: 'User',
        href: '/master-data/users',
    }
]

defineProps({
    users: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <Head title="User Data" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            {{ users }}
        </div>
    </AppLayout>
</template>
