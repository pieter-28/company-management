<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { toast } from 'vue-sonner';
import ModalCreate from '@/pages/Roles/Create.vue';
import ModalEdit from '@/pages/Roles/Edit.vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';
import {
    Plus,
    Edit2,
    Trash2,
    Shield,
    Users,
    MoreHorizontal,
    Search,
    Filter,
    X,
    CheckCircle2,
    AlertCircle,
} from 'lucide-vue-next';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';

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

// State management
const isAddDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const isDeleteDialogOpen = ref(false);
const searchQuery = ref('');
const selectedRole = ref<Role | null>(null);
const roleIdToDelete = ref<number | null>(null);
const editingRole = ref<Role | null>(null);

// Form state
const addform = useForm({
    name: '',
    permissions: [] as string[],
});

const editForm = useForm({
    id: null,
    name: '',
    permissions: [] as string[],
});

// Computed filtered roles
const filteredRoles = computed(() => {
    if (!searchQuery.value) return props.roles;

    return props.roles.filter(
        (role) =>
            role.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            role.permissions.some((p) =>
                p.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
            ),
    );
});

// Methods
const openCreateDialog = () => {
    addform.reset();
    isAddDialogOpen.value = true;
};

const openEditDialog = (role: Role) => {
    editingRole.value = role;
    editForm.name = role.name;
    editForm.permissions = role.permissions.map((p) => p.name);
    isEditDialogOpen.value = true;
};

const openDeleteDialog = (role: Role) => {
    selectedRole.value = role;
    roleIdToDelete.value = role.id;
    isDeleteDialogOpen.value = true;
};

const storeRole = () => {
    addform.post('/roles', {
        preserveScroll: true,
        onSuccess: () => {
            isAddDialogOpen.value = false;
            addform.reset();
            toast.success('Role created successfully!', {
                description: 'New role has been added to the system.',
                icon: CheckCircle2,
            });
        },
    });
};

