<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Shield,
    Users,
    CheckCircle2,
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
    created_at?: string;
    users_count?: number;
}

const props = defineProps<{
    roles: Role[];
    permissions: Permission[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="p-4"
        >
        <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Dashboard
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Overview of your application's status
                    </p>
                </div>
            </div>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total Roles</CardTitle
                        >
                        <Shield class="h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ roles.length ?? 0 }}</div>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Active roles in system
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total Permissions</CardTitle
                        >
                        <CheckCircle2 class="h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ permissions.length ?? 0 }}
                        </div>
                        <p class="mt-1 text-xs">
                            Available permissions
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Assigned Users</CardTitle
                        >
                        <Users class="h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{
                                roles.reduce(
                                    (acc, role) =>
                                        acc + (role.users_count || 0),
                                    0,
                                )
                            }}
                        </div>
                        <p class="mt-1 text-xs">
                            Users with assigned roles
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
