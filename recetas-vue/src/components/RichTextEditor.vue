<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'

const props = defineProps({
  modelValue: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [StarterKit],
  onUpdate({ editor }) {
    emit('update:modelValue', editor.getHTML())
  },
})
</script>

<template>
  <div class="border border-outline/30 focus-within:border-primary transition-colors">
    <!-- Toolbar -->
    <div class="flex flex-wrap gap-1 border-b border-outline/20 p-2 bg-surface-container-low">
      <button
        type="button"
        @click="editor.chain().focus().toggleBold().run()"
        :class="editor?.isActive('bold') ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-high'"
        class="w-8 h-8 flex items-center justify-center text-sm font-bold transition-colors"
        title="Negrita"
      >
        B
      </button>

      <button
        type="button"
        @click="editor.chain().focus().toggleItalic().run()"
        :class="editor?.isActive('italic') ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-high'"
        class="w-8 h-8 flex items-center justify-center text-sm italic transition-colors"
        title="Cursiva"
      >
        I
      </button>

      <div class="w-px bg-outline/20 mx-1"></div>

      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
        :class="editor?.isActive('heading', { level: 2 }) ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-high'"
        class="w-8 h-8 flex items-center justify-center text-xs font-bold transition-colors"
        title="Encabezado"
      >
        H2
      </button>

      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
        :class="editor?.isActive('heading', { level: 3 }) ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-high'"
        class="w-8 h-8 flex items-center justify-center text-xs font-bold transition-colors"
        title="Subencabezado"
      >
        H3
      </button>

      <div class="w-px bg-outline/20 mx-1"></div>

      <button
        type="button"
        @click="editor.chain().focus().toggleBulletList().run()"
        :class="editor?.isActive('bulletList') ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-high'"
        class="w-8 h-8 flex items-center justify-center transition-colors"
        title="Lista"
      >
        <span class="material-symbols-outlined text-base">format_list_bulleted</span>
      </button>

      <button
        type="button"
        @click="editor.chain().focus().toggleOrderedList().run()"
        :class="editor?.isActive('orderedList') ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-high'"
        class="w-8 h-8 flex items-center justify-center transition-colors"
        title="Lista numerada"
      >
        <span class="material-symbols-outlined text-base">format_list_numbered</span>
      </button>

      <div class="w-px bg-outline/20 mx-1"></div>

      <button
        type="button"
        @click="editor.chain().focus().undo().run()"
        :disabled="!editor?.can().undo()"
        class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high disabled:opacity-30 transition-colors"
        title="Deshacer"
      >
        <span class="material-symbols-outlined text-base">undo</span>
      </button>

      <button
        type="button"
        @click="editor.chain().focus().redo().run()"
        :disabled="!editor?.can().redo()"
        class="w-8 h-8 flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high disabled:opacity-30 transition-colors"
        title="Rehacer"
      >
        <span class="material-symbols-outlined text-base">redo</span>
      </button>
    </div>

    <!-- Editor area -->
    <EditorContent
      :editor="editor"
      class="tiptap-editor px-4 py-3 min-h-64 text-base text-on-surface outline-none"
    />
  </div>
</template>

<style scoped>
.tiptap-editor :deep(.tiptap) {
  outline: none;
  min-height: 16rem;
}

.tiptap-editor :deep(p) {
  margin-bottom: 0.75rem;
  line-height: 1.75;
}

.tiptap-editor :deep(h2) {
  font-family: var(--font-display);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
}

.tiptap-editor :deep(h3) {
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-top: 1.25rem;
  margin-bottom: 0.5rem;
}

.tiptap-editor :deep(ul) {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.tiptap-editor :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-bottom: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.tiptap-editor :deep(strong) {
  font-weight: 600;
}

.tiptap-editor :deep(em) {
  font-style: italic;
}

.tiptap-editor :deep(p.is-editor-empty:first-child::before) {
  content: attr(data-placeholder);
  color: var(--color-outline-variant);
  pointer-events: none;
  float: left;
  height: 0;
}
</style>
