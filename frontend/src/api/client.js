import axios from 'axios'

/**
 * Client HTTP centralizzato: incapsula l'indirizzo di base delle API e
 * l'inserimento automatico del token di autenticazione, evitando di
 * duplicare questa logica in ogni componente.
 */
const client = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

export const CHIAVE_TOKEN = 'medibook_token'

client.interceptors.request.use((config) => {
  const token = localStorage.getItem(CHIAVE_TOKEN)

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

// Un token scaduto o revocato riporta l'utente alla schermata di accesso.
client.interceptors.response.use(
  (risposta) => risposta,
  (errore) => {
    if (errore.response?.status === 401 && localStorage.getItem(CHIAVE_TOKEN)) {
      localStorage.removeItem(CHIAVE_TOKEN)
      window.location.assign('/login')
    }

    return Promise.reject(errore)
  },
)

/** Normalizza i messaggi di errore restituiti dal back-end. */
export function messaggioErrore(errore, predefinito = 'Si e verificato un errore imprevisto.') {
  return errore?.response?.data?.message ?? predefinito
}

/** Estrae gli errori di validazione (422) nel formato { campo: messaggio }. */
export function erroriValidazione(errore) {
  const errori = errore?.response?.data?.errors ?? {}

  return Object.fromEntries(
    Object.entries(errori).map(([campo, messaggi]) => [campo, messaggi[0]]),
  )
}

export default client
