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
        title: 'Permission',
        href: '/master-data/permission',
    }
]

const { toast } = useToast()

defineProps<Props>()

interface Props {
    permissions : PaginationData<Permission>
    errors: any
}

interface Permission {
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

const addPermission = (permissions: PaginationData<Permission>) => {
    form.post(route('permissions.store', { page: permissions.current_page }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            addDialog.value = false;
            form.reset();
            toast({
                title: 'Success',
                description: 'Permission created successfully',
            })
        },
    });
};

const deleteDialogOpen = ref(false);
const editDialogOpen = ref(false);
const selectedPermission = ref<Permission | null>(null);

const openDeleteDialog = (permission: Permission) => {
    selectedPermission.value = permission;
    deleteDialogOpen.value = true;
};

const deletePermission = (permissions: PaginationData<Permission>) => {
    const permissionName = selectedPermission.value?.name;
    console.log(selectedPermission.value);
    if (selectedPermission.value) {
        router.delete(route('permissions.destroy', { permission: selectedPermission.value?.id, page: permissions.current_page }), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                deleteDialogOpen.value = false;
                selectedPermission.value = null;
                toast({
                    title: 'Permission deleted',
                    description: 'Permission ' + permissionName + ' has been deleted successfully',
                });
            },
            onError: (errors) => {
                deleteDialogOpen.value = false;
                selectedPermission.value = null;
                toast({
                    title: 'Error',
                    description: errors.id || 'An error occurred while deleting the Permission',
                });
            },
        });
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
        form.put(route('permissions.update', { permission: selectedPermission.value?.id, page: permissions.current_page }), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                editDialogOpen.value = false;
                selectedPermission.value = null;
                form.reset();
                toast({
                    title: 'Success',
                    description: 'Permission updated successfully',
                });
            },
        });
    }
};
</script>

<template>
    <Toaster />
    <Head title="Permission Data" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Button @click="addDialog = true" variant="outline">Tambah Permission</Button>
            <FormDialog
                v-model:open="addDialog"
                title="Tambah Permission"
                description="Masukkan Nama dan email"
                submitText="Create account"
                :processing="form.processing"
                :onSubmit="() => addPermission(permissions)"
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
                    <Label for="display_name">Display Name</Label>
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
                <TableCaption>Data Permission</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nama</TableHead>
                        <TableHead>Display</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="permission in permissions.data" :key="permission.id">
                        <TableCell>{{ permission.name }}</TableCell>
                        <TableCell>{{ permission.display_name }}</TableCell>
                        <TableCell>{{ permission.description }}</TableCell>
                        <TableCell class="flex gap-2">
                            <Button @click="openEditDialog(permission)">Edit</Button>
                            <Button @click="openDeleteDialog(permission)" >Delete</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination
                v-slot="{ page }"
                :items-per-page="permissions.per_page"
                :total="permissions.total"
                :sibling-count="1"
                show-edges
                :default-page="permissions.current_page"
            >
                <PaginationList v-slot="{ items }" class="flex items-center justify-center gap-1">
                    <PaginationFirst @click="changePage('permissions.index', permissions.from)"/>
                    <PaginationPrev @click="changePage('permissions.index', permissions.current_page - 1)"/>

                    <template v-for="(item, index) in items">
                        <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                            <Button
                                class="w-10 h-10 p-0"
                                :variant="item.value === page ? 'default' : 'outline'"
                                @click="changePage('permissions.index', item.value)"
                            >
                                {{ item.value }}
                            </Button>
                        </PaginationListItem>
                        <PaginationEllipsis v-else :key="item.type" :index="index" />
                    </template>

                    <PaginationNext @click="changePage('permissions.index', permissions.current_page + 1)"/>
                    <PaginationLast @click="changePage('permissions.index', permissions.last_page)"/>
                </PaginationList>
            </Pagination>
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
            <Input
                id="name"
                v-model="form.name"
                error-message="name"
            />
            <InputError v-if="errors.name" :message="errors.name" />
        </div>
        <div class="grid gap-2">
            <Label for="display_name">Display Name</Label>
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
        :itemName="selectedPermission?.name"
        @update:open="deleteDialogOpen = $event"
        @delete="deletePermission(permissions)"
    />
</template>
