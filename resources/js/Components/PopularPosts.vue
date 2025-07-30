<template>
    <div class="bg-transparent rounded shadow-lg border border-[#363638] p-6">
        <h3 class="text-lg font-semibold mb-4">Popular Posts</h3>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
                v-for="(post, index) in posts"
                :key="index"
                class="flex items-center justify-between p-4 gap-4"
            >
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <img
                            :src="post.poster"
                            class="h-8 w-10 object-cover rounded"
                            :alt="post.title"
                            loading="lazy"
                        />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium line-clamp-1">
                            {{ post.title }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ formatDate(post.created_at) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <EyeIcon class="w-4 h-4 text-gray-400" />
                    <span class="text-sm font-medium">
                        {{ formatNumber(post.view_count) }}
                    </span>
                </div>
            </div>

            <div v-if="posts.length === 0" class="text-center py-8">
                <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium">No popular posts</h3>
                <p class="mt-1 text-sm">Posts with views will appear here.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { EyeIcon, DocumentTextIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    posts: {
        type: Array,
        required: true,
    },
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + "M";
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + "K";
    }
    return num.toString();
};
</script>