const updateRole = () => {
    if (!editingRole.value) return;

    editForm.put(`/roles/${editingRole.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditDialogOpen.value = false;
            editForm.reset();
            toast.success('Role updated successfully!', {
                description: 'Role permissions have been updated.',
                icon: CheckCircle2,
            });
        },
    });
};

const confirmDeleteRole = () => {
    if (!roleIdToDelete.value) return;

    router.delete(`/roles/${roleIdToDelete.value}`, {
        preserveScroll: true,
        onFinish: () => {
            roleIdToDelete.value = null;
            selectedRole.value = null;
            isDeleteDialogOpen.value = false;
            toast.success('Role deleted successfully!', {
                description: 'The role has been removed from the system.',
                icon: CheckCircle2,
            });
        },
    });
};

const clearSearch = () => {
    searchQuery.value = '';
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Roles', href: '/roles' },
];

// Permission badge color mapping
const getPermissionColor = (permissionName: string) => {
    const colors = {
        view: 'bg-blue-100 text-blue-800 border-blue-200',
        create: 'bg-green-100 text-green-800 border-green-200',
        edit: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        delete: 'bg-red-100 text-red-800 border-red-200',
        manage: 'bg-purple-100 text-purple-800 border-purple-200',
    };

    for (const [key, color] of Object.entries(colors)) {
        if (permissionName.toLowerCase().includes(key)) {
            return color;
        }
    }
    return 'bg-gray-100 text-gray-800 border-gray-200';
};
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-6">
            <!-- Header Section -->
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Role Management
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage roles and permissions for your application
                    </p>
                </div>
                <Button @click="openCreateDialog" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Create Role
                </Button>
            </div>

            <!-- Stats Cards -->
            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium"
                            >Total Roles</CardTitle
                        >
                        <Shield class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ roles.length }}</div>
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
                        <CheckCircle2 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ permissions.length }}
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">
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
                        <Users class="h-4 w-4 text-muted-foreground" />
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
                        <p class="mt-1 text-xs text-muted-foreground">
                            Users with assigned roles
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Card -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Roles & Permissions</CardTitle>
                            <CardDescription>
                                Manage role-based access control for your
                                application
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>

                <CardContent>
                    <!-- Search and Filter Bar -->
                    <div class="mb-4 flex items-center gap-2">
                        <div class="relative flex-1">
                            <Search
                                class="absolute top-2.5 left-2 h-4 w-4 text-muted-foreground"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Search roles or permissions..."
                                class="pr-8 pl-8"
                            />
                            <Button
                                v-if="searchQuery"
                                variant="ghost"
                                size="sm"
                                class="absolute top-1.5 right-1 h-6 w-6 p-0"
                                @click="clearSearch"
                            >
                                <X class="h-4 w-4" />
                            </Button>
                        </div>
                        <Button variant="outline" size="icon">
                            <Filter class="h-4 w-4" />
                        </Button>
                    </div>

                    <!-- Table Section -->
                    <div
                        v-if="filteredRoles.length > 0"
                        class="rounded-md border"
                    >
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">No</TableHead>
                                    <TableHead>Role Name</TableHead>
                                    <TableHead>Permissions</TableHead>
                                    <TableHead>Users</TableHead>
                                    <TableHead>Created</TableHead>
                                    <TableHead class="text-right"
                                        >Actions</TableHead
                                    >
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="(role, index) in filteredRoles"
                                    :key="role.id"
                                    class="group"
                                >
                                    <TableCell class="font-medium">
                                        {{ index + 1 }}
                                    </TableCell>

                                    <TableCell>
                                        <div class="flex items-center gap-2">
                                            <Shield
                                                class="h-4 w-4 text-muted-foreground"
                                            />
                                            <span class="font-medium">{{
                                                role.name
                                            }}</span>
                                        </div>
                                    </TableCell>

                                    <TableCell>
                                        <div
                                            class="flex max-w-md flex-wrap gap-1"
                                        >
                                            <Badge
                                                v-for="p in role.permissions"
                                                :key="p.id"
                                                :class="[
                                                    'font-normal',
                                                    getPermissionColor(p.name),
                                                ]"
                                                variant="outline"
                                            >
                                                {{ p.name }}
                                            </Badge>
                                            <Badge
                                                v-if="!role.permissions?.length"
                                                variant="outline"
                                                class="text-muted-foreground"
                                            >
                                                No permissions
                                            </Badge>
                                        </div>
                                    </TableCell>

                                    <TableCell>
                                        <Badge variant="secondary">
                                            {{ role.users_count || 0 }} users
                                        </Badge>
                                    </TableCell>

                                    <TableCell>
                                        <span
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{
                                                role.created_at
                                                    ? new Date(
                                                          role.created_at,
                                                      ).toLocaleDateString()
                                                    : '-'
                                            }}
                                        </span>
                                    </TableCell>

                                    <TableCell class="text-right">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                >
                                                    <MoreHorizontal
                                                        class="h-4 w-4"
                                                    />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuLabel
                                                    >Actions</DropdownMenuLabel
                                                >
                                                <DropdownMenuItem
                                                    @click="
                                                        openEditDialog(role)
                                                    "
                                                >
                                                    <Edit2
                                                        class="mr-2 h-4 w-4"
                                                    />
                                                    Edit Role
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem
                                                    @click="
                                                        openDeleteDialog(role)
                                                    "
                                                    class="text-red-600 focus:text-red-600"
                                                >
                                                    <Trash2
                                                        class="mr-2 h-4 w-4"
                                                    />
                                                    Delete Role
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <!-- No Data State -->
                    <div v-else-if="roles.length === 0" class="py-12">
                        <Empty>
                            <EmptyHeader>
                                <EmptyMedia variant="icon">
                                    <Shield class="h-12 w-12" />
                                </EmptyMedia>
                                <EmptyTitle>No Roles Yet</EmptyTitle>
                                <EmptyDescription>
                                    You haven't created any roles yet. Get
                                    started by creating your first role.
                                </EmptyDescription>
                            </EmptyHeader>
                            <EmptyContent>
                                <Button @click="openCreateDialog">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Create Role
                                </Button>
                            </EmptyContent>
                        </Empty>
                    </div>

                    <!-- No Search Results -->
                    <div
                        v-else-if="
                            roles.length > 0 && filteredRoles.length === 0
                        "
                        class="py-12 text-center"
                    >
                        <div class="mb-4 flex justify-center">
                            <Search
                                class="h-12 w-12 text-muted-foreground/50"
                            />
                        </div>
                        <h3 class="text-lg font-semibold">No results found</h3>
                        <p class="mt-1 text-sm text-muted-foreground">
                            No roles match your search query. Try adjusting your
                            filters.
                        </p>
                        <Button
                            variant="outline"
                            class="mt-4"
                            @click="clearSearch"
                        >
                            Clear Search
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Modals -->
        <ModalCreate
            :add-form="addform"
            :permissions="permissions"
            v-model:open="isAddDialogOpen"
            @confirm="storeRole"
        />

        <ModalEdit
            :edit-form="editForm"
            v-model:open="isEditDialogOpen"
            :permissions="permissions"
            :editing-task="editingRole"
            @confirm="updateRole"
        />

        <!-- Delete Confirmation Dialog -->
        <AlertDialog
            :open="isDeleteDialogOpen"
            @update:open="isDeleteDialogOpen = false"
        >
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle class="flex items-center gap-2">
                        <AlertCircle class="h-5 w-5 text-red-500" />
                        Delete Role
                    </AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete
                        <span class="font-semibold">{{
                            selectedRole?.name
                        }}</span
                        >? This action cannot be undone. Users with this role
                        may lose access permissions.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction
                        @click="confirmDeleteRole"
                        class="bg-red-600 hover:bg-red-700 focus:ring-red-600"
                    >
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>

<style scoped>
.container {
    max-width: 1400px;
}

/* Animations */
.group:hover {
    background-color: hsl(var(--muted) / 0.5);
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: hsl(var(--muted));
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: hsl(var(--muted-foreground) / 0.3);
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground) / 0.5);
}
</style>
