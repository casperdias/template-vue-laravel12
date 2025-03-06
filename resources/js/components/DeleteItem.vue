<template>
    <Dialog :open="open" @update:open="updateOpen">
        <DialogContent class="bg-white dark:bg-gray-900 shadow-lg rounded-lg">
            <DialogHeader>
                <DialogTitle>Confirm Delete</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete {{ itemName }}?
                </DialogDescription>
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
import { defineProps, defineEmits } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'

defineProps({
    open: Boolean,
    itemName: String
})

const emit = defineEmits(['update:open', 'delete'])

const updateOpen = (value: boolean) => {
    emit('update:open', value)
}

const onDelete = () => {
    emit('delete')
    emit('update:open', false)
}
</script>
