import { Navigate } from 'react-router-dom';
import { useAuth } from '../utils/useAuth';

export function PrivateRoute({ children, requiredRole }) {
  const { user, loading } = useAuth();

  if (loading) {
    return <div className="container text-center" style={{ marginTop: '50px' }}><p>Carregando...</p></div>;
  }

  if (!user) {
    return <Navigate to="/login" />;
  }

  if (requiredRole && user.role !== requiredRole) {
    return <Navigate to="/dashboard" />;
  }

  return children;
}

export function PublicRoute({ children }) {
  const { user, loading } = useAuth();

  if (loading) {
    return <div className="container text-center" style={{ marginTop: '50px' }}><p>Carregando...</p></div>;
  }

  if (user) {
    return <Navigate to="/dashboard" />;
  }

  return children;
}
