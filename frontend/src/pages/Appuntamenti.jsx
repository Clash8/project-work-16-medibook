import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import client, { messaggioErrore } from '../api/client'
import { useAppuntamenti } from '../hooks/useApi'
import { useAuth } from '../context/AuthContext'
import Avviso from '../components/Avviso'
import Caricamento from '../components/Caricamento'
import StatoAppuntamento from '../components/StatoAppuntamento'

/** Agenda dell'utente: prenotazioni per il paziente, visite ricevute per il medico. */
export default function Appuntamenti() {
  const { isMedico } = useAuth()
  const posizione = useLocation()
  const { elementi, caricamento, errore, ricarica } = useAppuntamenti()
  const [esito, setEsito] = useState(posizione.state?.messaggio ?? null)
  const [problema, setProblema] = useState(null)

  async function annulla(appuntamento) {
    if (!window.confirm("Confermi l'annullamento della prenotazione?")) {
      return
    }

    setProblema(null)

    try {
      const { data } = await client.delete(`/appuntamenti/${appuntamento.id}`)
      setEsito(data.message)
      ricarica()
    } catch (e) {
      setProblema(messaggioErrore(e, 'Annullamento non riuscito.'))
    }
  }

  if (caricamento) {
    return <Caricamento messaggio="Caricamento dell'agenda..." />
  }

  return (
    <div className="scheda">
      <h1>{isMedico ? 'Agenda delle visite' : 'I miei appuntamenti'}</h1>

      <Avviso tipo="successo">{esito}</Avviso>
      <Avviso tipo="errore">{problema ?? errore}</Avviso>

      {elementi.length === 0 ? (
        <Avviso tipo="informazione">
          {isMedico
            ? 'Non risultano visite in agenda.'
            : 'Non hai ancora prenotato alcuna visita.'}
        </Avviso>
      ) : (
        <div className="tabella__contenitore">
          <table className="tabella">
            <thead>
              <tr>
                <th>Data e ora</th>
                <th>{isMedico ? 'Paziente' : 'Medico'}</th>
                <th>{isMedico ? 'Motivo' : 'Specialità'}</th>
                <th>Stato</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              {elementi.map((appuntamento) => (
                <tr key={appuntamento.id}>
                  <td>{appuntamento.data_ora}</td>
                  <td>
                    {isMedico
                      ? appuntamento.paziente?.nome_completo
                      : appuntamento.medico?.nome_completo}
                  </td>
                  <td>{isMedico ? appuntamento.motivo ?? '—' : appuntamento.medico?.specialita}</td>
                  <td>
                    <StatoAppuntamento stato={appuntamento.stato} />
                  </td>
                  <td className="tabella__azioni">
                    {appuntamento.referto_id && (
                      <Link className="bottone bottone--piccolo" to={`/referti/${appuntamento.referto_id}`}>
                        Referto
                      </Link>
                    )}
                    {isMedico && appuntamento.stato !== 'annullato' && !appuntamento.referto_id && (
                      <Link
                        className="bottone bottone--piccolo"
                        to={`/referti/nuovo/${appuntamento.id}`}
                      >
                        Emetti referto
                      </Link>
                    )}
                    {!isMedico && appuntamento.annullabile && (
                      <button
                        type="button"
                        className="bottone bottone--piccolo bottone--pericolo"
                        onClick={() => annulla(appuntamento)}
                      >
                        Annulla
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  )
}
