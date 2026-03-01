<template>
  <div class="space-y-4">

    <!-- Zone de drop -->
    <div
      @dragover.prevent="isDragging = true"
      @dragleave="isDragging = false"
      @drop.prevent="handleDrop"
      @click="$refs.fileInput.click()"
      :class="[
        'border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-all duration-200',
        isDragging
          ? 'border-blue-500 bg-blue-50 scale-[1.02]'
          : 'border-gray-200 hover:border-blue-400 hover:bg-gray-50'
      ]"
    >
      <div class="flex flex-col items-center gap-3">
        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-2xl">
          📸
        </div>
        <div>
          <p class="font-semibold text-gray-700">Glissez vos images ici</p>
          <p class="text-sm text-gray-400 mt-1">ou cliquez pour sélectionner</p>
          <p class="text-xs text-gray-300 mt-1">JPG, PNG, WEBP — Max 5MB par image</p>
        </div>
      </div>
      <input
        ref="fileInput"
        type="file"
        multiple
        accept="image/*"
        class="hidden"
        @change="handleFileSelect"
      />
    </div>

    <!-- Prévisualisation avant upload -->
    <div v-if="previewFiles.length > 0" class="space-y-3">
      <div class="flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-700">
          {{ previewFiles.length }} image(s) sélectionnée(s)
        </p>
        <button
          @click="uploadImages"
          :disabled="uploading"
          class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors flex items-center gap-2"
        >
          <span v-if="uploading">⏳ Upload...</span>
          <span v-else>⬆️ Uploader</span>
        </button>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div
          v-for="(file, index) in previewFiles"
          :key="index"
          class="relative group rounded-xl overflow-hidden border border-gray-100 aspect-video bg-gray-50"
        >
          <img
            :src="file.preview"
            class="w-full h-full object-cover"
            :alt="file.name"
          />
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors" />
          <button
            @click.stop="removePreview(index)"
            class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
          >
            ×
          </button>
          <p class="absolute bottom-2 left-2 right-2 text-white text-xs truncate opacity-0 group-hover:opacity-100 transition-opacity">
            {{ file.name }}
          </p>
        </div>
      </div>

      <!-- Barre de progression -->
      <div v-if="uploading" class="w-full bg-gray-100 rounded-full h-2">
        <div
          class="bg-blue-600 h-2 rounded-full transition-all duration-300"
          :style="`width: ${uploadProgress}%`"
        />
      </div>
    </div>

    <!-- Images existantes -->
    <div v-if="existingImages.length > 0" class="space-y-3">
      <p class="text-sm font-semibold text-gray-700">
        Images actuelles ({{ existingImages.length }})
      </p>
      <div class="grid grid-cols-3 gap-3">
        <div
          v-for="image in existingImages"
          :key="image.id"
          class="relative group rounded-xl overflow-hidden border-2 aspect-video bg-gray-100 transition-all"
          :class="image.is_primary ? 'border-blue-500' : 'border-transparent'"
        >
          <img
            :src="image.url"
            class="w-full h-full object-cover"
            alt="Image ressource"
          />
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors" />

          <!-- Badge principale -->
          <div v-if="image.is_primary"
               class="absolute top-2 left-2 bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
            ⭐ Principale
          </div>

          <!-- Actions hover -->
          <div class="absolute inset-0 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              v-if="!image.is_primary"
              @click="setPrimary(image)"
              class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg font-medium hover:bg-blue-700 transition-colors"
            >
              ⭐ Principale
            </button>
            <button
              @click="deleteImage(image)"
              class="bg-red-500 text-white text-xs px-3 py-1.5 rounded-lg font-medium hover:bg-red-600 transition-colors"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>

    <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const props = defineProps({
  resourceId: { type: Number, required: true },
})

const emit = defineEmits(['updated'])

const isDragging     = ref(false)
const previewFiles   = ref([])
const existingImages = ref([])
const uploading      = ref(false)
const uploadProgress = ref(0)
const error          = ref(null)

function handleFileSelect(event) {
  addFiles(Array.from(event.target.files))
}

function handleDrop(event) {
  isDragging.value = false
  addFiles(Array.from(event.dataTransfer.files))
}

function addFiles(files) {
  files.forEach(file => {
    if (!file.type.startsWith('image/')) return
    const reader = new FileReader()
    reader.onload = (e) => {
      previewFiles.value.push({
        file,
        name:    file.name,
        preview: e.target.result,
      })
    }
    reader.readAsDataURL(file)
  })
}

function removePreview(index) {
  previewFiles.value.splice(index, 1)
}

async function uploadImages() {
  if (previewFiles.value.length === 0) return
  uploading.value      = true
  uploadProgress.value = 0
  error.value          = null

  try {
    const formData = new FormData()
    previewFiles.value.forEach(f => formData.append('images[]', f.file))

    await api.post(`/admin/resources/${props.resourceId}/images`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (e) => {
        uploadProgress.value = Math.round((e.loaded / e.total) * 100)
      },
    })

    previewFiles.value = []
    await fetchImages()
    emit('updated')
  } catch (e) {
    error.value = 'Erreur lors de l\'upload.'
  } finally {
    uploading.value = false
  }
}

async function deleteImage(image) {
  if (!confirm('Supprimer cette image ?')) return
  try {
    await api.delete(`/admin/images/${image.id}`)
    await fetchImages()
    emit('updated')
  } catch {
    error.value = 'Erreur lors de la suppression.'
  }
}

async function setPrimary(image) {
  try {
    await api.patch(`/admin/images/${image.id}/primary`)
    await fetchImages()
    emit('updated')
  } catch {
    error.value = 'Erreur lors de la mise à jour.'
  }
}

async function fetchImages() {
  const { data } = await api.get(`/admin/resources/${props.resourceId}`)
  existingImages.value = data.data?.images_list || []
}

onMounted(fetchImages)
</script>
