// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },

  modules: ['@nuxtjs/tailwindcss'],

  css: ['~/assets/css/main.css'],

  runtimeConfig: {
    public: {
      adapterUrl: process.env.NUXT_PUBLIC_ADAPTER_URL || 'http://localhost:4000/api',
    },
  },

  app: {
    head: {
      title: 'Klaim JHT - BPJS Ketenagakerjaan',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Sistem Pengajuan Klaim Jaminan Hari Tua (JHT) BPJS Ketenagakerjaan' },
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        {
          rel: 'preconnect',
          href: 'https://fonts.googleapis.com',
        },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        },
      ],
    },
  },

  compatibilityDate: '2024-11-01',
})
