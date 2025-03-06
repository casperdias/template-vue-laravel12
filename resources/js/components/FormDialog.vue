<template>
    <Dialog v-model:open="localOpen">
        <template v-if="trigger">
            <DialogTrigger>
                <slot name="trigger"></slot>
            </DialogTrigger>
        </template>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="onSubmit" class="space-y-4">
                <DialogHeader>
                    <DialogTitle>{{ title }}</DialogTitle>
                    <DialogDescription>
                        {{ description }}
                    </DialogDescription>
                </DialogHeader>
                <slot></slot>
                <DialogFooter>
                    <Button type="submit" class="mt-2 w-full" :disabled="processing">
                        <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                        {{ submitText }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogTrigger } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { LoaderCircle } from 'lucide-vue-next'
import { PropType, ref, watch } from 'vue'

const props = defineProps({
    open: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    submitText: {
        type: String,
        required: true
    },
    processing: {
        type: Boolean,
        required: true
    },
    onSubmit: {
        type: Function as PropType<() => void>,
        required: true
    },
    trigger: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:open']);

const localOpen = ref(props.open);

watch(localOpen, (newVal) => {
    emit('update:open', newVal);
});

watch(() => props.open, (newVal) => {
    localOpen.value = newVal;
});
</script>
