export function useApi() {
  const config = useRuntimeConfig()
  const baseURL = config.public.adapterUrl

  async function get(endpoint) {
    const response = await fetch(`${baseURL}${endpoint}`, {
      method: 'GET',
      headers: { 'Accept': 'application/json' },
    })
    return response.json()
  }

  async function post(endpoint, data) {
    const isFormData = data instanceof FormData
    const response = await fetch(`${baseURL}${endpoint}`, {
      method: 'POST',
      headers: isFormData ? { 'Accept': 'application/json' } : {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: isFormData ? data : JSON.stringify(data),
    })
    return response.json()
  }

  return { get, post }
}
