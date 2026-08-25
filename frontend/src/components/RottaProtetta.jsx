import { Navigate, useLocation } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import Caricamento from './Caricamento'

/** Consente l'accesso alla rotta solo agli utenti autenticati con il ruolo atteso. */
export default function RottaProtetta({ children, ruolo = null }) {
  const { autenticato, caricamento, utente } = useAuth()
  const posizione = useLocation()

  if (caricamento) {
    return <Caricamento messaggio="Verifica della sessione in corso..." />
  }

  if (!autenticato) {
    return <Navigate to="/login" state={{ da: posizione.pathname }} replace />
  }

  if (ruolo && utente.ruolo !== ruolo) {
    return <Navigate to="/" replace />
  }

  return children
}
