<template>
  <div class="min-h-screen bg-gray-100">

    <!-- Header -->
    <header class="bg-blue-600 shadow-md">
      <div class="max-w-4xl mx-auto px-4 py-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
          <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div>
          <h1 class="text-white font-bold text-lg leading-tight">BPJS Ketenagakerjaan</h1>
          <p class="text-blue-100 text-xs">Layanan Klaim Online</p>
        </div>
      </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8">

      <!-- ══ SUCCESS STATE ══ -->
      <div v-if="submitSuccess" class="bg-white rounded-xl shadow p-12 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Pengajuan Berhasil!</h2>
        <p class="text-gray-500 mb-6 max-w-md mx-auto">
          Klaim JHT Anda telah berhasil diajukan. Email konfirmasi telah dikirimkan ke
          <strong>{{ form.email }}</strong>.
        </p>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 max-w-xs mx-auto mb-6">
          <p class="text-xs text-gray-500 mb-1">Nomor Klaim</p>
          <p class="text-xl font-bold text-blue-700">{{ submitResult?.no_klaim }}</p>
          <p class="text-xs text-gray-400 mt-1">Simpan nomor ini untuk memantau status klaim Anda</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-md mx-auto text-left mb-8">
          <p class="text-sm font-semibold text-yellow-800 mb-1">📋 Informasi Penting</p>
          <ul class="text-xs text-yellow-700 space-y-1 list-disc list-inside">
            <li>Proses verifikasi membutuhkan 3–7 hari kerja</li>
            <li>Petugas akan menghubungi Anda sesuai cara konfirmasi yang dipilih</li>
            <li>Siapkan dokumen asli saat verifikasi</li>
          </ul>
        </div>
        <button
          class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition"
          @click="resetForm"
        >
          Ajukan Klaim Baru
        </button>
      </div>

      <!-- ══ FORM ══ -->
      <div v-else class="bg-white rounded-xl shadow">

        <!-- Title bar -->
        <div class="px-6 pt-6 pb-4 border-b border-gray-100">
          <h2 class="text-xl font-bold text-gray-800">Klaim JHT (Mengundurkan Diri / Habis Masa Kontrak)</h2>
          <p class="text-sm text-gray-500 mt-1">Harap isi data diri anda. Yakinkan data benar dan valid.</p>
        </div>

        <div class="px-6 py-6 space-y-5">

          <!-- Global error -->
          <div v-if="submitError" class="flex items-start gap-3 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <svg class="w-4 h-4 mt-0.5 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span>{{ submitError }}</span>
          </div>

          <!-- ── 1. Penyebab Klaim (fixed) ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Penyebab Klaim <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700 appearance-none cursor-not-allowed" disabled>
                <option>BERHENTI BEKERJA</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- ── 2. Hubungan dengan Pekerja ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Hubungan dengan Pekerja <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select
                v-model="form.jenis_kepesertaan"
                class="w-full px-3 py-2.5 border rounded-lg text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                :class="errors.jenis_kepesertaan ? 'border-red-400' : 'border-gray-300'"
              >
                <option value="">-- Pilih --</option>
                <option value="tenaga_kerja">TENAGA KERJA</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
            <p v-if="errors.jenis_kepesertaan" class="text-xs text-red-500 mt-1">{{ errors.jenis_kepesertaan }}</p>
          </div>

          <!-- ── 3. Upload E-KTP & Pas Foto ── -->
          <div class="grid grid-cols-2 gap-6">
            <!-- E-KTP -->
            <div>
              <div
                class="border border-dashed rounded-lg h-44 flex flex-col items-center justify-center cursor-pointer transition bg-gray-50"
                :class="idCardPreview ? 'border-blue-400' : errors.foto_ktp ? 'border-red-400' : 'border-gray-300 hover:border-blue-400'"
                @click="$refs.idCardInput.click()"
                @dragover.prevent
                @drop.prevent="onDropIdCard"
              >
                <input ref="idCardInput" type="file" accept="image/jpeg,image/jpg,image/png" class="hidden" @change="onIdCardSelect" />
                <div v-if="idCardPreview" class="relative w-full h-full">
                  <img :src="idCardPreview" alt="E-KTP" class="w-full h-full object-contain rounded-lg p-1" />
                  <button
                    type="button"
                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 shadow text-xs"
                    @click.stop="removeIdCard"
                  >✕</button>
                </div>
                <div v-else class="flex flex-col items-center text-gray-400">
                  <!-- Card icon -->
                  <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                  </svg>
                  <p class="text-sm text-gray-400">Upload E-KTP</p>
                </div>
              </div>
              <button
                type="button"
                class="w-full mt-2 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition"
                @click="$refs.idCardInput.click()"
              >
                Foto E-KTP
              </button>
              <p v-if="errors.foto_ktp" class="text-xs text-red-500 mt-1">{{ errors.foto_ktp }}</p>
            </div>

            <!-- Pas Foto -->
            <div>
              <div
                class="border border-dashed rounded-lg h-44 flex flex-col items-center justify-center cursor-pointer transition bg-gray-50"
                :class="portraitPreview ? 'border-blue-400' : errors.pas_foto ? 'border-red-400' : 'border-gray-300 hover:border-blue-400'"
                @click="$refs.portraitInput.click()"
                @dragover.prevent
                @drop.prevent="onDropPortrait"
              >
                <input ref="portraitInput" type="file" accept="image/jpeg,image/jpg,image/png" class="hidden" @change="onPortraitSelect" />
                <div v-if="portraitPreview" class="relative w-full h-full">
                  <img :src="portraitPreview" alt="Pas Foto" class="w-full h-full object-contain rounded-lg p-1" />
                  <button
                    type="button"
                    class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 shadow text-xs"
                    @click.stop="removePortrait"
                  >✕</button>
                </div>
                <div v-else class="flex flex-col items-center text-gray-400">
                  <!-- Person icon -->
                  <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <p class="text-sm text-gray-400">Pas Foto</p>
                </div>
              </div>
              <button
                type="button"
                class="w-full mt-2 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition"
                @click="$refs.portraitInput.click()"
              >
                Ambil Foto Diri
              </button>
              <p v-if="errors.pas_foto" class="text-xs text-red-500 mt-1">{{ errors.pas_foto }}</p>
            </div>
          </div>

          <!-- ── 4. NIK Pekerja ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Nomor Induk Kependudukan (NIK) Pekerja <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.nik"
              type="text"
              maxlength="16"
              placeholder="Isi Nomor E-KTP"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              :class="errors.nik ? 'border-red-400' : 'border-gray-300'"
              @input="onNikInput"
            />
            <p v-if="errors.nik" class="text-xs text-red-500 mt-1">{{ errors.nik }}</p>
          </div>

          <!-- ── 5. Nama Lengkap ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Nama Lengkap Pekerja (Sesuai KTP) <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.nama_lengkap"
              type="text"
              placeholder="Isi Nama sesuai KTP"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              :class="errors.nama_lengkap ? 'border-red-400' : 'border-gray-300'"
            />
            <p v-if="errors.nama_lengkap" class="text-xs text-red-500 mt-1">{{ errors.nama_lengkap }}</p>
          </div>

          <!-- ── 6. Tempat Lahir & Tanggal Lahir ── -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Tempat lahir Pekerja <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.tempat_lahir"
                type="text"
                placeholder="Isi Tempat lahir"
                class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                :class="errors.tempat_lahir ? 'border-red-400' : 'border-gray-300'"
              />
              <p v-if="errors.tempat_lahir" class="text-xs text-red-500 mt-1">{{ errors.tempat_lahir }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal Lahir Pekerja <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.tanggal_lahir"
                type="date"
                :max="today"
                class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                :class="errors.tanggal_lahir ? 'border-red-400' : 'border-gray-300'"
              />
              <p v-if="errors.tanggal_lahir" class="text-xs text-red-500 mt-1">{{ errors.tanggal_lahir }}</p>
            </div>
          </div>

          <!-- ── 7. Nama Ibu Kandung ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Nama Ibu Kandung Pekerja <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.nama_ibu_kandung"
              type="text"
              placeholder="Isi Nama Ibu Kandung"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              :class="errors.nama_ibu_kandung ? 'border-red-400' : 'border-gray-300'"
            />
            <p v-if="errors.nama_ibu_kandung" class="text-xs text-red-500 mt-1">{{ errors.nama_ibu_kandung }}</p>
          </div>

          <!-- ── 8. Alamat Email ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Alamat Email Pribadi Aktif <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.email"
              type="email"
              placeholder="Isi Alamat Email"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              :class="errors.email ? 'border-red-400' : 'border-gray-300'"
              @blur="validateEmailField"
            />
            <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
          </div>

          <!-- ── 9. Nomor BPJS (KPJ) ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Nomor Peserta BPJS Ketenagakerjaan (KPJ) <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.no_bpjs"
              type="text"
              maxlength="11"
              placeholder="Isi Nomor KPJ"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
              :class="errors.no_bpjs ? 'border-red-400' : 'border-gray-300'"
              @input="onNoBpjsInput"
            />
            <p v-if="errors.no_bpjs" class="text-xs text-red-500 mt-1">{{ errors.no_bpjs }}</p>
          </div>

          <!-- Verification trigger (hidden, fires when both NIK + KPJ filled) -->
          <div v-if="verificationError" class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            {{ verificationError }}
          </div>
          <div v-if="verificationSuccess" class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Kombinasi NIK dan Nomor KPJ ditemukan. Silakan lengkapi data lainnya dengan benar sesuai data kepesertaan.
          </div>

          <!-- ── 10. Sebab Klaim ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Sebab Klaim <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select
                v-model="form.sebab_klaim"
                class="w-full px-3 py-2.5 border rounded-lg text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                :class="errors.sebab_klaim ? 'border-red-400' : 'border-gray-300'"
              >
                <option value="">-- PILIH SEBAB KLAIM --</option>
                <option value="mengundurkan_diri">Mengundurkan Diri</option>
                <option value="berakhir_kontrak">Berakhir Kontrak</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
            <p v-if="errors.sebab_klaim" class="text-xs text-red-500 mt-1">{{ errors.sebab_klaim }}</p>
          </div>

          <!-- ── 11. Jenis Layanan (toggles, read-only dari verifikasi) ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
              Jenis layanan yang didapatkan <span class="text-red-500">*</span>
            </label>
            <div v-if="availableServices.length === 0" class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-400">
              Layanan akan tampil setelah verifikasi data KPJ &amp; NIK berhasil.
            </div>
            <div v-else class="flex flex-wrap gap-6">
              <div
                v-for="service in availableServices"
                :key="service.id"
                class="flex items-center gap-2"
              >
                <!-- Toggle switch — disabled, value ditentukan dari data kepesertaan -->
                <div
                  class="relative w-10 h-5 rounded-full cursor-not-allowed"
                  :class="form.layanan_dipilih.includes(service.id) ? 'bg-blue-400' : 'bg-gray-200'"
                >
                  <div
                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
                    :class="form.layanan_dipilih.includes(service.id) ? 'translate-x-5' : 'translate-x-0.5'"
                  />
                </div>
                <span class="text-sm text-gray-600">{{ service.nama }}</span>
              </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Jenis layanan ditentukan otomatis berdasarkan data kepesertaan Anda.</p>
            <p v-if="errors.layanan_dipilih" class="text-xs text-red-500 mt-1">{{ errors.layanan_dipilih }}</p>
          </div>

          <!-- ── 12. Cara Konfirmasi ── -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
              Pilih cara konfirmasi klaim: <span class="text-red-500">*</span>
            </label>
            <div class="space-y-3">
              <!-- Video Call -->
              <label class="flex items-center gap-3 cursor-pointer">
                <div
                  class="relative w-10 h-5 rounded-full transition-colors duration-200 cursor-pointer shrink-0"
                  :class="form.cara_konfirmasi === 'video_call' ? 'bg-blue-500' : 'bg-gray-300'"
                  @click="form.cara_konfirmasi = form.cara_konfirmasi === 'video_call' ? '' : 'video_call'"
                >
                  <div
                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
                    :class="form.cara_konfirmasi === 'video_call' ? 'translate-x-5' : 'translate-x-0.5'"
                  />
                </div>
                <span class="text-sm text-gray-700">Video Call Dengan Jadwal Konfirmasi Paling Cepat Tanggal</span>
              </label>
              <!-- Datang ke Kantor -->
              <label class="flex items-center gap-3 cursor-pointer">
                <div
                  class="relative w-10 h-5 rounded-full transition-colors duration-200 cursor-pointer shrink-0"
                  :class="form.cara_konfirmasi === 'datang_kantor' ? 'bg-blue-500' : 'bg-gray-300'"
                  @click="form.cara_konfirmasi = form.cara_konfirmasi === 'datang_kantor' ? '' : 'datang_kantor'"
                >
                  <div
                    class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
                    :class="form.cara_konfirmasi === 'datang_kantor' ? 'translate-x-5' : 'translate-x-0.5'"
                  />
                </div>
                <span class="text-sm text-gray-700">Datang Langsung Ke Kantor Cabang Dengan Jadwal Kedatangan Paling Cepat Sesuai Ketersediaan Pada Kantor Cabang Yang Dipilih</span>
              </label>
            </div>
            <p v-if="errors.cara_konfirmasi" class="text-xs text-red-500 mt-2">{{ errors.cara_konfirmasi }}</p>
          </div>

          <!-- Branch office selector — shown when datang ke kantor -->
          <div v-if="form.cara_konfirmasi === 'datang_kantor'">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Pilih Kantor Cabang <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <select
                v-model="form.kantor_cabang_id"
                class="w-full px-3 py-2.5 border rounded-lg text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                :class="errors.kantor_cabang_id ? 'border-red-400' : 'border-gray-300'"
              >
                <option value="">-- Pilih Kantor Cabang --</option>
                <optgroup v-for="(branchList, province) in kantorGrouped" :key="province" :label="province">
                  <option v-for="branch in branchList" :key="branch.id" :value="branch.id">
                    {{ branch.nama }}
                  </option>
                </optgroup>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
            <p v-if="errors.kantor_cabang_id" class="text-xs text-red-500 mt-1">{{ errors.kantor_cabang_id }}</p>
            <div v-if="selectedKantor" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm">
              <p class="font-semibold text-blue-800">{{ selectedKantor.nama }}</p>
              <p class="text-gray-600 text-xs mt-0.5">📍 {{ selectedKantor.alamat }}, {{ selectedKantor.kota }}</p>
              <p v-if="selectedKantor.telepon" class="text-gray-600 text-xs mt-0.5">📞 {{ selectedKantor.telepon }}</p>
            </div>
          </div>

        </div>

        <!-- ── Persetujuan & Submit Footer ── -->
        <div class="px-6 pt-4 pb-6 border-t border-gray-100 space-y-4">

          <!-- Persetujuan checkbox -->
          <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <label class="flex items-start gap-3 cursor-pointer">
              <input
                v-model="form.setuju"
                type="checkbox"
                class="mt-0.5 w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 shrink-0"
              />
              <p class="text-sm text-gray-700 leading-relaxed">
                Saya menyatakan bahwa semua data yang saya isi adalah <strong>benar dan dapat dipertanggungjawabkan</strong>.
                Saya memahami bahwa pengisian data yang tidak benar dapat dikenakan sanksi sesuai
                peraturan perundang-undangan yang berlaku.
              </p>
            </label>
            <p v-if="errors.setuju" class="text-xs text-red-500 mt-2 pl-7 flex items-center gap-1">
              <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              {{ errors.setuju }}
            </p>
          </div>

          <!-- Buttons -->
          <div class="flex items-center justify-end">
            <button
              type="button"
              class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold px-8 py-2.5 rounded-lg transition"
              :disabled="isSubmitting"
              @click="submitKlaim"
            >
              <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
              {{ isSubmitting ? 'Mengirimkan...' : 'Submit' }}
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="mt-8 py-4 text-center text-xs text-gray-400">
      <p>© 2024 BPJS Ketenagakerjaan &nbsp;|&nbsp; Hotline: 175 &nbsp;|&nbsp; care@bpjsketenagakerjaan.go.id</p>
    </footer>
  </div>
</template>

<script setup>
import { validateNIK, validateNoBPJS, validateEmail, validateRequired } from '~/utils/validators'

const { get, post } = useApi()

const today = new Date().toISOString().split('T')[0]

// ── UI state ──────────────────────────────────────────────────────────────────
const isVerifying         = ref(false)
const isSubmitting        = ref(false)
const verificationSuccess = ref(false)
const verificationError   = ref('')
const submitError         = ref('')
const submitSuccess       = ref(false)
const submitResult        = ref(null)

// File previews
const idCardPreview   = ref(null)
const portraitPreview = ref(null)

// Reference data
const branchOfficeList  = ref([])
const availableServices = ref([])

// ── Form model ────────────────────────────────────────────────────────────────
const form = reactive({
  jenis_kepesertaan: '',
  foto_ktp:          null,
  pas_foto:          null,
  nik:               '',
  nama_lengkap:      '',
  tempat_lahir:      '',
  tanggal_lahir:     '',
  nama_ibu_kandung:  '',
  email:             '',
  no_bpjs:           '',
  sebab_klaim:       '',
  layanan_dipilih:   [],
  cara_konfirmasi:   '',
  kantor_cabang_id:  '',
  setuju:            false,
})

// ── Errors ────────────────────────────────────────────────────────────────────
const errors = reactive({
  jenis_kepesertaan: '',
  foto_ktp:          '',
  pas_foto:          '',
  nik:               '',
  nama_lengkap:      '',
  tempat_lahir:      '',
  tanggal_lahir:     '',
  nama_ibu_kandung:  '',
  email:             '',
  no_bpjs:           '',
  sebab_klaim:       '',
  layanan_dipilih:   '',
  cara_konfirmasi:   '',
  kantor_cabang_id:  '',
  setuju:            '',
})

// ── Computed ──────────────────────────────────────────────────────────────────
const kantorGrouped = computed(() =>
  branchOfficeList.value.reduce((groups, branch) => {
    if (!groups[branch.provinsi]) groups[branch.provinsi] = []
    groups[branch.provinsi].push(branch)
    return groups
  }, {})
)

const selectedKantor = computed(() =>
  branchOfficeList.value.find((b) => b.id === form.kantor_cabang_id) || null
)

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(async () => {
  const res = await get('/referensi/kantor-cabang')
  if (res.success) branchOfficeList.value = res.data
})

// ── Watchers: fetch layanan saat NIK + KPJ lengkap, tapi TIDAK auto-fill field lain ────
watch([() => form.nik, () => form.no_bpjs], ([nik, bpjs]) => {
  if (nik.length === 16 && bpjs.length === 11) {
    fetchLayanan()
  } else {
    verificationSuccess.value = false
    verificationError.value   = ''
    availableServices.value   = []
    form.layanan_dipilih      = []
  }
})

// ── File handlers ─────────────────────────────────────────────────────────────
function processFile(file, type) {
  if (!file) return
  const allowed = ['image/jpeg', 'image/jpg', 'image/png']
  if (!allowed.includes(file.type)) {
    if (type === 'ktp') errors.foto_ktp = 'File harus berformat JPG atau PNG.'
    else errors.pas_foto = 'File harus berformat JPG atau PNG.'
    return
  }
  if (file.size > 2 * 1024 * 1024) {
    if (type === 'ktp') errors.foto_ktp = 'Ukuran file maksimal 2MB.'
    else errors.pas_foto = 'Ukuran file maksimal 2MB.'
    return
  }
  const reader = new FileReader()
  reader.onload = (e) => {
    if (type === 'ktp') {
      idCardPreview.value = e.target.result
      form.foto_ktp = file
      errors.foto_ktp = ''
    } else {
      portraitPreview.value = e.target.result
      form.pas_foto = file
      errors.pas_foto = ''
    }
  }
  reader.readAsDataURL(file)
}

function onIdCardSelect(e)  { processFile(e.target.files[0], 'ktp') }
function onPortraitSelect(e){ processFile(e.target.files[0], 'portrait') }
function onDropIdCard(e)    { processFile(e.dataTransfer.files[0], 'ktp') }
function onDropPortrait(e)  { processFile(e.dataTransfer.files[0], 'portrait') }
function removeIdCard()     { idCardPreview.value = null; form.foto_ktp = null }
function removePortrait()   { portraitPreview.value = null; form.pas_foto = null }

// ── Input handlers ────────────────────────────────────────────────────────────
function onNoBpjsInput() {
  form.no_bpjs = form.no_bpjs.replace(/\D/g, '')
  const r = validateNoBPJS(form.no_bpjs)
  errors.no_bpjs = r.valid ? '' : r.message
}

function onNikInput() {
  form.nik = form.nik.replace(/\D/g, '')
  const r = validateNIK(form.nik)
  errors.nik = r.valid ? '' : r.message
}

function validateEmailField() {
  const r = validateEmail(form.email)
  errors.email = r.valid ? '' : r.message
}


// ── Fetch layanan berdasarkan NIK + KPJ (tanpa auto-fill field lain) ─────────
async function fetchLayanan() {
  if (isVerifying.value) return
  verificationError.value   = ''
  verificationSuccess.value = false
  availableServices.value   = []
  form.layanan_dipilih      = []

  isVerifying.value = true
  try {
    const res = await post('/peserta/verifikasi', { no_bpjs: form.no_bpjs, nik: form.nik })
    if (res.success) {
      // Hanya ambil daftar layanan — field lain diisi manual oleh user
      availableServices.value = res.data.layanan
      form.layanan_dipilih    = res.data.layanan.map((s) => s.id)
      verificationSuccess.value = true
    } else {
      verificationError.value = res.message
    }
  } catch {
    verificationError.value = 'Tidak dapat menghubungi server. Silakan coba lagi.'
  } finally {
    isVerifying.value = false
  }
}

// ── Fungsi ini tidak dipakai lagi (tetap ada agar tidak break) ───────────────
async function verifikasiPeserta() {
  return fetchLayanan()
}

// ── Submit ────────────────────────────────────────────────────────────────────
async function submitKlaim() {
  submitError.value = ''
  let valid = true

  // Validasi field yang ada di form
  if (!form.jenis_kepesertaan) { errors.jenis_kepesertaan = 'Hubungan dengan pekerja wajib dipilih.'; valid = false }
  else errors.jenis_kepesertaan = ''

  if (!form.foto_ktp) { errors.foto_ktp = 'Foto E-KTP wajib diunggah.'; valid = false }
  if (!form.pas_foto) { errors.pas_foto = 'Pas foto wajib diunggah.'; valid = false }

  const nikR = validateNIK(form.nik)
  errors.nik = nikR.valid ? '' : nikR.message
  if (!nikR.valid) valid = false

  ;[
    ['nama_lengkap',     'Nama Lengkap'],
    ['tempat_lahir',     'Tempat Lahir'],
    ['tanggal_lahir',    'Tanggal Lahir'],
    ['nama_ibu_kandung', 'Nama Ibu Kandung'],
  ].forEach(([key, label]) => {
    const r = validateRequired(form[key], label)
    errors[key] = r.valid ? '' : r.message
    if (!r.valid) valid = false
  })

  const emailR = validateEmail(form.email)
  errors.email = emailR.valid ? '' : emailR.message
  if (!emailR.valid) valid = false

  const bpjsR = validateNoBPJS(form.no_bpjs)
  errors.no_bpjs = bpjsR.valid ? '' : bpjsR.message
  if (!bpjsR.valid) valid = false

  if (!form.sebab_klaim) { errors.sebab_klaim = 'Sebab klaim wajib dipilih.'; valid = false }
  else errors.sebab_klaim = ''

  if (form.layanan_dipilih.length === 0) {
    errors.layanan_dipilih = 'Pilih minimal satu jenis layanan.'; valid = false
  } else errors.layanan_dipilih = ''

  if (!form.cara_konfirmasi) {
    errors.cara_konfirmasi = 'Cara konfirmasi wajib dipilih.'; valid = false
  } else errors.cara_konfirmasi = ''

  if (form.cara_konfirmasi === 'datang_kantor' && !form.kantor_cabang_id) {
    errors.kantor_cabang_id = 'Pilih kantor cabang tujuan.'; valid = false
  } else errors.kantor_cabang_id = ''

  if (!verificationSuccess.value) {
    verificationError.value = 'Verifikasi kombinasi NIK dan Nomor KPJ belum berhasil. Pastikan keduanya terisi dengan benar.'
    valid = false
  }

  if (!form.setuju) {
    errors.setuju = 'Anda harus menyetujui pernyataan di atas sebelum mengirim.'
    valid = false
  } else {
    errors.setuju = ''
  }

  if (!valid) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    return
  }

  isSubmitting.value = true
  try {
    const payload = new FormData()

    // Hanya kirim field yang ada di form
    const textFields = [
      'no_bpjs', 'nik', 'nama_lengkap', 'nama_ibu_kandung',
      'tempat_lahir', 'tanggal_lahir', 'email',
      'sebab_klaim', 'cara_konfirmasi',
    ]
    textFields.forEach((key) => {
      if (form[key] !== null && form[key] !== undefined && form[key] !== '') {
        payload.append(key, form[key])
      }
    })

    if (form.kantor_cabang_id) payload.append('kantor_cabang_id', form.kantor_cabang_id)
    form.layanan_dipilih.forEach((id) => payload.append('layanan_dipilih[]', id))
    payload.append('foto_ktp', form.foto_ktp)
    payload.append('pas_foto', form.pas_foto)

    const res = await post('/klaim', payload)
    if (res.success) {
      submitResult.value  = res.data
      submitSuccess.value = true
      window.scrollTo({ top: 0, behavior: 'smooth' })
    } else {
      submitError.value = res.message || 'Terjadi kesalahan saat mengirim klaim.'
      // Tampilkan error per-field dari backend ke field yang sesuai
      if (res.errors) {
        Object.keys(res.errors).forEach((key) => {
          if (key in errors) {
            errors[key] = Array.isArray(res.errors[key]) ? res.errors[key][0] : res.errors[key]
          }
        })
      }
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  } catch {
    submitError.value = 'Tidak dapat menghubungi server. Periksa koneksi internet Anda.'
  } finally {
    isSubmitting.value = false
  }
}

// ── Reset ─────────────────────────────────────────────────────────────────────
function resetForm() {
  Object.assign(form, {
    jenis_kepesertaan: '', foto_ktp: null, pas_foto: null,
    nik: '', nama_lengkap: '', tempat_lahir: '', tanggal_lahir: '',
    nama_ibu_kandung: '', email: '', no_bpjs: '', sebab_klaim: '',
    layanan_dipilih: [], cara_konfirmasi: '', kantor_cabang_id: '',
    setuju: false,
  })
  idCardPreview.value       = null
  portraitPreview.value     = null
  submitSuccess.value       = false
  submitResult.value        = null
  verificationSuccess.value = false
  verificationError.value   = ''
  availableServices.value   = []
}
</script>
