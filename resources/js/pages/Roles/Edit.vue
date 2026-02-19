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
import { Loader2 } from 'lucide-vue-next';

interface Permission {
    id: number;
    name: string;
}

defineProps<{
    editForm: any;
    permissions: Permission[];
}>();

const emit = defineEmits<{
    (e: 'confirm'): void;
}>();

const open = defineModel<boolean>('open');
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-h-[80vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Edit Role</DialogTitle>
                <DialogDescription>Update role data.</DialogDescription>
            </DialogHeader>

            <form @submit.prevent="$emit('confirm')" class="space-y-4">
                <!-- ROLE NAME -->
                <div class="space-y-2">
                    <Label>Name</Label>
                    <Input
                        v-model="editForm.name"
                        type="text"
                        placeholder="Type role name"
                    />
                    <InputError :message="editForm.errors?.name" />
                </div>

                <!-- PERMISSIONS -->
                <div class="space-y-2">
                    <Label>Permissions</Label>

                    <div
                        class="grid max-h-60 grid-cols-2 gap-2 overflow-y-auto rounded border p-2"
                    >
                        <label
                            v-for="p in permissions"
                            :key="p.id"
                            class="flex items-center gap-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                :value="p.name"
                                v-model="editForm.permissions"
                            />
                            {{ p.name }}
                        </label>
                    </div>

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
