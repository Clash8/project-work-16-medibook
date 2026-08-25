import { Link } from 'react-router-dom'
import { useApi } from '../hooks/useApi'
import { useAuth } from '../context/AuthContext'
import Avviso from '../components/Avviso'
import Caricamento from '../components/Caricamento'

export default function Referti() {
  const { isMedico } = useAuth()
  const { elementi, caricamento, errore } = useApi('/referti')

  if (caricamento) {
    return <Caricamento messaggio="Caricamento dei referti..." />
  }

  return (
    <div className="scheda">
      <h1>{isMedico ? 'Referti emessi' : 'I miei referti'}</h1>

      <Avviso tipo="errore">{errore}</Avviso>

      {elementi.length === 0 ? (
        <Avviso tipo="informazione">Non risultano referti disponibili.</Avviso>
      ) : (
        <ul className="elenco">
          {elementi.map((referto) => (
            <li key={referto.id} className="elenco__voce">
              <div>
                <h2>{referto.diagnosi}</h2>
                <p className="meta">
                  Emesso il {referto.data_emissione} &middot;{' '}
                  {isMedico
                    ? referto.appuntamento?.paziente?.nome_completo
                    : referto.appuntamento?.medico?.nome_completo}{' '}
                  &middot; {referto.appuntamento?.medico?.specialita}
                </p>
              </div>
              <Link className="bottone bottone--piccolo" to={`/referti/${referto.id}`}>
                Apri
              </Link>
            </li>
          ))}
        </ul>
      )}
    </div>
  )
}
