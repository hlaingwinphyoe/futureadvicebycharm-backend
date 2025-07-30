<template>
    <div v-if="editor" class="buttons editor-header">
        <!-- Heading -->
        <button
            type="button"
            @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
            :class="{
                'editor-tab-active': editor.isActive('heading', { level: 1 }),
            }"
            class="editor-btn"
        >
            H1
        </button>
        <button
            type="button"
            @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
            :class="{
                'editor-tab-active': editor.isActive('heading', { level: 2 }),
            }"
            class="editor-btn"
        >
            H2
        </button>
        <button
            type="button"
            @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
            :class="{
                'editor-tab-active': editor.isActive('heading', { level: 3 }),
            }"
            class="editor-btn"
        >
            H3
        </button>

        <!-- Paragraph -->
        <button
            type="button"
            @click="editor.chain().focus().setParagraph().run()"
            :class="{ 'editor-tab-active': editor.isActive('paragraph') }"
            class="editor-btn"
        >
            P
        </button>

        <button
            type="button"
            @click="editor.chain().focus().setTextAlign('left').run()"
            :class="{
                'editor-tab-active': editor.isActive({ textAlign: 'left' }),
            }"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h10M4 18h14"
                />
            </svg>
        </button>
        <button
            type="button"
            @click="editor.chain().focus().setTextAlign('center').run()"
            :class="{
                'editor-tab-active': editor.isActive({ textAlign: 'center' }),
            }"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M8 12h8M6 18h12"
                />
            </svg>
        </button>
        <button
            type="button"
            @click="editor.chain().focus().setTextAlign('right').run()"
            :class="{
                'editor-tab-active': editor.isActive({ textAlign: 'right' }),
            }"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16m-10 6h10M6 18h14"
                />
            </svg>
        </button>
        <button
            type="button"
            @click="editor.chain().focus().setTextAlign('justify').run()"
            :class="{
                'editor-tab-active': editor.isActive({ textAlign: 'justify' }),
            }"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>

        <el-divider direction="vertical" />

        <!-- Bold Button -->
        <button
            type="button"
            @click="editor.chain().focus().toggleBold().run()"
            :disabled="!editor.can().chain().focus().toggleBold().run()"
            :class="{ 'editor-tab-active': editor.isActive('bold') }"
            class="editor-btn"
        >
            B
        </button>

        <!-- Italic Button -->
        <button
            type="button"
            @click="editor.chain().focus().toggleItalic().run()"
            :disabled="!editor.can().chain().focus().toggleItalic().run()"
            :class="{ 'editor-tab-active': editor.isActive('italic') }"
            class="editor-btn"
        >
            <i>I</i>
        </button>

        <!-- Underline Button -->
        <button
            type="button"
            @click="editor.chain().focus().toggleUnderline().run()"
            :class="{ 'editor-tab-active': editor.isActive('underline') }"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M7 5v5a5 5 0 0 0 10 0V5M5 19h14"
                />
            </svg>
        </button>

        <!-- Highlight -->
        <button
            type="button"
            @click="editor.chain().focus().toggleHighlight().run()"
            :class="{ 'editor-tab-active': editor.isActive('highlight') }"
            class="editor-btn"
        >
            H
        </button>

        <button
            type="button"
            @click="editor.chain().focus().setHorizontalRule().run()"
            :class="{ 'editor-tab-active': editor.isActive('horizontalRule') }"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path fill="currentColor" d="M4 13v-2h16v2z" />
            </svg>
        </button>

        <button
            type="button"
            @click="editor.chain().focus().toggleBulletList().run()"
            :class="{ 'editor-tab-active': editor.isActive('bulletList') }"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 6h11M9 12h11M9 18h11M5 6v.01M5 12v.01M5 18v.01"
                />
            </svg>
        </button>

        <!-- Undo -->
        <!-- <button
            type="button"
            @click="editor.chain().focus().undo().run()"
            :disabled="!editor.can().chain().focus().undo().run()"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 256 256"
            >
                <path
                    fill="currentColor"
                    d="M232 144a64.07 64.07 0 0 1-64 64H80a8 8 0 0 1 0-16h88a48 48 0 0 0 0-96H88v40a8 8 0 0 1-13.66 5.66l-48-48a8 8 0 0 1 0-11.32l48-48A8 8 0 0 1 88 40v40h80a64.07 64.07 0 0 1 64 64"
                />
            </svg>
        </button> -->

        <!-- Hard Break -->
        <button
            type="button"
            @click="editor.chain().focus().setHardBreak().run()"
            class="editor-btn py-1.5 cursor-pointer"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
            >
                <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m9 11l-4 4l4 4m-4-4h11a4 4 0 0 0 0-8h-1"
                />
            </svg>
        </button>
    </div>

    <!-- Editor Content -->
    <EditorContent :editor="editor" />
</template>

<script>
import { Editor, EditorContent, useEditor } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import { ref } from "vue";

export default {
    props: ["modelValue"],
    components: {
        EditorContent,
    },
    emits: ["update:modelValue"],
    setup(props, { emit }) {
        // const headingLevels = [1, 2, 3];
        // const headingLevel = ref(null);

        const editor = useEditor({
            content: props.modelValue,
            extensions: [StarterKit],
            editorProps: {
                attributes: {
                    class: "editor-body",
                },
            },
            onUpdate: ({ editor }) => {
                emit("update:modelValue", editor.getHTML());
            },
        });

        // Toggle heading based on selected level
        // const toggleHeading = (level) => {
        //     if (!editor.value) return;

        //     if (level) {
        //         editor.value.chain().focus().toggleHeading({ level }).run();
        //     }
        // };

        return {
            editor,
        };
    },
};
</script>
