import { Link } from 'react-router-dom';
import { useAuth } from '../utils/useAuth';
import './Header.css';

export function Header() {
  const { user, logout } = useAuth();

  const handleLogout = () => {
    logout();
    window.location.href = '/login';
  };

  return (
    <header className="header">
      <div className="container header-content">
        <Link to="/" className="logo">NR1 EAD</Link>
        <nav className="nav">
          {user ? (
            <>
              <span className="user-name">Olá, {user.nome}</span>
              {user.role === 'admin' && (
                <>
                  <Link to="/admin/dashboard">Painel Admin</Link>
                  <Link to="/admin/cursos">Cursos</Link>
                  <Link to="/admin/usuarios">Usuários</Link>
                </>
              )}
              {user.role === 'aluno' && (
                <Link to="/meus-cursos">Meus Cursos</Link>
              )}
              <button onClick={handleLogout} className="btn-logout">Sair</button>
            </>
          ) : (
            <>
              <Link to="/login">Login</Link>
              <Link to="/register">Registrar</Link>
            </>
          )}
        </nav>
      </div>
    </header>
  );
}
