import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import client, { erroriValidazione, messaggioErrore } from '../api/client'
import { useApi } from '../hooks/useApi'
import Avviso from '../components/Avviso'
import Caricamento from '../components/Caricamento'

/** Emissione del referto da parte del medico che ha effettuato la visita. */
export default function NuovoReferto() {
  const { appuntamentoId } = useParams()
  const navigate = useNavigate()
  const appuntamento = useApi(`/appuntamenti/${appuntamentoId}`)
  const [modulo, setModulo] = useState({ diagnosi: '', descrizione: '', prescrizione: '' })
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
      await client.post('/referti', {
        appuntamento_id: Number(appuntamentoId),
        ...modulo,
      })

      navigate('/referti')
    } catch (e) {
      setErrori(erroriValidazione(e))
      setErrore(messaggioErrore(e, 'Emissione del referto non riuscita.'))
    } finally {
      setInvio(false)
    }
  }

  if (appuntamento.caricamento) {
    return <Caricamento messaggio="Caricamento della visita..." />
  }

  const visita = appuntamento.dati?.data

  return (
    <div className="scheda">
      <h1>Emetti referto</h1>
      {visita && (
        <p className="meta">
          Visita del {visita.data_ora} &middot; paziente {visita.paziente?.nome_completo}
        </p>
      )}

      <Avviso tipo="errore">{errore ?? appuntamento.errore}</Avviso>

      <form onSubmit={invia}>
        <label htmlFor="diagnosi">Diagnosi</label>
        <input id="diagnosi" name="diagnosi" value={modulo.diagnosi} onChange={aggiorna} required />
        {errori.diagnosi && <span className="campo__errore">{errori.diagnosi}</span>}

        <label htmlFor="descrizione">Descrizione clinica</label>
        <textarea
          id="descrizione"
          name="descrizione"
          rows={6}
          value={modulo.descrizione}
          onChange={aggiorna}
          required
        />
        {errori.descrizione && <span className="campo__errore">{errori.descrizione}</span>}

        <label htmlFor="prescrizione">Prescrizione (facoltativa)</label>
        <textarea
          id="prescrizione"
          name="prescrizione"
          rows={4}
          value={modulo.prescrizione}
          onChange={aggiorna}
        />

        <button type="submit" className="bottone bottone--primario" disabled={invio}>
          {invio ? 'Salvataggio...' : 'Emetti referto'}
        </button>
      </form>
    </div>
  )
}
