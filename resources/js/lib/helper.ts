import { router } from '@inertiajs/vue3'

export function changePage(routeName: string, newPage: number) {
    router.get(routeName, { page: newPage }, {
        preserveState: true,
        preserveScroll: true,
    });
}
