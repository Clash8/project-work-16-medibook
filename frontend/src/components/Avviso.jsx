/** Messaggio di esito: tipo "errore", "successo" oppure "informazione". */
export default function Avviso({ tipo = 'informazione', children }) {
  if (!children) {
    return null
  }

  return (
    <div className={`avviso avviso--${tipo}`} role={tipo === 'errore' ? 'alert' : 'status'}>
      {children}
    </div>
  )
}
