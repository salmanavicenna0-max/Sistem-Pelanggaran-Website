<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
            <h2 class="text-lg font-semibold text-gray-800">Kelas</h2>
            <button @click="openModal()" class="bg-primary hover:bg-primary-light text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                + Tambah Kelas
            </button>
        </div>

        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 font-medium">Tahun Ajaran</th>
                        <th class="p-4 font-medium">Tingkat</th>
                        <th class="p-4 font-medium">Nama Kelas</th>
                        <th class="p-4 font-medium">Wali Kelas</th>
                        <th class="p-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-gray-800">{{ item.school_year?.name }}</td>
                        <td class="p-4 text-gray-600">{{ item.grade }}</td>
                        <td class="p-4 text-gray-800 font-medium">{{ item.name }}</td>
                        <td class="p-4 text-gray-600">{{ item.homeroom_teacher?.name || '-' }}</td>
                        <td class="p-4 text-right space-x-2">
                            <button @click="openModal(item)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>
                            <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="5" class="p-8 text-center text-gray-500">Belum ada data kelas.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">{{ isEditing ? 'Edit Kelas' : 'Tambah Kelas' }}</h3>
                <form @submit.prevent="saveItem">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                            <select v-model="form.school_year_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required>
                                <option value="" disabled>Pilih Tahun Ajaran</option>
                                <option v-for="sy in schoolYears" :key="sy.id" :value="sy.id">{{ sy.name }}</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat</label>
                                <select v-model="form.grade" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
                                <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" required placeholder="Contoh: X IPA 1" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas (Opsional)</label>
                            <select v-model="form.homeroom_teacher_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary">
                                <option :value="null">-- Tidak Ada --</option>
                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }}</option>
                            </select>
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
            schoolYears: [],
            teachers: [],
            isModalOpen: false,
            isEditing: false,
            isLoading: false,
            form: {
                id: null,
                school_year_id: '',
                homeroom_teacher_id: null,
                name: '',
                grade: 'X'
            }
        };
    },
    mounted() {
        this.fetchItems();
        this.fetchSchoolYears();
        this.fetchTeachers();
    },
    methods: {
        async fetchItems() {
            try {
                const response = await axios.get('/api/school-classes');
                this.items = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async fetchSchoolYears() {
            try {
                const response = await axios.get('/api/school-years');
                this.schoolYears = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async fetchTeachers() {
            try {
                const response = await axios.get('/api/teachers');
                this.teachers = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        openModal(item = null) {
            this.isEditing = !!item;
            if (item) {
                this.form = { ...item };
            } else {
                this.form = { id: null, school_year_id: '', homeroom_teacher_id: null, name: '', grade: 'X' };
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
                    await axios.put(`/api/school-classes/${this.form.id}`, this.form);
                } else {
                    await axios.post('/api/school-classes', this.form);
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
            if (confirm("Yakin ingin menghapus kelas ini?")) {
                try {
                    await axios.delete(`/api/school-classes/${id}`);
                    this.fetchItems();
                } catch (error) {
                    alert("Gagal menghapus data.");
                }
            }
        }
    }
};
</script>
