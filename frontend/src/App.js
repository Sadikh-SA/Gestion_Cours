import './App.css';
import { BrowserRouter as Router, Route, Switch, Routes } from 'react-router-dom';
import LoginRegister from './pages/LoginRegister';
import Cours from './pages/Cours';
//import Projet from './pages/Projet';

function App() {
  return (
    <div className="App">
      <div className="container mt-3">
        <Router>
          <Routes>
            <Route path="/" element={<LoginRegister />}/>
            <Route path="cours" element={<Cours />} />
          </Routes>
        </Router>
      </div>
    </div>
  );
}

export default App;