<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
            <h2 class="text-lg font-semibold text-gray-800">Aturan Prestasi / Penebusan</h2>
            <button @click="openModal()" class="bg-primary hover:bg-primary-light text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                + Tambah Aturan Prestasi
            </button>
        </div>

        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 font-medium">Kode</th>
                        <th class="p-4 font-medium">Prestasi / Tindakan Positif</th>
                        <th class="p-4 font-medium">Poin</th>
                        <th class="p-4 font-medium">Pilar / Kategori</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-gray-800 font-semibold">{{ item.code }}</td>
                        <td class="p-4 text-gray-800">{{ item.name }}</td>
                        <td class="p-4 font-semibold text-green-600">+{{ item.points }}</td>
                        <td class="p-4 text-gray-600">{{ item.pillar }}</td>
                        <td class="p-4">
                            <span :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            <button @click="openModal(item)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                            <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="6" class="p-8 text-center text-gray-500">Belum ada data aturan prestasi.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4">{{ isEditing ? 'Edit Aturan Prestasi' : 'Tambah Aturan Prestasi' }}</h3>
                <form @submit.prevent="saveItem">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Prestasi</label>
                            <input v-model="form.code" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required placeholder="Contoh: PR.1" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Poin (Angka Positif)</label>
                            <input v-model.number="form.points" type="number" min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required placeholder="10" />
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Prestasi / Penebusan</label>
                        <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilar / Kategori</label>
                        <input v-model="form.pillar" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required placeholder="Contoh: Akademik, Ekstrakurikuler, Penebusan" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Tambahan</label>
                        <textarea v-model="form.description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" placeholder="Opsional..."></textarea>
                    </div>

                    <div class="flex items-center mb-6">
                        <input v-model="form.is_active" type="checkbox" id="ach_active" class="h-4 w-4 text-primary border-gray-300 rounded" />
                        <label for="ach_active" class="ml-2 block text-sm text-gray-700">Aturan Berlaku (Aktif)</label>
                    </div>

                    <div class="flex justify-end space-x-3">
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
                code: '',
                name: '',
                points: 10,
                pillar: '',
                description: '',
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
                const response = await axios.get('/api/achievement-rules');
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
                this.form = { id: null, code: '', name: '', points: 10, pillar: '', description: '', is_active: true };
            }
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        async saveItem() {
            if (this.form.points <= 0) {
                alert("Poin prestasi harus berupa angka positif!");
                return;
            }
            this.isLoading = true;
            try {
                if (this.isEditing) {
                    await axios.put(`/api/achievement-rules/${this.form.id}`, this.form);
                } else {
                    await axios.post('/api/achievement-rules', this.form);
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
            if (confirm("Yakin ingin menghapus aturan prestasi ini?")) {
                try {
                    await axios.delete(`/api/achievement-rules/${id}`);
                    this.fetchItems();
                } catch (error) {
                    alert("Gagal menghapus data.");
                }
            }
        }
    }
};
</script>
