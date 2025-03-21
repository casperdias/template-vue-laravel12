<script setup lang="ts">
import DeleteItem from '@/components/DeleteItem.vue';
import FormDialog from '@/components/FormDialog.vue';
import InputError from '@/components/InputError.vue';
import TablePagination from '@/components/TablePagination.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Toaster from '@/components/ui/toast/Toaster.vue';
import { useToast } from '@/components/ui/toast/use-toast';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationData, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Search, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master-data',
    },
    {
        title: 'Permission',
        href: '/master-data/permission',
    },
];

const { toast } = useToast();

const props = defineProps<Props>();

interface Props {
    permissions: PaginationData<Permission>;
    errors: any;
}

interface Permission {
    id: number;
    name: string;
    display_name: string;
    description: string;
}

const form = useForm({
    name: '',
    display_name: '',
    description: '',
});

const addDialog = ref(false);

const addPermission = (permissions: PaginationData<Permission>) => {
    form.post(
        route('permissions.store', {
            page: permissions.current_page,
            search: searchTerm.value, // Ensure search is passed
        }),
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                addDialog.value = false;
                form.reset();
                toast({ title: 'Success', description: 'Permission created successfully' });
            },
        },
    );
};

const deleteDialogOpen = ref(false);
const editDialogOpen = ref(false);
const selectedPermission = ref<Permission | null>(null);

const openDeleteDialog = (permission: Permission) => {
    selectedPermission.value = permission;
    deleteDialogOpen.value = true;
};

const deletePermission = (permissions: PaginationData<Permission>) => {
    if (selectedPermission.value) {
        router.delete(
            route('permissions.destroy', {
                permission: selectedPermission.value.id,
                page: permissions.current_page,
                search: searchTerm.value, // Preserve search
            }),
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    deleteDialogOpen.value = false;
                    selectedPermission.value = null;
                    toast({ title: 'Deleted', description: 'Permission deleted successfully' });
                },
            },
        );
    }
};

const openEditDialog = (permission: Permission) => {
    selectedPermission.value = permission;
    form.name = permission.name;
    form.display_name = permission.display_name;
    form.description = permission.description;
    editDialogOpen.value = true;
};

const editPermission = (permissions: PaginationData<Permission>) => {
    if (selectedPermission.value) {
        form.put(
            route('permissions.update', {
                permission: selectedPermission.value.id,
                page: permissions.current_page,
                search: searchTerm.value, // Preserve search
            }),
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    editDialogOpen.value = false;
                    selectedPermission.value = null;
                    form.reset();
                    toast({ title: 'Success', description: 'Permission updated successfully' });
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
</script>

<template>
    <Toaster />
    <Head title="Permission Data" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative w-full max-w-sm items-center">
                <Input id="search" type="text" name="search" placeholder="Search..." class="pl-10" v-model="searchTerm" />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <Search class="size-6 text-muted-foreground" />
                </span>
            </div>
            <Button @click="addDialog = true" variant="outline">Tambah Permission</Button>
            <FormDialog
                v-model:open="addDialog"
                title="Tambah Permission"
                description="Masukkan Nama dan Deskripsi"
                submitText="Create account"
                :processing="form.processing"
                :onSubmit="() => addPermission(permissions)"
            >
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" v-model="form.name" error-message="name" />
                    <InputError v-if="errors.name" :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="display_name">Display Name</Label>
                    <Input id="display_name" v-model="form.display_name" error-message="display_name" />
                    <InputError v-if="errors.display_name" :message="errors.display_name" />
                </div>
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Input id="description" v-model="form.description" error-message="description" />
                    <InputError v-if="errors.description" :message="errors.description" />
                </div>
            </FormDialog>
            <TablePagination
                :columns="[
                    { key: 'name', label: 'Name' },
                    { key: 'display_name', label: 'Display Name' },
                    { key: 'description', label: 'Description' },
                ]"
                :data="permissions.data"
                :pagination="permissions"
                :caption="'Permission Data'"
                :actions="true"
                :routeName="permissions.path"
            >
                <template #actions="{ item }">
                    <Button @click="openEditDialog(item)">
                        <Pencil />
                    </Button>
                    <Button @click="openDeleteDialog(item)">
                        <Trash2 />
                    </Button>
                </template>
            </TablePagination>
        </div>
    </AppLayout>

    <!-- Edit Dialog -->
    <FormDialog
        v-model:open="editDialogOpen"
        title="Edit Permission"
        description="Edit Permission"
        submitText="Edit Permission"
        :processing="form.processing"
        :onSubmit="() => editPermission(permissions)"
    >
        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" v-model="form.name" error-message="name" />
            <InputError v-if="errors.name" :message="errors.name" />
        </div>
        <div class="grid gap-2">
            <Label for="display_name">Display Name</Label>
            <Input id="display_name" v-model="form.display_name" error-message="display_name" />
            <InputError v-if="errors.display_name" :message="errors.display_name" />
        </div>
        <div class="grid gap-2">
            <Label for="description">Description</Label>
            <Input id="description" v-model="form.description" error-message="description" />
            <InputError v-if="errors.description" :message="errors.description" />
        </div>
    </FormDialog>

    <!-- Delete Dialog -->
    <DeleteItem
        :open="deleteDialogOpen"
        :itemName="selectedPermission?.name"
        @update:open="deleteDialogOpen = $event"
        @delete="deletePermission(permissions)"
    />
</template>
