<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Eye, ShieldPlus, User, UserCog } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
];

const cards = [
    {
        title: 'User',
        description: 'CRUD User dan Role User',
        icon: User,
        url: '/master-data/users',
        list: ['CRUD User', 'Pengaturan Role User'],
    },
    {
        title: 'Role',
        description: 'CRUD Role dan Permission Role',
        icon: UserCog,
        url: '/master-data/roles',
        list: ['CRUD Role', 'Pengaturan Permission Role'],
    },
    {
        title: 'Permission',
        description: 'CRUD Permission',
        icon: ShieldPlus,
        url: '/master-data/permissions',
        list: ['CRUD Permission'],
    },
];
</script>

<template>
    <Head title="Master" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card
                    v-for="(card, index) in cards"
                    :key="index"
                    class="relative h-fit w-[400px] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <CardHeader>
                        <CardTitle>{{ card.title }}</CardTitle>
                        <CardDescription>{{ card.description }}</CardDescription>
                    </CardHeader>
                    <CardContent class="flex items-center space-x-3">
                        <component :is="card.icon" class="text-primary-500 h-20 w-20" />
                        <ul class="flex list-disc flex-col gap-2 pl-5">
                            <li v-for="(item, index) in card.list" :key="index" class="text-sm font-bold">
                                {{ item }}
                            </li>
                        </ul>
                    </CardContent>
                    <CardFooter>
                        <Link class="w-full" :href="card.url">
                            <Button class="w-full" variant="default">
                                <Eye class="ml-2 h-6 w-6" />
                                Lihat Data
                            </Button>
                        </Link>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
