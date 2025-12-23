import { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import '../styles/auth.css';

export function Register() {
  const [nome, setNome] = useState('');
  const [email, setEmail] = useState('');
  const [senha, setSenha] = useState('');
  const [senhaConfirm, setSenhaConfirm] = useState('');
  const [erro, setErro] = useState('');
  const [sucesso, setSucesso] = useState('');
  const [carregando, setCarregando] = useState(false);
  const { register } = useContext(AuthContext);
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErro('');
    setSucesso('');

    if (senha !== senhaConfirm) {
      setErro('As senhas não coincidem');
      return;
    }

    setCarregando(true);

    try {
      await register(nome, email, senha);
      setSucesso('Conta criada com sucesso! Você será redirecionado...');
      setTimeout(() => navigate('/login'), 2000);
    } catch (error) {
      setErro(error.response?.data?.message || 'Erro ao registrar');
    } finally {
      setCarregando(false);
    }
  };

  return (
    <div className="auth-container">
      <div className="auth-card">
        <h1>NR1 EAD</h1>
        <h2>Registrar</h2>

        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label>Nome</label>
            <input
              type="text"
              value={nome}
              onChange={(e) => setNome(e.target.value)}
              required
            />
          </div>

          <div className="form-group">
            <label>Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>

          <div className="form-group">
            <label>Senha</label>
            <input
              type="password"
              value={senha}
              onChange={(e) => setSenha(e.target.value)}
              required
            />
          </div>

          <div className="form-group">
            <label>Confirmar Senha</label>
            <input
              type="password"
              value={senhaConfirm}
              onChange={(e) => setSenhaConfirm(e.target.value)}
              required
            />
          </div>

          {erro && <div className="erro">{erro}</div>}
          {sucesso && <div className="sucesso">{sucesso}</div>}

          <button type="submit" disabled={carregando}>
            {carregando ? 'Registrando...' : 'Registrar'}
          </button>
        </form>

        <p>
          Já tem conta? <a href="/login">Faça login</a>
        </p>
      </div>
    </div>
  );
}
