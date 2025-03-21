<script setup lang="ts">
import DeleteItem from '@/components/DeleteItem.vue';
import FormDialog from '@/components/FormDialog.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/components/ui/pagination';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import Toaster from '@/components/ui/toast/Toaster.vue';
import { useToast } from '@/components/ui/toast/use-toast';
import AppLayout from '@/layouts/AppLayout.vue';
import { changePage } from '@/lib/helper';
import { PaginationData, User, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Pencil, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const { toast } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
    {
        title: 'User',
        href: '/master-data/user',
    },
];

interface Props {
    users: PaginationData<User>;
    roles: Record<string, string>;
    errors: any;
}

const props = defineProps<Props>();

const sendNotification = async (userId: number) => {
    try {
        const response = await axios.post(route('verification.send.id', { user: userId }));
        toast({
            title: 'Status Pengiriman',
            description: response.data.message,
        });
    } catch (error) {
        toast({
            title: 'Failed to send verification email',
            description: String(error),
        });
    }
};

const form = useForm({
    name: '',
    email: '',
    password: '',
    role_id: '',
});

const addDialog = ref(false);

const addUser = (users: PaginationData<User>) => {
    form.post(
        route('users.store', {
            page: users.current_page,
            search: searchTerm.value, // Preserve search
        }),
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                addDialog.value = false;
                form.reset();
            },
        },
    );
};

const deleteDialogOpen = ref(false);
const editDialogOpen = ref(false);
const selectedUser = ref<User | null>(null);

const openDeleteDialog = (user: User) => {
    selectedUser.value = user;
    deleteDialogOpen.value = true;
};

const deleteUser = (users: PaginationData<User>) => {
    const userName = selectedUser.value?.name;
    if (selectedUser.value) {
        router.delete(
            route('users.destroy', {
                user: selectedUser.value?.id,
                page: users.current_page,
                search: searchTerm.value, // Preserve search
            }),
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    deleteDialogOpen.value = false;
                    selectedUser.value = null;
                    toast({
                        title: 'User deleted',
                        description: 'User ' + userName + ' has been deleted successfully',
                    });
                },
                onError: (errors) => {
                    deleteDialogOpen.value = false;
                    selectedUser.value = null;
                    toast({
                        title: 'Error',
                        description: errors.id || 'An error occurred while deleting the user',
                    });
                },
            },
        );
    }
};

const openEditDialog = (user: User) => {
    selectedUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role_id = String(user.role?.id);
    editDialogOpen.value = true;
};

const editUser = (users: PaginationData<User>) => {
    if (selectedUser.value) {
        const userName = form.name;
        form.put(
            route('users.update', {
                user: selectedUser.value?.id,
                page: users.current_page,
                search: searchTerm.value, // Preserve search
            }),
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    editDialogOpen.value = false;
                    selectedUser.value = null;
                    form.reset();
                    toast({
                        title: 'User updated',
                        description: 'User ' + userName + ' has been updated successfully',
                    });
                },
            },
        );
    }
};

const searchTerm = ref(route().params.search || '');
let searchTimeout: ReturnType<typeof setTimeout>;

watch(searchTerm, (newTerm) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            props.users.path,
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
</script>

<template>
    <Toaster />
    <Head title="User Data" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative w-full max-w-sm items-center">
                <Input id="search" type="text" name="search" placeholder="Search..." class="pl-10" v-model="searchTerm" />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="size-6 text-muted-foreground" />
                </span>
            </div>
            <Button @click="addDialog = true" variant="outline">Tambah User</Button>
            <FormDialog
                v-model:open="addDialog"
                title="Tambah User"
                description="Masukkan Nama dan email"
                submitText="Create account"
                :processing="form.processing"
                :onSubmit="() => addUser(users)"
            >
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="role">Role</Label>
                    <Select v-model="form.role_id" id="role" required :tabindex="4">
                        <SelectTrigger>
                            <SelectValue v-if="form.role_id" :value="form.role_id">
                                {{ props.roles[form.role_id] }}
                            </SelectValue>
                            <SelectValue v-else> Select Role </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Roles</SelectLabel>
                                <SelectItem v-for="(role, id) in props.roles" :key="id" :value="id">
                                    {{ role }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.role_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>
            </FormDialog>
            <Table>
                <TableCaption>Data User Aplikasi</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="font-semibold text-black dark:text-white">Nama</TableHead>
                        <TableHead class="font-semibold text-black dark:text-white">Email</TableHead>
                        <TableHead class="font-semibold text-black dark:text-white">Role</TableHead>
                        <TableHead class="font-semibold text-black dark:text-white">Verifikasi Email</TableHead>
                        <TableHead class="font-semibold text-black dark:text-white">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in users.data" :key="user.id">
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>{{ user.role?.display_name }}</TableCell>
                        <TableCell v-if="user.email_verified_at">{{ user.email_verified_at }}</TableCell>
                        <TableCell v-else>
                            <form @submit.prevent="sendNotification(user.id)">
                                <Button type="submit">Kirim Verifikasi</Button>
                            </form>
                        </TableCell>
                        <TableCell class="flex gap-2">
                            <Button @click="openEditDialog(user)">
                                <Pencil />
                            </Button>
                            <Button @click="openDeleteDialog(user)">
                                <Trash2 />
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination
                v-slot="{ page }"
                :items-per-page="users.per_page"
                :total="users.total"
                :sibling-count="1"
                show-edges
                :default-page="users.current_page"
            >
                <PaginationList v-slot="{ items }" class="flex items-center justify-center gap-1">
                    <PaginationFirst @click="changePage(users.path, users.from)" />
                    <PaginationPrev @click="changePage(users.path, users.current_page - 1)" />

                    <template v-for="(item, index) in items">
                        <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                            <Button
                                class="h-10 w-10 p-0"
                                :variant="item.value === page ? 'default' : 'outline'"
                                @click="changePage(users.path, item.value)"
                            >
                                {{ item.value }}
                            </Button>
                        </PaginationListItem>
                        <PaginationEllipsis v-else :key="item.type" :index="index" />
                    </template>

                    <PaginationNext @click="changePage(users.path, users.current_page + 1)" />
                    <PaginationLast @click="changePage(users.path, users.last_page)" />
                </PaginationList>
            </Pagination>
        </div>
    </AppLayout>

    <!-- Edit Dialog -->
    <FormDialog
        v-model:open="editDialogOpen"
        title="Edit User"
        :description="`Edit ${selectedUser?.name || ''}`"
        submitText="Ubah"
        :processing="form.processing"
        :onSubmit="() => editUser(users)"
    >
        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="Email" />
            <InputError :message="form.errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="role">Role</Label>
            <Select v-model="form.role_id" id="role" required :tabindex="4">
                <SelectTrigger>
                    <SelectValue v-if="form.role_id" :value="form.role_id">
                        {{ props.roles[form.role_id] }}
                    </SelectValue>
                    <SelectValue v-else> Select Role </SelectValue>
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectLabel>Roles</SelectLabel>
                        <SelectItem v-for="(role, id) in props.roles" :key="id" :value="id">
                            {{ role }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
            <InputError :message="form.errors.role_id" />
        </div>
    </FormDialog>

    <!-- Delete Dialog -->
    <DeleteItem :open="deleteDialogOpen" :itemName="selectedUser?.name" @update:open="deleteDialogOpen = $event" @delete="deleteUser(users)" />
</template>
