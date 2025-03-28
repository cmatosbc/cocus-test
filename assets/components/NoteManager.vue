<template>
  <div class="note-manager">
    <div class="header">
      <h2>Notes</h2>
      <button @click="showCreateForm = true" class="btn-primary">New Note</button>
    </div>

    <!-- Create/Edit Note Form -->
    <div v-if="showCreateForm || editingNote" class="note-form">
      <h3>{{ editingNote ? 'Edit Note' : 'Create Note' }}</h3>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="title">Title</label>
          <input 
            type="text" 
            id="title" 
            v-model="noteForm.title" 
            required
          >
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea 
            id="message" 
            v-model="noteForm.message" 
            required
          ></textarea>
        </div>
        <div class="form-group">
          <label for="type">Type</label>
          <select 
            id="type" 
            v-model="noteForm.type" 
            required
          >
            <option value="0">Info</option>
            <option value="1">Warning</option>
            <option value="2">Critical</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn-primary">
            {{ editingNote ? 'Update' : 'Create' }}
          </button>
          <button 
            type="button" 
            @click="cancelForm" 
            class="btn-secondary"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- Notes List -->
    <div v-if="notes.length" class="notes-list">
      <div v-for="note in notes" :key="note.id" class="note-card">
        <div class="note-content">
          <h3>{{ note.title }}</h3>
          <p>{{ note.message }}</p>
          <div class="note-metadata">
            <span class="note-type" :class="'type-' + note.type">
              {{ ['Info', 'Warning', 'Critical'][note.type] }}
            </span>
            <span class="note-date">Created: {{ formatDate(note.createdAt) }}</span>
            <span class="note-date">Updated: {{ formatDate(note.updatedAt) }}</span>
          </div>
        </div>
        <div class="note-actions">
          <button @click="editNote(note)" class="btn-edit">
            Edit
          </button>
          <button @click="deleteNote(note.id)" class="btn-delete">
            Delete
          </button>
        </div>
      </div>
    </div>
    <div v-else class="no-notes">
      <p>No notes yet. Create your first note!</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const notes = ref([])
const showCreateForm = ref(false)
const editingNote = ref(null)
const noteForm = ref({
  title: '',
  message: '',
  type: 0
})

// Fetch all notes
const fetchNotes = async () => {
  try {
    const response = await axios.get('/api/notes')
    notes.value = response.data
  } catch (error) {
    console.error('Error fetching notes:', error)
  }
}

// Create or update note
const handleSubmit = async () => {
  try {
    if (editingNote.value) {
      await axios.put(`/api/notes/${editingNote.value.id}`, noteForm.value)
    } else {
      await axios.post('/api/notes', noteForm.value)
    }
    await fetchNotes()
    cancelForm()
  } catch (error) {
    console.error('Error saving note:', error)
  }
}

// Delete note
const deleteNote = async (id) => {
  if (!confirm('Are you sure you want to delete this note?')) return
  
  try {
    await axios.delete(`/api/notes/${id}`)
    await fetchNotes()
  } catch (error) {
    console.error('Error deleting note:', error)
  }
}

// Edit note
const editNote = (note) => {
  editingNote.value = note
  noteForm.value = {
    title: note.title,
    message: note.message,
    type: note.type
  }
  showCreateForm.value = true
}

// Cancel form
const cancelForm = () => {
  showCreateForm.value = false
  editingNote.value = null
  noteForm.value = {
    title: '',
    message: '',
    type: 0
  }
}

// Format date
const formatDate = (date) => {
  if (!date) return 'No date';
  try {
    const dateObj = new Date(date);
    if (isNaN(dateObj.getTime())) {
      return 'Invalid date format';
    }
    return dateObj.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (error) {
    console.error('Error formatting date:', error);
    return 'Invalid date';
  }
}

// Load notes on component mount
onMounted(fetchNotes)
</script>

<style scoped>
.note-manager {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.note-form {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.form-group textarea {
  min-height: 100px;
  resize: vertical;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.notes-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.note-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 20px;
  display: flex;
  flex-direction: column;
}

.note-content {
  flex: 1;
}

.note-content h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.note-metadata {
  margin-top: 10px;
  font-size: 0.85em;
  color: #666;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.note-type {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  font-weight: 500;
}

.type-0 { background: #e3f2fd; color: #1976d2; }
.type-1 { background: #fff3e0; color: #f57c00; }
.type-2 { background: #ffebee; color: #d32f2f; }

.note-date {
  color: #666;
}

.note-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #eee;
}

.btn-primary {
  background: #4CAF50;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.btn-secondary {
  background: #f5f5f5;
  color: #333;
  border: 1px solid #ddd;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-edit {
  background: #2196F3;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-delete {
  background: #f44336;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
}

.no-notes {
  text-align: center;
  padding: 40px;
  background: white;
  border-radius: 8px;
  color: #666;
}

button:hover {
  opacity: 0.9;
}

@media (max-width: 768px) {
  .notes-list {
    grid-template-columns: 1fr;
  }
}
</style>