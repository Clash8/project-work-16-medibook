import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { messaggioErrore, erroriValidazione } from '../api/client'
import Avviso from '../components/Avviso'

const moduloVuoto = {
  nome: '',
  cognome: '',
  email: '',
  telefono: '',
  codice_fiscale: '',
  data_nascita: '',
  password: '',
  password_confirmation: '',
}

export default function Registrazione() {
  const { registrazione } = useAuth()
  const navigate = useNavigate()
  const [modulo, setModulo] = useState(moduloVuoto)
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

    // I campi facoltativi lasciati vuoti non vengono inviati al back-end.
    const dati = Object.fromEntries(Object.entries(modulo).filter(([, valore]) => valore !== ''))

    try {
      await registrazione(dati)
      navigate('/prenota')
    } catch (e) {
      setErrori(erroriValidazione(e))
      setErrore(messaggioErrore(e, 'Registrazione non riuscita.'))
    } finally {
      setInvio(false)
    }
  }

  const campi = [
    { nome: 'nome', etichetta: 'Nome', tipo: 'text', obbligatorio: true },
    { nome: 'cognome', etichetta: 'Cognome', tipo: 'text', obbligatorio: true },
    { nome: 'email', etichetta: 'Indirizzo email', tipo: 'email', obbligatorio: true },
    { nome: 'telefono', etichetta: 'Telefono (facoltativo)', tipo: 'tel' },
    { nome: 'codice_fiscale', etichetta: 'Codice fiscale (facoltativo)', tipo: 'text' },
    { nome: 'data_nascita', etichetta: 'Data di nascita (facoltativa)', tipo: 'date' },
    { nome: 'password', etichetta: 'Password', tipo: 'password', obbligatorio: true },
    { nome: 'password_confirmation', etichetta: 'Conferma password', tipo: 'password', obbligatorio: true },
  ]

  return (
    <div className="scheda scheda--stretta">
      <h1>Registrazione paziente</h1>
      <p className="sottotitolo">Bastano pochi dati per prenotare la prima visita.</p>

      <Avviso tipo="errore">{errore}</Avviso>

      <form onSubmit={invia} noValidate>
        {campi.map((campo) => (
          <div key={campo.nome}>
            <label htmlFor={campo.nome}>{campo.etichetta}</label>
            <input
              id={campo.nome}
              name={campo.nome}
              type={campo.tipo}
              value={modulo[campo.nome]}
              onChange={aggiorna}
              required={campo.obbligatorio}
            />
            {errori[campo.nome] && <span className="campo__errore">{errori[campo.nome]}</span>}
          </div>
        ))}

        <button type="submit" className="bottone bottone--primario" disabled={invio}>
          {invio ? 'Registrazione in corso...' : 'Crea account'}
        </button>
      </form>

      <p className="nota">
        Hai già un account? <Link to="/login">Accedi</Link>
      </p>
    </div>
  )
}
