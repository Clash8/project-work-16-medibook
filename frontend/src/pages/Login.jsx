import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { messaggioErrore, erroriValidazione } from '../api/client'
import Avviso from '../components/Avviso'

export default function Login() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [modulo, setModulo] = useState({ email: '', password: '' })
  const [errori, setErrori] = useState({})
  const [errore, setErrore] = useState(null)
  const [invio, setInvio] = useState(false)

  function aggiorna(evento) {
    const { name, value } = evento.target
    setModulo((precedente) => ({ ...precedente, [name]: value }))
  }

  async function invia(evento) {
    evento.preventDefault()
    setInvio(true)
    setErrore(null)
    setErrori({})

    try {
      const utente = await login(modulo)
      navigate(utente.ruolo === 'medico' ? '/agenda' : '/appuntamenti')
    } catch (e) {
      setErrori(erroriValidazione(e))
      setErrore(messaggioErrore(e, 'Accesso non riuscito.'))
    } finally {
      setInvio(false)
    }
  }

  return (
    <div className="scheda scheda--stretta">
      <h1>Accedi a MediBook</h1>
      <p className="sottotitolo">Gestisci le tue visite e consulta i referti della clinica.</p>

      <Avviso tipo="errore">{errore}</Avviso>

      <form onSubmit={invia} noValidate>
        <label htmlFor="email">Indirizzo email</label>
        <input
          id="email"
          name="email"
          type="email"
          value={modulo.email}
          onChange={aggiorna}
          autoComplete="email"
          required
        />
        {errori.email && <span className="campo__errore">{errori.email}</span>}

        <label htmlFor="password">Password</label>
        <input
          id="password"
          name="password"
          type="password"
          value={modulo.password}
          onChange={aggiorna}
          autoComplete="current-password"
          required
        />
        {errori.password && <span className="campo__errore">{errori.password}</span>}

        <button type="submit" className="bottone bottone--primario" disabled={invio}>
          {invio ? 'Accesso in corso...' : 'Accedi'}
        </button>
      </form>

      <p className="nota">
        Non hai ancora un account? <Link to="/registrazione">Registrati come paziente</Link>
      </p>
    </div>
  )
}
