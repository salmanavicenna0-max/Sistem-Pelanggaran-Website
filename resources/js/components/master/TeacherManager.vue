<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
            <h2 class="text-lg font-semibold text-gray-800">Guru</h2>
            <div class="space-x-2">
                <button @click="downloadTemplate()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors border border-gray-300">
                    Unduh Template
                </button>
                <button @click="openImportModal()" class="bg-secondary hover:bg-secondary-dark text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Import Excel
                </button>
                <button @click="openModal()" class="bg-primary hover:bg-primary-light text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    + Tambah Guru
                </button>
            </div>
        </div>

        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 font-medium">Username (NUPTK/NIP)</th>
                        <th class="p-4 font-medium">Nama Guru</th>
                        <th class="p-4 font-medium">NIP</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-gray-800 font-medium">{{ item.username }}</td>
                        <td class="p-4 text-gray-800">{{ item.name }}</td>
                        <td class="p-4 text-gray-600">{{ item.nip || '-' }}</td>
                        <td class="p-4">
                            <span :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <button @click="openModal(item)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                            <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="5" class="p-8 text-center text-gray-500">Belum ada data guru.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">{{ isEditing ? 'Edit Guru' : 'Tambah Guru' }}</h3>
                <form @submit.prevent="saveItem">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username (NUPTK / NIP)</label>
                            <input v-model="form.username" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required />
                            <p class="text-xs text-gray-500 mt-1">Digunakan untuk login. Password default sama dengan username.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP (Opsional)</label>
                            <input v-model="form.nip" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" />
                        </div>
                        <div class="flex items-center">
                            <input v-model="form.is_active" type="checkbox" id="teacher_active" class="h-4 w-4 text-primary border-gray-300 rounded" />
                            <label for="teacher_active" class="ml-2 block text-sm text-gray-700">Status Aktif</label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-light focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" :disabled="isLoading">
                            {{ isLoading ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Import Modal -->
        <div v-if="isImportModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">Import Data Guru</h3>
                <form @submit.prevent="submitImport">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Excel (.xlsx, .csv)</label>
                        <input type="file" @change="handleFileUpload" accept=".xlsx,.xls,.csv" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeImportModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-secondary hover:bg-secondary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary" :disabled="isLoading">
                            {{ isLoading ? 'Mengimpor...' : 'Import' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            items: [],
            isModalOpen: false,
            isImportModalOpen: false,
            isEditing: false,
            isLoading: false,
            importFile: null,
            form: {
                id: null,
                name: '',
                username: '',
                nip: '',
                is_active: true
            }
        };
    },
    mounted() {
        this.fetchItems();
    },
    methods: {
        async fetchItems() {
            try {
                const response = await axios.get('/api/teachers');
                this.items = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        openModal(item = null) {
            this.isEditing = !!item;
            if (item) {
                this.form = { ...item, is_active: !!item.is_active };
            } else {
                this.form = { id: null, name: '', username: '', nip: '', is_active: true };
            }
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        async saveItem() {
            this.isLoading = true;
            try {
                if (this.isEditing) {
                    await axios.put(`/api/teachers/${this.form.id}`, this.form);
                } else {
                    await axios.post('/api/teachers', this.form);
                }
                this.closeModal();
                this.fetchItems();
            } catch (error) {
                alert(error.response?.data?.message || "Gagal menyimpan data.");
            } finally {
                this.isLoading = false;
            }
        },
        async deleteItem(id) {
            if (confirm("Yakin ingin menghapus guru ini?")) {
                try {
                    await axios.delete(`/api/teachers/${id}`);
                    this.fetchItems();
                } catch (error) {
                    alert("Gagal menghapus data.");
                }
            }
        },
        downloadTemplate() {
            window.open('/api/teachers/import/template', '_blank');
        },
        openImportModal() {
            this.isImportModalOpen = true;
            this.importFile = null;
        },
        closeImportModal() {
            this.isImportModalOpen = false;
            this.importFile = null;
        },
        handleFileUpload(event) {
            this.importFile = event.target.files[0];
        },
        async submitImport() {
            if (!this.importFile) return;
            
            this.isLoading = true;
            let formData = new FormData();
            formData.append('file', this.importFile);

            try {
                const response = await axios.post('/api/teachers/import', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                alert(response.data.message || 'Berhasil mengimpor data');
                this.closeImportModal();
                this.fetchItems();
            } catch (error) {
                alert(error.response?.data?.message || 'Gagal mengimpor data.');
            } finally {
                this.isLoading = false;
            }
        }
    }
};
</script>
