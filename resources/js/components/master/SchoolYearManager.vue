<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
            <h2 class="text-lg font-semibold text-gray-800">Tahun Ajaran</h2>
            <button @click="openModal()" class="bg-primary hover:bg-primary-light text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                + Tambah Tahun Ajaran
            </button>
        </div>

        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 font-medium">Nama</th>
                        <th class="p-4 font-medium">Mulai</th>
                        <th class="p-4 font-medium">Selesai</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-gray-800">{{ item.name }}</td>
                        <td class="p-4 text-gray-600">{{ formatDate(item.start_date) }}</td>
                        <td class="p-4 text-gray-600">{{ formatDate(item.end_date) }}</td>
                        <td class="p-4">
                            <span :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <button @click="openModal(item)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                            <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="5" class="p-8 text-center text-gray-500">Belum ada data tahun ajaran.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">{{ isEditing ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}</h3>
                <form @submit.prevent="saveItem">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tahun Ajaran</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required placeholder="Contoh: 2024/2025" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input v-model="form.start_date" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <input v-model="form.end_date" type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required />
                            </div>
                        </div>
                        <div class="flex items-center">
                            <input v-model="form.is_active" type="checkbox" id="is_active" class="h-4 w-4 text-primary border-gray-300 rounded" />
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">Tahun Ajaran Aktif</label>
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
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            items: [],
            isModalOpen: false,
            isEditing: false,
            isLoading: false,
            form: {
                id: null,
                name: '',
                start_date: '',
                end_date: '',
                is_active: false
            }
        };
    },
    mounted() {
        this.fetchItems();
    },
    methods: {
        async fetchItems() {
            try {
                const response = await axios.get('/api/school-years');
                this.items = response.data;
            } catch (error) {
                console.error("Error fetching school years:", error);
                alert("Gagal mengambil data.");
            }
        },
        openModal(item = null) {
            this.isEditing = !!item;
            if (item) {
                this.form = { ...item, is_active: !!item.is_active };
            } else {
                this.form = { id: null, name: '', start_date: '', end_date: '', is_active: true };
            }
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.form = { id: null, name: '', start_date: '', end_date: '', is_active: false };
        },
        async saveItem() {
            this.isLoading = true;
            try {
                if (this.isEditing) {
                    await axios.put(`/api/school-years/${this.form.id}`, this.form);
                } else {
                    await axios.post('/api/school-years', this.form);
                }
                this.closeModal();
                this.fetchItems();
            } catch (error) {
                console.error("Error saving:", error);
                alert(error.response?.data?.message || "Gagal menyimpan data.");
            } finally {
                this.isLoading = false;
            }
        },
        async deleteItem(id) {
            if (confirm("Yakin ingin menghapus data ini?")) {
                try {
                    await axios.delete(`/api/school-years/${id}`);
                    this.fetchItems();
                } catch (error) {
                    console.error("Error deleting:", error);
                    alert("Gagal menghapus data.");
                }
            }
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }
    }
};
</script>
