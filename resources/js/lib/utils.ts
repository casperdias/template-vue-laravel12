import type { Updater } from '@tanstack/vue-table'
import type { Ref } from 'vue'
import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'
import { router } from '@inertiajs/vue3'

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs))
}

export function valueUpdater<T extends Updater<any>>(updaterOrValue: T, ref: Ref) {
  ref.value
    = typeof updaterOrValue === 'function'
        ? updaterOrValue(ref.value)
        : updaterOrValue
}

export function changePage(routeName: string, newPage: number) {
    router.get(route(routeName), { page: newPage }, {
        preserveState: true,
        preserveScroll: true,
    });
}
