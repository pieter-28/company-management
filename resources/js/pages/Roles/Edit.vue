<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2, Search } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import { ScrollArea } from '@/components/ui/scroll-area';

interface Permission {
    id: number;
    name: string;
}

const props = defineProps<{
    editForm: any;
    permissions: Permission[];
}>();

const emit = defineEmits(['confirm']);
const open = defineModel<boolean>('open');

const searchQuery = ref('');

const filteredPermissions = computed(() => {
    if (!searchQuery.value) return props.permissions;

    return props.permissions.filter((p) =>
        p.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});
watch(open, (val) => {
    if (!val) searchQuery.value = '';
});
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-h-[80vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Edit Role</DialogTitle>
                <DialogDescription>Update role data.</DialogDescription>
            </DialogHeader>

            <form @submit.prevent="$emit('confirm')" class="space-y-5">
                <!-- ROLE NAME -->
                <div class="space-y-2">
                    <Label>Name</Label>
                    <Input
                        v-model="editForm.name"
                        type="text"
                        placeholder="Admin / Manager / Staff"
                    />
                    <InputError :message="editForm.errors?.name" />
                </div>

                <!-- PERMISSIONS -->
                <div class="space-y-3">
                    <!-- SEARCH -->
                    <div class="relative">
                        <Search
                            class="absolute top-2.5 left-3 h-4 w-4 text-muted-foreground"
                        />
                        <Input
                            type="search"
                            v-model="searchQuery"
                            placeholder="Search permission..."
                            class="pl-9"
                        />
                    </div>
                    <!-- LIST -->
                    <p class="text-sm text-muted-foreground">
                        Select permissions to assign to this role. You can
                        always update
                    </p>

                    <ScrollArea class="h-[260px] rounded-md border p-3">
                        <div
                            v-for="permission in filteredPermissions"
                            :key="permission.id"
                            class="flex items-start gap-3 py-2"
                        >
                            <input
                                type="checkbox"
                                :value="permission.name"
                                v-model="editForm.permissions"
                                class="h-4 w-4 rounded border-gray-300"
                            />
                            <div>
                                <p class="text-sm font-medium">
                                    {{ permission.name }}
                                </p>
                            </div>
                        </div>

                        <p
                            v-if="!filteredPermissions.length"
                            class="py-6 text-center text-sm text-muted-foreground"
                        >
                            Permission not found
                        </p>
                    </ScrollArea>
                    <InputError :message="editForm.errors?.permissions" />
                </div>

                <!-- ACTION -->
                <div class="flex justify-end gap-2">
                    <Button type="submit" :disabled="editForm.processing">
                        <Loader2
                            v-if="editForm.processing"
                            class="mr-2 h-4 w-4 animate-spin"
                        />
                        {{ editForm.processing ? 'Updating...' : 'Update' }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
