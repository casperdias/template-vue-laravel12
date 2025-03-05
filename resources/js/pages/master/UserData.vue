<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { User, PaginationData, type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { useToast } from '@/components/ui/toast/use-toast'
import Toaster from '@/components/ui/toast/Toaster.vue'
import axios from 'axios'
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/components/ui/pagination'

const { toast } = useToast()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
    {
        title: 'User',
        href: '/master-data/user',
    }
]

interface Props {
    users: PaginationData<User>
}

defineProps<Props>()

const sendNotification = async (userId: number) => {
    try {
        const response = await axios.post(route('verification.send.id', { user: userId }))
        toast({
            title: 'Status Pengiriman',
            description: response.data.message
        })
    } catch (error) {
        toast({
            title: 'Failed to send verification email',
            description: String(error)
        })
    }
}
</script>

<template>
    <Toaster />
    <Head title="User Data" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Table>
                <TableCaption>Data User Aplikasi</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nama</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Tanggal Dibuat</TableHead>
                        <TableHead>Verifikasi Email</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in users.data" :key="user.id">
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>{{ user.created_at }}</TableCell>
                        <TableCell v-if="user.email_verified_at">{{ user.email_verified_at }}</TableCell>
                        <TableCell v-else>
                            <form @submit.prevent="sendNotification(user.id)">
                                <Button type="submit">Kirim Verifikasi</Button>
                            </form>
                        </TableCell>
                        <TableCell class="flex gap-2">
                            <Link :href="route('users.edit', { user: user.id })">
                                <Button>Edit</Button>
                            </Link>
                            <Link :href="route('users.destroy', { user: user.id })">
                                <Button>Delete</Button>
                            </Link>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination v-slot="{ page }" :items-per-page="users.per_page" :total="users.total" :sibling-count="1" show-edges :default-page="users.current_page">
                <PaginationList v-slot="{ items }" class="flex items-center justify-center gap-1">
                    <PaginationFirst />
                    <PaginationPrev />

                    <template v-for="(item, index) in items">
                        <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                        <Button class="w-10 h-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                            {{ item.value }}
                        </Button>
                        </PaginationListItem>
                        <PaginationEllipsis v-else :key="item.type" :index="index" />
                    </template>

                    <PaginationNext />
                    <PaginationLast />
                </PaginationList>
            </Pagination>
        </div>
    </AppLayout>
</template>
