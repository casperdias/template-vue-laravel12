<script setup lang="ts">
import { ref } from "vue";
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationData, type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
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
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { changePage } from '@/lib/utils'
import DeleteItem from '@/components/DeleteItem.vue'
import FormDialog from '@/components/FormDialog.vue'
import InputError from '@/components/InputError.vue';
import { useToast } from '@/components/ui/toast/use-toast'
import Toaster from '@/components/ui/toast/Toaster.vue'

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
    form.post(route('roles.store', { page: roles.current_page }), {
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
        router.delete(route('roles.destroy', { role: selectedRole.value?.id, page: roles.current_page }), {
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
        form.put(route('roles.update', { role: selectedRole.value?.id, page: roles.current_page }), {
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
</script>

<template>
    <Toaster />
    <Head title="Role Data" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
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
            <Table>
                <TableCaption>Data Role</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nama</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="role in roles.data" :key="role.id">
                        <TableCell>{{ role.name }}</TableCell>
                        <TableCell>{{ role.display_name }}</TableCell>
                        <TableCell>{{ role.description }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button @click="openEditDialog(role)">Edit</Button>
                            <Button @click="openDeleteDialog(role)" >Delete</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination
                v-slot="{ page }"
                :items-per-page="roles.per_page"
                :total="roles.total"
                :sibling-count="1"
                show-edges
                :default-page="roles.current_page"
            >
                <PaginationList v-slot="{ items }" class="flex items-center justify-center gap-1">
                    <PaginationFirst @click="changePage('roles.index', roles.from)"/>
                    <PaginationPrev @click="changePage('roles.index', roles.current_page - 1)"/>

                    <template v-for="(item, index) in items">
                        <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                            <Button
                                class="w-10 h-10 p-0"
                                :variant="item.value === page ? 'default' : 'outline'"
                                @click="changePage('roles.index', item.value)"
                            >
                                {{ item.value }}
                            </Button>
                        </PaginationListItem>
                        <PaginationEllipsis v-else :key="item.type" :index="index" />
                    </template>

                    <PaginationNext @click="changePage('roles.index', roles.current_page + 1)"/>
                    <PaginationLast @click="changePage('roles.index', roles.last_page)"/>
                </PaginationList>
            </Pagination>
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
