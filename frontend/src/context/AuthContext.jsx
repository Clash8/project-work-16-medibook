import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react'
import client, { CHIAVE_TOKEN } from '../api/client'

const AuthContext = createContext(null)

/**
 * Conserva lo stato di autenticazione e lo rende disponibile all'intera
 * applicazione, ripristinando la sessione dal token salvato nel browser.
 */
export function AuthProvider({ children }) {
  const [utente, setUtente] = useState(null)
  const [caricamento, setCaricamento] = useState(true)

  useEffect(() => {
    const token = localStorage.getItem(CHIAVE_TOKEN)

    if (!token) {
      setCaricamento(false)
      return
    }

    client
      .get('/me')
      .then((risposta) => setUtente(risposta.data.data))
      .catch(() => localStorage.removeItem(CHIAVE_TOKEN))
      .finally(() => setCaricamento(false))
  }, [])

  const salvaSessione = useCallback((dati) => {
    localStorage.setItem(CHIAVE_TOKEN, dati.token)
    setUtente(dati.utente)
  }, [])

  const login = useCallback(
    async (credenziali) => {
      const { data } = await client.post('/login', credenziali)
      salvaSessione(data)
      return data.utente
    },
    [salvaSessione],
  )

  const registrazione = useCallback(
    async (dati) => {
      const { data } = await client.post('/register', dati)
      salvaSessione(data)
      return data.utente
    },
    [salvaSessione],
  )

  const logout = useCallback(async () => {
    try {
      await client.post('/logout')
    } finally {
      localStorage.removeItem(CHIAVE_TOKEN)
      setUtente(null)
    }
  }, [])

  const valore = useMemo(
    () => ({
      utente,
      caricamento,
      autenticato: Boolean(utente),
      isMedico: utente?.ruolo === 'medico',
      isPaziente: utente?.ruolo === 'paziente',
      login,
      registrazione,
      logout,
    }),
    [utente, caricamento, login, registrazione, logout],
  )

  return <AuthContext.Provider value={valore}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const contesto = useContext(AuthContext)

  if (!contesto) {
    throw new Error("useAuth deve essere utilizzato all'interno di AuthProvider")
  }

  return contesto
}
