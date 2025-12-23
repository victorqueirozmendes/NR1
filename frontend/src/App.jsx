import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { Header } from './components/Header';
import { PrivateRoute, PublicRoute } from './components/ProtectedRoute';
import { Login, Register } from './pages/Auth';
import { StudentDashboard, AdminDashboard } from './pages/Dashboard';
import { AdminCourses, AdminUsers } from './pages/AdminPages';
import { HomePage } from './pages/Home';
import './index.css';
import './pages/Auth.css';
import './pages/Dashboard.css';
import './pages/AdminPages.css';

function App() {
  return (
    <Router>
      <AuthProvider>
        <Header />
        <Routes>
          {/* Public Routes */}
          <Route path="/" element={<HomePage />} />
          <Route path="/login" element={<PublicRoute><Login /></PublicRoute>} />
          <Route path="/register" element={<PublicRoute><Register /></PublicRoute>} />

          {/* Student Routes */}
          <Route path="/dashboard" element={
            <PrivateRoute>
              <StudentDashboard />
            </PrivateRoute>
          } />
          <Route path="/meus-cursos" element={
            <PrivateRoute>
              <StudentDashboard />
            </PrivateRoute>
          } />

          {/* Admin Routes */}
          <Route path="/admin/dashboard" element={
            <PrivateRoute requiredRole="admin">
              <AdminDashboard />
            </PrivateRoute>
          } />
          <Route path="/admin/cursos" element={
            <PrivateRoute requiredRole="admin">
              <AdminCourses />
            </PrivateRoute>
          } />
          <Route path="/admin/usuarios" element={
            <PrivateRoute requiredRole="admin">
              <AdminUsers />
            </PrivateRoute>
          } />

          {/* 404 */}
          <Route path="*" element={
            <div className="container text-center" style={{ marginTop: '50px' }}>
              <h1>404 - Página não encontrada</h1>
            </div>
          } />
        </Routes>
      </AuthProvider>
    </Router>
  );
}

export default App;
