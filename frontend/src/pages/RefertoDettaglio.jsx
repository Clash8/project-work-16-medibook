import { Link, useParams } from 'react-router-dom'
import { useApi } from '../hooks/useApi'
import Avviso from '../components/Avviso'
import Caricamento from '../components/Caricamento'

export default function RefertoDettaglio() {
  const { id } = useParams()
  const { dati, caricamento, errore } = useApi(`/referti/${id}`)

  if (caricamento) {
    return <Caricamento messaggio="Caricamento del referto..." />
  }

  if (errore) {
    return (
      <div className="scheda">
        <Avviso tipo="errore">{errore}</Avviso>
        <Link to="/referti">Torna all'elenco</Link>
      </div>
    )
  }

  const referto = dati.data
  const visita = referto.appuntamento

  return (
    <article className="scheda">
      <p className="briciole">
        <Link to="/referti">Referti</Link> / Referto n. {referto.id}
      </p>

      <h1>{referto.diagnosi}</h1>
      <p className="meta">
        Visita del {visita?.data_ora} &middot; {visita?.medico?.nome_completo} &middot;{' '}
        {visita?.medico?.specialita} &middot; Referto emesso il {referto.data_emissione}
      </p>

      <section className="sezione">
        <h2>Descrizione clinica</h2>
        <p>{referto.descrizione}</p>
      </section>

      {referto.prescrizione && (
        <section className="sezione">
          <h2>Prescrizione</h2>
          <p>{referto.prescrizione}</p>
        </section>
      )}
    </article>
  )
}
