import { useCallback, useEffect, useState } from 'react'
import client, { messaggioErrore } from '../api/client'

/**
 * Hook riutilizzabile per il consumo delle API in sola lettura: incapsula la
 * chiamata HTTP, lo stato di caricamento e la gestione degli errori, così che
 * i componenti si limitino a descrivere l'interfaccia.
 */
export function useApi(url, { params = null, attivo = true } = {}) {
  const [dati, setDati] = useState(null)
  const [errore, setErrore] = useState(null)
  const [caricamento, setCaricamento] = useState(attivo)

  const chiaveParametri = params ? JSON.stringify(params) : null

  const carica = useCallback(() => {
    if (!attivo || !url) {
      setCaricamento(false)
      return
    }

    setCaricamento(true)
    setErrore(null)

    client
      .get(url, { params: chiaveParametri ? JSON.parse(chiaveParametri) : undefined })
      .then((risposta) => setDati(risposta.data))
      .catch((e) => setErrore(messaggioErrore(e, 'Impossibile caricare i dati richiesti.')))
      .finally(() => setCaricamento(false))
  }, [url, chiaveParametri, attivo])

  useEffect(carica, [carica])

  return { dati, elementi: dati?.data ?? [], errore, caricamento, ricarica: carica }
}

/** Specializzazione dell'hook per l'agenda dell'utente autenticato. */
export function useAppuntamenti(stato = null) {
  return useApi('/appuntamenti', { params: stato ? { stato } : null })
}
