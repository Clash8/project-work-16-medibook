import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import client, { messaggioErrore } from '../api/client'
import { useApi } from '../hooks/useApi'
import Avviso from '../components/Avviso'
import Caricamento from '../components/Caricamento'

function oggi() {
  return new Date().toISOString().slice(0, 10)
}

/**
 * Percorso guidato di prenotazione: specialità, medico, data e slot.
 * Ogni passo interroga l'API corrispondente e abilita il successivo.
 */
export default function Prenotazione() {
  const navigate = useNavigate()
  const [specialitaId, setSpecialitaId] = useState('')
  const [medicoId, setMedicoId] = useState('')
  const [data, setData] = useState(oggi())
  const [slot, setSlot] = useState(null)
  const [motivo, setMotivo] = useState('')
  const [errore, setErrore] = useState(null)
  const [invio, setInvio] = useState(false)

  const specialita = useApi('/specialita')

  const medici = useApi('/medici', {
    params: specialitaId ? { specialita: specialitaId } : null,
    attivo: Boolean(specialitaId),
  })

  const disponibilita = useApi('/disponibilita', {
    params: medicoId ? { medico: medicoId, data } : null,
    attivo: Boolean(medicoId && data),
  })

  // Cambiando un passo del percorso, le scelte successive vanno azzerate.
  function scegliSpecialita(valore) {
    setSpecialitaId(valore)
    setMedicoId('')
    setSlot(null)
  }

  function scegliMedico(valore) {
    setMedicoId(valore)
    setSlot(null)
  }

  function scegliData(valore) {
    setData(valore)
    setSlot(null)
  }

  const slotDisponibili = disponibilita.dati?.data?.slot ?? []

  async function prenota(evento) {
    evento.preventDefault()
    setInvio(true)
    setErrore(null)

    try {
      await client.post('/appuntamenti', {
        medico_id: Number(medicoId),
        data_ora: `${data} ${slot}`,
        ...(motivo ? { motivo } : {}),
      })

      navigate('/appuntamenti', { state: { messaggio: 'Prenotazione confermata.' } })
    } catch (e) {
      setErrore(messaggioErrore(e, 'Prenotazione non riuscita.'))
      disponibilita.ricarica()
      setSlot(null)
    } finally {
      setInvio(false)
    }
  }

  return (
    <div className="scheda">
      <h1>Prenota una visita</h1>
      <p className="sottotitolo">Scegli la specialità, il medico e l'orario che preferisci.</p>

      <Avviso tipo="errore">{errore ?? specialita.errore}</Avviso>

      <form onSubmit={prenota}>
        <div className="griglia griglia--tre">
          <div>
            <label htmlFor="specialita">1. Specialità</label>
            <select
              id="specialita"
              value={specialitaId}
              onChange={(e) => scegliSpecialita(e.target.value)}
              required
            >
              <option value="">Seleziona una specialità</option>
              {specialita.elementi.map((voce) => (
                <option key={voce.id} value={voce.id}>
                  {voce.nome} &mdash; {voce.costo.toFixed(2)} EUR
                </option>
              ))}
            </select>
          </div>

          <div>
            <label htmlFor="medico">2. Medico</label>
            <select
              id="medico"
              value={medicoId}
              onChange={(e) => scegliMedico(e.target.value)}
              disabled={!specialitaId || medici.caricamento}
              required
            >
              <option value="">
                {medici.caricamento ? 'Caricamento...' : 'Seleziona un medico'}
              </option>
              {medici.elementi.map((medico) => (
                <option key={medico.id} value={medico.id}>
                  {medico.nome_completo}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label htmlFor="data">3. Data</label>
            <input
              id="data"
              type="date"
              value={data}
              min={oggi()}
              onChange={(e) => scegliData(e.target.value)}
              disabled={!medicoId}
              required
            />
          </div>
        </div>

        {medicoId && (
          <div className="passo">
            <h2>4. Orario</h2>

            {disponibilita.caricamento && <Caricamento messaggio="Verifica delle disponibilità..." />}

            {!disponibilita.caricamento && slotDisponibili.length === 0 && (
              <Avviso tipo="informazione">
                Nessuno slot disponibile per la data selezionata: prova con un altro giorno.
              </Avviso>
            )}

            <div className="slot">
              {slotDisponibili.map((orario) => (
                <button
                  key={orario}
                  type="button"
                  className={`slot__voce ${slot === orario ? 'slot__voce--scelto' : ''}`}
                  onClick={() => setSlot(orario)}
                >
                  {orario}
                </button>
              ))}
            </div>
          </div>
        )}

        {slot && (
          <div className="passo">
            <label htmlFor="motivo">Motivo della visita (facoltativo)</label>
            <input
              id="motivo"
              type="text"
              value={motivo}
              maxLength={255}
              onChange={(e) => setMotivo(e.target.value)}
              placeholder="Es. controllo periodico"
            />

            <button type="submit" className="bottone bottone--primario" disabled={invio}>
              {invio ? 'Prenotazione in corso...' : `Conferma per il ${data} alle ${slot}`}
            </button>
          </div>
        )}
      </form>
    </div>
  )
}
