<script setup lang="ts">
import { ref, watch } from "vue";
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationData, type BreadcrumbItem } from '@/types';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import DeleteItem from '@/components/DeleteItem.vue'
import FormDialog from '@/components/FormDialog.vue'
import InputError from '@/components/InputError.vue';
import { useToast } from '@/components/ui/toast/use-toast'
import Toaster from '@/components/ui/toast/Toaster.vue'
import TablePagination from "@/components/TablePagination.vue";
import { Search } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
    {
        title: 'Role',
        href: '/master-data/role',
    }
]

const { toast } = useToast()

defineProps<Props>()

interface Props {
    roles: PaginationData<Role>
    errors: any
}

interface Role {
    id: number
    name: string
    display_name: string
    description: string
}

const form = useForm({
    name: '',
    display_name: '',
    description: '',
});

const addDialog = ref(false);

const addRole = (roles: PaginationData<Role>) => {
    form.post(route('roles.store', {
        page: roles.current_page,
        search: searchTerm.value // Preserve search
    }),
    {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            addDialog.value = false;
            form.reset();
            toast({
                title: 'Success',
                description: 'Role created successfully',
            })
        },
    });
};

const deleteDialogOpen = ref(false);
const editDialogOpen = ref(false);
const selectedRole = ref<Role | null>(null);

const openDeleteDialog = (role: Role) => {
    selectedRole.value = role;
    deleteDialogOpen.value = true;
};

const deleteRole = (roles: PaginationData<Role>) => {
    const roleName = selectedRole.value?.name;
    if (selectedRole.value) {
        router.delete(route('roles.destroy', {
            role: selectedRole.value?.id,
            page: roles.current_page,
            search: searchTerm.value // Preserve search
        }),
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                deleteDialogOpen.value = false;
                selectedRole.value = null;
                toast({
                    title: 'Role deleted',
                    description: 'Role ' + roleName + ' has been deleted successfully',
                });
            },
            onError: (errors) => {
                deleteDialogOpen.value = false;
                selectedRole.value = null;
                toast({
                    title: 'Error',
                    description: errors.id || 'An error occurred while deleting the Role',
                });
            },
        });
    }
};

const openEditDialog = (role: Role) => {
    selectedRole.value = role;
    form.name = role.name;
    form.display_name = role.display_name;
    form.description = role.description;
    editDialogOpen.value = true;
};

const editRole = (roles: PaginationData<Role>) => {
    if (selectedRole.value) {
        form.put(route('roles.update', {
            role: selectedRole.value?.id,
            page: roles.current_page,
            search: searchTerm.value // Preserve search
        }),
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                editDialogOpen.value = false;
                selectedRole.value = null;
                form.reset();
                toast({
                    title: 'Success',
                    description: 'Role updated successfully',
                });
            },
        });
    }
};

const searchTerm = ref(route().params.search || "");
let searchTimeout: ReturnType<typeof setTimeout>;

watch(searchTerm, (newTerm) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route("roles.index", {
            search: newTerm
        }));
    }, 300);
});
</script>

<template>
    <Toaster />
    <Head title="Role Data" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative w-full max-w-sm items-center">
                <Input id="search" type="text" name="search" placeholder="Search..." class="pl-10" v-model="searchTerm" />
                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                    <Search class="size-6 text-muted-foreground" />
                </span>
            </div>
            <Button @click="addDialog = true" variant="outline">Tambah Role</Button>
            <FormDialog
                v-model:open="addDialog"
                title="Tambah Role"
                description="Masukkan Nama dan Deskripsi"
                submitText="Create Role"
                :processing="form.processing"
                :onSubmit="() => addRole(roles)"
            >
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        error-message="name"
                    />
                    <InputError v-if="errors.name" :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="display_name">Role</Label>
                    <Input
                        id="display_name"
                        v-model="form.display_name"
                        error-message="display_name"
                    />
                    <InputError v-if="errors.display_name" :message="errors.display_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Input
                        id="description"
                        v-model="form.description"
                        error-message="description"
                    />
                    <InputError v-if="errors.description" :message="errors.description" />
                </div>
            </FormDialog>
            <TablePagination
                :columns="[
                    { key: 'name', label: 'Name' },
                    { key: 'display_name', label: 'Display Name' },
                    { key: 'description', label: 'Description' },
                ]"
                :data="roles.data"
                :pagination="roles"
                :caption="'Role Data'"
                :actions="true"
                routeName="roles.index"
            >
                <template #actions="{ item }">
                    <Button @click="openEditDialog(item)">Edit</Button>
                    <Button @click="openDeleteDialog(item)">Delete</Button>
                    <Link :href="route('dashboard')">
                        <Button>Atur Permission</Button>
                    </Link>
                </template>
            </TablePagination>
        </div>
    </AppLayout>

    <!-- Edit Dialog -->
    <FormDialog
        v-model:open="editDialogOpen"
        title="Edit Role"
        description="Edit Role"
        submitText="Edit Role"
        :processing="form.processing"
        :onSubmit="() => editRole(roles)"
    >
        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input
                id="name"
                v-model="form.name"
                error-message="name"
            />
            <InputError v-if="errors.name" :message="errors.name" />
        </div>
        <div class="grid gap-2">
            <Label for="display_name">Role</Label>
            <Input
                id="display_name"
                v-model="form.display_name"
                error-message="display_name"
            />
            <InputError v-if="errors.display_name" :message="errors.display_name" />
        </div>
        <div class="grid gap-2">
            <Label for="description">Description</Label>
            <Input
                id="description"
                v-model="form.description"
                error-message="description"
            />
            <InputError v-if="errors.description" :message="errors.description" />
        </div>
    </FormDialog>

    <!-- Delete Dialog -->
    <DeleteItem
        :open="deleteDialogOpen"
        :itemName="selectedRole?.name"
        @update:open="deleteDialogOpen = $event"
        @delete="deleteRole(roles)"
    />
</template>
