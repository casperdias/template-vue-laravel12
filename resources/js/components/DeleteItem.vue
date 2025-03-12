<template>
    <Dialog :open="open" @update:open="updateOpen">
        <DialogContent class="rounded-lg bg-white shadow-lg dark:bg-gray-900">
            <DialogHeader>
                <DialogTitle>Confirm Delete</DialogTitle>
                <DialogDescription> Are you sure you want to delete {{ itemName }}? </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="destructive" @click="onDelete">Delete</Button>
                <DialogClose as-child>
                    <Button variant="secondary">Cancel</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { defineEmits, defineProps } from 'vue';

defineProps({
    open: Boolean,
    itemName: String,
});

const emit = defineEmits(['update:open', 'delete']);

const updateOpen = (value: boolean) => {
    emit('update:open', value);
};

const onDelete = () => {
    emit('delete');
    emit('update:open', false);
};
</script>
