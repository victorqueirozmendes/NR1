import React from 'react';

export function HomePage() {
  return (
    <div className="container" style={{ marginTop: '50px' }}>
      <div className="card text-center">
        <h1>Bem-vindo à NR1 EAD</h1>
        <p style={{ fontSize: '18px', marginTop: '20px' }}>
          Plataforma de educação a distância para aprender e crescer
        </p>
        <div style={{ marginTop: '30px' }}>
          <a href="/login" style={{
            display: 'inline-block',
            padding: '12px 30px',
            background: '#007bff',
            color: 'white',
            borderRadius: '4px',
            marginRight: '10px'
          }}>
            Fazer Login
          </a>
          <a href="/register" style={{
            display: 'inline-block',
            padding: '12px 30px',
            background: '#28a745',
            color: 'white',
            borderRadius: '4px'
          }}>
            Registrar
          </a>
        </div>
      </div>
    </div>
  );
}
