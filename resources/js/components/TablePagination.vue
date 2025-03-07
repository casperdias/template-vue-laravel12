<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
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
import { Button } from '@/components/ui/button';
import { changePage } from '@/lib/utils';

defineProps({
    columns: {
        type: Array as () => { key: string; label: string }[],
        required: true,
    },
    data: {
        type: Array as () => any[],
        required: true,
    },
    pagination: {
        type: Object as () => {
            per_page: number;
            total: number;
            current_page: number;
            last_page: number;
            from: number;
        },
        required: true,
    },
    actions: {
        type: [Function, Boolean],
        required: false,
    },
    caption: {
        type: String,
        required: false,
    },
    routeName: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Table>
        <TableCaption>{{ caption }}</TableCaption>
        <TableHeader>
            <TableRow>
            <TableHead v-for="col in columns" :key="col.key">{{ col.label }}</TableHead>
            <TableHead v-if="actions">Actions</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="item in data" :key="item.id">
            <TableCell v-for="col in columns" :key="col.key">{{ item[col.key] }}</TableCell>
            <TableCell v-if="actions" class="space-x-2">
                <slot name="actions" :item="item"></slot>
            </TableCell>
            </TableRow>
        </TableBody>
    </Table>

    <Pagination v-slot="{ page }" :items-per-page="pagination.per_page" :total="pagination.total" :default-page="pagination.current_page">
        <PaginationList v-slot="{ items }" class="flex items-center justify-center gap-1">
            <PaginationFirst @click="changePage(routeName, pagination.from)" />
            <PaginationPrev @click="changePage(routeName, pagination.current_page - 1)" />

            <template v-for="(item, index) in items">
                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                    <Button :variant="item.value === page ? 'default' : 'outline'" @click="changePage(routeName, item.value)">{{ item.value }}</Button>
                </PaginationListItem>
                <PaginationEllipsis v-else :key="item.type" :index="index" />
            </template>

            <PaginationNext @click="changePage(routeName, pagination.current_page + 1)" />
            <PaginationLast @click="changePage(routeName, pagination.last_page)" />
        </PaginationList>
    </Pagination>
</template>
